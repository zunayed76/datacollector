@extends('layouts.layout')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h4 mb-0">
                    {{ Auth::user()->is_admin ? 'All Submissions' : 'My Submissions' }}
                </h2>
                @if(!Auth::user()->is_admin && !Auth::user()->is_profile_completed)
                    <a href="{{ route('submissions.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Complete Profile
                    </a>
                @endif
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4">#ID</th>
                                    @if(Auth::user()->is_admin)
                                        <th>User</th>
                                    @endif
                                    <th>Full Name</th>
                                    <th>NID Number</th>
                                    <th>Date Submitted</th>
                                    <th>Status</th>
                                    <th class="text-end px-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($submissions as $submission)
                                    <tr class="align-middle">
                                        <td class="px-4 text-muted">#{{ $submission->id }}</td>
                                        @if(Auth::user()->is_admin)
                                            <td>
                                                <div class="fw-bold">{{ $submission->user->name }}</div>
                                                <small class="text-muted">{{ $submission->user->email }}</small>
                                            </td>
                                        @endif
                                        <td>{{ $submission->name }}</td>
                                        <td><code>{{ $submission->nid_number }}</code></td>
                                        <td>{{ $submission->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge bg-success">Completed</span>
                                        </td>
                                        <td class="text-end px-4">
                                            <a href="{{ route('submissions.show', $submission->id) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> View Details
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ Auth::user()->is_admin ? '7' : '6' }}" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="fas fa-folder-open fa-3x mb-3"></i>
                                                <p>No submissions found.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Pagination Links (if using pagination) --}}
            @if(method_exists($submissions, 'links'))
                <div class="mt-4">
                    {{ $submissions->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection