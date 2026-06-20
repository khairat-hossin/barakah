<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SpreadsheetExporter
{
    /**
     * Build an .xlsx download from headings + rows.
     *
     * @param  array  $headings  e.g. ['Date', 'Member', 'Amount']
     * @param  iterable  $rows  each row an indexed array aligned to $headings
     */
    public static function download(array $headings, iterable $rows, string $filename, string $sheetTitle = 'Report'): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr($sheetTitle, 0, 31));

        // Header row
        $sheet->fromArray($headings, null, 'A1');
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headings));
        $sheet->getStyle("A1:{$lastCol}1")->getFont()->setBold(true);
        $sheet->getStyle("A1:{$lastCol}1")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E9ECEF');

        // Data rows
        $rowIndex = 2;
        foreach ($rows as $row) {
            $sheet->fromArray(array_values($row), null, "A{$rowIndex}");
            $rowIndex++;
        }

        // Auto-size columns
        for ($i = 1; $i <= count($headings); $i++) {
            $sheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
