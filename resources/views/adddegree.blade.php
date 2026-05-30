@extends('layouts.admin')

@section('title')
    Add Degree
@endsection

@section('header')
    @parent
@endsection

@section('content')
    <h1 class="h4 mb-3">Add Degree</h1>

    {{-- Error container for AJAX validation messages --}}
    <div id="addDegreeAlert" class="alert alert-danger d-none"></div>

    {{-- CSRF token (used by AJAX) – you can also rely on the meta tag if your layout includes it --}}
    <input type="hidden" id="csrf_token" value="{{ csrf_token() }}">

    <div class="d-grid gap-3" style="max-width: 520px;">
        <div>
            <label for="degreeName" class="form-label">Degree Name</label>
            <input id="degreeName" type="text" name="degree_name" class="form-control" value="{{ old('degree_name') }}" required>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('degree.index') }}" class="btn-ui btn-ui--ghost">Cancel</a>
            <button type="button" class="btn-ui btn-ui--primary" id="saveDegreeBtn">Save</button>
        </div>
    </div>
@endsection

@section('footer')
    @parent
@endsection

