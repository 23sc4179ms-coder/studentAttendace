@extends('format.admin_layout')

@section('title')
    Edit Student
@endsection

@section('header')
    @parent
@endsection

@section('content')
    <h1 class="h4 mb-3">Edit Student</h1>

    {{-- Error container for AJAX validation errors --}}
    <div id="editStudentAlert" class="alert alert-danger d-none"></div>

    {{-- CSRF token (used by app.js) --}}
    <input type="hidden" id="csrf_token" value="{{ csrf_token() }}">

    <div class="d-grid gap-3" style="max-width: 520px;">
        <div>
            <label for="firstName" class="form-label">First Name</label>
            <input id="firstName" type="text" name="first_name" class="form-control" 
                   value="{{ old('first_name', $student->first_name) }}" required>
        </div>

        <div>
            <label for="middleName" class="form-label">Middle Name</label>
            <input id="middleName" type="text" name="middle_name" class="form-control" 
                   value="{{ old('middle_name', $student->middle_name) }}">
        </div>

        <div>
            <label for="lastName" class="form-label">Last Name</label>
            <input id="lastName" type="text" name="last_name" class="form-control" 
                   value="{{ old('last_name', $student->last_name) }}" required>
        </div>

        <div>
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" class="form-control"
                   value="{{ old('email', optional($student->userAccount)->email) }}" required>
            @error('email')
                <p style="color: red;font-size: 0.875em">{{ $message }}</p>
            @enderror
        </div>

        

        <div>
            <label for="contactNo" class="form-label">Contact No</label>
            <input id="contactNo" type="text" name="contact_no" class="form-control" 
                   value="{{ old('contact_no', $student->contact_no) }}">
        </div>

        <div>
            <label for="degree" class="form-label">Degree</label>
            <select id="degree" name="degree_id" class="form-select">
                <option value="">— Select degree —</option>
                @foreach($degrees as $degree)
                    <option value="{{ $degree->id }}" 
                        {{ (string)old('degree_id', $student->degree_id) === (string)$degree->id ? 'selected' : '' }}>
                        {{ $degree->degree_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="profileImage" class="form-label">Profile Image</label>
            <input id="profileImage" type="file" name="profile_image" class="form-control" accept="image/*">
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('student.index') }}" class="btn-ui btn-ui--ghost">Cancel</a>
            <button type="button" class="btn-ui btn-ui--primary" id="updateStudentBtn" 
                    data-url="{{ route('student.update', $student->id) }}">Update</button>
        </div>
    </div>
@endsection

@section('footer')
    @parent
@endsection