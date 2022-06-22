<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use \Illuminate\Support\Arr;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// 条形码测试
Route::get('/barcode', function () {
    $typesOf1d = [
        'C39', 'C39+', 'C39E', 'C39E+', 'C93', 'S25',
//        'S25+', 'I25', 'I25+', 'C128', 'C128A', 'C128B',
//        'EAN2', 'EAN5', 'EAN8','EAN13', 'UPCA',
//        'UPCE', 'MSI', 'MSI+', 'POSTNET', 'PLANET','RMS4CC',
//        'KIX', 'CODABAR', 'CODE11', 'PHARMA', 'PHARMA2T'
    ];

    $barcodeOf1d = [];

    config('barcode.store_path', public_path("/testdir"));

    $path = [];
    foreach ($typesOf1d as $type) {
        $barcodeOf1d[$type] = \DNS1D::getBarcodePNG('123456', $type, 2, 50); // 生成base64
        $path[$type] = \DNS1D::getBarcodePNGPath('456789', $type, 2, 50); // 保存png条形码图片
    }
//    print_r($path);
    return view('barcode_test', compact('barcodeOf1d'));
});

// pdf测试
Route::get('/pdf', function () {
//    return \PDF::loadView('pdf_test')->stream('download.pdf');

    $barcodeFilePath = url('barcodes/456789c39.png');

//    echo $barcodeFilePath; exit;

    return \PDF::loadView('pdf_test', compact('barcodeFilePath'))
        ->setOptions([
            //'page-size' => 'A6',
            'page-width' => 70,
            'page-height' => 30,
            'margin-top' => 4,
            'margin-left' => 0,
            'margin-bottom' => 0,
            'margin-right' => 0,
            //'orientation' => 'landscape', // 横向
        ])
        ->download('download.pdf');
});

Route::get('validate', 'IndexController@testValidate');
Route::get('pdfs', 'IndexController@pdf');
Route::get('rent', 'IndexController@rent');
Route::get('ep', 'IndexController@ep');

Route::get('/test', function() {
    //echo 1234; exit;

    /*try {
        //    \EasyPost\EasyPost::setApiKey('cueqNZUb3ldeWTNX7MU3Mel8UXtaAMUi');
        \EasyPost\EasyPost::setApiKey('EZAKfcbbfdc990e94f5b861dbda20a818fbczf3UbIBy7QFfzdXHcEZyJA');

//    $address_params = [
//        "name"    => "Sawyer Bateman",
//        "street1" => "388 Townasend St",
//        "street2" => "Apt 20",
//        "city"    => "San Francisco",
//        "state"   => "CA",
//        "zip"     => "94107",
//        "country" => "United States"
//    ];

        $address_params = [
            "street1" => "6835 West Buckeye Road",
            "street2" => "",
            "city"    => "Phoenix",
            "state"   => "AZ",
            "zip"     => "85043",
            "country" => "United States"
        ];

//        $address_params = [
//            "street1" => "aaaaa",
//            "street2" => "",
//            "city"    => "vvv",
//            "state"   => "AZ",
//            "zip"     => "88888",
//            "country" => "United States"
//        ];

//        $verified_on_create = \EasyPost\Address::create_and_verify($address_params);
//
//        print_r(\EasyPost\Util::convertEasyPostObjectToArray($verified_on_create));

        $service = new \App\Services\BaseService();

        print_r($service->verifyAddress($address_params));

    } catch (Exception $e) {
        print_r($e->jsonBody);
    }*/

//    $chr = 'A';
//    for ($chr = 'AA'; $chr <= 'AT'; $chr ++) {
//        echo $chr . '-';
//    }

//    $chr = 'A';
//    echo ord('A');
//    echo chr(65);
//    echo ord($chr);

//    $order = \App\Models\OutboundOrder::query()->find(49);
//    $service = new \App\Services\ShipmentService();
//    $rates = $service->getRates($order);

//    $rates = $service->getUsableRates($order);

//    print_r($rates);

//    $a = ['name' => 'xcm', 'age' => 33];
//    $b = ['age' => 55];
//    print_r(array_merge($a, $b));

//    $a = [1, 1, 2];
//    $b = [2];
//    print_r(array_merge($a, $b));

    //$pdf = new setasign\Fpdi\Fpdi('P', 'mm', [102, 158]);
    //
    //$pdf->AddPage();
    //$pdf->SetMargins(0, 0);
    //$pdf->Image('uploads/label/202112/14/61b864786aede.png', 3.5, 0, 98, 138);
    //
    //$pdf->AddPage();
    //$pdf->SetMargins(0, 0);
    //$pdf->Image('label/platform/png/287532456560.png', 3.5, 0, 98, 138);
    //
    //$pdf->AddPage();
    //$pdf->setSourceFile('uploads/label/202112/13/61b6ff9a3c530.pdf');
    //$tplId = $pdf->importPage(1);
    //$pdf->SetMargins(0, 0);
    //$pdf->useTemplate($tplId);
    //
    //$pdf->AddPage();
    //$pdf->setSourceFile('uploads/label/202112/14/61b85e8a37ef3.pdf');
    //$tplId = $pdf->importPage(1);
    //$pdf->SetMargins(2, 0);
    //$pdf->useTemplate($tplId);
    //
    //$pdf->Output();

//    $path = "/testweb/home.php";
//    echo basename($path);

    //$feeModelItems = DB::table('fee_model_item')->select('fee_item_id', 'is_required')->get();
    //dd($feeModelItems);
    ////print_r($feeModelItems);
    //$ss = $feeModelItems->where('fee_item_id', '=', 21)->first();
    //dd($ss);

    //$arr = [
    //    1 => 'c',
    //    2 => 'b',
    //    3 => 'a',
    //];
    //dd(array_search('a', $arr));

    //dd(strtotime(''));

    //$currentDate = '2022-02-28';
    //$nextDate = date('Y-m-d', strtotime('+ 1 day', strtotime($currentDate)));
    //echo $nextDate;

    //$days = Carbon::parse('2022-2-20')->diffInDays('2022-2-21');
    //echo $days;

    //$a = 1.01333;
    //$b = '1.01333';
    //dd(1 == $a - 0.01333);

    //$str = 'shipment_label_TEST1234.pdf';
    //echo basename(ltrim($str, 'shipment_label_'), '.pdf');

    //$zip = \ZanySoft\Zip\Zip::create('file.zip');
    //$zip->add([
    //    //public_path('barcodes/690000001c128.png'),
    //    //public_path('barcodes/690000003c128.png'),
    //
    //    '/home/vagrant/code/www.shop.test/public/uploads/bulk/202203/03/USOP_On3djThRN7JlNs3.pdf',
    //    '/home/vagrant/code/www.shop.test/public/uploads/bulk/202203/03/USOP_WdISpjITVtw0280.pdf',
    //    '/home/vagrant/code/www.shop.test/public/uploads/bulk/202203/03/USOP_WX9DThL1HPgIqpR.pdf',
    //    '/home/vagrant/code/www.shop.test/public/uploads/bulk/202203/03/USOP_JjMfqc3k8y2sMud.pdf',
    //]);
    //$zip->close();
    //return response()->download(public_path('file.zip'))->deleteFileAfterSend(true);

    //$str = 'shipment_label_file';
    //echo \Illuminate\Support\Str::camel($str);

    //$zipper = new \PhpZip\ZipFile();
    //$zipper->addFile('/home/vagrant/code/www.shop.test/public/uploads/bulk/202203/03/USOP_On3djThRN7JlNs3.pdf', 'ooonnn.pdf');
    //$zipper->outputAsAttachment('运单文件.zip');

    //$str = 'SML_PO123456';
    ////echo substr($str, 0, 3);
    //echo substr($str, 4);
    $a = [1, 2];
    $b = [1, 2, 3];
    $c = array_diff($b, $a);
    dd($c);
});
