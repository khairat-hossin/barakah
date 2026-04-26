<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    private const STATUSES = [
        'draft',
        'proposed',
        'approved',
        'active',
        'completed',
        'cancelled',
    ];

    public function index(): View
    {
        $projects = Project::query()
            ->with('owner')
            ->latest()
            ->get();

        $statusCounts = array_merge(
            array_fill_keys(self::STATUSES, 0),
            $projects->countBy('status')->all(),
        );

        return view('projects.index', [
            'projects' => $projects,
            'statusCounts' => $statusCounts,
        ]);
    }

    public function create(): View
    {
        return view('projects.create', [
            'statuses' => self::STATUSES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'string', 'in:'.implode(',', self::STATUSES)],
            'budget_requested' => ['nullable', 'numeric', 'min:0'],
            'budget_approved' => ['nullable', 'numeric', 'min:0'],
            'expected_return_percentage' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'start_date' => ['nullable', 'date'],
            'deadline' => ['nullable', 'date', 'after_or_equal:start_date'],
            'progress_percentage' => ['required', 'integer', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['user_id'] = $request->user()->id;

        Project::create($validated);

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project created successfully.');
    }
}
