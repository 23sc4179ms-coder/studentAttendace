@extends('layouts.admin')

@section('title')
    Edit Degree
@endsection

@section('header')
    @parent
@endsection

@section('content')
    <h1 class="h4 mb-3">Edit Degree</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                {{ $error }}<br>
            @endforeach
        </div>
    @endif

    <form action="{{ route('degree.update', $degree->id) }}" method="POST" class="d-grid gap-3" style="max-width: 520px;">
        @csrf
        @method('PUT')

        <div>
            <label for="degreeName" class="form-label">Degree Name</label>
            <input id="degreeName" type="text" name="degree_name" class="form-control" value="{{ old('degree_name', $degree->degree_name) }}" required>
            @error('degree_name')
                <p style="color: red;font size: 0.875em">{{ $message }}</p>
            @enderror
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('degree.index') }}" class="btn-ui btn-ui--ghost">Cancel</a>
            <button type="submit" class="btn-ui btn-ui--primary">Update</button>
        </div>
    </form>
@endsection

@section('footer')
    @parent
@endsection


