@extends('format.layout')

@section('title', 'Student Details')

@section('header')
    @parent
@endsection

@section('content')
    <div id="studentDetailsContainer">
        <p><strong>First Name:</strong> <span id="detail_first_name">—</span></p>
        <p><strong>Middle Name:</strong> <span id="detail_middle_name">—</span></p>
        <p><strong>Last Name:</strong> <span id="detail_last_name">—</span></p>
        <p><strong>Contact No:</strong> <span id="detail_contact_no">—</span></p>
        <p><strong>Degree:</strong> <span id="detail_degree">—</span></p>
    </div>

    <div class="text-center mt-3">
        <div id="loadingSpinner" class="spinner-border text-primary" role="status" style="display: none;">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <input type="hidden" id="studentId" value="{{ $id ?? '' }}">
@endsection

@section('footer')
    @parent
@endsection