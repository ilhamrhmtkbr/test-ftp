<?php

namespace ilhamrhmtkbr\App\Exceptions;

/**
 * Redirect Exception
 * Digunakan untuk menghentikan eksekusi di FrankenPHP worker mode
 * tanpa menggunakan exit() yang akan kill worker
 */
class RedirectException extends \Exception
{
    private string $location;

    public function __construct(string $location)
    {
        $this->location = $location;
        parent::__construct("Redirect to: {$location}");
    }

    public function getLocation(): string
    {
        return $this->location;
    }
}