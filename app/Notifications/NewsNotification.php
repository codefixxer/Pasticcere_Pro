<?php
// app/Notifications/NewsNotification.php

namespace App\Notifications;

use App\Models\News;
use Illuminate\Notifications\Notification;

class NewsNotification extends Notification
{
    protected $news;

    public function __construct(News $news)
    {
        $this->news = $news;
    }

    public function via($notifiable)
    {
        return ['database']; // Save notification in the database
    }

    public function toDatabase($notifiable)
    {
        return [
            'news_id' => $this->news->id,
            'title' => $this->news->title,
            'content' => $this->news->content,
        ];
    }
}

