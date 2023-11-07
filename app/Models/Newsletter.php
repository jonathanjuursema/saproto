<?php

namespace App\Models;

use Artisan;
use Carbon;
use Eloquent;

/**
 * App\Models\Newsletter.
 *
 * @mixin Eloquent
 */
class Newsletter
{
    /** @return HashMapItem */
    private static function getSentAt()
    {
        $lastSent = HashMapItem::where('key', 'newsletter_last_sent')->first();
        if ($lastSent == null) {
            $lastSent = HashMapItem::create([
                'key' => 'newsletter',
                'subkey' => 'sent_at',
                'value' => 0,
            ]);
        }

        return $lastSent;
    }

    /** @return HashMapItem */
    private static function getUpdatedAt()
    {
        $lastUpdated = HashMapItem::where('key', 'newsletter_text_updated')->first();
        if ($lastUpdated == null) {
            $lastUpdated = HashMapItem::create([
                'key' => 'newsletter',
                'subkey' => 'updated_at',
                'value' => 0,
            ]);
        }

        return $lastUpdated;
    }

    /** @return HashMapItem */
    private static function getText()
    {
        $lastSent = HashMapItem::where('key', 'newsletter_text')->first();
        if ($lastSent == null) {
            $lastSent = HashMapItem::create([
                'key' => 'newsletter',
                'subkey' => 'text',
                'value' => '',
            ]);
        }

        return $lastSent;
    }

    /** @return void */
    public static function setUpdatedAt()
    {
        $lastSent = self::getSentAt();
        $lastSent->value = Carbon::now()->timestamp;
        $lastSent->save();
    }

    /** @return void */
    public static function setText($text)
    {
        $newsletterText = self::getText();
        $textUpdated = self::getUpdatedAt();

        $newsletterText->value = $text;
        $newsletterText->save();

        $textUpdated->value = Carbon::now()->timestamp;
        $textUpdated->save();
    }

    /** @return Carbon */
    public static function sentAt()
    {
        return Carbon::createFromTimestamp(self::getSentAt()->value);
    }

    /** @return Carbon */
    public static function updatedAt()
    {
        return Carbon::createFromTimestamp(self::getUpdatedAt()->value);
    }

    /** @return string */
    public static function text()
    {
        return self::getText()->value;
    }

    /** @return bool */
    public static function lastSentMoreThanWeekAgo()
    {
        $lastSent = self::sentAt();
        $current = Carbon::now();
        $diff = $lastSent->diffInWeeks($current);

        return $diff >= 1;
    }

    /** @return bool */
    public static function hasEvents()
    {
        $events = Event::getEventsForNewsletter();

        return $events->count() > 0;
    }

    /** @return bool */
    public static function showTextOnHomepage()
    {
        $daysSinceLastUpdated = self::updatedAt()->diffInDays();

        return !empty(self::text()) && $daysSinceLastUpdated < 10;
    }

    /** @return bool */
    public static function send()
    {
        Artisan::call('proto:newslettercron');
        self::setUpdatedAt();

        return true;
    }
}
