<?php

namespace App\Imports;

use Illuminate\Support\Str;

class MaterialImport
{

    public function getExcelData($file)
    {
        $this->clearTempFiles();
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($file);
        $worksheet = $spreadsheet->getSheet(0);
        $dataRows = $worksheet->toArray();

        foreach ($dataRows as $key => $value) {
            if (count(array_unique($value)) === 1) {
                unset($dataRows[$key]);
            }
        }

        if ($allImage = $worksheet->getDrawingCollection()) {
            foreach($allImage as $drawing){
                $cell = $worksheet->getCell($drawing->getCoordinates());
                $column = $cell->getColumn();
                $row = $cell->getRow();
                if (isset($dataRows[$row - 1])) {
                    if (ord($column) - 65 === 5) {
                        $filename = $drawing->getPath();
                        $extension = $drawing->getExtension();
                        $temFilename = '/temp/' . md5(Str::random(15) . time()) . '.' . $extension;
                        copy($filename, public_path($temFilename));
                        $dataRows[$row - 1][ord($column) - 65][] = $temFilename;
                    }
                }
            }
        }
        return array_slice($dataRows, 1);
    }

    public function clearTempFiles()
    {
        $dirname = public_path('temp');
        $handle = opendir($dirname);
        while (false != ($item = readdir($handle))) {
            if ( $item != "." && $item != ".." ) {
                unlink("$dirname/$item");
            }
        }
    }
}
