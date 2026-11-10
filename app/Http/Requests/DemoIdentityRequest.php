<?php

namespace App\Http\Requests;

use App\Support\LabData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DemoIdentityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'username' => ['bail', ...($this->isMethod('get') ? ['sometimes'] : []), 'required', 'string', 'min:3', 'max:30'],
            'role' => ['bail', ...($this->isMethod('get') ? ['sometimes'] : []), 'required', 'string', Rule::in(LabData::ROLES)],
            ...($this->isMethod('post') ? ['password' => ['bail', 'nullable', 'string', 'min:6', 'max:72']] : []),
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'username.required' => 'Nama pengguna wajib diisi.',
            'username.string' => 'Nama pengguna harus berupa teks.',
            'username.min' => 'Nama pengguna minimal 3 karakter.',
            'username.max' => 'Nama pengguna maksimal 30 karakter.',
            'role.required' => 'Pilih peran untuk membuka demo.',
            'role.string' => 'Peran harus berupa teks.',
            'role.in' => 'Peran yang dipilih tidak tersedia.',
            'password.string' => 'Kata sandi simulasi harus berupa teks.',
            'password.min' => 'Kata sandi simulasi minimal 6 karakter, atau biarkan kosong.',
            'password.max' => 'Kata sandi simulasi maksimal 72 karakter.',
        ];
    }

    /** @return array{username: string, role: string} */
    public function identity(): array
    {
        return [
            'username' => LabData::canonicalUsername($this->validated('username', 'Guest')),
            'role' => $this->validated('role', LabData::ROLES[0]),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return route('login');
    }
}
