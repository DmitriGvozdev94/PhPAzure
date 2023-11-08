from azure.identity import DefaultAzureCredential
from azure.storage.blob import BlobServiceClient

# Replace these with your specific details
account_url = ""
container_name = ""
blob_name = "test.txt"

# Create a credential object
credential = DefaultAzureCredential()

# Create a blob service client
blob_service_client = BlobServiceClient(account_url=account_url, credential=credential)

# Get the blob client
blob_client = blob_service_client.get_blob_client(container=container_name, blob=blob_name)

download_blob = blob_client.download_blob()
print(download_blob.readall())
