<?php
namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class ProfileNotFoundException extends Exception
{
    public function __construct($message = "The specified profile was not found.", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function report()
    {
        Log::error($this->getMessage());
    }

    public function render($request)
    {
        //
    }
}