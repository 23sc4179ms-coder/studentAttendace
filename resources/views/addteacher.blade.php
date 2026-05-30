@extends('layouts.admin')

@section('title')
    Add Teacher
@endsection

@section('header')
    @parent
@endsection

@section('content')
    <h1 class="h4 mb-3">Add Teacher</h1>

    <!-- No <form> tag – only AJAX will handle submission -->
    @csrf   {{-- CSRF token is still needed for AJAX (already set globally in $.ajaxSetup) --}}

    <div>
        <label for="firstName" class="form-label">First Name</label>
        <input id="firstName" type="text" name="first_name" class="form-control" value="{{ old('first_name') }}">
        @error('first_name')
            <p style="color: red; font-size: 0.875em;">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="middleName" class="form-label">Middle Name</label>
        <input id="middleName" type="text" name="middle_name" class="form-control" value="{{ old('middle_name') }}">
        @error('middle_name')
            <p style="color: red; font-size: 0.875em;">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="lastName" class="form-label">Last Name</label>
        <input id="lastName" type="text" name="last_name" class="form-control" value="{{ old('last_name') }}">
        @error('last_name')
            <p style="color: red; font-size: 0.875em;">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="email" class="form-label">Email</label>
        <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}">
        @error('email')
            <p style="color: red; font-size: 0.875em;">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="contactNo" class="form-label">Contact No</label>
        <input id="contactNo" type="text" name="contact_no" class="form-control" value="{{ old('contact_no') }}">
        @error('contact_no')
            <p style="color: red; font-size: 0.875em;">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="username" class="form-label">Username</label>
        <input id="username" type="text" name="username" class="form-control" value="{{ old('username') }}">
        @error('username')
            <p style="color: red; font-size: 0.875em;">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password" class="form-label">Password</label>
        <input id="password" type="password" name="password" class="form-control">
        @error('password')
            <p style="color: red; font-size: 0.875em;">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="profileImage" class="form-label">Profile Image</label>
        <input id="profileImage" type="file" name="profile_image" class="form-control" accept="image/*">
        @error('profile_image')
            <p style="color: red; font-size: 0.875em;">{{ $message }}</p>
        @enderror
    </div>

    <div class="d-flex gap-2 mt-3">
        {{-- Cancel button – adjust route name if needed (teacher.index or teachers.index) --}}
        <a href="{{ route('teacher.index') }}" class="btn-ui btn-ui--ghost">Cancel</a>
        <button type="button" id="savedTeacher" class="btn-ui btn-ui--primary">Save</button>
    </div>

    @if($errors->any())
        <div class="alert alert-danger mt-3">
            @foreach($errors->all() as $error)
                {{ $error }}<br>
            @endforeach
        </div>
    @endif
@endsection

@section('footer')
    @parent
@endsection

