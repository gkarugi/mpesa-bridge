<?php

if (!function_exists('stringNotNullOrEmpty')) {
    function stringNotNullOrEmpty($string) : bool
    {
        if (strlen(trim($string)) == 0) {
            return false;
        }

        return true;
    }
}

if (!function_exists('stringNullOrEmpty')) {
    function stringNullOrEmpty($string) : bool
    {
        return !stringNotNullOrEmpty($string);
    }
}

if (!function_exists('isValidUrl')) {
    function isValidUrl(string $url) :bool
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === true) {
            return true;
        } else {
            return false;
        }
    }
}
