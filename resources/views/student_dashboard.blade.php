@extends('layouts.student')

@section('title')
    Homepage
    @endsection

    @section('header')
        @parent
    @endsection

    @section('content')
       
        <div class="container mt-4">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Student Dashboard</h5>
                        <small class="text-muted">Welcome, {{ $logged_user }}!</small>
                    </div>
                    <a
                        href="{{ route('studentDashboard.edit', $user->id) }}"
                        class="btn-ui btn-ui--ghost btn-ui--sm"
                    >
                        Change Password
                    </a>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card shadow-sm">
                                <div class="card-header">
                                    <strong>My Attendance Courses</strong>
                                </div>
                                <div class="card-body p-0" id="studentCourses">
                                    <div class="list-group list-group-flush">
                                        @forelse(($attendanceCourses ?? collect()) as $c)
                                            <button
                                                type="button"
                                                class="list-group-item list-group-item-action btn-student-course"
                                                data-url="{{ route('studentDashboard.course.details', $c->id) }}"
                                            >
                                                {{ $c->course_name }}
                                            </button>
                                        @empty
                                            <div class="p-3 text-muted">No attendance records yet.</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8 mt-3 mt-md-0">
                            <div class="card shadow-sm">
                                <div class="card-header">
                                    <strong>Course Info</strong>
                                </div>
                                <div class="card-body" id="studentCourseDetails">
                                    <div class="text-muted">Click a course to view classmates and teacher.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('footer')
        @parent
    @endsection




