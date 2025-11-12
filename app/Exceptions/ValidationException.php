<?php

namespace ilhamrhmtkbr\App\Exceptions;

class ValidationException extends \Exception
{
    private array $validationErrors;

    public function __construct(array $validationErrors)
    {
        $this->validationErrors = $validationErrors;
    }

    public function getErrors(): array
    {
        foreach ($this->validationErrors as $key => $error) {
            if (is_null($error == null)) {
                unset($key[$error]);
            }
        }
        return $this->validationErrors;
    }
}
