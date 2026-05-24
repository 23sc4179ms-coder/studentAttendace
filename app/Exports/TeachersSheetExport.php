<?php

namespace App\Exports;

use App\Models\Teacher;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class TeachersSheetExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    /**
     * @return Collection<int, Teacher>
     */
    public function collection(): Collection
    {
        return Teacher::with(['userAccount'])
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'ID',
            'First Name',
            'Middle Name',
            'Last Name',
            'Email',
            'Contact No',
        ];
    }

    /**
    * @param Teacher $teacher
     * @return array<int, string|null>
     */
    public function map($teacher): array
    {
        return [
            (string) $teacher->id,
            $teacher->first_name,
            $teacher->middle_name,
            $teacher->last_name,
            $teacher->userAccount?->email ?? $teacher->email,
            $teacher->contact_no,
        ];
    }

    public function title(): string
    {
        return 'Teachers';
    }
}
