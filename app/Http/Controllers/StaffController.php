<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index()
    {
        $staff = User::where('role', 'gestionnaire')->get();
        return view('staff.index', compact('staff'));
    }

    public function create()
    {
        $currentCount = User::where('role', 'gestionnaire')->count();
        if ($currentCount >= 5) {
            return redirect()->route('staff.index')->with('error', 'Vous avez atteint la limite de 5 gestionnaires.');
        }
        return view('staff.create');
    }

    public function store(Request $request)
    {
        $currentCount = User::where('role', 'gestionnaire')->count();
        if ($currentCount >= 5) {
            return redirect()->route('staff.index')->with('error', 'Impossible d\'ajouter plus de 5 gestionnaires.');
        }

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'gestionnaire',
        ]);

        return redirect()->route('staff.index')->with('success', 'Nouveau gestionnaire recruté avec succès.');
    }

    public function destroy(User $user)
    {
        if ($user->role !== 'gestionnaire') {
            abort(403);
        }
        $user->delete();
        return redirect()->route('staff.index')->with('success', 'Le compte gestionnaire a été supprimé.');
    }
}
