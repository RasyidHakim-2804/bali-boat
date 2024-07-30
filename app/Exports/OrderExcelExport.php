<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class OrderExcelExport implements FromView, ShouldAutoSize, WithEvents, WithColumnFormatting, WithTitle
{
    protected $data;

    protected $start_date;

    protected $end_date;

    function __construct($data, $request) {
        $this->data             = $data;
        $this->start_date       = date("d-m-Y", strtotime($request->start_date));
        $this->end_date         = date("d-m-Y", strtotime($request->end_date));
    }

    public function view(): View
    {
        return view('excel.order', [
            'start_date'    => $this->start_date,
            'end_date'      => $this->end_date,
            'orders'        => $this->data,
        ]);
    }

    public function title(): string
    {
        return "Orders " . $this->start_date . ' - ' . $this->end_date;
    }

    public function registerEvents(): array
    {

        $styleTitle = [
            'font' => [
                'bold' => true,
                'size' => 16
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
        ];

        $styleHeading = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ];

        $styleAll = [
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
        ];

        $stylePrice = [
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
            ],
        ];

        $borderAll = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ]
        ];

        return [
            AfterSheet::class => function (AfterSheet $event) use ($styleHeading, $styleAll, $borderAll, $styleTitle, $stylePrice) {
                $event->sheet->mergeCells('A1:K1');
                $event->sheet->mergeCells('A2:K2');
                $event->sheet->getStyle('A1:K2')->applyFromArray($styleTitle);
                $event->sheet->getStyle('A4:K4')->applyFromArray($styleHeading);
                $event->sheet->getStyle('A4:K'.$event->sheet->getHighestRow())->applyFromArray($styleAll);
                $event->sheet->getStyle('F4:K'.$event->sheet->getHighestRow())->applyFromArray($stylePrice);
                $event->sheet->getStyle('A4:K'.$event->sheet->getHighestRow())->applyFromArray($borderAll);
            },
        ];
    }

    public function columnFormats(): array
    {
        return [
            'K' => NumberFormat::FORMAT_NUMBER,
            'K' => '#,##0',
            // 'G' => NumberFormat::FORMAT_NUMBER,
            // 'G' => '#,##0',
            // 'Q' => NumberFormat::FORMAT_NUMBER,
            // 'Q' => '#,##0',
            // 'R' => NumberFormat::FORMAT_NUMBER,
            // 'R' => '#,##0',
            // 'S' => NumberFormat::FORMAT_NUMBER,
            // 'S' => '#,##0',
        ];
    }
}