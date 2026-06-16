<?php

namespace App\Http\Controllers;

use App\Models\InvestmentType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvestmentTypeController extends Controller
{
    public function index(): View
    {
        $types = InvestmentType::latest('created_at')->paginate(20);
        return view('investment-types.index', ['types' => $types]);
    }

    public function create(): View
    {
        return view('investment-types.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:investment_types,code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:100'],
            'default_tenure_months' => ['nullable', 'integer'],
            'default_return_type' => ['required', 'in:fixed,variable,dividend'],
            'requires_approval' => ['sometimes', 'boolean'],
            'min_investment_amount' => ['nullable', 'numeric', 'min:0'],
            'max_investment_amount' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $validated['created_by'] = auth()->id();
        InvestmentType::create($validated);

        return redirect()->route('investment-types.index')
            ->with('success', 'Investment type created successfully.');
    }

    public function edit(InvestmentType $investmentType): View
    {
        return view('investment-types.edit', ['type' => $investmentType]);
    }

    public function update(Request $request, InvestmentType $investmentType): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:100'],
            'default_tenure_months' => ['nullable', 'integer'],
            'default_return_type' => ['required', 'in:fixed,variable,dividend'],
            'requires_approval' => ['sometimes', 'boolean'],
            'min_investment_amount' => ['nullable', 'numeric', 'min:0'],
            'max_investment_amount' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $validated['updated_by'] = auth()->id();
        $investmentType->update($validated);

        return redirect()->route('investment-types.index')
            ->with('success', 'Investment type updated successfully.');
    }

    public function destroy(InvestmentType $investmentType): RedirectResponse
    {
        // Check if any investments exist for this type
        if ($investmentType->investments()->exists()) {
            return back()->with('error', 'Cannot delete investment type with existing investments.');
        }

        $investmentType->delete();

        return redirect()->route('investment-types.index')
            ->with('success', 'Investment type deleted successfully.');
    }
}
