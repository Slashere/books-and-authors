<?php

namespace app\services;

use app\models\Book;
use app\models\Subscription;
use yii\base\InvalidConfigException;

class NotificationService
{
    /**
     * @throws InvalidConfigException
     */
    public static function notifyNewBook(Book $book): void
    {
        $authorIds = $book->getAuthors()->select('id')->column();

        if (empty($authorIds)) {
            return;
        }

        $subscriptions = Subscription::find()
            ->where(['author_id' => $authorIds])
            ->all();

        if (empty($subscriptions)) {
            return;
        }

        $smsService = new SmsService();

        foreach ($subscriptions as $subscription) {
            $message = sprintf(
                'Новая книга автора %s: "%s", %d г.',
                $subscription->author->full_name,
                $book->title,
                $book->year
            );
            $smsService->send($subscription->phone, $message);
        }
    }
}