@extends('layouts.app')

@section('content')

    <div class="container d-flex justify-content-center align-items-center flex-column" style="min-height: 50vh;">
        @if (session('success'))
            <div class="alert alert-success w-50">
                {{ session('success') }}
            </div>
        @endif
            <h1>Log In</h1>
        <form action="{{ route('login.submit') }}" method="POST" class="w-50">
            @csrf
            <div class="mb-3">
                <input type="email" name="email" class="form-control form-control-sm rounded" placeholder="Email" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control form-control-sm rounded" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-sm btn-long rounded-pill">Login</button>
        </form>
    </div>
@endsection

