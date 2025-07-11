<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function hashEncrypt($input)
{
    return password_hash($input, PASSWORD_BCRYPT);
}

function hashEncryptVerify($input, $hashed)
{
    return password_verify($input, $hashed);
}
