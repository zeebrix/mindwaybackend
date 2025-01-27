<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CustomreBrevoData;
use App\Models\Customer;
use SendinBlue\Client\Api\ContactsApi;
use SendinBlue\Client\ApiException;
use GuzzleHttp\Client;
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Model\CreateContact;
use SendinBlue\Client\Model\UpdateContact;
use SendinBlue\Client\Model\RemoveContactFromList;



// require_once '/home/mindw172/public_html/vendor/getbrevo/brevo-php/lib/Api/ContactsApi.php';
// require_once '/home/mindw172/public_html/vendor/getbrevo/brevo-php/lib/Configuration.php';
// require_once '/home/mindw172/public_html/vendor/getbrevo/brevo-php/lib/Model/UpdateContact.php';
// require_once '/home/mindw172/public_html/vendor/getbrevo/brevo-php/lib/Model/CreateContact.php';
// require_once '/home/mindw172/public_html/vendor/getbrevo/brevo-php/lib/Model/RemoveContactFromList.php';



class SyncBrevoContacts extends Command
{
    protected $signature = 'sync:brevo-contacts';
    protected $description = 'Sync Brevo contacts by removing common emails from list 9 and adding them to list 11';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        \Log::info("Cron job started");

        try {
            // Fetch all emails from CustomreBrevoData
            $allEmails = CustomreBrevoData::pluck('email')->toArray();
            // \Log::info("Fetched emails from CustomreBrevoData", ['emails' => $allEmails]);

            // Fetch all emails from Customer
            $signupUsers = Customer::pluck('email')->toArray();
            // \Log::info("Fetched emails from Customer", ['emails' => $signupUsers]);

            // Find common emails
            $commonEmails = array_intersect($allEmails, $signupUsers);
            // \Log::info("Common emails", ['emails' => $commonEmails]);

            if (empty($commonEmails)) {
                $this->info('No common emails found.');
                // \Log::info('No common emails found.');
                return;
            }

            // Set up the SendinBlue API configuration
            $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', env('BREVO_API_KEY'));
            $apiInstance = new ContactsApi(new Client(), $config);

            foreach ($commonEmails as $email) {
                try {
                    // Fetch contact data from Brevo
                    $contact = $apiInstance->getContactInfo($email);
                    // \Log::info("Fetched contact data", ['contact' => $contact]);

                    // Extract attributes
                    $attributes = $contact->getAttributes();
                    $firstName = $attributes['FIRSTNAME'] ?? '';
                    $lastName = $attributes['LASTNAME'] ?? '';
                    $codeAccess = $attributes['CODEACCESS'] ?? '';
                    $company = $attributes['COMPANY'] ?? '';
                    $ms = $attributes['MS'] ?? '';

                    // Check if the contact exists in list 9
                    if (in_array(9, $contact->getListIds())) {
                        // Remove the contact from list ID 9
                        $contactIdentifiers = new RemoveContactFromList([
                            'emails' => [$email]
                        ]);
                        $apiInstance->removeContactFromList(9, $contactIdentifiers);
                        // \Log::info("Contact {$email} removed from list ID 9.");
                    } else {
                        // \Log::info("Contact {$email} does not exist in list ID 9.");
                    }

                    // Add the contact to list 11 or update its attributes if it already exists
                    $updateContact = new UpdateContact([
                        'listIds' => [11], // New list ID
                        'attributes' => [
                            'FIRSTNAME' => $firstName,
                            'LASTNAME' => $lastName,
                            'CODEACCESS' => $codeAccess,
                            'COMPANY' => $company,
                            'MS' => $ms
                        ]
                    ]);
                    $apiInstance->updateContact($email, $updateContact);
                    // \Log::info("Contact {$email} added to list ID 11 with attributes.");

                    $this->info("Email {$email} updated in Brevo lists.");

                } catch (ApiException $e) {
                    $this->error('Error: ' . $e->getMessage());
                    // \Log::error('Error: ' . $e->getMessage());
                }
            }

        } catch (\Exception $e) {
            // \Log::error('Error in command execution: ' . $e->getMessage());
        }
    }
}
