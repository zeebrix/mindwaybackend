<?php
namespace App\Services;

use Illuminate\Support\Manager;

class CalendarManager extends Manager
{
    public function __construct($app)
    {
        parent::__construct($app);
    }

    public function getDefaultDriver()
    {
        return 'google';
    }

    protected function createGoogleDriver()
    {
        $config = config('services.google');
        return $this->buildProvider(GoogleProvider::class, $config);
    }

    protected function buildProvider($provider, $config)
    {
        return new $provider(
            request(),
            $config['client_id'],
            $config['client_secret'],
            $config['redirect_uri'],
            $config['scopes']
        );
    }
}