@extends('layouts.app')

@section('content')
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 50vh;">
        <form action="{{ route('password.change') }}" method="POST" class="w-50">
            @csrf
            <div class="mb-3">
                <input type="password" name="current_password" class="form-control form-control-sm rounded" placeholder="Current Password" required>
            </div>
            <div class="mb-3">
                <input type="password" name="new_password" class="form-control form-control-sm rounded" placeholder="New Password" required>
            </div>
            <div class="mb-3">
                <input type="password" name="new_password_confirmation" class="form-control form-control-sm rounded" placeholder="Confirm New Password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-sm btn-block rounded-pill">Change Password</button>
        </form>
    </div>
@endsection

