<?php

if(! function_exists('dd')) {
    function dd($value) 
    {
        var_dump($value);
        exit();
    }
}
