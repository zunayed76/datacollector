<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Division;
use App\Models\Submission; // Ensure this model exists after you create the table
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    public function dashboard()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Get the latest submission for the logged-in user
        $submission = Submission::with('user')->latest()->first();

        // You can also add counts for the dashboard if the user is an admin
        $totalSubmissions = $user->isAdmin() ? \App\Models\Submission::count() : null;

        return view('dashboard', compact('submission', 'totalSubmissions'));
    }
    /**
     * Display a listing of submissions based on role.  
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isAdmin()) {
            // Admin: Fetch all submissions from all users
            $submissions = Submission::with('user')->latest()->paginate(10);
            return view('submissions.index', compact('submissions'));
        }

        // Regular User: Fetch only their own submissions
        $submissions = Submission::where('user_id', $user->id)->latest()->paginate(10);
        return view('submissions.index', compact('submissions'));
    }

    /**
     * Show form to create a submission (linked to your dashboard 'Submit' button)
     */
    public function show(int $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Fetch submission with ALL nested relationships
        $submission = Submission::with([
            'user',
            'addresses.division',
            'addresses.district',
            'addresses.thana',
            'educations',
            'languages'
        ])->findOrFail($id);

        // 2. Security Check: Only the owner or an admin can view details
        if ( !$user->isAdmin() && $submission->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('submissions.show', compact('submission'));
    }
    public function create()
    {
        $divisions = Division::orderBy('name', 'asc')->get();
        return view('submissions.create', compact('divisions'));
    }

    /**
     * Store the submission
     */
    public function store(Request $request)
    {
        // dd($request->all());
        // die();
        $request->validate([
            'name'                      => 'required|string|max:255',
            'nid_number'                => 'nullable|string|max:20',
            'fathers_name'              => 'required|string|max:255',
            'mothers_name'              => 'required|string|max:255',
            'date_of_birth'             => 'nullable|date',
            'religion'                  => 'nullable|string|max:100',
            'emergency_contact_name'    => 'nullable|string|max:255',
            'emergency_contact_number'  => 'nullable|string|max:20',
            'gender'                    => 'nullable|string',
            'marital_status'            => 'nullable|string',
            'blood_group'               => 'nullable|string|max:5',
            
            // Addresses
            'present_division_id'       => 'required|exists:divisions,id',
            'present_district_id'       => 'required|exists:districts,id',
            'present_thana_id'          => 'required|exists:thanas,id',
            'present_address_details'   => 'nullable|string',
            'permanent_division_id'     => 'required|exists:divisions,id',
            'permanent_district_id'     => 'required|exists:districts,id',
            'permanent_thana_id'        => 'required|exists:thanas,id',
            'permanent_address_details' => 'nullable|string',

            // Files
            'nid_file'                  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'picture'                   => 'nullable|image|mimes:jpg,jpeg,png|max:5120',

            // Arrays (Education & Languages)
            // 'education'                 => 'required|array|min:1',
            // 'education.*.degree'        => 'required|string',
            // 'education.*.institute'     => 'required|string',
            // 'education.*.passing_year'  => 'required|numeric',
            // 'education.*.grade'         => 'required|string',
            // 'education.*.certificate'   => 'nullable|file|mimes:pdf|max:2048',
            
            // 'languages'                 => 'required|array|min:1',
            // 'languages.*.name'          => 'required|string',
            // 'languages.*.proficiency'   => 'required|string',
        ]);
        /** @var \App\Models\User $user */
        $user = Auth::user();

        DB::beginTransaction();

        try {
            $submission = new \App\Models\Submission();
            $submission->user_id = $user->id;
            $submission->name = $request->name;
            $submission->fathers_name = $request->fathers_name;
            $submission->mothers_name = $request->mothers_name;
            $submission->date_of_birth = $request->date_of_birth;
            $submission->nid_number = $request->nid_number;
            
            // Additional Fields
            $submission->emergency_contact_name = $request->emergency_contact_name;
            $submission->emergency_contact_number = $request->emergency_contact_number;
            $submission->religion = $request->religion;
            $submission->gender = $request->gender;
            $submission->blood_group = $request->blood_group;
            $submission->marital_status = $request->marital_status;

            // Handle File Upload
            if ($request->hasFile('nid_file')) {
                $nid_path = $request->file('nid_file')->store('uploads/nids', 'public');
                $submission->nid_file = $nid_path;
            }
            if ($request->hasFile('picture')) {
                $picture_path = $request->file('picture')->store('uploads/photos', 'public');
                $submission->picture = $picture_path;
            }

            $submission->save();

            // 3. Create Present Address
            $submission->addresses()->create([
                'type'             => 'present',
                'division_id'      => $request->present_division_id,
                'district_id'      => $request->present_district_id,
                'thana_id'         => $request->present_thana_id,
                'location_details' => $request->present_address_details,
            ]);

            // // 4. Create Permanent Address
            $submission->addresses()->create([
                'type'             => 'permanent',
                'division_id'      => $request->permanent_division_id,
                'district_id'      => $request->permanent_district_id,
                'thana_id'         => $request->permanent_thana_id,
                'location_details' => $request->permanent_address_details,
            ]);

            // 5. Create Education Rows
            // 5. Create Education Rows (with Certificates)
            if ($request->has('education')) {
                foreach ($request->education as $key => $edu) {
                    if (!empty($edu['degree'])) {
                        
                        $certPath = null;

                        // Files in arrays are accessed via the index key
                        if ($request->hasFile("education.$key.certificate")) {
                            $file = $request->file("education.$key.certificate");
                            $fileName = time() . '_edu_' . $key . '.' . $file->getClientOriginalExtension();
                            $certPath = $file->storeAs('uploads/certificates', $fileName, 'public');
                        }

                        $submission->educations()->create([
                            'degree'  => $edu['degree'],
                            'institute'  => $edu['institute'],
                            'passing_year' => $edu['passing_year'],
                            'grade'        => $edu['grade'],
                            'certificate' => $certPath, // Make sure this column exists in your education table
                        ]);
                    }
                }
            }

            // 6. Create Language Rows
            if ($request->has('languages')) {
                /** @var array $lang */
                foreach ($request->languages as $key => $lang) {
                    if (!empty($lang['name'])) {
                        $submission->languages()->create([
                            'language_name' => $lang['name'],
                            'proficiency_level'   => $lang['proficiency']
                        ]);
                    }
                }
            }
            
            $user->update([
                'is_profile_completed' => 1
            ]);
            DB::commit();
            return redirect()->route('submissions.index')->with('success', 'Data Uploaded Successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            // Log the error for debugging
            Log::error($e->getMessage());
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(int $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Fetch submission with relationships
        $submission = Submission::with([
            'addresses.division', 
            'addresses.district', 
            'addresses.thana', 
            'educations', 
            'languages'
        ])->findOrFail($id);

        // 2. Security Check
        if (!$user->isAdmin() && $submission->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // 3. Load Dropdown Data
        $divisions = \App\Models\Division::all();

        // 4. Pre-load Districts and Thanas for the existing addresses
        // We fetch them so the dropdowns aren't empty on page load.
        $presentAddress = $submission->addresses->where('type', 'present')->first();
        $permanentAddress = $submission->addresses->where('type', 'permanent')->first();

        // Get Districts for the selected Divisions
        $presentDistricts = $presentAddress ? \App\Models\District::where('division_id', $presentAddress->division_id)->get() : collect();
        $permanentDistricts = $permanentAddress ? \App\Models\District::where('division_id', $permanentAddress->division_id)->get() : collect();

        // Get Thanas for the selected Districts
        $presentThanas = $presentAddress ? \App\Models\Thana::where('district_id', $presentAddress->district_id)->get() : collect();
        $permanentThanas = $permanentAddress ? \App\Models\Thana::where('district_id', $permanentAddress->district_id)->get() : collect();

        return view('submissions.edit', compact(
            'submission', 
            'divisions', 
            'presentDistricts', 
            'permanentDistricts', 
            'presentThanas', 
            'permanentThanas'
        ));
    }

    public function update(Request $request, int $id)
    {
        $submission = Submission::findOrFail($id);
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Security Check
        if (!$user->isAdmin() && $submission->user_id !== $user->id) {
            abort(403);
        }
        // 1. Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'present_division_id' => 'required',
            'present_district_id' => 'required',
            'permanent_division_id' => 'required',
            'permanent_district_id' => 'required',
            'education.*.degree' => 'required|string',
            'education.*.institute' => 'required|string',
            'education.*.certificate' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ]);

        // 2. Update Main Submission Data
        $submissionData = $request->only([
            'name', 'nid_number', 'fathers_name', 'mothers_name', 
            'date_of_birth', 'religion', 'gender', 'marital_status', 
            'blood_group', 'emergency_contact_number', 'emergency_contact_name'
        ]);

        // Handle Main Profile Picture
        if ($request->hasFile('picture')) {
            if ($submission->picture) Storage::disk('public')->delete($submission->picture);
            $submissionData['picture'] = $request->file('picture')->store('profiles', 'public');
        }

        // Handle Main NID File
        if ($request->hasFile('nid_file')) {
            if ($submission->nid_file) Storage::disk('public')->delete($submission->nid_file);
            $submissionData['nid_file'] = $request->file('nid_file')->store('nids', 'public');
        }

        $submission->update($submissionData);

        // 3. Update Addresses (Mandatory)
        foreach (['present', 'permanent'] as $type) {
            $submission->addresses()->updateOrCreate(
                ['type' => $type],
                [
                    'division_id' => $request->input($type . '_division_id'),
                    'district_id' => $request->input($type . '_district_id'),
                    'thana_id' => $request->input($type . '_thana_id'),
                    'location_details' => $request->input($type . '_location_details'),
                ]
            );
        }

        // 4. Handle Education (The "Non-Buggy" Way)
        $keptEduIds = [];
        $educationInput = $request->input('education', []);

        foreach ($educationInput as $index => $item) {
            $eduData = [
                'degree'       => $item['degree'],
                'institute'    => $item['institute'],
                'passing_year' => $item['passing_year'],
                'grade'        => $item['grade'],
            ];

            // Check for new certificate upload for THIS specific row
            if ($request->hasFile("education.{$index}.certificate")) {
                // If updating an existing record, delete the old file first
                if (isset($item['id'])) {
                    $oldEdu = $submission->educations()->find($item['id']);
                    if ($oldEdu && $oldEdu->certificate) {
                        Storage::disk('public')->delete($oldEdu->certificate);
                    }
                }
                $eduData['certificate'] = $request->file("education.{$index}.certificate")->store('certificates', 'public');
            }

            // updateOrCreate uses the ID if present; if not, it creates a new record
            $eduRecord = $submission->educations()->updateOrCreate(
                ['id' => $item['id'] ?? null],
                $eduData
            );

            $keptEduIds[] = $eduRecord->id;
        }

        // Delete education records that were removed in the UI
        $submission->educations()->whereNotIn('id', $keptEduIds)->delete();

        // 5. Handle Languages (Optional, following same logic)
        $keptLangIds = [];
        $languagesInput = $request->input('languages', []);
        foreach ($languagesInput as $langItem) {
            $langRecord = $submission->languages()->updateOrCreate(
                ['id' => $langItem['id'] ?? null],
                [
                    'language_name' => $langItem['language_name'],
                    'proficiency_level' => $langItem['proficiency_level'],
                ]
            );
            $keptLangIds[] = $langRecord->id;
        }
        $submission->languages()->whereNotIn('id', $keptLangIds)->delete();

        return redirect()->route('submissions.show', $submission->id)
                        ->with('success', 'Profile updated successfully.');
    }
    public function downloadPdf(Submission $submission)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        // Check if the user is NOT an admin AND NOT the owner
        abort_if(
            !$user->isAdmin() && $submission->user_id !== $user->id, 
            403, 
            'You are not authorized to download this PDF.'
        );

        // Eager load relationships
        $submission->load(['user', 'addresses.division', 'addresses.district', 'addresses.thana', 'educations', 'languages']);

        //$pdf = Pdf::loadView('submissions.pdf', compact('submission'));
        $pdf = Pdf::loadView('submissions.pdf', compact('submission'))
              ->setPaper('a4', 'portrait')
              ->setOption([
                  'isRemoteEnabled' => true,
                  'isHtml5ParserEnabled' => true,
                  'chroot' => public_path(),
              ]);

        return $pdf->download('Submission-' . $submission->name . '.pdf');
    }
    public function destroy(Submission $submission)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        // Check if the user is NOT an admin AND NOT the owner
        // Authorization: Only admin or owner can delete
        if (!$user->isAdmin() && $submission->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // Optional: Delete physical files if they exist
        if ($submission->picture) {
            Storage::disk('public')->delete($submission->picture);
        }
        if ($submission->nid_file) {
            Storage::disk('public')->delete($submission->nid_file);
        }

        // Delete the submission (related records will delete if you set up 'onDelete cascade' in migrations)
        $submission->addresses()->delete();
        $submission->educations()->delete();
        $submission->languages()->delete();
        $submission->delete();

        return redirect()->route('submissions.index')->with('success', 'Submission deleted successfully.');
    }

}