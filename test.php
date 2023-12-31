<?php

require_once 'DefaultAzureCredentials.php';
require_once 'AzureStorageBlob.php';

$defaultCreds = new DefaultAzureCredentials();

$dotenv = parse_ini_file('.env');
foreach ($dotenv as $key => $value) {
    putenv("$key=$value");
}
$storageAccount = getenv('STORAGE_ACCOUNT_NAME');
$containerName = getenv('CONTAINER_NAME');
$blobName = getenv('BLOB_NAME');

$accountUrl = "https://$storageAccount.blob.core.windows.net";
$storageBlob = new AzureStorageBlob($accountUrl, $defaultCreds);

$blobContents = $storageBlob->downloadBlob($containerName, $blobName);

echo "Got Something?: \n";
echo $blobContents;

echo "\n";

?>
