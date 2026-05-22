<?php

namespace App\Exports;

use App\Models\Student;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class StudentsSheetExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    /**
     * @return Collection<int, Student>
     */
    public function collection(): Collection
    {
        return Student::with(['degree', 'userAccount'])
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
            'Degree',
        ];
    }

    /**
     * @param Student $student
     * @return array<int, string|null>
     */
    public function map($student): array
    {
        return [
            (string) $student->id,
            $student->first_name,
            $student->middle_name,
            $student->last_name,
            $student->userAccount?->email ?? null,
            $student->contact_no,
            $student->degree?->degree_name ?? null,
        ];
    }

    public function title(): string
    {
        return 'Students';
    }
}
