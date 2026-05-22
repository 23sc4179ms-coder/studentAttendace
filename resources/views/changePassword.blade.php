@extends('format.student_layout')

@section('title', 'Change Password')

@section('content')
    @php
        $role = session('logged_role');
        $updateRoute = ($role === 'teacher')
            ? route('teacherDashboard.update', $user->id)
            : route('studentDashboard.update', $user->id);
    @endphp
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h1 class="mb-4">Change Password</h1>

            <form action="{{ $updateRoute }}" method="POST">
                @csrf
                @method('PUT')
                @if(isset($msg) || session('msg'))
                    <div class="alert alert-danger">{{ session('msg') ?? $msg }}</div>
                @endif
                
                <div>
            <label for="password" class="form-label">Old Password</label>
            <input id="password" type="password" name="old_password" class="form-control" required>
            @error('old_password')
                <p style="color: red;font size: 0.875em">{{ $message }}</p>
            @enderror
        </div>

                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="new_password" name="password" required>
                </div>

                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="new_password_confirmation" name="password_confirmation" required>
                    </div>

                <button type="submit" class="btn btn-primary">Change Password</button>
            </form>
        </div>
    </div>
@endsection