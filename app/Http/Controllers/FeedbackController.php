<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->role === 'admin') {
            $feedbacks = Feedback::where('is_announcement', false)->whereNull('parent_id')->with('user', 'reactions')->latest()->paginate(20);
            $announcements = Feedback::where('is_announcement', true)->with('reactions.user')->latest()->get();
            $averageStars = Feedback::where('is_announcement', false)->avg('stars') ?? 5;
            return view('feedbacks.admin_index', compact('feedbacks', 'averageStars', 'announcements'));
        } else if ($user->role === 'proprietaire') {
            $myFeedbacks = Feedback::where('user_id', $user->id)->whereNull('parent_id')->latest()->get();
            $announcements = Feedback::where('is_announcement', true)->with(['reactions' => function($q) use ($user) {
                $q->where('user_id', $user->id);
            }])->latest()->get();
            return view('feedbacks.owner_index', compact('myFeedbacks', 'announcements'));
        }
        abort(403);
    }

    public function store(Request $request)
    {
        $request->validate([
            'stars'   => 'nullable|integer|min:1|max:5',
            'comment' => 'required|string|min:10',
            'is_announcement' => 'nullable|boolean',
            'parent_id' => 'nullable|exists:feedbacks,id',
        ]);

        $isAnnouncement = $request->boolean('is_announcement') && Auth::user()->isAdmin();

        Feedback::create([
            'user_id' => Auth::id(),
            'stars'   => $request->stars ?? 5,
            'comment' => $request->comment,
            'status'  => $isAnnouncement ? 'implemented' : 'pending',
            'is_announcement' => $isAnnouncement,
            'parent_id' => $request->parent_id,
        ]);

        $msg = $isAnnouncement ? 'Nouvelle fonctionnalité annoncée au Laboratoire !' : 'Merci pour votre retour !';
        if ($request->parent_id) $msg = 'Votre réaction a été enregistrée dans les suggestions reçues.';

        return back()->with('success', $msg);
    }

    public function updateStatus(Request $request, Feedback $feedback)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $request->validate([
            'status'     => 'required|in:pending,validated,implemented',
            'admin_note' => 'nullable|string',
        ]);

        $feedback->update([
            'status'     => $request->status,
            'admin_note' => $request->admin_note,
        ]);

        return back()->with('success', 'Statut du projet mis à jour.');
    }
}
