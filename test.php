<?php

function getAccessToken($resource) {
    // Check for environment variable credentials first
    if (getenv('AZURE_CLIENT_ID') && getenv('AZURE_CLIENT_SECRET') && getenv('AZURE_TENANT_ID')) {
        // If available, use them to acquire an access token (see previous examples for implementation)
        // ...

    } elseif (getenv('IDENTITY_ENDPOINT')) {
        // If the environment variables are not available, try to get a token from the managed identity
        $tokenUrl = getenv('IDENTITY_ENDPOINT') . '?api-version=2019-08-01&resource=' . urlencode($resource);
        $secret = getenv('IDENTITY_HEADER');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $tokenUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-IDENTITY-HEADER: ' . $secret]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);

        $responseData = json_decode($response, true);
        if (isset($responseData['access_token'])) {
            return $responseData['access_token'];
        }

    } else {
        // If managed identity is not available, try to get the access token from Azure CLI cache
        $command = 'az account get-access-token --resource ' . escapeshellarg($resource) . ' --output json';
        $output = shell_exec($command);
        $tokenData = json_decode($output, true);
        if (isset($tokenData['accessToken'])) {
            return $tokenData['accessToken'];
        }
    }

    throw new Exception('Unable to acquire an access token.');
}

// Example usage
try {
    $resource = 'https://storage.azure.com/';
    $accessToken = getAccessToken($resource);
    //echo "Access Token: " . $accessToken . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}



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
