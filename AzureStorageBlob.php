<?php

class AzureStorageBlob {

    //blob_service_client = BlobServiceClient(account_url=account_url, credential=credential)
    //blob_client = blob_service_client.get_blob_client(container=container_name, blob=blob_name)

    public function __construct($accountUrl, $credential) {
        $this->accountUrl = $accountUrl;
        $this->credential = $credential;
    }

    public function downloadBlob($container, $blobName) {

    }

    public function uploadBlob($container, $blobName, $content) {
        
    }

}


?>