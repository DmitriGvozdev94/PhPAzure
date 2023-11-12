<?php

require_once 'DefaultAzureCredentials.php';
require_once 'AzureStorageBlob.php';

$dotenv = parse_ini_file('.env');
foreach ($dotenv as $key => $value) {
    putenv("$key=$value");
}
$storageAccount = getenv('STORAGE_ACCOUNT_NAME');
$containerName = getenv('CONTAINER_NAME');
$blobName = getenv('BLOB_NAME');

// The full URI to the blob
$blobUri = "https://$storageAccount.blob.core.windows.net/$containerName/$blobName";
$accountUrl = "https://$storageAccount.blob.core.windows.net";

$defaultCreds = new DefaultAzureCredentials();
$storageBlob = new AzureStorageBlob($accountUrl, $defaultCreds);

$content = "Hello Azure";

$blobName = "2023-11-12.txt";
$storageBlob->uploadBlob($containerName, $blobName, $content);


?>
