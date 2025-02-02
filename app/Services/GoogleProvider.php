<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Google_Service_Calendar_ConferenceData;
use Google_Service_Calendar_CreateConferenceRequest;
class GoogleProvider extends AbstractProvider
{
    private ?GoogleClient $httpClient = null;
    private ?Google_Service_Calendar $calendarService = null;

    /**
     * Create the Google OAuth authentication URL.
     */
    public function createAuthUrl(): string
    {
        $client = $this->getHttpClient(); // Get the Google_Client instance

        $client->setAccessType('offline'); // This ensures a refresh token is included
        $client->setPrompt('consent'); // Ensures consent screen is shown if required

        $authUrl = $client->createAuthUrl();
        return $authUrl;
    }

    /**
     * Fetch access token using authorization code.
     */
    protected function fetchAccessTokenWithAuthCode(string $code): array
    {
        return $this->getHttpClient()->fetchAccessTokenWithAuthCode($code);
    }

    /**
     * Extract basic user profile information from credentials.
     */
    protected function getBasicProfile(array $credentials): array
    {
        $jwt = explode('.', $credentials['id_token']);
        $payload = json_decode(base64_decode($jwt[1]), true);

        return $payload ?? [];
    }

    /**
     * Map user profile to a standardized user object.
     */
    protected function toUser(array $profile)
    {
        return (object) [
            'id' => $profile['sub'] ?? null,
            'name' => $profile['name'] ?? null,
            'email' => $profile['email'] ?? null,
            'picture' => $profile['picture'] ?? null,
        ];
    }

    /**
     * Create a calendar event in the user's primary Google Calendar.
     */
    public function createEvent(array $eventData)
    {
        // Fetch the stored access token
        $accessToken = Crypt::decrypt($eventData['access_token']);
        $this->getHttpClient()->setAccessToken($accessToken);

        // Initialize the Google Calendar service
        $calendarService = $this->getCalendarService();

        // Create start date/time
        $startDateTime = new Google_Service_Calendar_EventDateTime();
        $startDateTime->setDateTime($eventData['start_time']);
        $startDateTime->setTimeZone($eventData['timezone'] ?? 'UTC');

        // Create end date/time
        $endDateTime = new Google_Service_Calendar_EventDateTime();
        $endDateTime->setDateTime($eventData['end_time']);
        $endDateTime->setTimeZone($eventData['timezone'] ?? 'UTC');

        // Create the event
        $event = new Google_Service_Calendar_Event();
        $event->setSummary($eventData['title']);
        $event->setDescription($eventData['description'] ?? '');
        $event->setStart($startDateTime);
        $event->setEnd($endDateTime);

        $attendees = [
        ['email' => $eventData['employee_email']], // Employee as attendee
        //['email' => $eventData['counselor_email'], 'organizer' => true] // Counselor as organizer
        ];
        $event->setAttendees($attendees);
        if ($eventData['communication_method'] != 'Video Call')
        {
            $event->setLocation('Phone Number');
        } else {
            // Create Google Meet link
            $conferenceData = new Google_Service_Calendar_ConferenceData();
            $conferenceRequest = new Google_Service_Calendar_CreateConferenceRequest();
            $conferenceRequest->setRequestId(uniqid()); // Unique request ID
            $conferenceData->setCreateRequest($conferenceRequest);
            $event->setConferenceData($conferenceData);
        }
        $createdEvent = $calendarService->events->insert('primary', $event, ['conferenceDataVersion' => 1,'sendUpdates' => 'all']);
        // Insert the event into the user's primary calendar
        return [
            'event_id' => $createdEvent->getId(),
            'summary' => $createdEvent->getSummary(),
            'description' => $createdEvent->getDescription(),
            'start_time' => $createdEvent->getStart()->getDateTime(),
            'end_time' => $createdEvent->getEnd()->getDateTime(),
            'attendees' => $createdEvent->getAttendees(),
            'meeting_link' => ($eventData['communication_method'] === 'Video Call') ? $createdEvent->getConferenceData()->getEntryPoints()[0]->uri??null : null,
        ];
    }
    public function deleteEvent(string $eventId, string $accessToken)
    {
        // Fetch the stored access token
        $this->getHttpClient()->setAccessToken(Crypt::decrypt($accessToken));

        // Initialize the Google Calendar service
        $calendarService = $this->getCalendarService();

        // Delete the event
        $calendarService->events->delete('primary', $eventId);

        return [
            'status' => 'success',
            'message' => 'Event deleted successfully.'
        ];
    }
    public function updateEvent(string $eventId, array $eventData)
    {
        // Fetch the stored access token
        $accessToken = $eventData['access_token'];
        
        $this->getHttpClient()->setAccessToken(Crypt::decrypt($accessToken));

        // Initialize the Google Calendar service
        $calendarService = $this->getCalendarService();

        // Fetch the existing event
        $event = $calendarService->events->get('primary', $eventId);

        // Update event details
        if (!empty($eventData['title'])) {
            $event->setSummary($eventData['title']);
        }
        if (!empty($eventData['description'])) {
            $event->setDescription($eventData['description']);
        }
        if (!empty($eventData['start_time'])) {
            $startDateTime = new Google_Service_Calendar_EventDateTime();
            $startDateTime->setDateTime($eventData['start_time']);
            $startDateTime->setTimeZone($eventData['timezone'] ?? 'UTC');
            $event->setStart($startDateTime);
        }
        if (!empty($eventData['end_time'])) {
            $endDateTime = new Google_Service_Calendar_EventDateTime();
            $endDateTime->setDateTime($eventData['end_time']);
            $endDateTime->setTimeZone($eventData['timezone'] ?? 'UTC');
            $event->setEnd($endDateTime);
        }
       $attendees = [
        ['email' => $eventData['employee_email']], // Employee as attendee
       // ['email' => $eventData['counselor_email'], 'organizer' => true] // Counselor as organizer
        ];
        $event->setAttendees($attendees);
        if (!empty($eventData['communication_method']) && $eventData['communication_method'] != 'Video Call') {
            $event->setLocation('Phone Number'); // Set location as "Phone"
            $emptyConferenceData = new Google_Service_Calendar_ConferenceData();
            $event->setConferenceData($emptyConferenceData);
        } else 
        {
            if (!empty($eventData['update_meeting_link'])) {
                $conferenceData = $event->getConferenceData() ?: new Google_Service_Calendar_ConferenceData();
                $conferenceRequest = new Google_Service_Calendar_CreateConferenceRequest();
                $conferenceRequest->setRequestId(uniqid());
                $conferenceData->setCreateRequest($conferenceRequest);
                $event->setConferenceData($conferenceData);
            }
        }
        $updatedEvent = $calendarService->events->update('primary', $eventId, $event, ['conferenceDataVersion' => 1,'sendUpdates' => 'all']);
        return [
            'event_id' => $updatedEvent->getId(),
            'summary' => $updatedEvent->getSummary(),
            'description' => $updatedEvent->getDescription(),
            'start_time' => $updatedEvent->getStart()->getDateTime(),
            'end_time' => $updatedEvent->getEnd()->getDateTime(),
            'attendees' => $updatedEvent->getAttendees(),
            'meeting_link' => ($eventData['communication_method'] === 'Video Call') ? $updatedEvent->getConferenceData()->getEntryPoints()[0]->uri ?? null:null,
        ];
    }



    /**
     * Get or initialize the Google Calendar service.
     */
    private function getCalendarService(): Google_Service_Calendar
    {
        if (!$this->calendarService) {
            $this->calendarService = new Google_Service_Calendar($this->getHttpClient());
        }

        return $this->calendarService;
    }

    /**
     * Get or initialize the Google HTTP client.
     */
    private function getHttpClient(): GoogleClient
    {
        if (!$this->httpClient) {
            $this->httpClient = new GoogleClient();
            $this->httpClient->setApplicationName(config('app.name'));
            $this->httpClient->setClientId($this->clientId);
            $this->httpClient->setClientSecret($this->clientSecret);
            $this->httpClient->setRedirectUri($this->redirectUrl);
            $this->httpClient->setScopes($this->scopes);
            $this->httpClient->setState(Crypt::encrypt($this->request->all()));
        }

        return $this->httpClient;
    }
}
