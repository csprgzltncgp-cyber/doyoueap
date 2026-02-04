<?php

namespace App\Notifications\EapOnline;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\PusherPushNotifications\PusherMessage;

class EapMessageCreated extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public string $message,
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['pusher-beams-eap-chat', 'pusher-beams-my-eap'];
    }

    public function toPushNotification(object $notifiable): PusherMessage
    {
        return PusherMessage::create()
            ->iOS()
            ->badge(1)
            ->body($this->message)
            ->withAndroid(
                PusherMessage::create()
                    ->title($this->message)
                    ->icon('icon')
            );
    }
}
