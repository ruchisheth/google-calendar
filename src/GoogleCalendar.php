<?php

namespace PosBill\GoogleCalendar;

use Carbon\Carbon;
use DateTime;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Calendar;
use Google_Service_Calendar_Event;

class GoogleCalendar
{
    /** @var Google_Service_Calendar */
    protected $calendarService;

    /** @var string */
    protected $calendarId;

    protected $client;

    // Temporary
    protected $accessToken;

    /*public function __constructOld(Google_Service_Calendar $calendarService, string $calendarId)
    {
        $this->calendarService = $calendarService;

        $this->calendarId = $calendarId;
    }*/


    public function __construct(
        Google_Service_Calendar $calendarService,
        Google_Client $client,
        string $calendarId = null)
    {
        $this->calendarService = $calendarService;

        $this->calendarId = $calendarId;

        $this->client = $client;

        //TODO: This is temporary remove this once implement
        $this->accessToken = [
            "access_token" => "ya29.a0AfH6SMCVObejMxfbSCsEv4J0gbkSbEbVMyDxcDp4YxymYPZHzbRlSqWhRvmWHXGQKGKz2mVW62UoJ6Ps3GKF13wQr-6LPjEa-SEt9Aun-Kgd-Uma_Os5os1hYXugPDrRlD2rulLVPo2yhTpdLM6pw2Fr04TlWf5MAM8",
            "expires_in" => 3599,
            "refresh_token" => "1//0gpUK4FsUXrlMCgYIARAAGBASNwF-L9IrVdBO08pI5QjIXHlcn6nR6XkWFCkwwmNxOlXCNAE-rD01rLhlq1DSXiTUh_pIbUrcmEQ",
            "scope" => "https://www.googleapis.com/auth/calendar",
            "token_type" => "Bearer",
            "created" => 1600254751,
        ];
    }

    public function getCalendarId(): string
    {
        return $this->calendarId;
    }

    public function getAuthUrl(array $params = [])
    {
        if (!empty($params)) {
            $params = strtr(base64_encode(json_encode($params)), '+/=', '-_,');

            $this->client->setState($params);
        }

        return $this->client->createAuthUrl();
    }

    public function createCalendar(array $input)
    {
        $calendar = new Google_Service_Calendar_Calendar();
        $calendar->setSummary($input['summary']);
        $calendar->setDescription($input['summary']);
        return $this->calendarService->calendars->insert($calendar);
    }

    public function getCalendars()
    {
        return $this->calendarService->calendarList->listCalendarList();
    }

    /*
     * @link https://developers.google.com/google-apps/calendar/v3/reference/events/list
     */
    public function listEvents(Carbon $startDateTime = null, Carbon $endDateTime = null, array $queryParameters = []): array
    {
        $parameters = ['singleEvents' => true];

        if (is_null($startDateTime)) {
            $startDateTime = Carbon::now()->startOfDay();
        }

        $parameters['timeMin'] = $startDateTime->format(DateTime::RFC3339);

        if (is_null($endDateTime)) {
            $endDateTime = Carbon::now()->addYear()->endOfDay();
        }
        $parameters['timeMax'] = $endDateTime->format(DateTime::RFC3339);

        $parameters = array_merge($parameters, $queryParameters);

        return $this
            ->calendarService
            ->events
            ->listEvents($this->calendarId, $parameters)
            ->getItems();
    }

    public function getEvent(string $eventId): Google_Service_Calendar_Event
    {
        return $this->calendarService->events->get($this->calendarId, $eventId);
    }

    /*
     * @link https://developers.google.com/google-apps/calendar/v3/reference/events/insert
     */
    /*public function insertEventOld($event, $optParams = []): Google_Service_Calendar_Event
    {
        if ($event instanceof Event) {
            $event = $event->googleEvent;
        }

        return $this->calendarService->events->insert($this->calendarId, $event, $optParams);
    }*/

    public function insertEvent($input, string $calendarId, array $optParams = []): Google_Service_Calendar_Event
    {
        $event = Event::create($input);
        $googleServiceEvent = $event->googleEvent;

        return $this->calendarService->events->insert($calendarId, $googleServiceEvent, $optParams);
    }

    public function updateEvent($event): Google_Service_Calendar_Event
    {
        if ($event instanceof Event) {
            $event = $event->googleEvent;
        }

        return $this->calendarService->events->update($this->calendarId, $event->id, $event);
    }

    public function deleteEvent($eventId)
    {
        if ($eventId instanceof Event) {
            $eventId = $eventId->id;
        }

        $this->calendarService->events->delete($this->calendarId, $eventId);
    }

    public function getService(): Google_Service_Calendar
    {
        return $this->calendarService;
    }

    public function getAccessTokenFromAuthCode(string $authCode)
    {
        return $this->client->fetchAccessTokenWithAuthCode($authCode);
    }

    public function setAccessToken(string $authCode = '')
    {
        $this->client->setAccessToken($this->accessToken);
        if ($this->client->isAccessTokenExpired()) {
            if ($this->client->getRefreshToken()) {
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            }
            /*else {
                $accessToken = $this->client->fetchAccessTokenWithAuthCode($authCode);
                // Check to see if there was an error.
                if (array_key_exists('error', $accessToken)) {
                    throw new RequiredParamsException(join(', ', $accessToken));
                }

                //TODO set access-token and refresh token in DB
                $this->client->setAccessToken($accessToken);
            }*/
        }
    }
}
