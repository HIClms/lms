<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class CustomException extends Exception
{
    public function report(){
        Log::debug("Testing exception errror log");
    }
}
