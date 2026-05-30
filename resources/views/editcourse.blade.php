@extends('layouts.admin')

@section('title')
	Edit Course
@endsection

@section('header')
	@parent
@endsection

@section('content')
	<h1 class="h4 mb-3">Edit Course</h1>

	<div id="editCourseAlert" class="alert alert-danger d-none"></div>

	<input type="hidden" id="csrf_token" value="{{ csrf_token() }}">

	<div class="d-grid gap-3" style="max-width: 520px;">
		<div>
			<label for="courseName" class="form-label">Course Name</label>
			<input id="courseName" type="text" name="course_name" class="form-control" value="{{ old('course_name', $course->course_name) }}" required>
		</div>

		<div class="d-flex gap-2">
			<a href="{{ route('course.index') }}" class="btn-ui btn-ui--ghost">Cancel</a>
			<button type="button" class="btn-ui btn-ui--primary" id="updateCourseBtn" data-url="{{ route('course.update', $course->id) }}">Update</button>
		</div>
	</div>
@endsection

@section('footer')
	@parent
@endsection


