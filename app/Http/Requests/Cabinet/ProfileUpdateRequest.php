<?php

namespace App\Http\Requests\Cabinet;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\SafeString;
use App\Rules\NoUrlForName;

class ProfileUpdateRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', new SafeString(false), new NoUrlForName()],
            'email' => ['required', 'email:rfc,dns', 'max:255', 'unique:users,email,' . $this->user()->id],
            'gender' => ['nullable', 'in:male,female,other'],
            'phone' => ['nullable', 'regex:/^\d{3,20}$/'],
            'country_code' => ['nullable', 'regex:/^\+\d{1,4}$/'],
            'country' => ['nullable', 'string', 'max:2'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,heic,heif', 'max:2560'],
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
            'avatar.image' => 'The file must be an image.',
            'avatar.mimes' => 'Only JPG, PNG, GIF, and HEIC images are allowed.',
            'avatar.max' => 'The image size must not exceed 2.5 MB.',
        ];
    }
}
