@extends('layouts.layout')

@section('header')
    <h1 class="m-0">Submit Profile Data</h1>
@endsection

@section('content')
<div class="container-fluid">
    <form action="{{ route('submissions.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="dismiss" aria-hidden="true">×</button>
                            <h5><i class="icon fas fa-ban"></i> Error!</h5>
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-warning">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
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
                                    <input type="text" name="name" class="form-control" value="{{ Auth::user()->name }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>NID Number</label>
                                    <input type="text" name="nid_number" class="form-control" placeholder="10/ 17 Digits" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Father's Name</label>
                                    <input type="text" name="fathers_name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mother's Name</label>
                                    <input type="text" name="mothers_name" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>NID Copy (PDF Only)</label>
                            <div class="custom-file">
                                <input type="file" name="nid_file" class="custom-file-input" accept="application/pdf, image/jpeg, image/jpg" required>
                                <label class="custom-file-label">Choose NID File</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-6">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title">Present Address</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Division</label>
                            <select name="present_division_id" id="present_division" class="form-control select2-enable" style="width: 100%;">
                                <option value="" disabled selected>Select Division</option>
                                @foreach($divisions as $division)
                                    <option value="{{ $division->id }}">{{ $division->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>District</label>
                            <select name="present_district_id" id="present_district" class="form-control select2-enable" style="width: 100%;">
                                <option value="">Select Division First</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Thana</label>
                            <select name="present_thana_id" id="present_thana" class="form-control select2-enable" style="width: 100%;">
                                <option value="">Select District First</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Address Details</label>
                            <textarea name="present_address_details" id="present_details" class="form-control" rows="2" placeholder="House/Road/Village"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-outline card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Permanent Address</h3>
                        <div class="card-tools">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="same_as_present">
                                <label for="same_as_present" class="custom-control-label">Same as Present</label>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Division</label>
                            <select name="permanent_division_id" id="permanent_division" class="form-control select2-enable" style="width: 100%;">
                                <option value="" disabled selected>Select Division</option>
                                @foreach($divisions as $division)
                                    <option value="{{ $division->id }}">{{ $division->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>District</label>
                            <select name="permanent_district_id" id="permanent_district" class="form-control select2-enable" style="width: 100%;">
                                <option value="">Select Division First</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Thana</label>
                            <select name="permanent_thana_id" id="permanent_thana" class="form-control select2-enable" style="width: 100%;">
                                <option value="">Select District First</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Address Details</label>
                            <textarea name="permanent_address_details" id="permanent_details" class="form-control" rows="2" placeholder="House/Road/Village"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Educational Qualifications</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary" id="add-education">
                                <i class="fas fa-plus-circle text-primary"></i> Add Degree
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered" id="education-table">
                            <thead>
                                <tr>
                                    <th>Degree</th>
                                    <th>Institute</th>
                                    <th>Passing Year</th>
                                    <th>Grade/CGPA</th>
                                    <th>Certificate (PDF)</th>
                                    <th style="width: 40px"></th>
                                </tr>
                            </thead>
                            <tbody id="education-body">
                                <tr class="edu-row">
                                    <td><input type="text" name="education[0][degree]" class="form-control" required></td>
                                    <td><input type="text" name="education[0][institute]" class="form-control" required></td>
                                    <td><input type="number" name="education[0][passing_year]" class="form-control" placeholder="YYYY" required></td>
                                    <td><input type="text" name="education[0][grade]" class="form-control" required></td>
                                    <td>
                                        <div class="custom-file">
                                            <input type="file" name="education[0][certificate]" class="custom-file-input">
                                            <label class="custom-file-label">Choose</label>
                                        </div>
                                    </td>
                                    <td><button type="button" class="btn btn-danger btn-sm remove-edu"><i class="fas fa-times"></i></button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title">Language Proficiency</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary" id="add-language">
                                <i class="fas fa-plus-circle text-info"></i> Add Language
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered" id="language-table">
                            <thead>
                                <tr>
                                    <th>Language Name</th>
                                    <th>Proficiency Level</th>
                                    <th style="width: 40px"></th>
                                </tr>
                            </thead>
                            <tbody id="language-body">
                                <tr class="lang-row">
                                    <td><input type="text" name="languages[0][name]" class="form-control" placeholder="e.g. Bengali" required></td>
                                    <td>
                                        <select name="languages[0][proficiency]" class="form-control">
                                            <option value="Beginner">Beginner</option>
                                            <option value="Intermediate">Intermediate</option>
                                            <option value="Fluent">Fluent</option>
                                            <option value="Native">Native</option>
                                        </select>
                                    </td>
                                    <td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-times"></i></button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row pb-5 mt-3">
            <div class="col-12">
                <button type="submit" class="btn btn-success btn-lg float-right">Submit</button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // 1. INITIALIZE ALL COUNTERS
    let eduCount = 1; 
    let eduRows = 1;
    let langCount = 1; // THIS WAS MISSING
    const MAX_EDU = 5;

    console.log("Script loaded and ready");
    function fetchDropdownData(url, targetDropdown, placeholder) {
        targetDropdown.empty().append('<option value="">Loading...</option>');
        
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                targetDropdown.empty().append('<option value="" disabled selected>' + placeholder + '</option>');
                $.each(data, function(key, value) {
                    targetDropdown.append('<option value="' + value.id + '">' + value.name + '</option>');
                });
            },
            error: function() {
                targetDropdown.empty().append('<option value="">Error loading data</option>');
            }
        });
    }

    // PRESENT ADDRESS CHANGE LISTENERS
    // Fetch Districts using named route
    // --- PRESENT ADDRESS ---
    $('#present_division').on('change', function() {
        let divId = $(this).val();
        if (divId) {
            // Generate the URL with a placeholder, then swap it in JS
            let url = "{{ route('get.districts', ':id') }}".replace(':id', divId);
            fetchDropdownData(url, $('#present_district'), 'Select District');
            $('#present_thana').empty().append('<option value="">Select District First</option>');
        }
    });

    $('#present_district').on('change', function() {
        let distId = $(this).val();
        if (distId) {
            let url = "{{ route('get.thanas', ':id') }}".replace(':id', distId);
            fetchDropdownData(url, $('#present_thana'), 'Select Thana');
        }
    });

    // --- PERMANENT ADDRESS ---
    $('#permanent_division').on('change', function() {
        let divId = $(this).val();
        if (divId) {
            let url = "{{ route('get.districts', ':id') }}".replace(':id', divId);
            fetchDropdownData(url, $('#permanent_district'), 'Select District');
            $('#permanent_thana').empty().append('<option value="">Select District First</option>');
        }
    });

    $('#permanent_district').on('change', function() {
        let distId = $(this).val();
        if (distId) {
            let url = "{{ route('get.thanas', ':id') }}".replace(':id', distId);
            fetchDropdownData(url, $('#permanent_thana'), 'Select Thana');
        }
    });
    // "SAME AS PRESENT" CHECKBOX LOGIC
    // --- SAME AS PRESENT CHECKBOX ---
    $('#same_as_present').on('change', function() {
        if ($(this).is(':checked')) {
            const divId = $('#present_division').val();
            const distId = $('#present_district').val();
            const thanaId = $('#present_thana').val();

            // 1. Sync Division - This triggers the listener using the correct Named Route
            $('#permanent_division').val(divId).trigger('change');

            // 2. Wait for Districts to load, then sync District
            setTimeout(function() {
                $('#permanent_district').val(distId).trigger('change');
                
                // 3. Wait for Thanas to load, then sync Thana
                setTimeout(function() {
                    $('#permanent_thana').val(thanaId).trigger('change');
                }, 700); // Increased delay for local network lag
            }, 700);

            $('#permanent_details').val($('#present_details').val());
        }
    });
    // 2. EDUCATION ADD LOGIC
    $(document).on('click', '#add-education', function(e) {
        e.preventDefault();
        console.log("Education button clicked. Current rows:", eduRows);
        
        if (eduRows < MAX_EDU) {
            let html = `
            <tr class="edu-row">
                <td><input type="text" name="education[${eduCount}][degree]" class="form-control" required></td>
                <td><input type="text" name="education[${eduCount}][institute]" class="form-control" required></td>
                <td><input type="number" name="education[${eduCount}][passing_year]" class="form-control" required></td>
                <td><input type="text" name="education[${eduCount}][grade]" class="form-control" required></td>
                <td>
                    <div class="custom-file">
                        <input type="file" name="education[${eduCount}][certificate]" class="custom-file-input">
                        <label class="custom-file-label">Choose</label>
                    </div>
                </td>
                <td><button type="button" class="btn btn-danger btn-sm remove-edu"><i class="fas fa-times"></i></button></td>
            </tr>`;
            
            $('#education-body').append(html);
            eduCount++;
            eduRows++;

            if (eduRows === MAX_EDU) {
                $(this).prop('disabled', true).addClass('disabled').html('<i class="fas fa-ban"></i> Limit Reached');
            }
        }
    });

    // 3. LANGUAGE ADD LOGIC
    $(document).on('click', '#add-language', function(e) {
        e.preventDefault();
        console.log("Language button clicked");
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

    // 4. REMOVAL LOGIC
    $(document).on('click', '.remove-edu', function() {
        $(this).closest('tr').remove();
        eduRows--;
        $('#add-education').prop('disabled', false).removeClass('disabled').html('<i class="fas fa-plus-circle"></i> Add Degree');
    });

    $(document).on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
    });

    // 5. FILE INPUT LABEL FIX
    $(document).on('change', '.custom-file-input', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName);
    });
});
</script>
@endsection