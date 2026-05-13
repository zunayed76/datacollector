<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Submission; // Ensure this model exists after you create the table
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubmissionController extends Controller
{
    /**
     * Display a listing of submissions based on role.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // if ($user->isAdmin()) {
        //     // Admin: Fetch all submissions from all users
        //     $submissions = Submission::with('user')->latest()->get();
        //     return view('submissions.admin_index', compact('submissions'));
        // }

        // Regular User: Fetch only their own submissions
        $submissions = Submission::where('user_id', $user->id)->latest()->get();
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
        if ( !$user->is_admin && $submission->user_id !== $user->id) {
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

        $submission = Submission::with(['addresses', 'educations', 'languages'])->findOrFail($id);

        // Security Check
        if (!$user->is_admin && $submission->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $divisions = \App\Models\Division::all(); // Needed for dropdowns
        return view('submissions.edit', compact('submission', 'divisions'));
    }

    public function update(Request $request, int $id)
    {
        $submission = Submission::findOrFail($id);
        $user = Auth::user();

        // Security Check
        if (!$user->is_admin && $submission->user_id !== $user->id) {
            abort(403);
        }

        try {
            DB::transaction(function () use ($request, $submission) {
                // 1. Update Basic Info
                $submission->update($request->only([
                    'name', 'fathers_name', 'mothers_name', 'nid_number', 
                    'date_of_birth', 'religion', 'gender', 'blood_group', 
                    'marital_status', 'emergency_contact_name', 'emergency_contact_number'
                ]));

                // 2. Handle Files (Only update if new file is uploaded)
                if ($request->hasFile('picture')) {
                    $submission->picture = $request->file('picture')->store('uploads/photos', 'public');
                }
                if ($request->hasFile('nid_file')) {
                    $submission->nid_file = $request->file('nid_file')->store('uploads/nids', 'public');
                }
                $submission->save();

                // 3. Update Addresses (Present/Permanent)
                // Logic: Update the existing rows based on address_type
                foreach (['present', 'permanent'] as $type) {
                    $submission->addresses()->where('address_type', $type)->update([
                        'division_id' => $request->input($type . '_division_id'),
                        'district_id' => $request->input($type . '_district_id'),
                        'thana_id'    => $request->input($type . '_thana_id'),
                        'address_details' => $request->input($type . '_address_details'),
                    ]);
                }

                // 4. Update Education & Languages
                // Simplest approach for arrays: Delete old ones and re-insert new ones
                $submission->educations()->delete();
                foreach ($request->education as $edu) {
                    $submission->educations()->create([
                        'degree_name'      => $edu['degree'],
                        'institution_name' => $edu['institute'],
                        'passing_year'     => $edu['passing_year'],
                        'grade'            => $edu['grade'],
                        // Handle certificate logic if necessary
                    ]);
                }

                $submission->languages()->delete();
                foreach ($request->languages as $lang) {
                    $submission->languages()->create($lang);
                }
            });

            return redirect()->route('submissions.show', $id)->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Update failed: ' . $e->getMessage());
        }
    }


}