@extends('layouts.app')

@section('content')
    <div class="container d-flex justify-content-center align-items-center  flex-column" style="min-height: 50vh;">
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <h1>Sign Up</h1>
        <form action="{{ route('signup.submit') }}" method="POST" class="w-50">
            @csrf
            <div class="mb-3">
                <input type="text" name="name" class="form-control form-control-sm rounded" value="{{ old('name') }}" placeholder="Name" required>
                @error('name')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-3">
                <input type="email" name="email" class="form-control form-control-sm rounded" value="{{ old('email') }}" placeholder="Email" required>
                @error('email')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control form-control-sm rounded" placeholder="Password" required>
                @error('password')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-3">
                <input type="password" name="password_confirmation" class="form-control form-control-sm rounded" placeholder="Confirm Password" required>
                @error('password_confirmation')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary btn-sm btn-block rounded-pill">Sign Up</button>
        </form>
    </div>
@endsection
