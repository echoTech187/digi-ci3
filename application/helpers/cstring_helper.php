<?php

function generateCredentialKey($length = 30) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $bytes = random_bytes($length);
    $key = '';
    for ($i = 0; $i < $length; $i++) {
        $key .= $characters[ord($bytes[$i]) % $charactersLength];
    }
    return $key;
}