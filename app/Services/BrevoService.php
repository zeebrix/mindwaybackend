<?php
namespace App\Services;
use Brevo\Client\Api\ContactsApi;
use Brevo\Client\Configuration;
use Brevo\Client\ApiException;
use GuzzleHttp\Client;

class BrevoService
{
    protected $apiInstance;

    public function __construct()
    {
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', env('BREVO_API_KEY'));
        $this->apiInstance = new ContactsApi(new Client(), $config);
    }

    public function removeUserFromList($email)
    {
        $listIds = [9, 11]; // Define lists
        $contactIdentifier = $email;

        foreach ($listIds as $listId) {
            try {
                // Remove the contact from the specific list
                $this->apiInstance->removeContactFromList($listId, $contactIdentifier);
                echo "User removed from list $listId\n";
            } catch (ApiException $e) {
                echo "Error removing user from list $listId: " . $e->getMessage();
            }
        }
    }
}

?>