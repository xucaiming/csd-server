<?php

namespace App\Imports;

//use App\Models\Sku;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
//use Maatwebsite\Excel\Concerns\WithMapping;
//use Maatwebsite\Excel\Concerns\ToModel;
//use Maatwebsite\Excel\Concerns\WithValidation;

class DataImport implements
    ToCollection
//    WithValidation
{

    public $data;
    protected $delTitle;

    public function __construct($delTitle = 1)
    {
        $this->delTitle = $delTitle;
    }

    public function collection(Collection $rows)
    {
        $this->delTitle($rows);
        $this->data = $rows;
    }

    public function delTitle (&$rows) {
        $rows = $rows->slice($this->delTitle)->values();
    }

//    public function model(array $row)
//    {
//        return new Sku([
//            'goods_name' => $row[0],
//            'goods_name_en' => $row[1],
//        ]);
//    }

//    public function rules(): array
//    {
//        return [];
//    }

}
