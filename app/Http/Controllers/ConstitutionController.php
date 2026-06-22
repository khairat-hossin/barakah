<?php

namespace App\Http\Controllers;

use App\Models\ConstitutionSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ConstitutionController extends Controller
{
    /** Public reading page (all logged-in users). */
    public function index(): View
    {
        return view('constitution.index', [
            'sections' => ConstitutionSection::published()->ordered()->get(),
        ]);
    }

    /** Admin: list/manage sections. */
    public function manage(): View
    {
        return view('constitution.manage', [
            'sections' => ConstitutionSection::ordered()->get(),
        ]);
    }

    public function create(): View
    {
        return view('constitution.form', ['section' => new ConstitutionSection()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['title']);
        $data['sort_order'] = (int) (ConstitutionSection::max('sort_order') ?? 0) + 1;

        ConstitutionSection::create($data);

        return redirect()->route('constitution.manage')->with('success', 'Section added.');
    }

    public function edit(ConstitutionSection $section): View
    {
        return view('constitution.form', ['section' => $section]);
    }

    public function update(Request $request, ConstitutionSection $section): RedirectResponse
    {
        $data = $this->validated($request);
        if ($section->title !== $data['title']) {
            $data['slug'] = $this->uniqueSlug($data['title'], $section->id);
        }
        $section->update($data);

        return redirect()->route('constitution.manage')->with('success', 'Section updated.');
    }

    public function destroy(ConstitutionSection $section): RedirectResponse
    {
        $section->delete();

        return redirect()->route('constitution.manage')->with('success', 'Section deleted.');
    }

    /** Move a section up or down in the ordering. */
    public function move(Request $request, ConstitutionSection $section): RedirectResponse
    {
        $dir = $request->input('direction') === 'up' ? 'up' : 'down';

        $neighbor = ConstitutionSection::query()
            ->when($dir === 'up',
                fn ($q) => $q->where('sort_order', '<', $section->sort_order)->orderByDesc('sort_order'),
                fn ($q) => $q->where('sort_order', '>', $section->sort_order)->orderBy('sort_order'))
            ->first();

        if ($neighbor) {
            $a = $section->sort_order;
            $section->update(['sort_order' => $neighbor->sort_order]);
            $neighbor->update(['sort_order' => $a]);
        }

        return redirect()->route('constitution.manage');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:50'],
            'body' => ['nullable', 'string'],
            'is_published' => ['nullable', 'boolean'],
        ]) + ['is_published' => $request->boolean('is_published')];
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'section';
        $slug = $base;
        $i = 2;
        while (ConstitutionSection::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
