<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvestmentDocumentRequest;
use App\Models\Investment;
use App\Models\InvestmentDocument;
use App\Services\InvestmentDocumentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvestmentDocumentController extends Controller
{
    public function __construct(
        private InvestmentDocumentService $documentService,
    ) {
        $this->middleware('auth');
    }

    public function store(StoreInvestmentDocumentRequest $request, Investment $investment): RedirectResponse
    {
        $file = $request->file('file');
        $type = $request->input('document_type');

        $this->documentService->uploadDocument($investment, $file, $type, [
            'notes' => $request->input('notes'),
            'is_public' => $request->boolean('is_public'),
        ]);

        return redirect()->route('investments.show', $investment)
            ->with('success', 'Document uploaded successfully.');
    }

    public function verify(Investment $investment, InvestmentDocument $document): RedirectResponse
    {
        $this->authorize('verify', $document);

        $this->documentService->verifyDocument($document);

        return redirect()->route('investments.show', $investment)
            ->with('success', 'Document verified successfully.');
    }

    public function download(Investment $investment, InvestmentDocument $document): StreamedResponse
    {
        $this->authorize('download', $document);

        return Storage::disk('private')->download(
            $document->file_path,
            $document->file_name
        );
    }

    public function destroy(Investment $investment, InvestmentDocument $document): RedirectResponse
    {
        $this->authorize('delete', $document);

        $this->documentService->deleteDocument($document);

        return redirect()->route('investments.show', $investment)
            ->with('success', 'Document deleted successfully.');
    }
}
