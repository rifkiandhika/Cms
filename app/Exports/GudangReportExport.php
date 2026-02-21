<?php

namespace App\Exports;

use App\Models\HistoryGudang;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class GudangReportExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    WithStyles, 
    WithTitle, 
    ShouldAutoSize,
    WithEvents
{
    protected $request;
    protected $rowNumber = 0;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = HistoryGudang::with([
            'supplier',
            'detailGudang.gudang',
            'detailGudang.barangObat',
            'detailGudang.alkes',
            'detailGudang.reagensia'
        ]);

        // Apply filters
        if ($this->request->filled('tanggal_dari')) {
            $query->whereDate('waktu_proses', '>=', $this->request->tanggal_dari);
        }

        if ($this->request->filled('tanggal_sampai')) {
            $query->whereDate('waktu_proses', '<=', $this->request->tanggal_sampai);
        }

        if ($this->request->filled('gudang_id')) {
            $query->whereHas('detailGudang', function($q) {
                $q->where('gudang_id', $this->request->gudang_id);
            });
        }

        if ($this->request->filled('supplier_id')) {
            $query->where('supplier_id', $this->request->supplier_id);
        }

        if ($this->request->filled('status')) {
            $query->where('status', $this->request->status);
        }

        if ($this->request->filled('barang_type')) {
            $query->whereHas('detailGudang', function($q) {
                $q->where('barang_type', $this->request->barang_type);
            });
        }

        $sortBy = $this->request->input('sort_by', 'waktu_proses');
        $sortOrder = $this->request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Waktu',
            'Kode Gudang',
            'Nama Gudang',
            'Nama Barang',
            'Jenis Barang',
            'No Batch',
            'Supplier',
            'Status',
            'Jumlah',
            'No Referensi',
            'Tipe Referensi',
        ];
    }

    public function map($history): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $history->waktu_proses->format('d/m/Y'),
            $history->waktu_proses->format('H:i:s'),
            $history->detailGudang->gudang->kode_gudang ?? '-',
            $history->detailGudang->gudang->nama_gudang ?? '-',
            $history->detailGudang->nama_barang ?? '-',
            $history->detailGudang->jenis_barang ?? '-',
            $history->detailGudang->no_batch ?? '-',
            $history->supplier->nama_supplier ?? '-',
            ucfirst($history->status),
            $history->jumlah,
            $history->no_referensi ?? '-',
            $history->referensi_type ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Laporan Gudang';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Get highest row and column
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                
                // Apply borders to all cells
                $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                // Center align for specific columns
                $sheet->getStyle('A2:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('J2:J' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('K2:K' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Add title and info above table
                $sheet->insertNewRowBefore(1, 4);
                
                $sheet->setCellValue('A1', 'LAPORAN HISTORY GUDANG');
                $sheet->mergeCells('A1:' . $highestColumn . '1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                $sheet->setCellValue('A2', 'Rumah Sakit [Nama RS Anda]');
                $sheet->mergeCells('A2:' . $highestColumn . '2');
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $periode = 'Dicetak: ' . now()->format('d F Y H:i:s');
                if ($this->request->filled('tanggal_dari') || $this->request->filled('tanggal_sampai')) {
                    $dari = $this->request->tanggal_dari ? \Carbon\Carbon::parse($this->request->tanggal_dari)->format('d F Y') : '...';
                    $sampai = $this->request->tanggal_sampai ? \Carbon\Carbon::parse($this->request->tanggal_sampai)->format('d F Y') : '...';
                    $periode .= ' | Periode: ' . $dari . ' s/d ' . $sampai;
                }
                
                $sheet->setCellValue('A3', $periode);
                $sheet->mergeCells('A3:' . $highestColumn . '3');
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['size' => 10, 'italic' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Auto-fit columns
                foreach (range('A', $highestColumn) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}