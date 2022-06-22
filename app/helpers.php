<?php

use Illuminate\Support\Arr;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

function changExcelRowToDataMap(array $fields, array $importData): array
{
    $rowsData = [];
    foreach ($importData as $row) {
        $mapRow = [];
        foreach($fields as $key => $field) {
            if ($row[$key]) {
                $mapRow[$field] = trim($row[$key]);
            }
        }
        array_push($rowsData, $mapRow);
    }
    return $rowsData;
}

function changeImportDataEmptyStringToNull(array &$importData) {
    foreach ($importData as $key => $row) {
        unset($importData[$key]['messages']);
        unset($importData[$key]['error']);
        foreach ($row as $k => $value) {
            if (!$value && $value !== 0) {
                unset($importData[$key][$k]);
            }
        }
    }
}

function addKey(array $data): array
{
    foreach ($data as $key => $val) {
        $data[$key]['key'] = $key;
    }
    return $data;
}

function validateImportData (array &$data, array $rules, array $messages = [], array $attributes = [])
{
    $allPassed =  true;
    foreach ($data as $key => $row) {

        $data[$key] = Arr::except($data[$key], ['error', 'messages']);

        $validator = \Validator::make($row, $rules, $messages, $attributes);
        if ($validator->passes()) {
            $data[$key]['error'] = false;
        } else {
            $data[$key]['error'] = true;
            $msgArr = [];
            $errors = $validator->errors()->toArray();
            foreach ($errors as $k => $error) {
                $msgArr[$k] = $error[0];
            }
            $data[$key]['messages'] = $msgArr;
        }
        $allPassed &= !$data[$key]['error'];
    }

    return $allPassed;
}

function setExcelSelect(\Maatwebsite\Excel\Sheet $sheet, string $cellName, array $options) {
    $carrierValidation = $sheet->getDelegate()->getCell($cellName)->getDataValidation();
    $carrierValidation->setType(DataValidation::TYPE_LIST);
    $carrierValidation->setErrorStyle(DataValidation::STYLE_STOP);
    $carrierValidation->setAllowBlank(true);
    $carrierValidation->setShowErrorMessage(true);
    $carrierValidation->setShowDropDown(true);
    $carrierValidation->setFormula1('"' . implode(',', $options) . '"');
}
