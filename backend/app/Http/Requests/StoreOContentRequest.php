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
            'name' => 'required|string|max:255',
            'status' => 'nullable|string|max:255',
            'os_id' => 'required|exists:os,id',
            'created_by' => 'nullable|exists:users,id',
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
            'name.required' => 'กรุณาระบุชื่อเนื้อหา',
            'os_id.required' => 'กรุณาเลือกหัวข้อย่อย os',
            'os_id.exists' => 'ไม่พบข้อมูล os ที่เลือก',
        ];
    }
}
