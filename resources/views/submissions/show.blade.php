@extends('layouts.layout')

@section('header')
    <h1 class="m-0">Profile Details: {{ $submission->name }}</h1>
@endsection

@section('content')
<div class="container-fluid pb-5">
    <div class="row mt-3 pb-5">
        <div class="col-12">
            <!-- Always visible: Back to List -->
            <a href="{{ route('submissions.index') }}" class="btn btn-default">
                <i class="fas fa-list"></i> Back to List
            </a>
                <a href="{{ route('submissions.edit', $submission->id) }}" class="btn btn-warning float-right ml-2">
                    <i class="fas fa-edit"></i> Edit Profile
                </a>
        </div>
    </div>
    <!-- SECTION 1: PERSONAL INFORMATION -->
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Personal Information</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3 text-center">
                            <label class="d-block">Profile Picture</label>
                            @if($submission->picture)
                                <img src="{{ asset('storage/' . $submission->picture) }}" class="img-thumbnail" style="max-width: 200px;">
                            @else
                                <p class="text-muted">No picture uploaded</p>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Full Name:</strong> {{ $submission->name }}</p>
                                    <p><strong>NID Number:</strong> {{ $submission->nid_number }}</p>
                                    <p><strong>Father's Name:</strong> {{ $submission->fathers_name }}</p>
                                    <p><strong>Mother's Name:</strong> {{ $submission->mothers_name }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Date of Birth:</strong> {{ $submission->date_of_birth ? $submission->date_of_birth->format('d M, Y') : 'N/A' }}</p>
                                    <p><strong>Religion:</strong> {{ $submission->religion }}</p>
                                    <p><strong>Gender:</strong> {{ ucfirst($submission->gender) }}</p>
                                    <p><strong>Blood Group:</strong> {{ $submission->blood_group }}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Emergency Contact Name:</strong> {{ $submission->emergency_contact_name }}</p>
                                    <p><strong>Emergency Contact Number:</strong> {{ $submission->emergency_contact_number }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Marital Status:</strong> {{ ucfirst($submission->marital_status) }}</p>
                                    <p><strong>NID File:</strong> 
                                        @if($submission->nid_file)
                                            <a href="{{ asset('storage/' . $submission->nid_file) }}" target="_blank" class="btn btn-xs btn-info">View Attachment</a>
                                        @else
                                            <span class="text-muted">No file</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION 2: ADDRESSES -->
    <div class="row">
        <!-- Present Address -->
        <div class="col-md-6">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">Present Address</h3>
                </div>
                <div class="card-body">
                    @php $present = $submission->addresses->where('address_type', 'present')->first(); @endphp
                    <p><strong>Division:</strong> {{ $present->division->name ?? 'N/A' }}</p>
                    <p><strong>District:</strong> {{ $present->district->name ?? 'N/A' }}</p>
                    <p><strong>Thana:</strong> {{ $present->thana->name ?? 'N/A' }}</p>
                    <p><strong>Details:</strong> {{ $present->address_details ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Permanent Address -->
        <div class="col-md-6">
            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Permanent Address</h3>
                </div>
                <div class="card-body">
                    @php $permanent = $submission->addresses->where('address_type', 'permanent')->first(); @endphp
                    <p><strong>Division:</strong> {{ $permanent->division->name ?? 'N/A' }}</p>
                    <p><strong>District:</strong> {{ $permanent->district->name ?? 'N/A' }}</p>
                    <p><strong>Thana:</strong> {{ $permanent->thana->name ?? 'N/A' }}</p>
                    <p><strong>Details:</strong> {{ $permanent->address_details ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION 3: EDUCATIONAL QUALIFICATIONS -->
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Educational Qualifications</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Degree</th>
                                <th>Institute</th>
                                <th>Passing Year</th>
                                <th>Grade/CGPA</th>
                                <th>Certificate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($submission->educations as $edu)
                                <tr>
                                    <td>{{ $edu->degree_name }}</td>
                                    <td>{{ $edu->institution_name }}</td>
                                    <td>{{ $edu->passing_year }}</td>
                                    <td>{{ $edu->grade }}</td>
                                    <td>
                                        @if($edu->certificate_file)
                                            <a href="{{ asset('storage/' . $edu->certificate_file) }}" target="_blank">View File</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center">No education records found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION 4: LANGUAGE PROFICIENCY -->
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">Language Proficiency</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Language Name</th>
                                <th>Proficiency Level</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($submission->languages as $lang)
                                <tr>
                                    <td>{{ $lang->name }}</td>
                                    <td><span class="badge badge-info">{{ $lang->proficiency }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center">No languages added.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <a href="{{ route('submissions.index') }}" class="btn btn-default">Back to List</a>
            @if(Auth::user()->is_admin)
                <button class="btn btn-success float-right">Approve Submission</button>
            @endif
        </div>
    </div>
</div>
@endsection