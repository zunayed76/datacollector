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
        // $request->validate([
        //     'name' => 'required|string|max:255',
        //     'fathers_name' => 'required|string|max:255',
        //     'mothers_name' => 'required|string|max:255',
        //     'nid_number' => 'required|numeric|unique:submissions,nid_number',
        //     'nid_file' => 'nullable|mimes:pdf,jpg,jpeg,png|max:5120',
            
        //     // Address Validations
        //     'present_division_id' => 'required',
        //     'present_district_id' => 'required',
        //     'present_thana_id' => 'required',
        //     'permanent_division_id' => 'required',
        //     'permanent_district_id' => 'required',
        //     'permanent_thana_id' => 'required',
        // ]);
        /** @var \App\Models\User $user */
        $user = Auth::user();

        DB::beginTransaction();

        try {
            // 2. Create Submission (Personal Data Only)
            $submission = new \App\Models\Submission();
            $submission->name = $request->name;
            $submission->user_id = $user->id;
            $submission->fathers_name = $request->fathers_name;
            $submission->mothers_name = $request->mothers_name;
            $submission->nid_number = $request->nid_number;

            // Handle File Upload
            if ($request->hasFile('nid_file')) {
                $path = $request->file('nid_file')->store('uploads/nids', 'public');
                $submission->nid_file = $path;
            }

            $submission->save();

            // 3. Create Present Address
            // $submission->addresses()->create([
            //     'type'             => 'present',
            //     'division_id'      => $request->present_division_id,
            //     'district_id'      => $request->present_district_id,
            //     'thana_id'         => $request->present_thana_id,
            //     'location_details' => $request->present_address_details,
            // ]);

            // // 4. Create Permanent Address
            // $submission->addresses()->create([
            //     'type'             => 'permanent',
            //     'division_id'      => $request->permanent_division_id,
            //     'district_id'      => $request->permanent_district_id,
            //     'thana_id'         => $request->permanent_thana_id,
            //     'location_details' => $request->permanent_address_details,
            // ]);

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
            
            // $user->update([
            //     'is_profile_completed' => 1
            // ]);
            DB::commit();
            return redirect()->route('submissions.index')->with('success', 'Data Uploaded Successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            // Log the error for debugging
            Log::error($e->getMessage());
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }
}