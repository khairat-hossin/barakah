<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Document;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentController extends Controller
{
    private const ALLOWED_MIMES = ['image/jpeg', 'image/png', 'application/pdf'];
    private const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5 MB
    private const DOCUMENT_TYPES = [
        'nid_copy' => 'NID Copy',
        'birth_registration_copy' => 'Birth Registration Copy',
        'trade_license' => 'Trade License',
        'passport_copy' => 'Passport Copy',
        'membership_agreement' => 'Membership Agreement',
        'nominee_form' => 'Nominee Form',
        'bank_account_proof' => 'Bank Account Proof',
        'other_attachment' => 'Other Attachment',
    ];

    public function index(Member $member): View
    {
        $documents = $member->documents()
            ->orderByDesc('upload_date')
            ->get();

        return view('documents.index', [
            'member' => $member,
            'documents' => $documents,
            'documentTypes' => self::DOCUMENT_TYPES,
        ]);
    }

    public function create(Member $member): View
    {
        return view('documents.create', [
            'member' => $member,
            'documentTypes' => self::DOCUMENT_TYPES,
        ]);
    }

    public function store(Request $request, Member $member): RedirectResponse
    {
        $validated = $request->validate([
            'document_type' => ['required', 'in:' . implode(',', array_keys(self::DOCUMENT_TYPES))],
            'file' => ['required', 'file', 'max:5120', 'mimes:pdf,jpeg,png,jpg'],
            'remarks' => ['nullable', 'string', 'max:500'],
        ]);

        $file = $validated['file'];

        // Verify MIME type
        if (!in_array($file->getMimeType(), self::ALLOWED_MIMES)) {
            return back()->with('error', 'Invalid file type. Only PDF, JPG, and PNG are allowed.');
        }

        // Store file
        $path = $file->store("documents/{$member->id}", 'private');
        $fileName = $file->getClientOriginalName();

        $document = $member->documents()->create([
            'document_type' => $validated['document_type'],
            'file_path' => $path,
            'file_name' => $fileName,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'upload_date' => now(),
            'uploaded_by' => auth()->id(),
            'remarks' => $validated['remarks'],
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'uploaded',
            'entity_type' => 'Document',
            'entity_id' => $document->id,
            'new_value' => ['document_type' => $validated['document_type'], 'file_name' => $fileName],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now(),
        ]);

        return redirect()->route('documents.index', $member)
            ->with('success', 'Document uploaded successfully');
    }

    public function download(Document $document): BinaryFileResponse
    {
        $this->authorize('view', $document);

        return Storage::disk('private')->download($document->file_path, $document->file_name);
    }

    public function destroy(Document $document): RedirectResponse
    {
        $this->authorize('delete', $document);

        Storage::disk('private')->delete($document->file_path);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'deleted',
            'entity_type' => 'Document',
            'entity_id' => $document->id,
            'old_value' => ['file_name' => $document->file_name],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
        ]);

        $member = $document->member;
        $document->delete();

        return back()->with('success', 'Document deleted');
    }

    public function verify(Request $request, Document $document): RedirectResponse
    {
        $this->authorize('verify', $document);

        $document->update([
            'verified' => true,
            'verified_by' => auth()->id(),
            'verification_date' => now()->toDateString(),
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'verified',
            'entity_type' => 'Document',
            'entity_id' => $document->id,
            'new_value' => ['verified' => true],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now(),
        ]);

        return back()->with('success', 'Document verified');
    }
}
