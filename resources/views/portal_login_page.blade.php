@extends('format.portal_login_layout')

@section('title', 'Login')

@section('content')
    <div class="auth-split">
        <section class="auth-panel auth-panel--brand">
            <a href="/home" class="site-brand" aria-label="EGG ALL Home">
                <span class="site-brand__mark" aria-hidden="true"></span>
                <span class="site-brand__text">EGG ALL</span>
            </a>

            <h1 class="mt-4">Sign in</h1>
            <p class="mb-0">Access the dashboard to manage students, courses, and attendance.</p>
        </section>

        <section class="auth-panel">
            <div id="loginAlert" class="alert alert-danger d-none"></div>

            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" autocomplete="username" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" autocomplete="current-password" required>
            </div>

            <button type="button" class="btn-ui btn-ui--primary w-100" id="loginBtn">Login</button>
        </section>
    </div>
@endsection