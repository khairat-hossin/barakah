<?php

namespace App\Support;

use Illuminate\Support\Facades\View;

class PdfRenderer
{
    /**
     * Render a Blade view to a downloadable PDF via mPDF.
     */
    public static function download(string $view, array $data, string $filename, array $config = [])
    {
        $tempDir = storage_path('app/mpdf');
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0775, true);
        }

        $mpdf = new \Mpdf\Mpdf(array_merge([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 14,
            'margin_bottom' => 14,
            'tempDir' => $tempDir,
        ], $config));

        $mpdf->WriteHTML(View::make($view, $data)->render());

        return response(
            $mpdf->Output($filename, \Mpdf\Output\Destination::STRING_RETURN),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }
}
