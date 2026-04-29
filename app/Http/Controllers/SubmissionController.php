<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Submission; // Ensure this model exists after you create the table

class SubmissionController extends Controller
{
    /**
     * Display a listing of submissions based on role.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isAdmin()) {
            // Admin: Fetch all submissions from all users
            $submissions = Submission::with('user')->latest()->get();
            return view('submissions.admin_index', compact('submissions'));
        }

        // Regular User: Fetch only their own submissions
        $submissions = Submission::where('user_id', $user->id)->latest()->get();
        return view('submissions.user_index', compact('submissions'));
    }

    /**
     * Show form to create a submission (linked to your dashboard 'Submit' button)
     */
    public function create()
    {
        return view('submissions.create');
    }

    /**
     * Store the submission
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
        ]);

        Submission::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return redirect()->route('submissions.index')->with('success', 'Submitted successfully!');
    }
}