<?php

enum AuthType: string {
    case ServicePrincipal = 'serviceprincipal';
    case ManagedIdentity = 'managedidentity';
    case AzLogin = 'azlogin';
}

class DefaultAzureCredentials {
    protected $accessToken;
    public AuthType $authType;


    public function __construct() {
        $this->checkAvailableCredentials();
    }

    public function getAccessToken() {
        //TODO This should go to the correct endpoint based on what is requested, detect storage, pass in the storage resource, etc
        $resource = 'https://storage.azure.com/';
        if($this->authType == AuthType::AzLogin){
            $command = "az account get-access-token --resource $resource --output json";
            $output = shell_exec($command);
            $tokenData = json_decode($output, true);
            if (isset($tokenData['accessToken'])) {
                return $tokenData['accessToken'];
            }
            else {
                throw new Exception("Failed to grab Access token from AZLOGIN");
            }
        }
    }

    private function checkAvailableCredentials() {
        if($this->hasServicePrincipal()) {
            echo "Detected Service Principal";
            $this->authType = AuthType::ServicePrincipal;
            return;
        }
        if($this->hasManagedIdentity()) {
            echo "Detected Managed Identity";
            $this->authType = AuthType::ManagedIdentity;
            return;
        }
        if($this->hasAzLogin()) {
            echo "Detected Az Login";
            $this->authType = AuthType::AzLogin;
            return;
        }
        throw new Exception('No Valid Authentication Methods');
    }

    private function hasServicePrincipal() {
        return getenv('AZURE_CLIENT_ID') && getenv('AZURE_CLIENT_SECRET') && getenv('AZURE_TENANT_ID');
    }

    private function hasManagedIdentity() {
        return getenv('IDENTITY_ENDPOINT');
    }

    private function hasAzLogin() {
        $command = 'az account get-access-token --output json';
        $output = shell_exec($command);
        $tokenData = json_decode($output, true);
        if (isset($tokenData['accessToken'])) {
            return True;
        }
    }
}

## Replicating python if __main__
if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) {
    $defaultCreds = new DefaultAzureCredentials();
    $accessToken = $defaultCreds->getAccessToken();
    
    echo $accessToken;
}

?>
