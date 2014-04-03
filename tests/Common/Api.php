<?php

namespace Common;

use Exception;

class Api
{
    function simple() 
    {

    }
    
    function greet($name)
    {
        return sprintf("Hello, %s!", $name);
    }

    function exception($e)
    {
        throw new Exception($e);
    }
}