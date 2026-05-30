<?php

namespace App\Exports;

use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentsTeachersReport implements FromCollection, WithHeadings
{
    /**
     * @return Collection<int, array<string, string|null>>
     */
    public function collection(): Collection
    {
        $students = Student::with(['degree', 'userAccount'])
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get()
            ->map(function (Student $student): array {
                return [
                    'type' => 'Student',
                    'id' => (string) $student->id,
                    'first_name' => $student->first_name,
                    'middle_name' => $student->middle_name,
                    'last_name' => $student->last_name,
                    'email' => $student->userAccount?->email ?? null,
                    'contact_no' => $student->contact_no,
                    'degree' => $student->degree?->degree_name ?? null,
                ];
            });

        $teachers = Teacher::with(['userAccount'])
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get()
            ->map(function (Teacher $teacher): array {
                return [
                    'type' => 'Teacher',
                    'id' => (string) $teacher->id,
                    'first_name' => $teacher->first_name,
                    'middle_name' => $teacher->middle_name,
                    'last_name' => $teacher->last_name,
                    'email' => $teacher->userAccount?->email ?? $teacher->email,
                    'contact_no' => $teacher->contact_no,
                    'degree' => null,
                ];
            });

        return $students
            ->merge($teachers)
            ->sortBy([
                ['type', 'asc'],
                ['last_name', 'asc'],
                ['first_name', 'asc'],
            ])
            ->values();
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'Type',
            'ID',
            'First Name',
            'Middle Name',
            'Last Name',
            'Email',
            'Contact No',
            'Degree',
        ];
    }
}

