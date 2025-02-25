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
use Illuminate\Support\Str;
use Google\Service\Calendar\Channel;

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
    public function getAllEventsOld(string $accessToken, string $timeMin = null, string $timeMax = null)
    {
        // Fetch the stored access token
        $this->getHttpClient()->setAccessToken(Crypt::decrypt($accessToken));

        // Initialize Google Calendar service
        $calendarService = $this->getCalendarService();

        // Get Calendar default timezone
        $calendar = $calendarService->calendarList->get('primary');
        $calendarTimeZone = $calendar->getTimeZone(); // Default calendar timezone

        // Set time range to the current month's remaining days if null
        if (!$timeMin || !$timeMax) {
            $now = now();
            $timeMin = $now->toRfc3339String(); // Today's date and time
            $timeMax = $now->endOfMonth()->toRfc3339String(); // Last day of the current month
        }

        // Get events
        $events = $calendarService->events->listEvents('primary', [
            'timeMin' => $timeMin,
            'timeMax' => $timeMax,
            'singleEvents' => true,
            'orderBy' => 'startTime'
        ])->getItems();
        // Format response
        return collect($events)->map(function ($event) use ($calendarTimeZone) {
            return [
                'event_id' => $event->getId(),
                'summary' => $event->getSummary(),
                'description' => $event->getDescription() ?? '',
                'start_time' => $event->getStart()->getDateTime() ?? $event->getStart()->getDate(),
                'start_timezone' => $event->getStart()->getTimeZone() ?? $calendarTimeZone,
                'end_time' => $event->getEnd()->getDateTime() ?? $event->getEnd()->getDate(),
                'end_timezone' => $event->getEnd()->getTimeZone() ?? $calendarTimeZone,
                'attendees' => $event->getAttendees() ?? [],
                'meeting_link' => $event->getConferenceData() && $event->getConferenceData()->getEntryPoints() 
                                ? $event->getConferenceData()->getEntryPoints()[0]->uri 
                                : null,
            ];
        })->toArray();
    }
    public function getAllEvents(string $accessToken, string $timeMin = null, string $timeMax = null)
    {
        // Fetch the stored access token
        $this->getHttpClient()->setAccessToken(Crypt::decrypt($accessToken));

        // Initialize Google Calendar service
        $calendarService = $this->getCalendarService();

        // Set time range to the current month's remaining days if null
        if (!$timeMin || !$timeMax) {
            $now = now();
            $timeMin = $now->toRfc3339String(); // Today's date and time
            $timeMax = $now->endOfMonth()->toRfc3339String(); // Last day of the current month
        }

        // Fetch all calendars
        $calendarList = $calendarService->calendarList->listCalendarList()->getItems();
        $allEvents = [];

        foreach ($calendarList as $calendar) {
            $calendarId = $calendar->getId();
            $calendarTimeZone = $calendar->getTimeZone() ?? 'UTC';

            // Get events for the current calendar
            $events = $calendarService->events->listEvents($calendarId, [
                'timeMin' => $timeMin,
                'timeMax' => $timeMax,
                'singleEvents' => true,
                'orderBy' => 'startTime'
            ])->getItems();

            // Format and store events
            foreach ($events as $event) {
                $allEvents[] = [
                    'calendar_id' => $calendarId,
                    'calendar_name' => $calendar->getSummary(),
                    'event_id' => $event->getId(),
                    'summary' => $event->getSummary(),
                    'description' => $event->getDescription() ?? '',
                    'start_time' => $event->getStart()->getDateTime() ?? $event->getStart()->getDate(),
                    'start_timezone' => $event->getStart()->getTimeZone() ?? $calendarTimeZone,
                    'end_time' => $event->getEnd()->getDateTime() ?? $event->getEnd()->getDate(),
                    'end_timezone' => $event->getEnd()->getTimeZone() ?? $calendarTimeZone,
                    'attendees' => $event->getAttendees() ?? [],
                    'meeting_link' => $event->getConferenceData() && $event->getConferenceData()->getEntryPoints() 
                                    ? $event->getConferenceData()->getEntryPoints()[0]->uri 
                                    : null,
                ];
            }
        }
        return $allEvents;
    }
    public function watchCalendarOLD($counselor)
    {
        $accessToken = $counselor->googleToken->access_token;
        $client = $this->getHttpClient();
        $client->setAccessToken(Crypt::decrypt($accessToken));

        $calendarService = $this->getCalendarService();
        $this->stopAllWebhooks($counselor);
        $channel = new Channel();
        $channel->setId(Str::uuid());
        $channel->setType('web_hook');
        $channel->setAddress(env('GOOGLE_CALENDAR_WEBHOOK_URL'));
        $response = $calendarService->events->watch('primary', $channel);
        $counselor->update([
            'google_webhook_channel_id' => $response->id,
            'google_webhook_resource_id' => $response->resourceId,
            'google_webhook_expiration' => $response->expiration ?? null
        ]);
        Log::info('Google Calendar Watch Response:', (array) $response);
        return $response;
    }
    public function watchAllCalendars($counselor)
{
    $accessToken = $counselor->googleToken->access_token;
    $client = $this->getHttpClient();
    $client->setAccessToken(Crypt::decrypt($accessToken));

    $calendarService = $this->getCalendarService();
    $this->stopAllWebhooks($counselor); // Stop existing webhooks before creating new ones

    // Fetch all user calendars
    $calendarList = $calendarService->calendarList->listCalendarList()->getItems();
    $webhookData = [];

    foreach ($calendarList as $calendar) {
        $calendarId = $calendar->getId();
        $channel = new Channel();
        $channel->setId(Str::uuid());
        $channel->setType('web_hook');
        $channel->setAddress(env('GOOGLE_CALENDAR_WEBHOOK_URL'));

        try {
            $response = $calendarService->events->watch($calendarId, $channel);
            Log::info("Google Calendar Watch Response for {$calendarId}:", (array) $response);

            // Store webhook details for tracking (optional)
            $webhookData[] = [
                'calendar_id' => $calendarId,
                'channel_id' => $response->id,
                'resource_id' => $response->resourceId,
                'expiration' => $response->expiration ?? null,
            ];
        } catch (\Exception $e) {
            Log::error("Failed to watch calendar {$calendarId}: " . $e->getMessage());
        }
    }

    // Optionally, store webhook data in DB (if tracking multiple webhooks per user)
    $counselor->update([
        'google_webhook_data' => json_encode($webhookData),
    ]);

    return $webhookData;
}


public function stopAllWebhooks($counselor)
{
    if (!$counselor->google_webhook_data) {
        Log::info("No existing webhooks to stop for counselor: {$counselor->id}");
        return;
    }
    try {
        $calendarService = $this->getCalendarService();
        $webhooks = json_decode($counselor->google_webhook_data, true);

        foreach ($webhooks as $webhook) {
            if (empty($webhook['channel_id']) || empty($webhook['resource_id'])) {
                continue; // Skip invalid entries
            }

            $channel = new Channel();
            $channel->setId($webhook['channel_id']);
            $channel->setResourceId($webhook['resource_id']);

            try {
                $calendarService->channels->stop($channel);
                Log::info("Stopped webhook for calendar: {$webhook['calendar_id']} (Channel ID: {$webhook['channel_id']})");
            } catch (\Exception $e) {
                Log::error("Error stopping webhook for calendar {$webhook['calendar_id']}: " . $e->getMessage());
            }
        }

        // Clear all webhook data
        $counselor->update([
            'google_webhook_data' => null,
        ]);

        Log::info("Stopped all webhooks for counselor: {$counselor->id}");
    } catch (\Exception $e) {
        Log::error("Error stopping all webhooks for counselor {$counselor->id}: " . $e->getMessage());
    }
}

    
}
