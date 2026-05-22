@extends('format.admin_layout')

@section('title')
	Add Course
@endsection

@section('header')
	@parent
@endsection

@section('content')
	<h1 class="h4 mb-3">Add Course</h1>

	<div id="addCourseAlert" class="alert alert-danger d-none"></div>

	<input type="hidden" id="csrf_token" value="{{ csrf_token() }}">

	<div class="d-grid gap-3" style="max-width: 520px;">
		<div>
			<label for="courseName" class="form-label">Course Name</label>
			<input id="courseName" type="text" name="course_name" class="form-control" value="{{ old('course_name') }}" required>
		</div>

		<div class="d-flex gap-2">
			<a href="{{ route('course.index') }}" class="btn-ui btn-ui--ghost">Cancel</a>
			<button type="button" class="btn-ui btn-ui--primary" id="saveCourseBtn" data-url="{{ route('course.store') }}">Save</button>
		</div>
	</div>
@endsection

@section('footer')
	@parent
@endsection
