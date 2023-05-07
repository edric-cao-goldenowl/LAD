<?php
include './../vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;


// Hard-coded credentials
$s3Client = new S3Client([
    'version'     => 'latest',
    'region'      => 'ap-southeast-1',
    'credentials' => [
        'key'    => 'AKIAWK6JJURBCV4O46V7',
        'secret' => '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',
    ],
]);

//$url = $s3Client->getObjectUrl('GetObject', 'private-folder/GO Lịch nghỉ Tết 2023.png');


$cmd = $s3Client->getCommand('GetObject', [
    'Bucket' => 'edric',
    'Key' => 'private-folder/GO Lịch nghỉ Tết 2023.png'
]);


$request = $s3Client->createPresignedRequest($cmd, '+20 minutes');

// Get the actual presigned-url
$presignedUrl = (string)$request->getUri();
echo $presignedUrl;
