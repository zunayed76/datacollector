<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Submission; // Ensure this model exists after you create the table
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        // 1. Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'nid_number' => 'required|numeric|unique:submissions,nid_number',
            'nid_file' => 'nullable|mimes:pdf,jpg,jpeg,png|max:5120',
            
            // Address Validations
            'present_division' => 'required',
            'present_district' => 'required',
            'present_thana' => 'required',
            'permanent_division' => 'required',
            'permanent_district' => 'required',
            'permanent_thana' => 'required',
        ]);

        DB::beginTransaction();

        try {
            // 2. Create Submission (Personal Data Only)
            $submission = new \App\Models\Submission();
            $submission->name = $request->name;
            $submission->father_name = $request->father_name;
            $submission->mother_name = $request->mother_name;
            $submission->nid_number = $request->nid_number;

            // Handle File Upload
            if ($request->hasFile('nid_file')) {
                // Stores in storage/app/public/uploads/nids
                $path = $request->file('nid_file')->store('uploads/nids', 'public');
                $submission->nid_path = $path;
            }

            $submission->save();

            // 3. Create Present Address
            $submission->addresses()->create([
                'type'             => 'present',
                'division_id'      => $request->present_division,
                'district_id'      => $request->present_district,
                'thana_id'         => $request->present_thana,
                'union'            => $request->present_union, // Ensure this exists in your form
                'location_details' => $request->present_location_details,
            ]);

            // 4. Create Permanent Address
            $submission->addresses()->create([
                'type'             => 'permanent',
                'division_id'      => $request->permanent_division,
                'district_id'      => $request->permanent_district,
                'thana_id'         => $request->permanent_thana,
                'union'            => $request->permanent_union,
                'location_details' => $request->permanent_location_details,
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
                            'degree_name'  => $edu['degree'],
                            'institution'  => $edu['institute'],
                            'passing_year' => $edu['passing_year'],
                            'grade'        => $edu['grade'],
                            'certificate_path' => $certPath, // Make sure this column exists in your education table
                        ]);
                    }
                }
            }

            // 6. Create Language Rows
            if ($request->has('language')) {
                foreach ($request->language as $lang) {
                    if (!empty($lang['name'])) {
                        $submission->languages()->create([
                            'language_name' => $lang['name'],
                            'proficiency'   => $lang['level']
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('submissions.index')->with('success', 'Data Uploaded Successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            // Log the error for debugging
            \Log::error($e->getMessage());
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }
}