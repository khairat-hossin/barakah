<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseAttachment;
use App\Models\ExpenseCategory;
use App\Models\ExpenseStatusHistory;
use App\Models\Member;
use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    private const FUND_SOURCES = ['operating', 'reserve', 'emergency'];
    private const PAYMENT_METHODS = ['cash', 'bank', 'mobile_banking', 'other'];
    private const ATTACHMENT_TYPES = ['receipt', 'invoice', 'voucher', 'supporting_document'];

    public function index(): View
    {
        $expenses = Expense::query()
            ->with(['category', 'member', 'creator'])
            ->latest('expense_date')
            ->get();

        $totalExpenses = $expenses->sum('amount');
        $monthlyExpenses = $expenses->filter(fn($e) => $e->expense_date->isCurrentMonth())->sum('amount');
        $pendingCount = Expense::pending()->count();

        $categories = ExpenseCategory::active()->orderBy('name')->get();

        return view('expenses.index', [
            'expenses' => $expenses,
            'totalExpenses' => $totalExpenses,
            'monthlyExpenses' => $monthlyExpenses,
            'pendingCount' => $pendingCount,
            'categories' => $categories,
            'fundSources' => self::FUND_SOURCES,
        ]);
    }

    public function create(): View
    {
        $categories = ExpenseCategory::active()->orderBy('name')->get();
        $members = Member::where('status', 'active')->orderBy('name')->get();

        return view('expenses.create', [
            'categories' => $categories,
            'members' => $members,
            'fundSources' => self::FUND_SOURCES,
            'paymentMethods' => self::PAYMENT_METHODS,
            'attachmentTypes' => self::ATTACHMENT_TYPES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:expense_categories,id'],
            'member_id' => ['nullable', 'exists:members,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'expense_date' => ['required', 'date', 'before_or_equal:today'],
            'fund_source' => ['required', 'in:' . implode(',', self::FUND_SOURCES)],
            'payment_method' => ['required', 'in:' . implode(',', self::PAYMENT_METHODS)],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $validated['created_by'] = $request->user()->id;
        $validated['status'] = $request->input('action') === 'submit' ? 'pending' : 'draft';
        $validated['expense_number'] = $this->generateExpenseNumber();

        $expense = Expense::create($validated);

        // Log status transition if submitted
        if ($validated['status'] === 'pending') {
            ExpenseStatusHistory::create([
                'expense_id' => $expense->id,
                'status_from' => 'draft',
                'status_to' => 'pending',
                'changed_by' => $request->user()->id,
                'changed_at' => now(),
            ]);
        }

        // Log to AuditLog
        AuditLog::create([
            'user_id' => $request->user()->id,
            'action_type' => 'expense_created',
            'entity_type' => 'Expense',
            'entity_id' => $expense->id,
            'new_value' => $expense->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $message = $validated['status'] === 'pending'
            ? 'Expense submitted for approval.'
            : 'Expense saved as draft. You can submit it for approval later.';

        return redirect()->route('expenses.show', $expense)
            ->with('success', $message);
    }

    public function show(Expense $expense): View
    {
        $expense->load(['category', 'member', 'creator', 'approver', 'attachments.uploader', 'statusHistories.changedBy']);

        $auditLogs = AuditLog::where('entity_type', 'Expense')
            ->where('entity_id', $expense->id)
            ->with('user')
            ->latest('timestamp')
            ->get();

        return view('expenses.show', [
            'expense' => $expense,
            'auditLogs' => $auditLogs,
            'attachmentTypes' => self::ATTACHMENT_TYPES,
        ]);
    }

    public function receipt(Expense $expense)
    {
        $this->authorize('view', $expense);

        $expense->load(['category', 'member', 'creator', 'approver']);

        return \App\Support\PdfRenderer::download(
            'expenses.receipt',
            [
                'expense' => $expense,
                'org' => \App\Models\OrganizationProfile::first(),
            ],
            'expense-receipt-' . ($expense->expense_number ?: $expense->id) . '.pdf',
            ['margin_top' => 10, 'margin_bottom' => 10, 'margin_left' => 10, 'margin_right' => 10]
        );
    }

    public function edit(Expense $expense): View
    {
        if ($expense->status !== 'draft') {
            return back()->with('error', 'Only draft expenses can be edited.');
        }

        $categories = ExpenseCategory::active()->orderBy('name')->get();
        $members = Member::where('status', 'active')->orderBy('name')->get();

        return view('expenses.edit', [
            'expense' => $expense,
            'categories' => $categories,
            'members' => $members,
            'fundSources' => self::FUND_SOURCES,
            'paymentMethods' => self::PAYMENT_METHODS,
        ]);
    }

    public function update(Request $request, Expense $expense): RedirectResponse
    {
        if ($expense->status !== 'draft') {
            return back()->with('error', 'Only draft expenses can be edited.');
        }

        $validated = $request->validate([
            'category_id' => ['required', 'exists:expense_categories,id'],
            'member_id' => ['nullable', 'exists:members,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'expense_date' => ['required', 'date', 'before_or_equal:today'],
            'fund_source' => ['required', 'in:' . implode(',', self::FUND_SOURCES)],
            'payment_method' => ['required', 'in:' . implode(',', self::PAYMENT_METHODS)],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $oldValues = $expense->toArray();
        $expense->update($validated);

        // Log to AuditLog
        AuditLog::create([
            'user_id' => $request->user()->id,
            'action_type' => 'expense_updated',
            'entity_type' => 'Expense',
            'entity_id' => $expense->id,
            'old_value' => $oldValues,
            'new_value' => $expense->fresh()->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('expenses.show', $expense)
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        if ($expense->status !== 'draft') {
            return back()->with('error', 'Only draft expenses can be deleted.');
        }

        $expenseId = $expense->id;
        $expense->delete();

        AuditLog::create([
            'user_id' => request()->user()->id,
            'action_type' => 'deleted',
            'entity_type' => 'Expense',
            'entity_id' => $expenseId,
            'old_value' => $expense->toArray(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('expenses.index')
            ->with('success', 'Draft expense deleted.');
    }

    public function approve(Expense $expense): View
    {
        if ($expense->status !== 'pending') {
            return back()->with('error', 'Only pending expenses can be approved.');
        }

        $expense->load(['category', 'member', 'creator']);

        return view('expenses.approve', [
            'expense' => $expense,
        ]);
    }

    public function approveStore(Request $request, Expense $expense): RedirectResponse
    {
        if ($expense->status !== 'pending') {
            return back()->with('error', 'Only pending expenses can be approved.');
        }

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $expense->approve($request->user()->id, $validated['notes'] ?? null);

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action_type' => 'expense_approved',
            'entity_type' => 'Expense',
            'entity_id' => $expense->id,
            'new_value' => ['status' => 'approved', 'approved_by' => $request->user()->id],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('expenses.show', $expense)
            ->with('success', 'Expense approved successfully.');
    }

    public function markAsPaid(Request $request, Expense $expense): RedirectResponse
    {
        if ($expense->status !== 'approved') {
            return back()->with('error', 'Only approved expenses can be marked as paid.');
        }

        $expense->markAsPaid($request->user()->id);

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action_type' => 'expense_paid',
            'entity_type' => 'Expense',
            'entity_id' => $expense->id,
            'new_value' => ['status' => 'paid', 'paid_at' => now()],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('expenses.show', $expense)
            ->with('success', 'Expense marked as paid.');
    }

    public function storeAttachment(Request $request, Expense $expense): RedirectResponse
    {
        if ($expense->status === 'paid') {
            return back()->with('error', 'Cannot add attachments to paid expenses.');
        }

        $validated = $request->validate([
            'attachment_type' => ['required', 'in:' . implode(',', self::ATTACHMENT_TYPES)],
            'file' => ['required', 'file', 'max:5120', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx'],
        ]);

        $file = $validated['file'];

        // Validate MIME type
        $allowedMimes = [
            'image/jpeg', 'image/png', 'application/pdf',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/msword'
        ];

        if (!in_array($file->getMimeType(), $allowedMimes)) {
            return back()->with('error', 'Invalid file type.');
        }

        // Store file
        $path = $file->store("expenses/{$expense->id}", 'private');

        // Create attachment record
        $attachment = $expense->attachments()->create([
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'attachment_type' => $validated['attachment_type'],
            'uploaded_by' => $request->user()->id,
            'created_at' => now(),
        ]);

        // Log to AuditLog
        AuditLog::create([
            'user_id' => $request->user()->id,
            'action_type' => 'attachment_uploaded',
            'entity_type' => 'ExpenseAttachment',
            'entity_id' => $attachment->id,
            'new_value' => [
                'expense_id' => $expense->id,
                'file_name' => $file->getClientOriginalName(),
                'attachment_type' => $validated['attachment_type'],
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Attachment uploaded successfully.');
    }

    public function downloadAttachment(ExpenseAttachment $attachment)
    {
        return Storage::disk('private')->download(
            $attachment->file_path,
            $attachment->file_name
        );
    }

    public function deleteAttachment(Request $request, ExpenseAttachment $attachment): RedirectResponse
    {
        $expense = $attachment->expense;

        if ($expense->status === 'paid') {
            return back()->with('error', 'Cannot delete attachments from paid expenses.');
        }

        $attachmentId = $attachment->id;

        $attachment->delete();

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action_type' => 'attachment_deleted',
            'entity_type' => 'ExpenseAttachment',
            'entity_id' => $attachmentId,
            'old_value' => ['file_name' => $attachment->file_name],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Attachment deleted.');
    }

    public function datatable(Request $request): JsonResponse
    {
        $draw = $request->get('draw', 1);
        $start = $request->get('start', 0);
        $length = $request->get('length', 10);
        $search = $request->get('search')['value'] ?? '';
        $category = $request->get('category', '');
        $status = $request->get('status', '');
        $fundSource = $request->get('fund_source', '');

        $query = Expense::with(['category', 'member', 'creator']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('expense_number', 'like', "%{$search}%")
                    ->orWhereHas('member', fn($m) => $m->where('name', 'like', "%{$search}%"));
            });
        }

        if ($category) {
            $query->where('category_id', $category);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($fundSource) {
            $query->where('fund_source', $fundSource);
        }

        $filtered = $query->count();
        $total = Expense::count();

        $expenses = $query->latest('expense_date')
            ->offset($start)
            ->limit($length)
            ->get()
            ->map(fn($e) => [
                'id' => $e->id,
                'expense_number' => $e->expense_number,
                'expense_date' => $e->expense_date->format('d M Y'),
                'category' => $e->category->name,
                'title' => $e->title,
                'member' => $e->member?->name ?? '-',
                'amount' => number_format($e->amount, 2),
                'fund_source' => $e->fund_source,
                'status' => $e->status,
            ]);

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $expenses,
        ]);
    }

    private function generateExpenseNumber(): string
    {
        $year = now()->year;
        $maxNumber = Expense::whereYear('created_at', $year)
            ->latest('id')
            ->value('expense_number');

        if (!$maxNumber) {
            $sequence = 1;
        } else {
            $parts = explode('-', $maxNumber);
            $sequence = (int)end($parts) + 1;
        }

        return sprintf('EXP-%d-%06d', $year, $sequence);
    }
}
