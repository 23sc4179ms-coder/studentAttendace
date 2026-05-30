@extends('layouts.admin')

@section('title')
    Courses
@endsection

@section('header')
    @parent
@endsection

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">Courses</h5>
                <small class="text-muted">Manage courses</small>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('course.create') }}" class="btn-ui btn-ui--primary">Add Course</a>
                <a href="{{ route('manageStudents') }}" class="btn-ui btn-ui--ghost">Back</a>
            </div>
        </div>

        <div class="card-body">
            @if (session('message'))
                <div class="alert alert-success">{{ session('message') }}</div>
            @endif

            <div id="coursesList"></div>
        </div>
    </div>
</div>
@endsection

@section('footer')
    @parent
@endsection


