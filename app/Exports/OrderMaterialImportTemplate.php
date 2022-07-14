<?php

namespace App\Exports;

use App\Models\CustomWindow;
use App\Models\MaterialType;
use App\Models\MaterialUnit;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class OrderMaterialImportTemplate implements Responsable, WithHeadings, WithEvents, WithColumnFormatting
{
    use Exportable;

    public $subsectorId;

    private $fileName = '订单物料明细批量导入模板.xlsx';

    public function headings(): array
    {
        return [
            '*物料编码',
            '*物料名称',
            '*类别',
            '*单位',
            '*数量',
            '*客户窗口',
            '单价',
            '含税单价',
            '*税率',
            '*交货日期*',
        ];
    }

    public function registerEvents(): array
    {
        $types = MaterialType::all()->pluck('name')->toArray();
        $units = MaterialUnit::all()->pluck('name')->toArray();
        return [
            AfterSheet::class => function (AfterSheet $event) use ($units, $types) {
                $sheet = $event->sheet;

                if (count($units)) {
                    for ($i = 2; $i <= 1000; $i++) {
                        setExcelSelect($sheet, 'D' . $i, $units);
                    }
                }

                if (count($types)) {
                    for ($i = 2; $i <= 1000; $i++) {
                        setExcelSelect($sheet, 'C' . $i, $types);
                    }
                }

                $event->sheet->getDelegate()->getStyle('A1:J1')->getAlignment()->setVertical('center');
                $event->sheet->getDelegate()->getStyle('A1:J1')->getAlignment()->setHorizontal('center');
                $event->sheet->getDelegate()->getStyle('A1:J1')->applyFromArray([
                    'font' => [
                        'name' => 'Arial',
                        'bold' => true,
                        'italic' => false,
                        'strikethrough' => false,
                    ],
                ]);

                $event->sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(25);
                $event->sheet->getColumnDimension('B')->setAutoSize(false)->setWidth(25);
                $event->sheet->getColumnDimension('C')->setAutoSize(false)->setWidth(20);
                $event->sheet->getColumnDimension('D')->setAutoSize(false)->setWidth(15);
                $event->sheet->getColumnDimension('E')->setAutoSize(false)->setWidth(10);
                $event->sheet->getColumnDimension('F')->setAutoSize(false)->setWidth(10);
                $event->sheet->getColumnDimension('G')->setAutoSize(false)->setWidth(20);
                $event->sheet->getColumnDimension('H')->setAutoSize(false)->setWidth(15);
                $event->sheet->getColumnDimension('I')->setAutoSize(false)->setWidth(15);
                $event->sheet->getColumnDimension('J')->setAutoSize(false)->setWidth(15);
            },
        ];
    }

    public function columnFormats(): array
    {
        $formats = [];
        for ($chr = 'A'; $chr <= 'T'; $chr ++) {
            $formats[$chr] = NumberFormat::FORMAT_TEXT;
        }
        for ($chr = 'AA'; $chr <= 'AT'; $chr ++) {
            $formats[$chr] = NumberFormat::FORMAT_TEXT;
        }
        return $formats;
    }
}
