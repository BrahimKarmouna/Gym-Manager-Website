@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Assistant Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <h3>Welcome, {{ Auth::guard('assistant')->user()->name }}!</h3>
                    <p>Role: {{ Auth::guard('assistant')->user()->role }}</p>
                    <p>Gym: {{ $gym->name ?? 'Not assigned' }}</p>

                    <div class="mt-4">
                        <h4>Quick Links</h4>
                        <div class="list-group">
                            <a href="#" class="list-group-item list-group-item-action">View Schedule</a>
                            <a href="#" class="list-group-item list-group-item-action">Manage Clients</a>
                            <a href="#" class="list-group-item list-group-item-action">Training Sessions</a>
                            <a href="#" class="list-group-item list-group-item-action">My Profile</a>
                        </div>
                    </div>

                    <div class="mt-4">
                        <form action="{{ route('assistant.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
