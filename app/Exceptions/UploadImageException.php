<?php

namespace ilhamrhmtkbr\App\Exceptions;

use Exception;

class UploadImageException extends Exception
{
    private $validationErrors;

    public function __construct(array $validationErrors)
    {
        $this->validationErrors = $validationErrors;
    }

    public function getErrors()
    {
        return $this->validationErrors;
    }
}
