@extends('layouts.admin')

@section('title')
    Attendance Records
@endsection

@section('header')
    @parent
@endsection

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">Attendance Records</h5>
                <small class="text-muted">Track student attendance by course, section, and teacher.</small>
            </div>
            <a href="{{ route('student.index') }}" class="btn-ui btn-ui--ghost">Back</a>
        </div>

        <div class="card-body">
            <div id="bulkAttendanceAlert" class="alert alert-danger d-none"></div>

            <div class="row g-3">
                <div class="col-md-4">
                    <label for="bulkCourseId" class="form-label">Course</label>
                    <select id="bulkCourseId" class="form-select" required>
                        <option value="">Select course...</option>
                        @foreach(($courses ?? []) as $course)
                            @php /** @var \App\Models\Course $course */ @endphp
                            <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="bulkTeacherId" class="form-label">Teacher</label>
                    <select id="bulkTeacherId" class="form-select" required>
                        <option value="">Select teacher...</option>
                        @foreach(($teachers ?? []) as $t)
                            @php /** @var \App\Models\teacher $t */ @endphp
                            @php $tName = trim($t->first_name . ' ' . ($t->middle_name ?? '') . ' ' . $t->last_name); @endphp
                            <option value="{{ $t->id }}">{{ $tName }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="bulkSection" class="form-label">Section</label>
                    <input id="bulkSection" type="text" class="form-control" placeholder="e.g. BSIT-2A" />
                </div>
            </div>

            <hr class="my-4" />

            <div class="row mb-3 align-items-center">
                <div class="col-md-6 mb-2 mb-md-0">
                    <div class="input-group">
                        <input id="attendanceStudentsSearch" type="search" class="form-control" placeholder="Search students...">
                        <button id="attendanceStudentsSearchBtn" class="btn btn-outline-secondary" type="button">Search</button>
                        <button id="attendanceStudentsClearSearch" class="btn btn-outline-secondary" type="button">Clear</button>
                    </div>
                </div>
                <div class="col-md-6 text-md-end d-flex justify-content-md-end gap-2">
                    <div class="text-muted align-self-center" id="selectedCountText">0 selected</div>
                    <button type="button" class="btn-ui btn-ui--primary" id="bulkAttendanceBtn" data-url="{{ route('course.bulkAttendance') }}">Save Attendance</button>
                </div>
            </div>

            <div id="attendanceStudentsList" data-url="{{ route('attendance.students') }}"></div>
        </div>
    </div>
</div>
@endsection

@section('footer')
    @parent
@endsection


