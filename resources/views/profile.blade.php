@extends('layouts.app')
    
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Profile') }}</div>
    
                <div class="card-body">
                    <form method="POST" action="{{ route('user.profile.store') }}" enctype="multipart/form-data">
                        @csrf
    
                        @if (session('success'))
                            <div class="alert alert-success" role="alert" class="text-danger">
                                {{ session('success') }}
                            </div>
                        @endif
  
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Avatar: </label>
                                <input id="avatar" type="file" class="form-control @error('avatar') is-invalid @enderror" name="avatar" value="{{ old('avatar') }}"  autocomplete="avatar">
  
                                @error('avatar')
                                    <span role="alert" class="text-danger">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
  
                            <div class="mb-3 col-md-6">
                                <img src="/avatars/{{ (Auth::user()->avatar ?? null) ?: 'default.png' }}" style="width:80px;margin-top: 10px;">
                            </div>
  
                        </div>
  
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Name: </label>
                                <input class="form-control" type="text" id="name" name="name" value="{{ auth()->user()->name }}" autofocus="" >
                                @error('name')
                                    <span role="alert" class="text-danger">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
   
                            <div class="mb-3 col-md-6">
                                <label for="email" class="form-label">Email: </label>
                                <input class="form-control" type="text" id="email" name="email" value="{{ auth()->user()->email }}" autofocus="" readonly>
                                @error('email')
                                    <span role="alert" class="text-danger">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
   
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="password" class="form-label">Password: </label>
                                <input class="form-control" type="password" id="password" name="password" autofocus=""  autocomplete="new-password">
                                @error('password')
                                    <span role="alert" class="text-danger">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
   
                            <div class="mb-3 col-md-6">
                                <label for="confirm_password" class="form-label">Confirm Password: </label>
                                <input class="form-control" type="password" id="confirm_password" name="confirm_password" autofocus="" >
                                @error('confirm_password')
                                    <span role="alert" class="text-danger">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
   
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="mobile_no" class="form-label">Phone: </label>
                                <input class="form-control" type="text" id="mobile_no" name="mobile_no" value="{{ auth()->user()->mobile_no }}" autofocus="" >
                                @error('mobile_no')
                                    <span role="alert" class="text-danger">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
   
                            <div class="mb-3 col-md-6">
                                <label for="department" class="form-label">Department: </label>
                                <input class="form-control" type="text" id="department" name="department" value="{{ auth()->user()->department }}" autofocus="" readonly>
                                @error('department')
                                    <span role="alert" class="text-danger">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
    
                        <div class="row mb-0">
                            <div class="col-md-12 offset-md-5">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Upload Profile') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection