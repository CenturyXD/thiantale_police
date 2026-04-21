<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Content;

class StoreContentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // จัดการสิทธิ์ที่ middleware แล้ว
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            // 'section' => 'required|in:' . implode(',', array_keys(Content::SECTIONS)),
            'section' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'body_images' => 'nullable|array',
            'body_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx',
            'status' => 'required|in:draft,published',
            'publish_date' => 'nullable|date',
            'order' => 'nullable|integer|min:0',
            'body_image_ids' => 'nullable|array',
            'body_image_ids.*' => 'integer|exists:content_images,id',
            'slot' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'display_on' => 'nullable|string|max:255'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'กรุณากรอกหัวข้อ',
            'title.max' => 'หัวข้อต้องไม่เกิน 255 ตัวอักษร',
            'section.required' => 'กรุณาเลือกหมวดหมู่',
            'section.in' => 'หมวดหมู่ไม่ถูกต้อง',
            'image.image' => 'ไฟล์ต้องเป็นรูปภาพเท่านั้น',
            'image.mimes' => 'รูปภาพต้องเป็นไฟล์ jpeg, png, jpg, gif หรือ webp',
            'image.max' => 'รูปภาพต้องมีขนาดไม่เกิน 2MB',
            'body_images.array' => 'รูปในเนื้อหาต้องเป็นรายการ',
            'body_images.*.image' => 'รูปในเนื้อหาต้องเป็นไฟล์รูปภาพเท่านั้น',
            'body_images.*.mimes' => 'รูปในเนื้อหาต้องเป็นไฟล์ jpeg, png, jpg, gif หรือ webp',
            'body_images.*.max' => 'รูปในเนื้อหาแต่ละรูปต้องมีขนาดไม่เกิน 4MB',
            'file.file' => 'ไฟล์ไม่ถูกต้อง',
            'file.mimes' => 'ไฟล์ต้องเป็น pdf, doc, docx, xls, xlsx, ppt หรือ pptx',
            'file.max' => 'ไฟล์ต้องมีขนาดไม่เกิน 10MB',
            'status.required' => 'กรุณาเลือกสถานะ',
            'status.in' => 'สถานะไม่ถูกต้อง',
            'publish_date.date' => 'วันที่เผยแพร่ไม่ถูกต้อง',
            'order.integer' => 'ลำดับต้องเป็นตัวเลข',
            'order.min' => 'ลำดับต้องมากกว่าหรือเท่ากับ 0',
            'body_image_ids.array' => 'รายการรูปในเนื้อหาต้องเป็น array',
            'body_image_ids.*.integer' => 'รหัสรูปในเนื้อหาต้องเป็นตัวเลข',
            'body_image_ids.*.exists' => 'ไม่พบรูปในเนื้อหาบางรายการ'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'หัวข้อ',
            'content' => 'เนื้อหา',
            'section' => 'หมวดหมู่',
            'image' => 'รูปภาพ',
            'body_images' => 'รูปในเนื้อหา',
            'file' => 'ไฟล์แนบ',
            'status' => 'สถานะ',
            'publish_date' => 'วันที่เผยแพร่',
            'order' => 'ลำดับ',
            'body_image_ids' => 'รูปในเนื้อหา'
        ];
    }
}
