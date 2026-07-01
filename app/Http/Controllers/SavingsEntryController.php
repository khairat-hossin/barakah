<?php

namespace App\Http\Controllers;

use App\Mail\DepositReceiptMail;
use App\Models\Member;
use App\Models\PaymentMethod;
use App\Models\SavingsEntry;
use App\Support\Notify;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class SavingsEntryController extends Controller
{
    /**
     * Notify admins (bell) and email the member their deposit receipt.
     */
    private function notifyDeposit(SavingsEntry $savingsEntry): void
    {
        $savingsEntry->loadMissing('member');
        $member = $savingsEntry->member;

        Notify::admins(
            'New deposit recorded',
            ($member?->name ?? 'A member') . ' deposited Tk ' . number_format($savingsEntry->amount, 0),
            'dollar-sign',
            route('deposits.show', $savingsEntry),
        );

        Notify::mailQuietly($member?->email, new DepositReceiptMail($savingsEntry));
    }

    public function index(): View
    {
        $entries = SavingsEntry::query()
            ->with(['member', 'recorder'])
            ->latest('deposit_date')
            ->latest()
            ->get();

        return view('deposits.index', [
            'entries' => $entries,
            'totalCollected' => $entries->sum(fn (SavingsEntry $entry): float => (float) $entry->amount),
            'monthlyCollected' => $entries
                ->filter(fn (SavingsEntry $entry): bool => optional($entry->deposit_date)->isCurrentMonth() === true)
                ->sum(fn (SavingsEntry $entry): float => (float) $entry->amount),
        ]);
    }

    public function create(): View
    {
        return view('deposits.create', [
            'members' => Member::query()->where('status', 'active')->orderBy('name')->get(),
            'paymentMethods' => PaymentMethod::active()->ordered()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $paymentMethodIds = PaymentMethod::active()->pluck('id')->toArray();

        $validated = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'deposit_date' => ['required', 'date'],
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'transaction_id' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'months' => ['required', 'array', 'min:1'],
            'months.*' => ['string', 'regex:/^\d{1,2}\/\d{4}$/'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:5120'],
        ]);

        $months = $validated['months'];
        unset($validated['months']);

        // Block deposits for deactivated members.
        if (Member::whereKey($validated['member_id'])->value('status') !== 'active') {
            return back()->withInput()->withErrors([
                'member_id' => 'This member is deactivated — no new deposits can be recorded.',
            ]);
        }

        // Enforce one deposit per member per month — reject any month already paid.
        $duplicateMonths = [];
        foreach ($months as $monthYear) {
            [$month, $year] = explode('/', $monthYear);
            $exists = \App\Models\MemberDepositMonth::where('member_id', $validated['member_id'])
                ->where('month', (int) $month)
                ->where('year', (int) $year)
                ->exists();
            if ($exists) {
                $duplicateMonths[] = \Carbon\Carbon::createFromDate((int) $year, (int) $month, 1)->format('M Y');
            }
        }

        if (! empty($duplicateMonths)) {
            return back()
                ->withInput()
                ->withErrors([
                    'months' => 'This member already has a deposit for: ' . implode(', ', $duplicateMonths) . '. Only one deposit per month is allowed.',
                ]);
        }

        $validated['recorded_by'] = $request->user()->id;
        $validated['payment_method'] = PaymentMethod::whereKey($validated['payment_method_id'])->value('code');

        $savingsEntry = \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $months) {
            // Create the savings entry
            $savingsEntry = SavingsEntry::create($validated);

            // Create monthly deposit records
            foreach ($months as $monthYear) {
                [$month, $year] = explode('/', $monthYear);
                \App\Models\MemberDepositMonth::create([
                    'member_id' => $validated['member_id'],
                    'month' => (int) $month,
                    'year' => (int) $year,
                    'savings_entry_id' => $savingsEntry->id,
                ]);
            }

            return $savingsEntry;
        });

        $this->notifyDeposit($savingsEntry);

        return redirect()
            ->route('deposits.index')
            ->with('success', 'Deposit recorded successfully for ' . count($months) . ' month(s).');
    }

    public function datatable(Request $request): JsonResponse
    {
        $draw = $request->get('draw', 1);
        $start = $request->get('start', 0);
        $length = $request->get('length', 10);
        $search = $request->get('search')['value'] ?? '';
        $paymentMethod = $request->get('payment_method', '');
        $fromMonth = $request->get('from_month', '');
        $toMonth = $request->get('to_month', '');

        $query = SavingsEntry::with(['member', 'recorder']);

        if ($search) {
            $query->whereHas('member', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('member_code', 'like', "%{$search}%");
            });
        }

        if ($paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        }

        // Filter by deposit_date month range (inclusive). Inputs are "YYYY-MM".
        if (preg_match('/^\d{4}-\d{2}$/', $fromMonth)) {
            $query->where('deposit_date', '>=', \Carbon\Carbon::createFromFormat('Y-m', $fromMonth)->startOfMonth());
        }
        if (preg_match('/^\d{4}-\d{2}$/', $toMonth)) {
            $query->where('deposit_date', '<=', \Carbon\Carbon::createFromFormat('Y-m', $toMonth)->endOfMonth());
        }

        $filtered = $query->count();
        $total = SavingsEntry::count();

        $entries = $query->latest('deposit_date')
            ->latest()
            ->offset($start)
            ->limit($length)
            ->get();

        $data = $entries->map(function (SavingsEntry $entry) {
            return [
                'id' => $entry->id,
                'member_id' => $entry->member_id,
                'member_name' => $entry->member->name ?? 'N/A',
                'member_status' => $entry->member->status ?? null,
                'amount' => $entry->amount,
                'deposit_date' => $entry->deposit_date,
                'payment_method' => $entry->payment_method,
                'transaction_id' => $entry->transaction_id ?? '',
                'recorder_name' => $entry->recorder->name ?? 'System',
            ];
        });

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data,
        ]);
    }

    public function show(SavingsEntry $savingsEntry): View
    {
        $savingsEntry->load(['member', 'recorder']);

        return view('deposits.show', [
            'deposit' => $savingsEntry,
        ]);
    }

    public function sendReceipt(SavingsEntry $savingsEntry): RedirectResponse
    {
        $this->authorize('view', $savingsEntry);

        $savingsEntry->loadMissing('member');
        $email = $savingsEntry->member?->email;

        if (! $email) {
            return back()->with('error', 'This member has no email address on file.');
        }

        try {
            Mail::to($email)->queue(new DepositReceiptMail($savingsEntry));
            return back()->with('success', 'Deposit receipt is being emailed to ' . $email . '.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Could not send the email: ' . $e->getMessage());
        }
    }

    public function receipt(SavingsEntry $savingsEntry)
    {
        $this->authorize('view', $savingsEntry);

        $savingsEntry->load(['member', 'recorder', 'paymentMethod']);

        return \App\Support\PdfRenderer::download(
            'deposits.receipt',
            [
                'deposit' => $savingsEntry,
                'org' => \App\Models\OrganizationProfile::first(),
            ],
            'deposit-receipt-' . ($savingsEntry->transaction_id ?: $savingsEntry->id) . '.pdf',
            ['title' => 'Deposit Receipt — ' . ($savingsEntry->member?->name ?? ''), 'margin_top' => 10, 'margin_bottom' => 10, 'margin_left' => 10, 'margin_right' => 10]
        );
    }

    public function edit(SavingsEntry $savingsEntry): View
    {
        return view('deposits.edit', [
            'deposit' => $savingsEntry,
            'members' => Member::query()->where('status', 'active')->orderBy('name')->get(),
            'paymentMethods' => PaymentMethod::active()->ordered()->get(),
        ]);
    }

    public function update(Request $request, SavingsEntry $savingsEntry): RedirectResponse
    {
        $validated = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'deposit_date' => ['required', 'date'],
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'transaction_id' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:5120'],
        ]);

        $validated['payment_method'] = PaymentMethod::whereKey($validated['payment_method_id'])->value('code');

        $savingsEntry->update($validated);

        return redirect()
            ->route('deposits.show', $savingsEntry)
            ->with('success', 'Deposit updated successfully.');
    }

    public function destroy(SavingsEntry $savingsEntry): RedirectResponse
    {
        $savingsEntry->delete();

        return redirect()
            ->route('deposits.index')
            ->with('success', 'Deposit deleted successfully.');
    }

    /**
     * Check whether a member already has a deposit recorded for a given month.
     * Used by the deposit forms to warn before submitting.
     */
    public function checkMonth(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'month' => ['required', 'date_format:Y-m'],
        ]);

        $monthDate = \Carbon\Carbon::createFromFormat('Y-m', $validated['month']);

        $exists = \App\Models\MemberDepositMonth::where('member_id', $validated['member_id'])
            ->where('month', $monthDate->month)
            ->where('year', $monthDate->year)
            ->exists();

        return response()->json([
            'exists' => $exists,
            'month_label' => $monthDate->format('M Y'),
        ]);
    }

    /**
     * One-click mark-as-paid: records a deposit for a member + month with
     * auto-filled amount (from member's shares), a generated transaction id,
     * and the default Bank Transfer payment method. No manual fields.
     */
    public function markPaid(Request $request): JsonResponse
    {
        $this->authorize('create', SavingsEntry::class);

        $validated = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'month' => ['required', 'date_format:Y-m'],
        ]);

        $member = \App\Models\Member::findOrFail($validated['member_id']);
        $monthDate = \Carbon\Carbon::createFromFormat('Y-m', $validated['month']);

        // Block deposits for deactivated members.
        if (! $member->isActive()) {
            return response()->json([
                'message' => "{$member->name} is deactivated — no new deposits can be recorded.",
            ], 422);
        }

        // Enforce one deposit per member per month.
        $alreadyDeposited = \App\Models\MemberDepositMonth::where('member_id', $member->id)
            ->where('month', $monthDate->month)
            ->where('year', $monthDate->year)
            ->exists();

        if ($alreadyDeposited) {
            return response()->json([
                'message' => "{$member->name} already has a deposit for " . $monthDate->format('M Y') . '.',
            ], 422);
        }

        $amount = $member->getCalculatedMonthlyDepositAmount();

        if ($amount <= 0) {
            return response()->json([
                'message' => "Cannot record a deposit for {$member->name}: monthly amount is 0. Assign shares first.",
            ], 422);
        }

        // Resolve default payment method (Bank Transfer), fall back to any active one.
        $paymentMethod = PaymentMethod::where('code', 'bank_transfer')->first()
            ?? PaymentMethod::active()->ordered()->first();

        if (! $paymentMethod) {
            return response()->json([
                'message' => 'No payment method configured.',
            ], 422);
        }

        // Generate a unique transaction id.
        do {
            $transactionId = 'AUTO-' . now()->format('YmdHis') . '-' . strtoupper(\Illuminate\Support\Str::random(5));
        } while (SavingsEntry::where('transaction_id', $transactionId)->exists());

        $savingsEntry = \Illuminate\Support\Facades\DB::transaction(function () use ($member, $monthDate, $amount, $paymentMethod, $transactionId) {
            $savingsEntry = SavingsEntry::create([
                'member_id' => $member->id,
                'amount' => $amount,
                'deposit_date' => $monthDate->copy()->endOfMonth(),
                'payment_method_id' => $paymentMethod->id,
                'payment_method' => $paymentMethod->code,
                'transaction_id' => $transactionId,
                'notes' => 'Marked as paid for ' . $monthDate->format('F Y'),
                'recorded_by' => auth()->id(),
            ]);

            \App\Models\MemberDepositMonth::create([
                'member_id' => $member->id,
                'month' => $monthDate->month,
                'year' => $monthDate->year,
                'savings_entry_id' => $savingsEntry->id,
            ]);

            return $savingsEntry;
        });

        $this->notifyDeposit($savingsEntry);

        return response()->json([
            'message' => "Deposit recorded for {$member->name} ({$monthDate->format('M Y')}).",
            'amount' => $amount,
            'transaction_id' => $transactionId,
        ], 201);
    }

    /**
     * Show the bulk (matrix) deposit import screen.
     */
    public function bulkImportForm(): View
    {
        $this->authorize('create', SavingsEntry::class);

        return view('deposits.import');
    }

    /**
     * Download a ready-to-fill matrix template for the bulk deposit import:
     * row 1 = each member's code (with their name as a hover comment), column A
     * = a year of example month names, and a second sheet listing code → name.
     */
    public function bulkImportTemplate(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $this->authorize('create', SavingsEntry::class);

        $members = Member::query()
            ->whereNotNull('member_code')
            ->where('member_code', '!=', '')
            ->orderByRaw('CAST(SUBSTRING(member_code, 2) AS UNSIGNED)')
            ->get(['member_code', 'name']);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        // --- Sheet 1: the matrix to fill ---
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Deposits');
        $sheet->setCellValue('A1', 'Month');

        $col = 2; // start at column B
        foreach ($members as $member) {
            $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $sheet->setCellValue($letter . '1', $member->member_code);
            // Member name as a hover comment so the filler knows who each code is.
            $sheet->getComment($letter . '1')->getText()->createText($member->name);
            $sheet->getColumnDimension($letter)->setWidth(12);
            $col++;
        }
        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(max(2, $col - 1));

        // Example month names down column A (one calendar year). The actual year
        // is taken from the import form and rolls over at each December → January.
        $months = ['January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'];
        $row = 2;
        foreach ($months as $m) {
            $sheet->setCellValue('A' . $row, $m);
            $row++;
        }

        // Style + freeze the header row and the month column.
        $headerRange = 'A1:' . $lastColLetter . '1';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle($headerRange)->getFill()->getStartColor()->setARGB('FFD3D3D3');
        $sheet->getStyle('A2:A' . ($row - 1))->getFont()->setBold(true);
        $sheet->getColumnDimension('A')->setWidth(16);
        $sheet->freezePane('B2');

        // --- Sheet 2: instructions + member reference ---
        $ref = $spreadsheet->createSheet();
        $ref->setTitle('Instructions');
        $ref->setCellValue('A1', 'How to fill this template');
        $ref->getStyle('A1')->getFont()->setBold(true)->setSize(12);
        $notes = [
            '1. On the "Deposits" sheet, each column (B onward) is a member — the code is in row 1 (hover for the name).',
            '2. Column A holds the month names, one per row. Keep them in calendar order; the year rolls over at December → January.',
            '3. In each cell, type the deposit amount that member paid that month. Leave blank (or 0) if there was no deposit.',
            '4. Add or remove month rows as needed. The first row\'s year is set on the import screen.',
            '5. Member codes must match existing members exactly. The reference list below shows all current codes.',
        ];
        $r = 3;
        foreach ($notes as $n) {
            $ref->setCellValue('A' . $r, $n);
            $r++;
        }
        $r += 1;
        $ref->setCellValue('A' . $r, 'Code');
        $ref->setCellValue('B' . $r, 'Member Name');
        $ref->getStyle('A' . $r . ':B' . $r)->getFont()->setBold(true);
        $r++;
        foreach ($members as $member) {
            $ref->setCellValue('A' . $r, $member->member_code);
            $ref->setCellValue('B' . $r, $member->name);
            $r++;
        }
        $ref->getColumnDimension('A')->setWidth(14);
        $ref->getColumnDimension('B')->setWidth(40);

        $spreadsheet->setActiveSheetIndex(0);

        $filename = 'Deposit_Import_Template_' . now()->format('Y-m-d') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet))->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Import a months × member-codes matrix of deposit amounts.
     *
     * Layout: row 1 = member codes (from column B); column A = month names
     * (contiguous, starting at the given start year); each cell = the deposit
     * amount for that member in that month. Blank/zero cells are skipped.
     * The import is idempotent — a member+month that already has a deposit is
     * skipped, so re-running never duplicates. With "dry run" checked nothing
     * is written; you just get the summary.
     */
    public function bulkImport(Request $request): RedirectResponse
    {
        $this->authorize('create', SavingsEntry::class);

        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
            'start_year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'dry_run' => ['nullable', 'boolean'],
        ]);

        $dryRun = (bool) ($request->boolean('dry_run'));

        $paymentMethod = PaymentMethod::where('code', 'bank_transfer')->first()
            ?? PaymentMethod::active()->ordered()->first();

        if (! $paymentMethod) {
            return back()->with('error', 'No payment method is configured. Add a Bank Transfer payment method first.');
        }

        try {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $spreadsheet = $reader->load($validated['file']->path());
            // Always read the first sheet — the template adds an Instructions
            // sheet, and whichever sheet the user last viewed becomes "active".
            $worksheet = $spreadsheet->getSheet(0);
        } catch (\Throwable $e) {
            return back()->with('error', 'Could not read the file: ' . $e->getMessage());
        }

        $highestColumn = $worksheet->getHighestDataColumn();
        $highestColIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
        $highestRow = $worksheet->getHighestDataRow();

        // Map each data column (B..) to a member by the code in row 1.
        $membersByCode = Member::pluck('id', 'member_code'); // code => id
        $columnMembers = [];   // colIndex => member_id
        $unknownCodes = [];    // colIndex => code
        for ($col = 2; $col <= $highestColIndex; $col++) {
            $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $code = trim((string) $worksheet->getCell($letter . '1')->getValue());
            if ($code === '') {
                continue;
            }
            if ($membersByCode->has($code)) {
                $columnMembers[$col] = $membersByCode->get($code);
            } else {
                $unknownCodes[$col] = $code;
            }
        }

        $monthNames = [
            'january' => 1, 'february' => 2, 'march' => 3, 'april' => 4,
            'may' => 5, 'june' => 6, 'july' => 7, 'august' => 8,
            'september' => 9, 'october' => 10, 'november' => 11, 'december' => 12,
        ];

        $year = (int) $validated['start_year'];
        $prevMonthNum = null;

        $toCreate = [];          // pending deposits
        $created = 0;
        $skippedExisting = 0;
        $skippedUnknownCode = 0;
        $skippedEmpty = 0;
        $totalAmount = 0.0;
        $rowWarnings = [];

        for ($rowNum = 2; $rowNum <= $highestRow; $rowNum++) {
            $monthRaw = trim((string) $worksheet->getCell('A' . $rowNum)->getValue());
            if ($monthRaw === '') {
                continue;
            }

            $key = strtolower(preg_replace('/[^a-zA-Z]/', '', $monthRaw));
            if (! isset($monthNames[$key])) {
                $rowWarnings[] = "Row {$rowNum}: unrecognised month \"{$monthRaw}\" — skipped.";
                continue;
            }
            $monthNum = $monthNames[$key];

            // Roll the year forward whenever the month wraps back (e.g. Dec -> Jan).
            if ($prevMonthNum !== null && $monthNum <= $prevMonthNum) {
                $year++;
            }
            $prevMonthNum = $monthNum;

            $monthDate = \Carbon\Carbon::createFromDate($year, $monthNum, 1);

            for ($col = 2; $col <= $highestColIndex; $col++) {
                if (isset($unknownCodes[$col])) {
                    // Count non-empty cells under an unknown code so the summary is honest.
                    $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                    $v = $worksheet->getCell($letter . $rowNum)->getValue();
                    if ($v !== null && $v !== '' && (float) $v > 0) {
                        $skippedUnknownCode++;
                    }
                    continue;
                }

                if (! isset($columnMembers[$col])) {
                    continue;
                }

                $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                $value = $worksheet->getCell($letter . $rowNum)->getValue();

                if ($value === null || $value === '' || ! is_numeric($value) || (float) $value <= 0) {
                    $skippedEmpty++;
                    continue;
                }

                $memberId = $columnMembers[$col];

                $alreadyExists = \App\Models\MemberDepositMonth::where('member_id', $memberId)
                    ->where('month', $monthNum)
                    ->where('year', $year)
                    ->exists();

                if ($alreadyExists) {
                    $skippedExisting++;
                    continue;
                }

                $toCreate[] = [
                    'member_id' => $memberId,
                    'amount' => (float) $value,
                    'month' => $monthNum,
                    'year' => $year,
                    'deposit_date' => $monthDate->copy()->endOfMonth(),
                    'label' => $monthDate->format('F Y'),
                ];
                $created++;
                $totalAmount += (float) $value;
            }
        }

        // Build a stable transaction id from the member code per row.
        $codeById = array_flip($membersByCode->toArray()); // id => code
        foreach ($toCreate as &$row) {
            $code = $codeById[$row['member_id']] ?? $row['member_id'];
            $row['transaction_id'] = 'IMP-' . sprintf('%04d%02d', $row['year'], $row['month']) . '-' . $code;
        }
        unset($row);

        $summary = [
            'dry_run' => $dryRun,
            'created' => $created,
            'skipped_existing' => $skippedExisting,
            'skipped_unknown_code' => $skippedUnknownCode,
            'skipped_empty' => $skippedEmpty,
            'total_amount' => $totalAmount,
            'unknown_codes' => array_values(array_unique($unknownCodes)),
            'warnings' => $rowWarnings,
            'matched_members' => count($columnMembers),
        ];

        if ($dryRun) {
            $summary['message'] = "Preview only — nothing was written.";
            return back()->with('import_summary', $summary);
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($toCreate, $paymentMethod, $request) {
            foreach ($toCreate as $row) {
                $entry = SavingsEntry::create([
                    'member_id' => $row['member_id'],
                    'amount' => $row['amount'],
                    'deposit_date' => $row['deposit_date'],
                    'payment_method_id' => $paymentMethod->id,
                    'payment_method' => $paymentMethod->code,
                    'transaction_id' => $row['transaction_id'],
                    'notes' => 'Imported deposit for ' . $row['label'],
                    'recorded_by' => $request->user()->id,
                ]);

                \App\Models\MemberDepositMonth::create([
                    'member_id' => $row['member_id'],
                    'month' => $row['month'],
                    'year' => $row['year'],
                    'savings_entry_id' => $entry->id,
                ]);
            }
        });

        $summary['message'] = "Imported {$created} deposit(s) totalling Tk " . number_format($totalAmount, 0) . '.';

        return back()->with('import_summary', $summary);
    }

    public function quickStore(Request $request)
    {
        $this->authorize('create', SavingsEntry::class);

        $validated = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'month' => ['required', 'date_format:Y-m'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'transaction_id' => ['required', 'string', 'max:100', 'unique:savings_entries'],
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $monthDate = \Carbon\Carbon::createFromFormat('Y-m', $validated['month']);

        // Block deposits for deactivated members.
        if (Member::whereKey($validated['member_id'])->value('status') !== 'active') {
            return response()->json([
                'message' => 'This member is deactivated — no new deposits can be recorded.',
                'errors' => ['member_id' => ['Member is deactivated.']],
            ], 422);
        }

        // Enforce one deposit per member per month.
        $alreadyDeposited = \App\Models\MemberDepositMonth::where('member_id', $validated['member_id'])
            ->where('month', $monthDate->month)
            ->where('year', $monthDate->year)
            ->exists();

        if ($alreadyDeposited) {
            return response()->json([
                'message' => 'This member already has a deposit for ' . $monthDate->format('M Y') . '. Only one deposit per month is allowed.',
                'errors' => [
                    'month' => ['A deposit for this month already exists for this member.'],
                ],
            ], 422);
        }

        $savingsEntry = \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $monthDate) {
            $savingsEntry = SavingsEntry::create([
                'member_id' => $validated['member_id'],
                'amount' => $validated['amount'],
                'deposit_date' => $monthDate->endOfMonth(),
                'payment_method_id' => $validated['payment_method_id'],
                'payment_method' => PaymentMethod::whereKey($validated['payment_method_id'])->value('code'),
                'transaction_id' => $validated['transaction_id'],
                'notes' => $validated['notes'] ?? null,
                'recorded_by' => auth()->id(),
            ]);

            // Record the month as paid
            \App\Models\MemberDepositMonth::create([
                'member_id' => $validated['member_id'],
                'month' => $monthDate->month,
                'year' => $monthDate->year,
                'savings_entry_id' => $savingsEntry->id,
            ]);

            return $savingsEntry;
        });

        $this->notifyDeposit($savingsEntry);

        return response()->json([
            'message' => 'Deposit recorded successfully',
            'deposit' => $savingsEntry,
        ], 201);
    }
}
