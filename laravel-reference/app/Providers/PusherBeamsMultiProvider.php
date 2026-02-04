<?php

namespace App\Providers;

use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;
use NotificationChannels\PusherPushNotifications\PusherChannel;
use Pusher\PushNotifications\PushNotifications;

class PusherBeamsMultiProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $config = config('services.pusher');

        $this->app->singleton('pusher.beams.eap-chat', fn (): PushNotifications => new PushNotifications(['instanceId' => $config['eap-chat']['beams_instance_id'], 'secretKey' => $config['eap-chat']['beams_secret_key']]));

        $this->app->singleton('pusher.beams.my-eap', fn (): PushNotifications => new PushNotifications(['instanceId' => $config['my-eap']['beams_instance_id'], 'secretKey' => $config['my-eap']['beams_secret_key']]));
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Notification::extend('pusher-beams-eap-chat', fn ($app): PusherChannel => new PusherChannel($app->make('pusher.beams.eap-chat'), $app->make('events')));

        Notification::extend('pusher-beams-my-eap', fn ($app): PusherChannel => new PusherChannel($app->make('pusher.beams.my-eap'), $app->make('events')));
    }
}
