<?php
namespace App\Services;
use Brevo\Client\Api\ContactsApi;
use Brevo\Client\Configuration;
use Brevo\Client\ApiException;
use GuzzleHttp\Client;
use Brevo\Client\Model\CreateContact;
use Brevo\Client\Model\RemoveContactFromList;
use Exception;

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
        // $contactIdentifier = $email;

        // foreach ($listIds as $listId) {
        //     try {
        //         // Remove the contact from the specific list
        //         $this->apiInstance->removeContactFromList($listId, $contactIdentifier);
        //         echo "User removed from list $listId\n";
        //     } catch (ApiException $e) {
        //         echo "Error removing user from list $listId: " . $e->getMessage();
        //     }
        //     catch (\Throwable $th) {
        //         //throw $th;
        //     }
        // }

        try {
            // Get contact info to check which lists the contact is in
            $contactInfo = $this->apiInstance->getContactInfo($email);
            $existingLists = $contactInfo->getListIds(); // Get list IDs the contact is in
            // Loop through the target list IDs and remove if the contact exists in that list
            foreach ($listIds as $listId) {
                if (in_array($listId, $existingLists)) {
                    // Create the request body
                    $contactIdentifiers = new RemoveContactFromList([
                        'emails' => [$email]
                    ]);
                    // Remove the contact from the list
                    $this->apiInstance->removeContactFromList($listId, $contactIdentifiers);
                    \Log::info("User removed from list $listId", ['email' => $email, 'listId' => $listId]);
                }
            }
        } catch (Exception $e) {
            \Log::error("Error removing user from list : " . $e->getMessage(), ['email' => $email]);
        }
    }
    public function addUserToList($email, $name, $code, $company_name, $max_session, $listId = 11)
    {
        $createContact = new CreateContact([
            'email' => $email,
            'attributes' => (object) [
                'EMAIL' => $email,
                'FIRSTNAME' => $name,
                'CODEACCESS' => $code,
                'COMPANY' => $company_name,
                'MS' => $max_session,
                'LASTNAME' => ""
            ],
            'listIds' => [$listId],
        ]);
        try {
            return $this->apiInstance->createContact($createContact);
        } catch (ApiException $e) {
            // throw new \Exception("Brevo API Error: " . $e->getMessage());
        }
        catch (\Throwable $th) {
            //throw $th;
        }
    }
}

?>