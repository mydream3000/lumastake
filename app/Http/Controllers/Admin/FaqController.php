<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\ToastMessage;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Display a listing of FAQs
     */
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            $faqs = Faq::orderBy('order')->orderBy('id')->get();

            return response()->json([
                'data' => $faqs->map(function ($faq) {
                    $plainAnswer = strip_tags($faq->answer);
                    return [
                        'id' => $faq->id,
                        'question' => $faq->question,
                        'answer' => mb_substr($plainAnswer, 0, 100) . (mb_strlen($plainAnswer) > 100 ? '...' : ''),
                        'is_active' => $faq->is_active ? 'Active' : 'Inactive',
                        'order' => $faq->order,
                        'created_at' => $faq->created_at->format('d, M, Y'),
                    ];
                }),
                'total' => $faqs->count(),
                'per_page' => $faqs->count(),
                'current_page' => 1,
                'last_page' => 1,
            ]);
        }

        return view('admin.faqs.index');
    }

    /**
     * Show the form for creating a new FAQ
     */
    public function create()
    {
        return view('admin.faqs.create');
    }

    /**
     * Store a newly created FAQ in database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:1000',
            'answer' => 'required|string',
            'is_active' => 'nullable|boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = $validated['order'] ?? 0;

        Faq::create($validated);

        ToastMessage::create([
            'user_id' => auth()->id(),
            'message' => 'FAQ successfully created',
            'type' => 'success',
        ]);

        return redirect()->route('admin.faqs.index');
    }

    /**
     * Show the form for editing the specified FAQ
     */
    public function edit(Faq $faq)
    {
        return view('admin.faqs.edit', compact('faq'));
    }

    /**
     * Update the specified FAQ in database
     */
    public function update(Request $request, Faq $faq)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:1000',
            'answer' => 'required|string',
            'is_active' => 'nullable|boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = $validated['order'] ?? 0;

        $faq->update($validated);

        ToastMessage::create([
            'user_id' => auth()->id(),
            'message' => 'FAQ successfully updated',
            'type' => 'success',
        ]);

        return redirect()->route('admin.faqs.index');
    }

    /**
     * Remove the specified FAQ from database
     */
    public function destroy(Faq $faq)
    {
        $faq->delete();

        ToastMessage::create([
            'user_id' => auth()->id(),
            'message' => 'FAQ successfully deleted',
            'type' => 'success',
        ]);

        return redirect()->route('admin.faqs.index');
    }
}

