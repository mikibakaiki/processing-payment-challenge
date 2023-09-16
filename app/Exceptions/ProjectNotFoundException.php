<?php
namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class ProjectNotFoundException extends Exception
{
    public function __construct($message = "The specified project was not found.", $code = 0, Exception $previous = null)
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