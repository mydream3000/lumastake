<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use Illuminate\Http\Request;

class ContactSubmissionController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactSubmission::query()->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%");
            });
        }

        $submissions = $query->paginate(20)->withQueryString();

        $counts = [
            'all' => ContactSubmission::count(),
            'new' => ContactSubmission::where('status', 'new')->count(),
            'in_progress' => ContactSubmission::where('status', 'in_progress')->count(),
            'resolved' => ContactSubmission::where('status', 'resolved')->count(),
            'closed' => ContactSubmission::where('status', 'closed')->count(),
        ];

        return view('admin.contact-submissions.index', compact('submissions', 'counts'));
    }

    public function show(ContactSubmission $submission)
    {
        $submission->load('user');
        return view('admin.contact-submissions.show', compact('submission'));
    }

    public function updateStatus(Request $request, ContactSubmission $submission)
    {
        $request->validate([
            'status' => 'required|in:new,in_progress,resolved,closed',
            'admin_note' => 'nullable|string|max:2000',
        ]);

        $submission->update([
            'status' => $request->status,
            'admin_note' => $request->admin_note,
        ]);

        return redirect()->back()->with('success', 'Submission status updated successfully.');
    }

    public function destroy(ContactSubmission $submission)
    {
        $submission->delete();
        return redirect()->route('admin.contact-submissions.index')->with('success', 'Submission deleted successfully.');
    }
}
