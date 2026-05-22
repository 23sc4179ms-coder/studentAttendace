@extends('format.layout')

@section('title')
    Degree Details
@endsection

@section('header')
    @parent
@endsection

@section('content')
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
        <h1 class="h4 m-0">Degree Details</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('degree.edit', $degree->id) }}" class="btn-ui btn-ui--ghost">Edit</a>
            <a href="{{ route('degree.index') }}" class="btn-ui btn-ui--ghost">Back</a>
        </div>
    </div>

    <div class="card p-3" style="max-width: 640px;">
        <div class="mb-2"><strong>ID:</strong> {{ $degree->id }}</div>
        <div><strong>Degree Name:</strong> {{ $degree->degree_name }}</div>
    </div>
@endsection

@section('footer')
    @parent
@endsection
