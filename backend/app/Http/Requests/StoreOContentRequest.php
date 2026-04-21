<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOContentRequest extends FormRequest
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
            'os_id' => 'required|exists:os,id',
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'section' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
            'status' => 'required|in:draft,published',
            'publish_date' => 'nullable|date',
            'order' => 'nullable|integer|min:0',
            'display_on' => 'nullable|string|max:255',
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
            'os_id.required' => 'กรุณาเลือกหัวข้อย่อย os',
            'os_id.exists' => 'ไม่พบข้อมูล os ที่เลือก',
            'section.required' => 'กรุณาระบุ section',
            'status.required' => 'กรุณาเลือกสถานะ',
            'status.in' => 'สถานะไม่ถูกต้อง',
            'image.image' => 'ไฟล์ต้องเป็นรูปภาพเท่านั้น',
            'file.file' => 'ไฟล์แนบไม่ถูกต้อง',
            'publish_date.date' => 'วันที่เผยแพร่ไม่ถูกต้อง',
            'order.integer' => 'ลำดับต้องเป็นตัวเลข',
            'order.min' => 'ลำดับต้องมากกว่าหรือเท่ากับ 0',
        ];
    }
}
