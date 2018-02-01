<?php

include('src/BarcodeGenerator.php');
include('src/BarcodeGeneratorPNG.php');

$generator = new Picqer\Barcode\BarcodeGeneratorPNG();

header("Content-Type: image/png");

if(array_key_exists("data",$_GET)) {
    $code = htmlspecialchars($_GET["data"]);
    if(is_numeric($code)){
        echo $generator->getBarcode($code, $generator::TYPE_CODE_128, 1, 50);
        exit;
    }
}
$name = __dir__."/error_image.png";
$img = imagecreatefrompng($name);
imagepng($img);
imagedestroy($img);
