<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubOContentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // ให้ middleware จัดการสิทธิ์
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'main_sub_o_content_id' => 'required|exists:main_o_contents,id',
            'title' => 'nullable|string|max:255',
            'section' => 'sometimes|nullable|string|max:255',
            'content' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
            'status' => 'sometimes|required|in:draft,published',
            'publish_date' => 'nullable|date',
            'order' => 'nullable|integer|min:0',
            'author_id' => 'nullable|exists:users,id',
            'url' => 'nullable|string|max:255',
        ];
    }
}
