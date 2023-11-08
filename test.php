<?php

require_once 'DefaultAzureCredentials.php';

$defaultCreds = new DefaultAzureCredentials();



$dotenv = parse_ini_file('.env');
foreach ($dotenv as $key => $value) {
    putenv("$key=$value");
}
$storageAccount = getenv('STORAGE_ACCOUNT_NAME');
$containerName = getenv('CONTAINER_NAME');
$blobName = getenv('BLOB_NAME');

// The full URI to the blob
$blobUri = "https://$storageAccount.blob.core.windows.net/$containerName/$blobName";

// Create the HTTP headers for the request with the access token
$accessToken = $defaultCreds->getAccessToken();
$headers = [
    "Authorization: Bearer $accessToken",
    "x-ms-version: 2020-04-08", // use the latest version appropriate for your scenario
    "x-ms-date: " . gmdate('D, d M Y H:i:s T', time())
];

// Initialize cURL session
$ch = curl_init($blobUri);

// Set cURL options
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // only for testing, in production you should verify peer

// Execute the cURL session
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
    // Handle error, for example:
    die('cURL returned error: ' . curl_error($ch));
}

// Close cURL session
curl_close($ch);

// $response now contains the contents of the blob
echo $response;


?>
