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
