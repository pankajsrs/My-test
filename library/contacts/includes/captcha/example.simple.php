<?php
/**
 * Death by Captcha PHP API simplest usage example
 *
 * @package DBCAPI
 * @subpackage PHP
 */

/**
 * DBC API clients
 */
require_once 'deathbycaptcha.php';


// Put your DBC username & password here:
//$client = new DeathByCaptcha_HttpClient($argv[1], $argv[2]);
$client = new DeathByCaptcha_SocketClient($argv[1], $argv[2]);
$client->is_verbose = true;

echo "Your balance is {$client->balance} US cents\n";

// Put your CAPTCHA image file name or file resource, and optional solving
// timeout (in seconds) here; you'll get CAPTCHA details array on success:
if ($captcha = $client->decode($argv[3], DeathByCaptcha_Client::DEFAULT_TIMEOUT)) {
    echo "CAPTCHA {$captcha['captcha']} solved: {$captcha['text']}\n";

    // Report if the CAPTCHA was solved incorrectly. Make sure the CAPTCHA
    // was in fact solved incorrectly!
    $client->report($captcha['captcha']);
}
