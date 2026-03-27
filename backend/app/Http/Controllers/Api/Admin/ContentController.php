<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContentRequest;
use App\Http\Requests\UpdateContentRequest;
use App\Models\Content;
use App\Models\ContentImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ContentController extends Controller
{
    public function index(Request $request)
    {
        $query = Content::with(['author:id,name,email', 'bodyImages']);

        if ($request->has('section')) {
            $query->where('section', $request->section);
        }

        if( $request->has('slot')) {
            $query->where('slot', $request->slot);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('content', 'LIKE', "%{$search}%");
            });
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 15);
        $contents = $query->paginate($perPage);

        return response()->json($contents);
    }

    public function store(StoreContentRequest $request)
    {
        $data = $request->validated();
        $data['author_id'] = auth()->id();
        $bodyImageIds = $data['body_image_ids'] ?? [];

        if ($request->hasFile('image')) {
            $data['image'] = $this->storePublicFile($request->file('image'), 'uploads/contents/images');
        }

        if ($request->hasFile('file')) {
            $data['file_url'] = $this->storePublicFile($request->file('file'), 'uploads/contents/files');
        }

        unset($data['body_image_ids'], $data['body_images']);

        $content = Content::create($data);

        if ($request->hasFile('body_images')) {
            $createdBodyImageIds = $this->createBodyImagesForContent($content, $request->file('body_images'));
            $bodyImageIds = array_merge($bodyImageIds, $createdBodyImageIds);
        }

        if (is_array($bodyImageIds) && count($bodyImageIds) > 0) {
            $this->syncBodyImages($content, $bodyImageIds);
        }

        $content->load(['author:id,name,email', 'bodyImages']);

        return response()->json([
            'success' => true,
            'message' => 'Content created successfully',
            'data' => $content,
        ], 201);
    }

    public function show($id)
    {
        $content = Content::with(['author:id,name,email', 'bodyImages'])->find($id);

        if (!$content) {
            return response()->json([
                'success' => false,
                'message' => 'Content not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $content,
        ]);
    }

    public function update(UpdateContentRequest $request, $id)
    {
        $content = Content::find($id);

        if (!$content) {
            return response()->json([
                'success' => false,
                'message' => 'Content not found',
            ], 404);
        }

        $data = $request->validated();
        $bodyImageIds = $data['body_image_ids'] ?? null;

        if ($request->boolean('remove_image') && $content->image) {
            $this->deletePublicFile($content->image);
            $data['image'] = null;
        }

        if ($request->boolean('remove_file') && $content->file_url) {
            $this->deletePublicFile($content->file_url);
            $data['file_url'] = null;
        }

        if ($request->hasFile('image')) {
            if ($content->image) {
                $this->deletePublicFile($content->image);
            }
            $data['image'] = $this->storePublicFile($request->file('image'), 'uploads/contents/images');
        }

        if ($request->hasFile('file')) {
            if ($content->file_url) {
                $this->deletePublicFile($content->file_url);
            }
            $data['file_url'] = $this->storePublicFile($request->file('file'), 'uploads/contents/files');
        }

        unset($data['remove_image'], $data['remove_file'], $data['body_image_ids'], $data['body_images']);

        $content->update($data);

        if ($request->hasFile('body_images')) {
            $createdBodyImageIds = $this->createBodyImagesForContent($content, $request->file('body_images'));
            if (is_array($bodyImageIds)) {
                $bodyImageIds = array_merge($bodyImageIds, $createdBodyImageIds);
            }
        }

        if (is_array($bodyImageIds)) {
            $this->syncBodyImages($content, $bodyImageIds);
        }

        $content->load(['author:id,name,email', 'bodyImages']);

        return response()->json([
            'success' => true,
            'message' => 'Content updated successfully',
            'data' => $content,
        ]);
    }

    public function destroy($id)
    {
        $content = Content::find($id);

        if (!$content) {
            return response()->json([
                'success' => false,
                'message' => 'Content not found',
            ], 404);
        }

        if ($content->image) {
            $this->deletePublicFile($content->image);
        }

        if ($content->file_url) {
            $this->deletePublicFile($content->file_url);
        }

        foreach ($content->bodyImages as $bodyImage) {
            $this->deletePublicFile($bodyImage->image_path);
        }

        $content->delete();

        return response()->json([
            'success' => true,
            'message' => 'Content deleted successfully',
        ]);
    }

    public function getSections()
    {
        return response()->json([
            'success' => true,
            'data' => Content::SECTIONS,
        ]);
    }

    public function uploadEditorImage(Request $request)
    {
        $baseValidated = $request->validate([
            'content_id' => 'required|exists:contents,id',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('images')) {
            $request->validate([
                'images' => 'required|array|min:1',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            ]);

            $files = $request->file('images');
        } elseif ($request->hasFile('image')) {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            ]);

            $singleFile = $request->file('image');
            $files = is_array($singleFile) ? $singleFile : [$singleFile];
        } else {
            throw ValidationException::withMessages([
                'images' => ['At least one image is required.'],
            ]);
        }

        $contentId = (int) $baseValidated['content_id'];
        $baseSortOrder = (int) ($baseValidated['sort_order'] ?? 0);
        $createdImages = [];

        foreach ($files as $index => $file) {
            $path = $this->storePublicFile($file, 'uploads/contents/body-images');

            $contentImage = ContentImage::create([
                'content_id' => $contentId,
                'image_path' => $path,
                'sort_order' => $baseSortOrder + $index + 1,
            ]);

            $createdImages[] = [
                'id' => $contentImage->id,
                'content_id' => $contentImage->content_id,
                'path' => $contentImage->image_path,
                'url' => $contentImage->image_url,
                'sort_order' => $contentImage->sort_order,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Editor image uploaded successfully',
            'data' => count($createdImages) === 1 ? $createdImages[0] : $createdImages,
        ], 201);
    }

    public function destroyEditorImage(Content $content, ContentImage $contentImage)
    {
        if ($contentImage->content_id !== $content->id) {
            return response()->json([
                'success' => false,
                'message' => 'Image does not belong to this content',
            ], 422);
        }

        $this->deletePublicFile($contentImage->image_path);
        $contentImage->delete();

        return response()->json([
            'success' => true,
            'message' => 'Content image deleted successfully',
        ]);
    }

    private function storePublicFile($uploadedFile, string $directory): string
    {
        $targetDirectory = public_path($directory);
        File::ensureDirectoryExists($targetDirectory);

        $filename = Str::random(40) . '.' . $uploadedFile->getClientOriginalExtension();
        $uploadedFile->move($targetDirectory, $filename);

        return $directory . '/' . $filename;
    }

    private function deletePublicFile(?string $relativePath): void
    {
        if (!$relativePath) {
            return;
        }

        $fullPath = public_path($relativePath);

        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }

    private function syncBodyImages(Content $content, array $bodyImageIds): void
    {
        $normalizedIds = array_values(array_unique(array_map('intval', $bodyImageIds)));

        $selectedImages = ContentImage::whereIn('id', $normalizedIds)->get()->keyBy('id');

        if (count($normalizedIds) !== $selectedImages->count()) {
            throw ValidationException::withMessages([
                'body_image_ids' => ['Some body image IDs are invalid.'],
            ]);
        }

        foreach ($normalizedIds as $index => $imageId) {
            $image = $selectedImages->get($imageId);

            $image->update([
                'content_id' => $content->id,
                'sort_order' => $index + 1,
            ]);
        }

        $imagesToRemove = empty($normalizedIds)
            ? $content->bodyImages()->get()
            : $content->bodyImages()->whereNotIn('id', $normalizedIds)->get();

        foreach ($imagesToRemove as $imageToRemove) {
            $this->deletePublicFile($imageToRemove->image_path);
            $imageToRemove->delete();
        }
    }

    private function createBodyImagesForContent(Content $content, $uploadedFiles): array
    {
        $files = is_array($uploadedFiles) ? $uploadedFiles : [$uploadedFiles];
        $lastSortOrder = (int) $content->bodyImages()->max('sort_order');
        $createdIds = [];

        foreach ($files as $index => $file) {
            $path = $this->storePublicFile($file, 'uploads/contents/body-images');

            $image = ContentImage::create([
                'content_id' => $content->id,
                'image_path' => $path,
                'sort_order' => $lastSortOrder + $index + 1,
            ]);

            $createdIds[] = $image->id;
        }

        return $createdIds;
    }
}
