<?php

namespace ilhamrhmtkbr\App\Helper;

use DateTime;

class TimeHelper
{
    public static function getTimeAgo(string $time): string
    {
        // Membuat objek DateTime untuk waktu sekarang dan waktu yang diberikan
        $now = new \DateTime('now', new \DateTimeZone('Asia/Jakarta')); // Ganti dengan zona waktu yang sesuai
        $past = new \DateTime($time, new \DateTimeZone('Asia/Jakarta')); // Ganti dengan zona waktu yang sesuai

        // Menghitung selisih dalam detik
        $diffInSeconds = $now->getTimestamp() - $past->getTimestamp();

        if ($diffInSeconds < 60) {
            $timeAgo = $diffInSeconds . ' seconds ago';
        } elseif ($diffInSeconds < 3600) {
            $timeAgo = floor($diffInSeconds / 60) . ' minutes ago';
        } elseif ($diffInSeconds < 86400) {
            $timeAgo = floor($diffInSeconds / 3600) . ' hours ago';
        } else {
            $timeAgo = floor($diffInSeconds / 86400) . ' days ago';
        }

        return $timeAgo;
    }

    public static function getTimeBasic(string $time): string
    {

        $date = new DateTime($time);
        $formattedDate = $date->format('j F Y \a\t g a');
        return $formattedDate;
    }

    public static function getTime(string $time): string
    {

        $date = new DateTime($time);
        $formattedDate = $date->format('j F Y');
        return $formattedDate;
    }

    public static function getClock(string $time): string
    {
        $dateTime = new DateTime($time);
        $formattedTime = $dateTime->format("h:i A");

        return $formattedTime;
    }

    public static function isEndDateGreaterThanStartDate(string $startDate, string $endDate): bool
    {
        $startDate = new DateTime($startDate);
        $endDate = new DateTime($endDate);

        return $startDate > $endDate;
    }

    public static function getSecond(string $time): int
    {
        list($clock, $minute, $second) = explode(':', $time);
        return $clock * 3600 + $minute * 60 + $second;
    }
}
