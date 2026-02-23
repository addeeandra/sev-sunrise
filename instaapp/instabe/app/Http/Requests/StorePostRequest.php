<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'text' => ['nullable', 'string', 'max:2200'],
            'images' => ['required', 'array', 'min:1', 'max:10'],
            'images.*' => ['required', 'image', 'mimes:jpeg,png,gif,webp', 'max:10240'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'images.required' => 'At least one image is required.',
            'images.min' => 'At least one image is required.',
            'images.*.image' => 'Each file must be a valid image.',
            'images.*.max' => 'Each image must not exceed 10MB.',
        ];
    }
}
