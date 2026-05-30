@extends('layouts.admin')

@section('title')
    Student Page
@endsection

@section('header')
    @parent
@endsection

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">Students & Teachers</h5>
                <small class="text-muted">This is {{ $logged_role }} Page — Welcome, {{ $logged_user }}!</small>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('student.create') }}" class="btn-ui btn-ui--primary">Add Student</a>
                <a href="{{ route('teacher.create') }}" class="btn-ui btn-ui--secondary">Add Teacher</a>
                @if(($logged_role ?? null) === 'admin')
                    <a href="{{ route('attendance.index') }}" class="btn-ui btn-ui--ghost">Attendance Records</a>
                @endif
            </div>
        </div>

        <div class="card-body">
            @if (session('message'))
                <div class="alert alert-success">{{ session('message') }}</div>
            @endif

            <div class="row mb-3 align-items-center">
                <div class="col-md-6 mb-2 mb-md-0">
                    <div class="input-group">
                        <input id="tableSearch" type="search" class="form-control" placeholder="Search...">
                        <button id="clearSearch" class="btn btn-outline-secondary" type="button">Clear</button>
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="btn-group" role="group">
                        <button type="button" id="showStudentsBtn" class="btn btn-outline-primary active">Students</button>
                        <button type="button" id="showTeachersBtn" class="btn btn-outline-primary">Teachers</button>
                    </div>
                </div>
            </div>

            {{-- Student table container (used by autoReloadStudents) --}}
            <div id="studentsList"></div>

            {{-- Teacher table container (used by autoReloadTeachers) --}}
            <div id="teachersList" class="d-none"></div>
        </div>
    </div>
</div>

@include('layouts.includes.student_modals', [
    'courses' => $courses ?? collect(),
    'allTeachers' => $allTeachers ?? collect(),
    'logged_role' => $logged_role ?? null,
])
@endsection

@section('footer')
    @parent
@endsection

