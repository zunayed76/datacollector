@extends('layouts.layout')

@section('header')
    <h1 class="m-0">Edit Submission: {{ $submission->name }}</h1>
@endsection

@section('content')
<div class="container-fluid">
    <form action="{{ route('submissions.update', $submission->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible m-2">
                            <button type="button" class="close" data-dismiss="dismiss" aria-hidden="true">×</button>
                            <h5><i class="icon fas fa-ban"></i> Error!</h5>
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="card-header">
                        <h3 class="card-title">Personal Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Full Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $submission->name) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>NID Number</label>
                                    <input type="text" name="nid_number" class="form-control" value="{{ old('nid_number', $submission->nid_number) }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Father's Name</label>
                                    <input type="text" name="fathers_name" class="form-control" value="{{ old('fathers_name', $submission->fathers_name) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mother's Name</label>
                                    <input type="text" name="mothers_name" class="form-control" value="{{ old('mothers_name', $submission->mothers_name) }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date of Birth</label>
                                    <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $submission->date_of_birth ? $submission->date_of_birth->format('Y-m-d') : '') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Religion</label>
                                    <input type="text" name="religion" class="form-control" value="{{ old('religion', $submission->religion) }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Emergency Contact Number</label>
                                    <input type="text" name="emergency_contact_number" class="form-control" value="{{ old('emergency_contact_number', $submission->emergency_contact_number) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Emergency Contact Name</label>
                                    <input type="text" name="emergency_contact_name" class="form-control" value="{{ old('emergency_contact_name', $submission->emergency_contact_name) }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Gender</label>
                                    <select name="gender" class="form-control">
                                        <option value="male" {{ old('gender', $submission->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', $submission->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Marital Status</label>
                                    <select name="marital_status" class="form-control">
                                        @foreach(['single', 'married', 'divorced'] as $status)
                                            <option value="{{ $status }}" {{ old('marital_status', $submission->marital_status) == $status ? 'selected' : '' }}>
                                                {{ ucfirst($status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Blood Group</label>
                                    <select name="blood_group" class="form-control">
                                        @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $group)
                                            <option value="{{ $group }}" {{ old('blood_group', $submission->blood_group) == $group ? 'selected' : '' }}>{{ $group }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Update NID Copy (Leave blank to keep current)</label>
                                    <div class="custom-file">
                                        <input type="file" name="nid_file" class="custom-file-input">
                                        <label class="custom-file-label">Choose new file...</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Update Profile Picture (Leave blank to keep current)</label>
                                    <div class="custom-file">
                                        <input type="file" name="picture" class="custom-file-input">
                                        <label class="custom-file-label">Choose new photo...</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ADDRESSES SECTION -->
        <div class="row">
            @foreach(['present', 'permanent'] as $type)
            @php 
                $addr = $submission->addresses->where('address_type', $type)->first(); 
                $cardClass = $type == 'present' ? 'card-info' : 'card-secondary';
            @endphp
            <div class="col-md-6">
                <div class="card card-outline {{ $cardClass }}">
                    <div class="card-header">
                        <h3 class="card-title">{{ ucfirst($type) }} Address</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Division</label>
                            <select name="{{ $type }}_division_id" id="{{ $type }}_division" class="form-control">
                                @foreach($divisions as $division)
                                    <option value="{{ $division->id }}" {{ $addr->division_id == $division->id ? 'selected' : '' }}>{{ $division->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>District</label>
                            <select name="{{ $type }}_district_id" id="{{ $type }}_district" class="form-control">
                                <option value="{{ $addr->district_id }}">{{ $addr->district->name ?? 'Select District' }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Thana</label>
                            <select name="{{ $type }}_thana_id" id="{{ $type }}_thana" class="form-control">
                                <option value="{{ $addr->thana_id }}">{{ $addr->thana->name ?? 'Select Thana' }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Address Details</label>
                            <textarea name="{{ $type }}_address_details" class="form-control" rows="2">{{ old($type.'_address_details', $addr->address_details) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- EDUCATION SECTION -->
        <div class="row">
            <div class="col-md-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Educational Qualifications</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" id="add-education">
                                <i class="fas fa-plus"></i> Add Degree
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Degree</th>
                                    <th>Institute</th>
                                    <th>Year</th>
                                    <th>Grade</th>
                                    <th style="width: 40px"></th>
                                </tr>
                            </thead>
                            <tbody id="education-body">
                                @foreach($submission->educations as $index => $edu)
                                <tr class="edu-row">
                                    <td><input type="text" name="education[{{ $index }}][degree]" value="{{ $edu->degree_name }}" class="form-control" required></td>
                                    <td><input type="text" name="education[{{ $index }}][institute]" value="{{ $edu->institution_name }}" class="form-control" required></td>
                                    <td><input type="number" name="education[{{ $index }}][passing_year]" value="{{ $edu->passing_year }}" class="form-control" required></td>
                                    <td><input type="text" name="education[{{ $index }}][grade]" value="{{ $edu->grade }}" class="form-control" required></td>
                                    <td><button type="button" class="btn btn-danger btn-sm remove-edu"><i class="fas fa-times"></i></button></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- LANGUAGE SECTION -->
        <div class="row">
            <div class="col-md-12">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title">Language Proficiency</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" id="add-language">
                                <i class="fas fa-plus"></i> Add Language
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Language Name</th>
                                    <th>Proficiency Level</th>
                                    <th style="width: 40px"></th>
                                </tr>
                            </thead>
                            <tbody id="language-body">
                                @foreach($submission->languages as $index => $lang)
                                <tr class="lang-row">
                                    <td><input type="text" name="languages[{{ $index }}][name]" value="{{ $lang->name }}" class="form-control" required></td>
                                    <td>
                                        <select name="languages[{{ $index }}][proficiency]" class="form-control">
                                            @foreach(['Beginner', 'Intermediate', 'Fluent', 'Native'] as $level)
                                                <option value="{{ $level }}" {{ $lang->proficiency == $level ? 'selected' : '' }}>{{ $level }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-times"></i></button></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row pb-5 mt-3">
            <div class="col-12">
                <a href="{{ route('submissions.show', $submission->id) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-success float-right">Save Changes</button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // 1. Initialize counters based on existing data
        let eduCount = {{ $submission->educations->count() }};
        let langCount = {{ $submission->languages->count() }};
        const MAX_EDU = 5;

        // Helper for AJAX Dropdowns
        function fetchDropdownData(url, targetDropdown, placeholder) {
            targetDropdown.empty().append('<option value="">Loading...</option>');
            $.ajax({
                url: url, type: 'GET', dataType: 'json',
                success: function(data) {
                    targetDropdown.empty().append('<option value="" disabled selected>' + placeholder + '</option>');
                    $.each(data, function(key, value) {
                        targetDropdown.append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                }
            });
        }

        // Address Change Listeners (Unified for both types)
        ['present', 'permanent'].forEach(type => {
            $(`#${type}_division`).on('change', function() {
                let url = "{{ route('get.districts', ':id') }}".replace(':id', $(this).val());
                fetchDropdownData(url, $(`#${type}_district`), 'Select District');
                $(`#${type}_thana`).empty().append('<option value="">Select District First</option>');
            });

            $(`#${type}_district`).on('change', function() {
                let url = "{{ route('get.thanas', ':id') }}".replace(':id', $(this).val());
                fetchDropdownData(url, $(`#${type}_thana`), 'Select Thana');
            });
        });

        // Add Education
        $('#add-education').on('click', function() {
            if ($('.edu-row').length < MAX_EDU) {
                let html = `
                <tr class="edu-row">
                    <td><input type="text" name="education[${eduCount}][degree]" class="form-control" required></td>
                    <td><input type="text" name="education[${eduCount}][institute]" class="form-control" required></td>
                    <td><input type="number" name="education[${eduCount}][passing_year]" class="form-control" required></td>
                    <td><input type="text" name="education[${eduCount}][grade]" class="form-control" required></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-edu"><i class="fas fa-times"></i></button></td>
                </tr>`;
                $('#education-body').append(html);
                eduCount++;
            }
        });

        // Add Language
        $('#add-language').on('click', function() {
            let html = `
            <tr class="lang-row">
                <td><input type="text" name="languages[${langCount}][name]" class="form-control" required></td>
                <td>
                    <select name="languages[${langCount}][proficiency]" class="form-control">
                        <option value="Beginner">Beginner</option>
                        <option value="Intermediate">Intermediate</option>
                        <option value="Fluent">Fluent</option>
                        <option value="Native">Native</option>
                    </select>
                </td>
                <td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-times"></i></button></td>
            </tr>`;
            $('#language-body').append(html);
            langCount++;
        });

        // Removal Logic
        $(document).on('click', '.remove-edu, .remove-row', function() {
            $(this).closest('tr').remove();
        });

        // Custom File Label
        $(document).on('change', '.custom-file-input', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        });
    });
</script>
@endsection