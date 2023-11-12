<?php

class AzureStorageBlob {

    //blob_service_client = BlobServiceClient(account_url=account_url, credential=credential)
    //blob_client = blob_service_client.get_blob_client(container=container_name, blob=blob_name)

    public function __construct($accountUrl, $credential) {
        $this->accountUrl = $accountUrl;
        $this->credential = $credential;
    }

    private function getHeaders() {
        $accessToken = $this->credential->getAccessToken();
        $headers = [
            "Authorization: Bearer $accessToken",
            "x-ms-version: 2020-04-08", // use the latest version appropriate for your scenario
            "x-ms-date: " . gmdate('D, d M Y H:i:s T', time())
        ];

        return $headers;
    }

    public function downloadBlob($container, $blobName) {
        
        $blobUri = "$this->accountUrl/$container/$blobName";


        // Initialize cURL session
        $ch = curl_init($blobUri);

        // Set cURL options
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getHeaders);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

        // Execute the cURL session
        $response = curl_exec($ch);

        // Check for errors
        if (curl_errno($ch)) {
            // Handle error, for example:
            die('cURL returned error: ' . curl_error($ch));
        }

        // Close cURL session
        curl_close($ch);
        return $response;   
    }

    public function uploadBlob($container, $blobName, $content) {
        $blobUri = "$this->accountUrl/$container/$blobName";


        // Initialize cURL session
        $ch = curl_init($blobUri);

        $headers = $this->getHeaders();
                
        $headers[] = "x-ms-blob-type: BlockBlob";
        $headers[] = "Content-Type: application/octet-stream";

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // Execute the request
        $response = curl_exec($ch);
        
        // Check for errors
        if(curl_errno($ch)){
            throw new Exception(curl_error($ch));
        }
        
        // Close the cURL session
        curl_close($ch);

        echo $response;


    }

}


?>