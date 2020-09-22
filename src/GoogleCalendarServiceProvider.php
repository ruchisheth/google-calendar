<?php

namespace PosBill\GoogleCalendar;

use Illuminate\Support\ServiceProvider;
use PosBill\GoogleCalendar\Exceptions\InvalidConfiguration;

class GoogleCalendarServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/google-calendar.php' => config_path('google-calendar.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/google-calendar.php', 'google-calendar');

        $this->app->bind(GoogleCalendar::class, function () {
            $config = config('google-calendar');
            $this->guardAgainstInvalidConfiguration($config);

            //return GoogleCalendarFactory::createForCalendarId($config['calendar_id']);
            return GoogleCalendarFactory::createGoogleCalendarClient();
        });

        $this->app->alias(GoogleCalendar::class, 'laravel-google-calendar');
    }

    /**
     * @param array|null $config
     *
     * @throws InvalidConfiguration
     */
    protected function guardAgainstInvalidConfiguration(array $config = null)
    {
        /*if (empty($config['calendar_id'])) {
            throw InvalidConfiguration::calendarIdNotSpecified();
        }*/

        if (!file_exists($config['service_account_credentials_json'])) {
            throw InvalidConfiguration::credentialsJsonDoesNotExist($config['service_account_credentials_json']);
        }
    }
}
