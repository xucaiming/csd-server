<?php

namespace App\Exports;

use App\Models\MaterialType;
use App\Models\MaterialUnit;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class MaterialImportTemplate implements Responsable, WithHeadings, WithEvents
{
    use Exportable;

    private $fileName = '物料批量导入模板.xlsx';

    public function headings(): array
    {
        return [
            '物料编码',
            '物料名称',
            '计量单位',
            '物料类型',
            '原厂料号',
            '物料图片',
            '备注',
        ];
    }

    public function registerEvents(): array
    {
        $units = MaterialUnit::all()->pluck('name')->toArray();
        $types = MaterialType::all()->pluck('name')->toArray();
        return [
            AfterSheet::class => function (AfterSheet $event) use ($units, $types) {
                $sheet = $event->sheet;

                if (count($units)) {
                    for ($i = 2; $i <= 1000; $i++) {
                        setExcelSelect($sheet, 'C' . $i, $units);
                    }
                }

                if (count($types)) {
                    for ($i = 2; $i <= 1000; $i++) {
                        setExcelSelect($sheet, 'D' . $i, $types);
                    }
                }

                $event->sheet->getDelegate()->getStyle('A1:G1')->getAlignment()->setVertical('center');
                $event->sheet->getDelegate()->getStyle('A1:G1')->getAlignment()->setHorizontal('center');
                $event->sheet->getDelegate()->getStyle('A1:G1')->applyFromArray([
                    'font' => [
                        'name' => 'Arial',
                        'bold' => true,
                        'italic' => false,
                        'strikethrough' => false,
                    ],
                ]);

                $event->sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(25);
                $event->sheet->getColumnDimension('B')->setAutoSize(false)->setWidth(25);
                $event->sheet->getColumnDimension('C')->setAutoSize(false)->setWidth(15);
                $event->sheet->getColumnDimension('D')->setAutoSize(false)->setWidth(20);
                $event->sheet->getColumnDimension('E')->setAutoSize(false)->setWidth(25);
                $event->sheet->getColumnDimension('F')->setAutoSize(false)->setWidth(40);
                $event->sheet->getColumnDimension('G')->setAutoSize(false)->setWidth(30);
            },
        ];
    }
}
