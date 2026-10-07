@extends('layouts.app')

@section('content')
    <div class="container-flux m-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Dashboard') }}</div>

                    <div class="card-body">
                            @php
                                header('Location: ' . URL::to('/dashboard'), true, 302);
                                exit();
                            @endphp

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
