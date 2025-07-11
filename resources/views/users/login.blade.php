@extends('layouts.master')
@section('title',"login")
@section('content')
<div class="container mt-5">
    <h2>Login</h2>
    <form action="{{ route('login') }}" method="POST">
        {{ csrf_field() }}
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
    {{-- <a href="{{ route('password.request') }}" class="text-decoration-none text-primary">
        <i class="bi bi-unlock"></i> Forgot Password?
    </a> --}}

    </div>
@endsection
