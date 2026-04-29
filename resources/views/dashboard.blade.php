@extends('layouts.layout')

@section('header')
    <h1 class="m-0">User Dashboard</h1>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-6 col-6">
        @if(!Auth::user()->is_profile_completed)
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>Create</h3>
                    <p>Setup Your Profile</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <a href="{{ route('profile.create') }}" class="small-box-footer">
                    Start Now <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        @else
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>Update</h3>
                    <p>Modify Profile Details</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-edit"></i>
                </div>
                <a href="{{ route('profile.edit') }}" class="small-box-footer">
                    Edit Profile <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        @endif
    </div>

    <div class="col-lg-6 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>View</h3>
                <p>Check Submission History</p>
            </div>
            <div class="icon">
                <i class="fas fa-list"></i>
            </div>
            <a href="{{ route('submissions.index') }}" class="small-box-footer">
                See History <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <p>Status: 
            @if(Auth::user()->is_profile_completed)
                <span class="badge badge-success">Profile Data Submission Completed</span>
            @else
                <span class="badge badge-danger">Profile Data Submission Pending</span>
            @endif
        </p>
    </div>
</div>
@endsection