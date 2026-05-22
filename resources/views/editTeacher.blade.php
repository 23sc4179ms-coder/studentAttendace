@extends('format.admin_layout')

@section('title')
    Edit Teacher
@endsection

@section('header')
    @parent
@endsection

@section('content')
    <h1 class="h4 mb-3">Edit Teacher</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                {{ $error }}<br>
            @endforeach
        </div>
    @endif

    <form id="editTeacherForm" action="{{ route('teacher.update', $teacher->id) }}" method="POST" enctype="multipart/form-data" class="d-grid gap-3" style="max-width: 520px;">
        @csrf
        @method('PUT')

        <div>
            <label for="firstName" class="form-label">First Name</label>
            <input id="firstName" type="text" name="first_name" class="form-control" value="{{ old('first_name', $teacher->first_name) }}" required>
            @error('first_name')
                <p style="color: red;font size: 0.875em">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="middleName" class="form-label">Middle Name</label>
            <input id="middleName" type="text" name="middle_name" class="form-control" value="{{ old('middle_name', $teacher->middle_name) }}">
            @error('middle_name')
                <p style="color: red;font size: 0.875em">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="lastName" class="form-label">Last Name</label>
            <input id="lastName" type="text" name="last_name" class="form-control" value="{{ old('last_name', $teacher->last_name) }}" required>
            @error('last_name')
                <p style="color: red;font size: 0.875em">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" class="form-control" value="{{ old('email', optional($teacher->userAccount)->email ?? $teacher->email) }}" required>
            @error('email')
                <p style="color: red;font-size: 0.875em">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="contactNo" class="form-label">Contact No</label>
            <input id="contactNo" type="text" name="contact_no" class="form-control" value="{{ old('contact_no', $teacher->contact_no) }}">
            @error('contact_no')
                <p style="color: red;font size: 0.875em">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="profileImage" class="form-label">Profile Image</label>
            <input id="profileImage" type="file" name="profile_image" class="form-control" accept="image/*">
            @error('profile_image')
                <p style="color: red;font-size: 0.875em">{{ $message }}</p>
            @enderror
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('teacher.index') }}" class="btn-ui btn-ui--ghost">Cancel</a>
            <button type="submit" id="updateTeacher" class="btn-ui btn-ui--primary">Update</button>
        </div>
    </form>
@endsection

@section('footer')
    @parent
@endsection


