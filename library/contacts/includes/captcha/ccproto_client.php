<?php
/**
 * DeCaptcher API client replacement
 *
 * @package DBCAPI
 */

/**
 * Death by Captcha API client
 */
require_once 'deathbycaptcha.php';


/**#@+
 * DeCaptcher API client constants, updated v20091005
 */
// ERROR CODES
define('ccERR_OK', 0); // everything went OK
define('ccERR_GENERAL', -1); // general internal error
define('ccERR_STATUS', -2); // status is not correct
define('ccERR_NET_ERROR', -3); // network data transfer error
define('ccERR_TEXT_SIZE', -4); // text is not of an appropriate size
define('ccERR_OVERLOAD', -5); // server's overloaded
define('ccERR_BALANCE', -6); // not enough funds to complete the request
define('ccERR_TIMEOUT', -7); // requiest timed out
define('ccERR_UNKNOWN', -200); // unknown error

// picture processing TIMEOUTS
define('ptoDEFAULT', 0); // default timeout, server-specific
define('ptoLONG', 1); // long timeout for picture, server-specfic
define('pto30SEC', 2); // 30 seconds timeout for picture
define('pto60SEC', 3); // 60 seconds timeout for picture
define('pto90SEC', 4); // 90 seconds timeout for picture

// picture processing TYPES
define('ptUNSPECIFIED', 0); // picture type unspecified

define('CC_PROTO_VER', 1);        //    protocol version
define('CC_RAND_SIZE', 256);        //    size of the random sequence for authentication procedure
define('CC_MAX_TEXT_SIZE', 100);        //    maximum characters in returned text for picture
define('CC_MAX_LOGIN_SIZE', 100);        //    maximum characters in login string
define('CC_MAX_PICTURE_SIZE', 200000);        //    200 K bytes for picture seems sufficient for all purposes
define('CC_HASH_SIZE', 32);

define('cmdCC_UNUSED', 0);
define('cmdCC_LOGIN', 1);        //    login
define('cmdCC_BYE', 2);        //    end of session
define('cmdCC_RAND', 3);        //    random data for making hash with login+password
define('cmdCC_HASH', 4);        //    hash data
define('cmdCC_PICTURE', 5);        //    picture data, deprecated
define('cmdCC_TEXT', 6);        //    text data, deprecated
define('cmdCC_OK', 7);        //
define('cmdCC_FAILED', 8);        //
define('cmdCC_OVERLOAD', 9);        //
define('cmdCC_BALANCE', 10);        //    zero balance
define('cmdCC_TIMEOUT', 11);        //    time out occured
define('cmdCC_PICTURE2', 12);        //    picture data
define('cmdCC_PICTUREFL', 13);        //    picture failure
define('cmdCC_TEXT2', 14);        //    text data
define('cmdCC_SYSTEM_LOAD', 15);        //    system load

define('SIZEOF_CC_PACKET', 6);

define('SIZEOF_CC_PICT_DESCR', 20);

define('sCCC_INIT', 1);  // initial status, ready to issue LOGIN on client
define('sCCC_LOGIN', 2);  // LOGIN is sent, waiting for RAND (login accepted) or CLOSE CONNECTION (login is unknown)    
define('sCCC_HASH', 3);  // HASH is sent, server may CLOSE CONNECTION (hash is not recognized)
define('sCCC_PICTURE', 4);
/**#@-*/


/**
 * DeCaptcher API client replacement
 *
 * @package DBCAPI
 * @subpackage PHP
 */
class ccproto
{
    /**
     * Proper DBC API client
     *
     * @var DeathByCaptcha_Client
     */
    protected $_client = null;

    /**
     * Service status
     *
     * @var int
     */
    public $status = null;


    /**
     * Initializes the client
     *
     * @return int Error code
     */
    public function init()
    {
        $this->status = sCCC_INIT;
        return ccERR_OK;
    }

    /**
     * @ignore
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * Logs into system
     *
     * @param string $hostname Unused
     * @param mixed  $port     Unused
     * @param string $login
     * @param string $pass
     * @return int Error code
     */
    public function login($hostname, $port, $login, $pass)
    {
        //$this->_client = new DeathByCaptcha_HttpClient($login, $pass);
        $this->_client = new DeathByCaptcha_SocketClient($login, $pass);
        $this->status = sCCC_PICTURE;
        return ccERR_OK;
    }

    /**
     * Finalizes client usage properly
     *
     * @return int Error code
     */
    public function close()
    {
        $this->status = sCCC_INIT;
        return ccERR_OK;
    }

    /**
     * Determines current system load, just a stub
     *
     * @param int $system_load System load holder
     * @return int Error code
     */
    public function system_load(&$system_load)
    {
        $system_load = 0;
        return ccERR_OK;
    }

    /**
     * Fetches user's balance
     *
     * @param int $balance Balance holder
     * @return int Error code
     */
    public function balance(&$balance)
    {
        if ($this->status != sCCC_PICTURE) {
            return ccERR_STATUS;
        } else {
            try {
                $balance = $this->_client->get_balance() / 100;
                return ccERR_OK;
            } catch (Exception $e) {
                return ccERR_UNKNOWN;
            }
        }
    }

    /**
     * Uploads a picture and poll for decoded test
     *
     * @param string $pict      Raw image file content
     * @param int    $pict_to   Timeout holder
     * @param string $pict_type Picture type holder
     * @param string $test      Decoded text holder
     * @param int    $major_id  CAPTCHA major ID holder
     * @param int    $minor_id  CAPTCHA minor ID holder
     * @return int Error code
     */
    public function picture2($pict, &$pict_to, &$pict_type, &$text, &$major_id=null, &$minor_id=null)
    {
        if ($this->status != sCCC_PICTURE) {
            return ccERR_STATUS;
        }

        $errno = ccERR_OK;
        $pict_to = ptoDEFAULT;
        $pict_type = ptUNSPECIFIED;

        $fn = tempnam(null, 'captcha');
        file_put_contents($fn, $pict);
        try {
            if ($captcha = $this->_client->decode($fn)) {
                $major_id = $captcha['captcha'];
                $text = $captcha['text'];
            }
        } catch (DeathByCaptcha_InvalidAccountException $e) {
            $errno = ccERR_BALANCE;
        } catch (DeathByCaptcha_InvalidCaptchaException $e) {
            $errno = ccERR_GENERAL;
        } catch (Exception $e) {
            $errno = ccERR_UNKNOWN;
        }
        @unlink($fn);
        return $errno;
    }

    /**
     * Uploads image and polls for decoded text
     *
     * @deprecated
     * @param string $pict Raw image content
     * @param string $text Decoded text holder
     * @return int Error code
     */
    public function picture($pict, &$text)
    {
        $pict_to = $pict_type = $major_id = $minor_id = 0;
        return $this->picture2($pict, $pict_to, $pict_type, $text, $major_id, $minor_id);
    }

    /**
     * Reports incorrectly decoded CAPTCHA
     *
     * @param int $major_id
     * @param int $minor_id
     * @return int Error code
     */
    public function picture_bad2($major_id, $minor_id)
    {
        if ($this->status != sCCC_PICTURE) {
            return ccERR_STATUS;
        } else {
            try {
                $this->_client->report($major_id);
                return ccERR_OK;
            } catch (Exception $e) {
                return ccERR_NET_ERROR;
            }
        }
    }

    /**
     * Reports incorrectly decoded CAPTCHA
     *
     * @deprecated
     * @return int Error code
     */
    public function picture_bad()
    {
        return ccERR_NET_ERROR;
    }
}
