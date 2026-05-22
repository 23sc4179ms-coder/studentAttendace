@extends('format.login_layout')

@section('title', 'Login')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h1 class="mb-4">Login</h1>

            {{-- No <form> – AJAX handled by app.js --}}
            <div id="loginAlert" class="alert alert-danger d-none"></div>

            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <button type="button" class="btn btn-primary" id="loginBtn">Login</button>
        </div>
    </div>
@endsection