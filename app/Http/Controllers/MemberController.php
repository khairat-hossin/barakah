<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Helpers\ShareHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        ]);
    }

    public function show(Member $member): View
    {
        return view('members.show', [
            'member' => $member,
            'emiPerMonth' => ShareHelper::calculateEmiPerMonth($member->id),
        ]);
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

        // Auto-generate member code: BRYYnnn (BR = Barakah, YY = year, nnn = sequence)
        $currentYear = now()->format('y'); // 26 for 2026
        $yearCount = Member::whereYear('created_at', now()->year)->count();
        $nextSequence = str_pad($yearCount + 1, 3, '0', STR_PAD_LEFT);
        $validated['member_code'] = "BR{$currentYear}{$nextSequence}";

        Member::create($validated);

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
                        $member = Member::create([
                            'name' => $name,
                            'email' => !empty($email) ? $email : null,
                            'phone' => !empty($phone) ? $phone : null,
                            'status' => 'active',
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
