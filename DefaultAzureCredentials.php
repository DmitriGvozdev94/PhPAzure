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


$defaultCreds = new DefaultAzureCredentials();

?>
