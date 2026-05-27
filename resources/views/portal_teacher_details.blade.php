@extends('format.portal_admin_layout')

@section('title')
    Teacher Details
@endsection

@section('header')
    @parent
@endsection

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Teacher Details</h5>
                <a href="{{ route('manageStudents') }}" class="btn-ui btn-ui--ghost">Back</a>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> {{ $teacher->last_name }}, {{ $teacher->first_name }} @if($teacher->middle_name) {{ $teacher->middle_name }} @endif</p>
                <p><strong>Email:</strong> {{ $teacher->email }}</p>
                <p><strong>Contact No:</strong> {{ $teacher->contact_no ?? '—' }}</p>
                <div class="mt-3">
                    <a href="{{ route('teacher.edit', $teacher->id) }}" class="btn btn-primary">Edit</a>
                    <form action="{{ route('teacher.destroy', $teacher->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this teacher?')">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    @parent
@endsection
