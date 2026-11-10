<?php

namespace App\Http\Requests;

use App\Support\LabData;
use Illuminate\Validation\Rule;

class InventoryRequest extends DemoIdentityRequest
{
    /** @return array<string, array<mixed>> */
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'q' => ['bail', 'nullable', 'string', 'max:100'],
            'kategori' => ['bail', 'nullable', 'string', Rule::in(LabData::categories())],
            'status' => ['bail', 'nullable', 'string', Rule::in(['tersedia', 'dipinjam', 'pemeliharaan'])],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            ...parent::messages(),
            'q.string' => 'Pencarian harus berupa teks.',
            'q.max' => 'Pencarian maksimal 100 karakter.',
            'kategori.string' => 'Kategori harus berupa teks.',
            'kategori.in' => 'Kategori perangkat tidak tersedia.',
            'status.string' => 'Status harus berupa teks.',
            'status.in' => 'Status perangkat tidak tersedia.',
        ];
    }

    /** @return array{q: string, kategori: string, status: string} */
    public function filters(): array
    {
        return [
            'q' => $this->validated('q') ?? '',
            'kategori' => $this->validated('kategori') ?? '',
            'status' => $this->validated('status') ?? '',
        ];
    }
}
