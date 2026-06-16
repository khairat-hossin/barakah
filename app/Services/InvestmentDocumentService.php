<?php

namespace App\Services;

use App\Models\Investment;
use App\Models\InvestmentDocument;
use App\Models\AuditLog;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class InvestmentDocumentService
{
    private const ALLOWED_MIMES = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/msword',
    ];

    public function uploadDocument(Investment $investment, UploadedFile $file, string $type, array $metadata = []): InvestmentDocument
    {
        // Validate MIME type
        if (!in_array($file->getMimeType(), self::ALLOWED_MIMES)) {
            throw new \Exception("Invalid file type: {$file->getMimeType()}");
        }

        // Store file in private disk
        $path = $file->store("investments/{$investment->id}", 'private');

        $document = InvestmentDocument::create([
            'investment_id' => $investment->id,
            'document_type' => $type,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'uploaded_by' => auth()->id(),
            'notes' => $metadata['notes'] ?? null,
            'is_public' => $metadata['is_public'] ?? false,
        ]);

        // Log to AuditLog
        AuditLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'investment_document_uploaded',
            'entity_type' => 'InvestmentDocument',
            'entity_id' => $document->id,
            'new_value' => [
                'file_name' => $document->file_name,
                'document_type' => $type,
                'file_size' => $document->file_size,
            ],
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->header('User-Agent'),
        ]);

        return $document;
    }

    public function verifyDocument(InvestmentDocument $document): void
    {
        $document->verify(auth()->user());

        // Log to AuditLog
        AuditLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'investment_document_verified',
            'entity_type' => 'InvestmentDocument',
            'entity_id' => $document->id,
            'new_value' => ['verified_at' => $document->verified_at],
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->header('User-Agent'),
        ]);
    }

    public function deleteDocument(InvestmentDocument $document): void
    {
        // Delete file from storage
        if (Storage::disk('private')->exists($document->file_path)) {
            Storage::disk('private')->delete($document->file_path);
        }

        $fileName = $document->file_name;
        $document->delete();

        // Log to AuditLog
        AuditLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'deleted',
            'entity_type' => 'InvestmentDocument',
            'entity_id' => $document->id,
            'old_value' => ['file_name' => $fileName],
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->header('User-Agent'),
        ]);
    }

    public function getDownloadPath(InvestmentDocument $document): string
    {
        return Storage::disk('private')->path($document->file_path);
    }
}
