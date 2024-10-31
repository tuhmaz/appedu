<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseMessage;
use Kreait\Firebase\Factory;

class FirebaseMessageNotification extends Notification
{
    private $title;
    private $body;

    public function __construct($title, $body)
    {
        $this->title = $title;
        $this->body = $body;
    }

    public function via($notifiable)
    {
        return ['firebase'];
    }

    public function toFirebase($notifiable)
    {
        $messaging = (new Factory())->withServiceAccount(config('firebase.credentials.file'))->createMessaging();
        $notification = FirebaseMessage::create($this->title, $this->body);

        $message = CloudMessage::withTarget('topic', 'all')->withNotification($notification);

        $messaging->send($message);
    }
}
