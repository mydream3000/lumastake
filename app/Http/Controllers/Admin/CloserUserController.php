<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CloserUserNote;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CloserUserController extends Controller
{
    /**
     * Display users list for Closer (also accessible by admins)
     */
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            $query = User::query()->where('is_admin', false)->where('is_closer', false);

            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('id', 'like', "%{$search}%");
                });
            }

            // Filter by closer status
            if ($request->filled('closer_status')) {
                $query->whereHas('closerNotes', function ($q) use ($request) {
                    $q->where('status', $request->closer_status);
                });
            }

            // Sorting
            $sortField = $request->get('sort', 'created_at');
            $sortDirection = $request->get('direction', 'desc');
            $allowedSorts = ['id', 'name', 'email', 'created_at', 'account_type'];
            if (in_array($sortField, $allowedSorts)) {
                $query->orderBy($sortField, $sortDirection);
            } else {
                $query->orderBy('created_at', 'desc');
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $users = $query->paginate($perPage);

            // Get latest closer note for each user on this page
            $userIds = $users->pluck('id')->all();
            $latestNotes = [];
            if (!empty($userIds)) {
                $notes = CloserUserNote::whereIn('user_id', $userIds)
                    ->with('closer:id,name')
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->groupBy('user_id');

                foreach ($notes as $userId => $userNotes) {
                    $latest = $userNotes->first();
                    $latestNotes[$userId] = [
                        'comment' => $latest->comment,
                        'status' => $latest->status,
                        'closer_name' => $latest->closer->name ?? 'Unknown',
                        'updated_at' => $latest->updated_at->format('d, M, Y H:i'),
                    ];
                }
            }

            return response()->json([
                'data' => $users->map(function ($user) use ($latestNotes) {
                    $note = $latestNotes[$user->id] ?? null;
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone ? trim(($user->country_code ? $user->country_code . ' ' : '') . $user->phone) : null,
                        'account_type' => $user->account_type,
                        'country' => $user->country,
                        'created_at' => $user->created_at->format('d, M, Y'),
                        'closer_comment' => $note['comment'] ?? '',
                        'closer_status' => $note['status'] ?? '',
                        'closer_name' => $note['closer_name'] ?? '',
                        'closer_updated_at' => $note['updated_at'] ?? '',
                    ];
                }),
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
            ]);
        }

        return view('admin.closer.users.index');
    }

    /**
     * Show user details for Closer (limited view)
     */
    public function show(User $user)
    {
        $notes = CloserUserNote::where('user_id', $user->id)
            ->with('closer:id,name')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.closer.users.show', compact('user', 'notes'));
    }

    /**
     * Save a closer note (comment/status) for a user
     */
    public function saveNote(Request $request, User $user)
    {
        $validated = $request->validate([
            'comment' => 'nullable|string|max:1000',
            'status' => 'nullable|string|in:int,no-int,re-call,fake',
        ]);

        $note = CloserUserNote::create([
            'user_id' => $user->id,
            'closer_id' => Auth::id(),
            'comment' => $validated['comment'] ?? null,
            'status' => $validated['status'] ?? null,
        ]);

        $note->load('closer:id,name');

        return response()->json([
            'success' => true,
            'message' => 'Note saved',
            'note' => [
                'id' => $note->id,
                'comment' => $note->comment,
                'status' => $note->status,
                'closer_name' => $note->closer->name ?? 'Unknown',
                'created_at' => $note->created_at->format('d, M, Y H:i'),
            ],
        ]);
    }
}
