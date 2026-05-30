<?php

namespace App\Http\Controllers;

use App\Exports\StudentsTeachersReport;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function studentsTeachers()
    {
        $filename = 'students_teachers_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new StudentsTeachersReport(), $filename);
    }
}

