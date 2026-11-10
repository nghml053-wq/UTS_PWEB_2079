<?php

namespace App\Http\Requests;

use App\Support\LabData;
use Illuminate\Validation\Rule;

class ScheduleRequest extends DemoIdentityRequest
{
    /** @return array<string, array<mixed>> */
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'tanggal' => ['bail', 'nullable', 'string', 'date_format:Y-m-d'],
            'ruangan' => ['bail', 'nullable', 'string', Rule::in(array_column(LabData::rooms(), 'id'))],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            ...parent::messages(),
            'tanggal.string' => 'Tanggal harus berupa teks.',
            'tanggal.date_format' => 'Gunakan tanggal yang valid dengan format YYYY-MM-DD.',
            'ruangan.string' => 'Ruangan harus berupa teks.',
            'ruangan.in' => 'Ruangan yang dipilih tidak tersedia.',
        ];
    }
}
