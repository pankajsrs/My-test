<?php
/**
 * Death by Captcha PHP API full usage example
 *
 * @package DBCAPI
 * @subpackage PHP
 */

/**
 * DBC API clients
 */
require_once 'deathbycaptcha.php';


// Put your DBC username & password here.
//$client = new DeathByCaptcha_HttpClient($argv[1], $argv[2]);
$client = new DeathByCaptcha_SocketClient($argv[1], $argv[2]);
$client->is_verbose = true;
echo "Your balance is {$client->balance} US cents\n";
// Put your CAPTCHA image file name or file resource here, you'll get
// CAPTCHA details array on success:
if ($captcha = $client->upload($argv[3])) {
    echo "CAPTCHA {$captcha['captcha']} uploaded\n";

    sleep(DeathByCaptcha_Client::DEFAULT_TIMEOUT);

    // Poll for CAPTCHA text:
    if ($text = $client->get_text($captcha['captcha'])) {
        echo "CAPTCHA {$captcha['captcha']} solved: {$text}\n";

        // Report if the CAPTCHA was solved incorrectly. Make sure the CAPTCHA
        // was in fact solved incorrectly!
        $client->report($captcha['captcha']);
    } else {
        // Remove an unsolved CAPTCHA
        $client->remove($captcha['captcha']);
    }
}
