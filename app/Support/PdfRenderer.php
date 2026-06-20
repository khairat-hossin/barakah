<?php

namespace App\Support;

use Illuminate\Support\Facades\View;

class PdfRenderer
{
    /**
     * Render a Blade view to raw PDF bytes via mPDF.
     */
    public static function raw(string $view, array $data, array $config = []): string
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

        return $mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);
    }

    /**
     * Render a Blade view to a PDF HTTP response.
     *
     * @param  string  $disposition  'inline' shows the PDF in the browser;
     *                                'attachment' forces a download (use in production).
     */
    public static function download(string $view, array $data, string $filename, array $config = [], string $disposition = 'inline')
    {
        return response(
            self::raw($view, $data, $config),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => $disposition . '; filename="' . $filename . '"',
            ]
        );
    }
}
