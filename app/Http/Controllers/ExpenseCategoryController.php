<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseCategoryController extends Controller
{
    public function index(): View
    {
        $categories = ExpenseCategory::query()
            ->with(['creator', 'updater'])
            ->latest()
            ->paginate(15);

        return view('expense-categories.index', [
            'categories' => $categories,
        ]);
    }

    public function create(): View
    {
        return view('expense-categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:expense_categories,name'],
            'code' => ['required', 'string', 'max:50', 'unique:expense_categories,code'],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        $validated['created_by'] = $request->user()->id;

        ExpenseCategory::create($validated);

        return redirect()->route('expense-categories.index')
            ->with('success', 'Expense category created successfully.');
    }

    public function edit(ExpenseCategory $expenseCategory): View
    {
        return view('expense-categories.edit', [
            'category' => $expenseCategory,
        ]);
    }

    public function update(Request $request, ExpenseCategory $expenseCategory): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:expense_categories,name,' . $expenseCategory->id],
            'code' => ['required', 'string', 'max:50', 'unique:expense_categories,code,' . $expenseCategory->id],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        $validated['updated_by'] = $request->user()->id;

        $expenseCategory->update($validated);

        return redirect()->route('expense-categories.index')
            ->with('success', 'Expense category updated successfully.');
    }

    public function destroy(ExpenseCategory $expenseCategory): RedirectResponse
    {
        // Check if category has any expenses
        if ($expenseCategory->expenses()->count() > 0) {
            return back()->with('error', 'Cannot delete category with existing expenses.');
        }

        $expenseCategory->delete();

        return redirect()->route('expense-categories.index')
            ->with('success', 'Expense category deleted successfully.');
    }
}
