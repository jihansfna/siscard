<?php

namespace App\Exports;

use App\Models\Karyawan;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class EmployeeExport
{
    public function export()
    {
        $employees = Karyawan::orderBy('created_at', 'asc')->get();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Karyawan');

        // Headers
        $headers = ['No', 'Badge', 'Nama', 'Departemen', 'Line', 'Jabatan', 'Tanggal Masuk', 'Tanggal Keluar', 'Tempat Lahir', 'Tanggal Lahir', 'Alamat'];
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'];

        // Header styling
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1b007c']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ];

        foreach ($headers as $index => $header) {
            $cell = $columns[$index] . '1';
            $sheet->setCellValue($cell, $header);
        }
        $sheet->getStyle('A1:K1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Data
        $row = 2;
        foreach ($employees as $index => $emp) {
            $sheet->setCellValue("A{$row}", $index + 1);
            $sheet->setCellValue("B{$row}", $emp->badge);
            $sheet->setCellValue("C{$row}", $emp->nama);
            $sheet->setCellValue("D{$row}", $emp->departemen ?? '-');
            $sheet->setCellValue("E{$row}", $emp->line ?? '-');
            $sheet->setCellValue("F{$row}", $emp->jabatan ?? '-');
            $sheet->setCellValue("G{$row}", $emp->tanggal_masuk?->format('d/m/Y') ?? '-');
            $sheet->setCellValue("H{$row}", $emp->tanggal_keluar?->format('d/m/Y') ?? '-');
            $sheet->setCellValue("I{$row}", $emp->tempat_lahir ?? '-');
            $sheet->setCellValue("J{$row}", $emp->tanggal_lahir?->format('d/m/Y') ?? '-');
            $sheet->setCellValue("K{$row}", $emp->alamat ?? '-');

            // Alternate row color
            if ($index % 2 === 0) {
                $sheet->getStyle("A{$row}:K{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F3F4F6');
            }

            $row++;
        }

        // Data styling
        $dataStyle = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ];
        $sheet->getStyle("A2:K{$row}")->applyFromArray($dataStyle);
        $sheet->getStyle("A2:A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Auto width
        foreach ($columns as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'karyawan_' . date('Y-m-d_His') . '.xlsx';
        $tempFile = storage_path('app/' . $filename);
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }

    public static function template()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import');

        $headers = ['Badge *', 'Nama *', 'Departemen', 'Line', 'Jabatan', 'Tanggal Masuk (dd/mm/yyyy)', 'Tanggal Keluar (dd/mm/yyyy)', 'Tempat Lahir', 'Tanggal Lahir (dd/mm/yyyy)', 'Alamat'];
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1b007c']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ];

        foreach ($headers as $index => $header) {
            $sheet->setCellValue($columns[$index] . '1', $header);
        }
        $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Example row
        $sheet->setCellValue('A2', '12345');
        $sheet->setCellValue('B2', 'John Doe');
        $sheet->setCellValue('C2', 'IT');
        $sheet->setCellValue('D2', 'Line 1');
        $sheet->setCellValue('E2', 'Staff');
        $sheet->setCellValue('F2', '01/01/2025');
        $sheet->setCellValue('G2', '');
        $sheet->setCellValue('H2', 'Batam');
        $sheet->setCellValue('I2', '15/06/1990');
        $sheet->setCellValue('J2', 'Jl. Contoh No. 123');

        $sheet->getStyle('A2:J2')->getFont()->setItalic(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF888888'));

        foreach ($columns as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'template_import_karyawan.xlsx';
        $tempFile = storage_path('app/' . $filename);
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
