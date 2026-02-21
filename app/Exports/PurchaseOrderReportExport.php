<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PurchaseOrderReportExport implements FromView, WithTitle, WithStyles, ShouldAutoSize
{
    protected $purchaseOrders;
    protected $statistics;
    protected $filters;

    public function __construct($purchaseOrders, $statistics, $filters)
    {
        $this->purchaseOrders = $purchaseOrders;
        $this->statistics = $statistics;
        $this->filters = $filters;
    }

    public function view(): View
    {
        return view('reports.purchase-orders.excel', [
            'purchaseOrders' => $this->purchaseOrders,
            'statistics' => $this->statistics,
            'filters' => $this->filters
        ]);
    }

    public function title(): string
    {
        return 'Laporan PO';
    }

    public function styles(Worksheet $sheet)
    {
        // Calculate dynamic row positions
        $currentRow = 1;
        
        // Title styling (Row 1)
        $sheet->mergeCells("A{$currentRow}:L{$currentRow}"); // Changed to L (12 columns)
        $sheet->getStyle("A{$currentRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0d6efd']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        $sheet->getRowDimension($currentRow)->setRowHeight(30);
        $currentRow++; // Now row 2

        // Date styling (Row 2)
        $sheet->mergeCells("A{$currentRow}:L{$currentRow}");
        $sheet->getStyle("A{$currentRow}")->applyFromArray([
            'font' => ['size' => 10, 'italic' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
        $currentRow++; // Now row 3
        $currentRow++; // Empty row, now row 4

        // Statistics header (Row 4)
        $statsHeaderRow = $currentRow;
        $sheet->mergeCells("A{$statsHeaderRow}:L{$statsHeaderRow}");
        $sheet->getStyle("A{$statsHeaderRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'e9ecef']
            ]
        ]);
        $currentRow++; // Now row 5

        // Statistics rows (2 rows)
        $currentRow += 2; // Skip stats rows, now row 7
        $currentRow++; // Empty row, now row 8

        // Check if filters are applied
        $hasFilters = !empty(array_filter($this->filters));
        if ($hasFilters) {
            $filterHeaderRow = $currentRow;
            $sheet->mergeCells("A{$filterHeaderRow}:L{$filterHeaderRow}");
            $sheet->getStyle("A{$filterHeaderRow}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'fff3cd']
                ]
            ]);
            $currentRow++; // Move to filter content

            // Count filter rows
            $filterCount = 0;
            if (!empty($this->filters['tanggal_dari'])) $filterCount++;
            if (!empty($this->filters['tanggal_sampai'])) $filterCount++;
            if (!empty($this->filters['status'])) $filterCount++;
            if (!empty($this->filters['tipe_po'])) $filterCount++;
            if (!empty($this->filters['no_po'])) $filterCount++;
            if (!empty($this->filters['id_supplier'])) $filterCount++;
            
            $currentRow += $filterCount; // Skip filter rows
            $currentRow++; // Empty row after filters
        }

        // Table header row
        $headerRow = $currentRow;
        $sheet->getStyle("A{$headerRow}:L{$headerRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '495057']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
        $sheet->getRowDimension($headerRow)->setRowHeight(25);

        // Data rows
        $dataStartRow = $headerRow + 1;
        $lastDataRow = $dataStartRow + count($this->purchaseOrders) - 1;
        
        // Apply borders to all data cells
        if (count($this->purchaseOrders) > 0) {
            $sheet->getStyle("A{$dataStartRow}:L{$lastDataRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'dee2e6']
                    ]
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true
                ]
            ]);

            // Center align for specific columns
            $sheet->getStyle("A{$dataStartRow}:A{$lastDataRow}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER); // No
            $sheet->getStyle("D{$dataStartRow}:D{$lastDataRow}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER); // Tipe
            $sheet->getStyle("I{$dataStartRow}:I{$lastDataRow}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER); // Status

            // Right align for currency columns
            $sheet->getStyle("J{$dataStartRow}:K{$lastDataRow}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        // Footer row
        $footerRow = $lastDataRow + 1;
        $sheet->getStyle("A{$footerRow}:L{$footerRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 11],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'f8f9fa']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Info row
        $infoRow = $footerRow + 2;
        $sheet->mergeCells("A{$infoRow}:L{$infoRow}");
        $sheet->getStyle("A{$infoRow}")->applyFromArray([
            'font' => ['size' => 9, 'italic' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Number format for currency columns (Grand Total & Total Diterima)
        $sheet->getStyle("J{$dataStartRow}:K{$lastDataRow}")->getNumberFormat()
            ->setFormatCode('#,##0');
        $sheet->getStyle("J{$footerRow}:K{$footerRow}")->getNumberFormat()
            ->setFormatCode('#,##0');

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(5);  // No
        $sheet->getColumnDimension('B')->setWidth(18); // No PO
        $sheet->getColumnDimension('C')->setWidth(12); // Tanggal
        $sheet->getColumnDimension('D')->setWidth(10); // Tipe
        $sheet->getColumnDimension('E')->setWidth(15); // Unit Pemohon
        $sheet->getColumnDimension('F')->setWidth(20); // Pemohon
        $sheet->getColumnDimension('G')->setWidth(15); // Unit Tujuan
        $sheet->getColumnDimension('H')->setWidth(25); // Supplier/Tujuan
        $sheet->getColumnDimension('I')->setWidth(30); // Nama Barang (NEW)
        $sheet->getColumnDimension('J')->setWidth(25); // Status
        $sheet->getColumnDimension('K')->setWidth(15); // Grand Total
        $sheet->getColumnDimension('L')->setWidth(15); // Total Diterima

        return [];
    }
}