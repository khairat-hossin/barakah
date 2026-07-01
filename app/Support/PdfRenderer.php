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

        // Register the bundled Bengali font (Hind Siliguri) so Bangla text
        // renders instead of empty boxes. useOTL enables OpenType shaping so
        // Bengali conjuncts/matras form correctly; useSubstitutions + a
        // backupSubsFont list put this font first when the CSS font (e.g.
        // sans-serif) lacks Bengali glyphs, so no per-template change is needed.
        $defaultFontDirs = (new \Mpdf\Config\ConfigVariables())->getDefaults()['fontDir'];
        $defaultFontData = (new \Mpdf\Config\FontVariables())->getDefaults()['fontdata'];

        // 'title' and 'footer' are our own pseudo-config keys — pull them out
        // before handing the rest of the config to mPDF.
        $title = $config['title'] ?? null;
        $footer = $config['footer'] ?? null;
        unset($config['title'], $config['footer']);

        $mpdf = new \Mpdf\Mpdf(array_merge([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 14,
            'margin_bottom' => 14,
            'tempDir' => $tempDir,
            'fontDir' => array_merge($defaultFontDirs, [resource_path('fonts')]),
            'fontdata' => $defaultFontData + [
                'hindsiliguri' => [
                    'R' => 'HindSiliguri-Regular.ttf',
                    'B' => 'HindSiliguri-Bold.ttf',
                    'useOTL' => 0xFF,
                ],
            ],
            'useSubstitutions' => true,
            'backupSubsFont' => ['hindsiliguri', 'dejavusanscondensed', 'freeserif'],
        ], $config));

        if ($title) {
            $mpdf->SetTitle($title);
        }
        $mpdf->SetAuthor(Branding::name());

        // Footer on every page: a note on the left + "Page X of Y" on the right.
        // Pass 'footer' => true for the default note, or a string for custom text.
        if ($footer) {
            $note = is_string($footer)
                ? $footer
                : 'This is a system generated report, no signature required.';

            $mpdf->SetHTMLFooter(
                '<table width="100%" style="font-size: 8px; color: #6c757d; border-top: 0.5px solid #dee2e6;">'
                . '<tr>'
                . '<td style="text-align: left; border: none; padding-top: 3px;">' . e($note) . '</td>'
                . '<td style="text-align: right; border: none; padding-top: 3px;">Page {PAGENO} of {nbpg}</td>'
                . '</tr></table>'
            );
        }

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
        // Give the PDF a readable document title (browser tab name). Callers can
        // pass one via $config['title']; otherwise derive it from the filename.
        if (! isset($config['title'])) {
            $config['title'] = self::titleFromFilename($filename);
        }

        return response(
            self::raw($view, $data, $config),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => $disposition . '; filename="' . $filename . '"',
            ]
        );
    }

    /**
     * Turn a filename like "deposit-report-20260601-20260630.pdf" into a title
     * like "Deposit Report" — taking the leading words up to the first segment
     * that begins with a digit.
     */
    private static function titleFromFilename(string $filename): string
    {
        $base = pathinfo($filename, PATHINFO_FILENAME);
        $words = [];
        foreach (explode('-', $base) as $part) {
            if ($part === '' || preg_match('/^\d/', $part)) {
                break;
            }
            $words[] = $part;
        }

        $title = ucwords(trim(implode(' ', $words)));

        return $title !== '' ? $title : 'Document';
    }
}
