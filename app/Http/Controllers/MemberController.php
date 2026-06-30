<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\User;
use App\Helpers\ShareHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MemberController extends Controller
{
    private const STATUSES = [
        'active',
        'inactive',
        'suspended',
    ];

    public function index(): View
    {
        $members = Member::query()
            ->withSum('savingsEntries', 'amount')
            ->latest()
            ->get();

        $statusCounts = array_merge(
            array_fill_keys(self::STATUSES, 0),
            $members->countBy('status')->all(),
        );

        return view('members.index', [
            'members' => $members,
            'statusCounts' => $statusCounts,
            'totalSaved' => $members->sum(fn (Member $member): float => (float) ($member->savings_entries_sum_amount ?? 0)),
        ]);
    }

    public function create(): View
    {
        return view('members.create', [
            'statuses' => self::STATUSES,
            'nextMemberCode' => $this->generateMemberCode(),
        ]);
    }

    public function show(Member $member): View
    {
        return view('members.show', [
            'member' => $member,
            'emiPerMonth' => ShareHelper::calculateEmiPerMonth($member->id),
        ]);
    }

    /**
     * Create a login user account from a member's details (or link an existing
     * user with the same email), assign the Member role, and link them.
     */
    public function createUser(Member $member): RedirectResponse
    {
        abort_unless(auth()->user()->can('manage users'), 403);

        if ($member->user_id) {
            return back()->with('error', 'This member already has a user account.');
        }

        if (! $member->email) {
            return back()->with('error', 'This member has no email address. Add one before creating a user.');
        }

        // Link an existing user with the same email instead of duplicating.
        $existing = User::where('email', $member->email)->first();
        if ($existing) {
            $member->update(['user_id' => $existing->id]);
            return back()->with('success', "Linked {$member->name} to the existing user account ({$member->email}).");
        }

        $password = Str::password(10);
        $user = User::create([
            'name' => $member->name,
            'email' => $member->email,
            'password' => $password,
            'email_verified_at' => now(),
        ]);
        $user->assignRole('Member');

        $member->update(['user_id' => $user->id]);

        return back()->with('success', "User account created for {$member->name}. Login: {$member->email} — Temporary password: {$password} (share it securely; ask them to change it).");
    }

    public function portfolio(Member $member)
    {
        $this->authorize('view', $member);

        $member->load([
            'nominees',
            'savingsEntries' => fn ($q) => $q->orderByDesc('deposit_date'),
            'savingsEntries.paymentMethod',
            'shareTransfersFrom.toMember',
            'shareTransfersTo.fromMember',
        ]);

        $org = \App\Models\OrganizationProfile::first();
        $shareFaceValue = $org?->share_face_value ?? 0;
        $currentShares = \App\Models\MemberShareOwnership::where('member_id', $member->id)
            ->whereNull('ownership_end_date')->count();

        // Member-linked expenses
        $expenses = \App\Models\Expense::with('category')
            ->where('member_id', $member->id)
            ->orderByDesc('expense_date')
            ->get();

        // Investments where this member is the investor
        $investments = \App\Models\Investment::with('investmentType')
            ->where('investor_id', $member->id)
            ->orderByDesc('start_date')
            ->get();

        return \App\Support\PdfRenderer::download(
            'members.portfolio',
            [
                'member' => $member,
                'org' => $org,
                'currentShares' => $currentShares,
                'shareFaceValue' => $shareFaceValue,
                'shareValue' => $currentShares * $shareFaceValue,
                'totalDeposits' => $member->savingsEntries->sum('amount'),
                'expenses' => $expenses,
                'totalExpenses' => $expenses->sum('amount'),
                'investments' => $investments,
                'emiPerMonth' => ShareHelper::calculateEmiPerMonth($member->id),
            ],
            'member-portfolio-' . ($member->member_code ?: $member->id) . '.pdf'
        );
    }

    /**
     * Build a member code prefix from the organization's short name (or name),
     * so each association produces its own prefix. Falls back to "MBR".
     */
    /**
     * Generate the next sequential member code: M0001, M0002, ...
     * Based on the highest existing M#### so deleted gaps never cause collisions.
     */
    private function generateMemberCode(): string
    {
        $max = (int) Member::withTrashed()
            ->where('member_code', 'REGEXP', '^M[0-9]+$')
            ->selectRaw('MAX(CAST(SUBSTRING(member_code, 2) AS UNSIGNED)) as mx')
            ->value('mx');

        return 'M' . str_pad($max + 1, 4, '0', STR_PAD_LEFT);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'name_bn' => ['required', 'string', 'max:255'],
            'father_name' => ['required', 'string', 'max:255'],
            'mother_name' => ['required', 'string', 'max:255'],
            'spouse_name' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date', 'before_or_equal:today'],
            'gender' => ['required', 'in:male,female,other'],
            'marital_status' => ['nullable', 'in:single,married,divorced,widowed'],
            'nationality' => ['required', 'string', 'max:100'],
            'nid_number' => ['nullable', 'string', 'max:50', 'unique:members,nid_number'],
            'birth_registration' => ['nullable', 'string', 'max:50', 'unique:members,birth_registration'],
            'passport_number' => ['nullable', 'string', 'max:50', 'unique:members,passport_number'],
            'tax_id' => ['nullable', 'string', 'max:50', 'unique:members,tax_id'],
            'email' => ['required', 'email', 'max:255', 'unique:members,email'],
            'phone' => ['required', 'string', 'max:20', 'unique:members,phone'],
            'secondary_mobile' => ['nullable', 'string', 'max:20', 'unique:members,secondary_mobile'],
            'whatsapp_number' => ['nullable', 'string', 'max:20', 'unique:members,whatsapp_number'],
            'present_address_village' => ['required_if:same_as_permanent,false', 'string', 'max:255'],
            'present_address_po' => ['required_if:same_as_permanent,false', 'string', 'max:255'],
            'present_address_union' => ['required_if:same_as_permanent,false', 'string', 'max:255'],
            'present_address_upazila' => ['required_if:same_as_permanent,false', 'string', 'max:255'],
            'present_address_district' => ['required_if:same_as_permanent,false', 'string', 'max:255'],
            'present_address_postal' => ['required_if:same_as_permanent,false', 'string', 'max:10'],
            'same_as_permanent' => ['boolean'],
            'permanent_address_village' => ['required', 'string', 'max:255'],
            'permanent_address_po' => ['required', 'string', 'max:255'],
            'permanent_address_union' => ['required', 'string', 'max:255'],
            'permanent_address_upazila' => ['required', 'string', 'max:255'],
            'permanent_address_district' => ['required', 'string', 'max:255'],
            'permanent_address_postal' => ['required', 'string', 'max:10'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'business_name' => ['nullable', 'string', 'max:255'],
            'trade_license_number' => ['nullable', 'string', 'max:100'],
            'office_designation' => ['nullable', 'string', 'max:255'],
            'employer_name' => ['nullable', 'string', 'max:255'],
            'office_address' => ['nullable', 'string', 'max:1000'],
        ]);

        // Handle permanent address copy
        if ($validated['same_as_permanent'] ?? false) {
            $validated['permanent_address_village'] = $validated['present_address_village'];
            $validated['permanent_address_po'] = $validated['present_address_po'];
            $validated['permanent_address_union'] = $validated['present_address_union'];
            $validated['permanent_address_upazila'] = $validated['present_address_upazila'];
            $validated['permanent_address_district'] = $validated['present_address_district'];
            $validated['permanent_address_postal'] = $validated['present_address_postal'];
        }

        // Auto-generate member code: {PREFIX}{YY}{nnn} — prefix derived from the
        // organization's short name so each association gets its own code prefix.
        $validated['member_code'] = $this->generateMemberCode();

        $member = Member::create($validated);

        \App\Support\Notify::admins(
            'New member added',
            $member->name . ' (' . $member->member_code . ') has been registered.',
            'user-plus',
            route('members.show', $member),
        );

        return redirect()->route('members.index')->with('success', 'Member added successfully.');
    }

    public function edit(Member $member): View
    {
        return view('members.edit', [
            'member' => $member,
            'statuses' => self::STATUSES,
        ]);
    }

    public function update(Request $request, Member $member): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'name_bn' => ['required', 'string', 'max:255'],
            'father_name' => ['required', 'string', 'max:255'],
            'mother_name' => ['required', 'string', 'max:255'],
            'spouse_name' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date', 'before_or_equal:today'],
            'gender' => ['required', 'in:male,female,other'],
            'marital_status' => ['nullable', 'in:single,married,divorced,widowed'],
            'nationality' => ['required', 'string', 'max:100'],
            'nid_number' => ['nullable', 'string', 'max:50', 'unique:members,nid_number,'.$member->id],
            'birth_registration' => ['nullable', 'string', 'max:50', 'unique:members,birth_registration,'.$member->id],
            'passport_number' => ['nullable', 'string', 'max:50', 'unique:members,passport_number,'.$member->id],
            'tax_id' => ['nullable', 'string', 'max:50', 'unique:members,tax_id,'.$member->id],
            'email' => ['required', 'email', 'max:255', 'unique:members,email,'.$member->id],
            'phone' => ['required', 'string', 'max:20', 'unique:members,phone,'.$member->id],
            'secondary_mobile' => ['nullable', 'string', 'max:20', 'unique:members,secondary_mobile,'.$member->id],
            'whatsapp_number' => ['nullable', 'string', 'max:20', 'unique:members,whatsapp_number,'.$member->id],
            'present_address_village' => ['required_if:same_as_permanent,false', 'string', 'max:255'],
            'present_address_po' => ['required_if:same_as_permanent,false', 'string', 'max:255'],
            'present_address_union' => ['required_if:same_as_permanent,false', 'string', 'max:255'],
            'present_address_upazila' => ['required_if:same_as_permanent,false', 'string', 'max:255'],
            'present_address_district' => ['required_if:same_as_permanent,false', 'string', 'max:255'],
            'present_address_postal' => ['required_if:same_as_permanent,false', 'string', 'max:10'],
            'same_as_permanent' => ['boolean'],
            'permanent_address_village' => ['required', 'string', 'max:255'],
            'permanent_address_po' => ['required', 'string', 'max:255'],
            'permanent_address_union' => ['required', 'string', 'max:255'],
            'permanent_address_upazila' => ['required', 'string', 'max:255'],
            'permanent_address_district' => ['required', 'string', 'max:255'],
            'permanent_address_postal' => ['required', 'string', 'max:10'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'business_name' => ['nullable', 'string', 'max:255'],
            'trade_license_number' => ['nullable', 'string', 'max:100'],
            'office_designation' => ['nullable', 'string', 'max:255'],
            'employer_name' => ['nullable', 'string', 'max:255'],
            'office_address' => ['nullable', 'string', 'max:1000'],
        ]);

        $member->update($validated);

        return redirect()->route('members.index')->with('success', 'Member updated successfully.');
    }

    public function destroy(Member $member): RedirectResponse
    {
        $member->delete();

        return redirect()->route('members.index')->with('success', 'Member deleted successfully.');
    }

    public function datatable(Request $request): JsonResponse
    {
        $draw = $request->get('draw', 1);
        $start = $request->get('start', 0);
        $length = $request->get('length', 10);
        $search = $request->get('search')['value'] ?? '';
        $status = $request->get('status', '');

        $query = Member::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('member_code', 'like', "%{$search}%");
        }

        if ($status) {
            $query->where('status', $status);
        }

        $filtered = $query->count();
        $total = Member::count();

        $members = $query->latest()
            ->offset($start)
            ->limit($length)
            ->get();

        $data = $members->map(function (Member $member) {
            return [
                'id' => $member->id,
                'name' => $member->name,
                'code' => $member->member_code ?? 'N/A',
                'email' => $member->email ?? 'N/A',
                'phone' => $member->phone ?? 'N/A',
                'status' => $member->status,
                'joinDate' => $member->join_date?->format('M d, Y') ?? '-',
                'has_user' => $member->user_id !== null,
                'has_email' => ! empty($member->email),
            ];
        });

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data,
        ]);
    }

    public function importForm(): View
    {
        return view('members.import', [
            'statuses' => self::STATUSES,
        ]);
    }

    public function import(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
        ]);

        try {
            $file = $validated['file'];
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $spreadsheet = $reader->load($file->path());
            $worksheet = $spreadsheet->getActiveSheet();

            $imported = 0;
            $skipped = 0;
            $errors = [];

            $rowNum = 2;
            while (true) {
                $name = trim($worksheet->getCell("A{$rowNum}")->getValue() ?? '');
                $email = trim($worksheet->getCell("B{$rowNum}")->getValue() ?? '');
                $phone = trim($worksheet->getCell("C{$rowNum}")->getValue() ?? '');
                $numberOfShares = (int)($worksheet->getCell("D{$rowNum}")->getValue() ?? 0);

                if (empty($name)) {
                    $rowNum++;
                    if ($rowNum > 1000) break;
                    continue;
                }

                try {
                    // Check if member already exists
                    $member = Member::where('name', $name)->first();
                    if ($member) {
                        $skipped++;
                    } else {
                        // Convert username to email if needed
                        $finalEmail = null;
                        if (!empty($email)) {
                            $finalEmail = strpos($email, '@') !== false ? $email : ($email . '@' . config('app.member_email_domain'));
                        }

                        $member = Member::create([
                            'name' => $name,
                            'email' => $finalEmail,
                            'phone' => !empty($phone) ? $phone : null,
                            'status' => 'active',
                            'member_code' => $this->generateMemberCode(),
                        ]);
                        $imported++;
                    }

                    // Assign shares
                    if ($numberOfShares > 0) {
                        $this->assignShares($member, $numberOfShares);
                    }
                } catch (\Exception $e) {
                    $errors[] = "Row {$rowNum} ('{$name}'): " . $e->getMessage();
                }

                $rowNum++;
            }

            $message = "Import completed! Created: {$imported}, Skipped: {$skipped}";
            if (!empty($errors)) {
                $message .= ". Errors: " . count($errors);
            }

            return redirect()
                ->route('members.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error reading file: ' . $e->getMessage());
        }
    }

    private function assignShares(Member $member, int $shareCount): void
    {
        $availableShares = \App\Models\Share::doesntHave('currentOwner')->limit($shareCount)->get();

        foreach ($availableShares as $share) {
            \App\Models\MemberShareOwnership::create([
                'member_id' => $member->id,
                'share_id' => $share->id,
                'ownership_start_date' => now(),
            ]);
        }
    }

    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = ['Name', 'Email', 'Phone', 'Number of Shares'];
        $sheet->fromArray($headers, null, 'A1');

        // Format headers
        $headerStyle = $sheet->getStyle('A1:D1');
        $headerStyle->getFont()->setBold(true);
        $headerStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $headerStyle->getFill()->getStartColor()->setARGB('FFD3D3D3');

        // Add sample data
        $sample = [
            ['Ahmed Hassan', 'ahmed@example.com', '01700123456', 3],
            ['Fatima Khan', 'fatima@example.com', '01800234567', 2],
            ['Mohammad Ali', '', '01900345678', 1],
        ];
        $sheet->fromArray($sample, null, 'A2');

        // Auto-size columns
        foreach (['A', 'B', 'C', 'D'] as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Add notes
        $sheet->setCellValue('A8', 'Instructions:');
        $sheet->setCellValue('A9', '1. Name is required');
        $sheet->setCellValue('A10', '2. Email is optional');
        $sheet->setCellValue('A11', '3. Phone is optional');
        $sheet->setCellValue('A12', '4. Number of Shares must be a number (e.g., 1, 2, 3)');

        // Make instructions italic
        for ($i = 8; $i <= 12; $i++) {
            $sheet->getStyle("A{$i}")->getFont()->setItalic(true);
            $sheet->getStyle("A{$i}")->getFont()->setSize(9);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Members_Import_Template_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $writer->save('php://output');
        exit;
    }
}
