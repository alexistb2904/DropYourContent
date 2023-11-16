<?php
function checkRegex($regex, $string)
{
    if ($regex == "username") {
        return preg_match('/^[a-zA-Z0-9_-]+$/', $string) === 1;
    } elseif ($regex == "name") {
        return preg_match('/[^a-zA-Z\s]{3,}/', $string) === 1;
    } elseif ($regex == "email") {
        return preg_match('/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/', $string) === 1;
    } elseif ($regex == "password") {
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9])(?!.*[;\'"`\\\\]).{6,}$/', $string) === 1;
    } else {
        return false;
    }
}

var_dump(checkRegex("email", "Alexis@1Thierry-Bellefond"));