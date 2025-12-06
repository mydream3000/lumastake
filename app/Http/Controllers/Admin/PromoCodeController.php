<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use Illuminate\Http\Request;

class PromoCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $promoCodes = PromoCode::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.promo.index', compact('promoCodes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.promo.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:promo_codes,code',
            'name' => 'nullable|string|max:255',
            'start_balance' => 'required|numeric|min:0',
            'max_uses' => 'required|integer|min:0',
            'uses_per_user' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['used_count'] = 0;

        PromoCode::create($validated);

        return redirect()->route('admin.promo.index')
            ->with('success', 'Promo code created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PromoCode $promoCode)
    {
        return view('admin.promo.edit', compact('promoCode'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PromoCode $promoCode)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:promo_codes,code,' . $promoCode->id,
            'name' => 'nullable|string|max:255',
            'start_balance' => 'required|numeric|min:0',
            'max_uses' => 'required|integer|min:0',
            'uses_per_user' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $promoCode->update($validated);

        return redirect()->route('admin.promo.index')
            ->with('success', 'Promo code updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PromoCode $promoCode)
    {
        $promoCode->delete();

        return redirect()->route('admin.promo.index')
            ->with('success', 'Promo code deleted successfully');
    }
}
