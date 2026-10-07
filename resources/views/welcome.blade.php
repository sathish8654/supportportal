@extends('layouts.app')

@section('content')
<div class="container py-4 py-md-5">
    <div class="row align-items-center justify-content-center text-center mb-5">
        <div class="col-lg-8">
            <h1 class="display-4 fw-bold text-dark mb-3">Welcome to Support Portal</h1>
            <p class="lead text-muted mb-4">Manage and track your support tickets efficiently. High-performance portal for prompt solutions.</p>
            @if (Auth::user())
            <div class="d-flex justify-content-center gap-3">
                <a href="{{ url('/dashboard') }}" class="btn btn-premium px-4 py-2">Go to Dashboard</a>
            </div>
            @endif
        </div>
    </div>
    
    <div class="row justify-content-center">
        <div class="col-md-10 text-center">
            <a href="{{ url('/dashboard') }}" class="d-inline-block card-premium p-2 overflow-hidden shadow-lg border-0 transition-hover">
                <img class="img-fluid rounded" src="img/wallpaper.jpg" style="max-height: 480px; width: 100%; object-fit: cover; border-radius: 8px;">
            </a>
        </div>
    </div>
</div>

@if (Auth::user())
<script>
window.location = "/dashboard";
</script>
@endif
@endsection