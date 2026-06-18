<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentMethodController extends Controller
{
    public function index(): View
    {
        $paymentMethods = PaymentMethod::query()
            ->with(['creator', 'updater'])
            ->latest()
            ->paginate(15);

        return view('payment-methods.index', [
            'paymentMethods' => $paymentMethods,
        ]);
    }

    public function create(): View
    {
        return view('payment-methods.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:payment_methods,name'],
            'code' => ['required', 'string', 'max:50', 'unique:payment_methods,code'],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        $validated['created_by'] = $request->user()->id;

        PaymentMethod::create($validated);

        return redirect()->route('payment-methods.index')
            ->with('success', 'Payment method created successfully.');
    }

    public function edit(PaymentMethod $paymentMethod): View
    {
        return view('payment-methods.edit', [
            'paymentMethod' => $paymentMethod,
        ]);
    }

    public function update(Request $request, PaymentMethod $paymentMethod): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:payment_methods,name,' . $paymentMethod->id],
            'code' => ['required', 'string', 'max:50', 'unique:payment_methods,code,' . $paymentMethod->id],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        $validated['updated_by'] = $request->user()->id;

        $paymentMethod->update($validated);

        return redirect()->route('payment-methods.index')
            ->with('success', 'Payment method updated successfully.');
    }

    public function destroy(PaymentMethod $paymentMethod): RedirectResponse
    {
        // Check if payment method has any savings entries
        if ($paymentMethod->savingsEntries()->count() > 0) {
            return back()->with('error', 'Cannot delete payment method with existing deposits.');
        }

        $paymentMethod->delete();

        return redirect()->route('payment-methods.index')
            ->with('success', 'Payment method deleted successfully.');
    }
}
