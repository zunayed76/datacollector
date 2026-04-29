<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ProfileController extends Controller
{
    /**
     * Show the form for creating a new profile.
     */
    public function create()
    {
        // If already completed, redirect to edit
        // if (Auth::user()->is_profile_completed) {
        //     return redirect()->route('profile.edit');
        // }
        
        return view('profile.create');
    }

    /**
     * Store a newly created profile in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'phone' => 'required|string|max:15',
            // Add other fields you want to collect (e.g., address, bio)
        ]);

        /** @var \App\Models\User $user */
        $user->update([
            'phone' => $request->phone,
            //'is_profile_completed' => true, // Flip the flag here
        ]);

        return redirect()->route('dashboard')->with('success', 'Profile setup complete!');
    }

    /**
     * Show the form for editing the profile.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the profile in storage.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'phone' => 'required|string|max:15',
        ]);
        /** @var \App\Models\User $user */
        $user->update($request->only('phone'));

        return redirect()->route('dashboard')->with('success', 'Profile updated successfully!');
    }
}