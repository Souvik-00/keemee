@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h4 mb-3">Sign In</h1>
                    <p class="text-muted mb-4">Use your email or username with password.</p>

                    <form method="POST" action="{{ route('login.attempt') }}" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label for="login" class="form-label">Email or Username</label>
                            <input type="text" class="form-control" id="login" name="login" value="{{ old('login') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
