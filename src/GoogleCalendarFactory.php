<?php

namespace PosBill\GoogleCalendar;

use Google_Client;
use Google_Service_Calendar;

class GoogleCalendarFactory
{
    public static function createForCalendarId(string $calendarId): GoogleCalendar
    {
        $config = config('google-calendar');

        $client = self::createAuthenticatedGoogleClient($config);

        $service = new Google_Service_Calendar($client);

        return self::createCalendarClient($service, $calendarId);
    }

    public static function createGoogleCalendarClient(): GoogleCalendar
    {
        $config = config('google-calendar');

        $client = self::createAuthenticatedGoogleClient($config);

        $service = new Google_Service_Calendar($client);

        return self::createCalendarClient($service, $client);
    }

    public static function createAuthenticatedGoogleClient(array $config): Google_Client
    {
        $client = new Google_Client;

        $client->setScopes([
            Google_Service_Calendar::CALENDAR,
        ]);

        $client->setAuthConfig($config['service_account_credentials_json']);


        return $client;
    }

    protected static function createCalendarClient(Google_Service_Calendar $service, Google_Client $client, string $calendarId = null): GoogleCalendar
    {
        return new GoogleCalendar($service, $client, $calendarId);
    }

    /*protected static function createCalendarClientOld(Google_Service_Calendar $service, string $calendarId): GoogleCalendar
    {
        return new GoogleCalendar($service, $calendarId);
    }*/
}
