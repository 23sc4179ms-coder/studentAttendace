@extends('format.portal_admin_layout')

@section('title')
    Homepage
    @endsection

    @section('header')
        @parent
    @endsection

    @section('content')
       
        Welcome, {{ $logged_user }}!<br>
         This is {{ $logged_role }} Dashboard<br>
        <a
            href="{{ route('teacherDashboard.edit', $user->id) }}"
            class="btn-ui btn-ui--ghost btn-ui--sm"
        >
            Change Password
        </a>
        <br>
        <h1 class="h4 mt-3">Teacher Dashboard</h1>

        @php
            $coursesList = $courses ?? collect();
        @endphp

        <div class="row mt-3">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <strong>Courses to Teach</strong>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush" id="teacherCourses">
                            @forelse($coursesList as $course)
                                @php /** @var \App\Models\Course $course */ @endphp
                                <button
                                    type="button"
                                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center btn-show-attendance"
                                    data-course-id="{{ $course->id }}"
                                    data-url="{{ route('teacherDashboard.course.attendance', $course->id) }}"
                                >
                                    <span>{{ $course->course_name }}</span>
                                    <span class="badge text-bg-secondary">{{ (int) ($course->attendance_count ?? 0) }}</span>
                                </button>
                            @empty
                                <div class="p-3 text-muted">No courses assigned yet.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8 mt-3 mt-md-0">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <strong>Attendance Records</strong>
                    </div>
                    <div class="card-body" id="attendanceRecordsList">
                        <div class="text-muted">Click a course to view attendance records.</div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('footer')
        @parent
    @endsection


