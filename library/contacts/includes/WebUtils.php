<?php
/**
 * @(#) WebUtils.php
 * @author Hasan hasan@improsys.com
 * @history
 *          created  : Mahmud Hasan : Date : 04-03-2010
 * @version 1.0
 *
 * Copyright Improsys.
 *
 * All rights reserved.
 *
 * This software is the confidential and proprietary information
 * of Improsys. ("Confidential Information").
 * You shall not disclose such Confidential Information and shall use
 * it only in accordance with the terms of the license agreement
 * you entered into with Improsys.
 */

class Cookie {
	function Cookie ($ck_str, $uri="") {
        $pairs = explode(';',$ck_str);
        $c_idx = 0;
        foreach ($pairs as $pair) {
         	$vals = explode('=', $pair, 2);
         	$name = trim($vals[0]);
            if (isset($vals[1])) {
	         	$value = trim($vals[1]);
         	} else {
	        	$value = '';
        	}
         	if ($c_idx == 0) {
				$this->name = $name;
				$this->value = $value;
			}
			$c_idx++;
            
		}
	}
};


class CookieContainer {
 	var $cookies = array();
    
	function addCookie ($cookie) {
		$name = $cookie->name;
		$n = count($this->cookies);
		for ($i=0; $i<$n; ++$i) {
			$cookie1 = $this->cookies[$i];
			if (strcmp($cookie1->name,$name) == 0) {
                $this->cookies[$i] = $cookie;
				return;		
			}
		}
		$this->cookies[] = $cookie;
	}

    function getCookieString ($uri="") {
        $cookiestr = "";
		foreach ($this->cookies as $cookie) {
            $cookiestr .= "$cookie->name=$cookie->value; ";
		}
        $cookiestr = substr($cookiestr, 0, strlen($cookiestr)-2);
		return $cookiestr;
	}
    function getCookieValue($name){
		$CK = NULL;
		foreach ($this->cookies as $cookie) {
            if($cookie->name == $name) {
               $CK = $cookie->value;
            }
		}
        return $CK;
	}
    function deleteCookieByName($name) {
		$n = count($this->cookies);
        $t_arr = array();
		foreach ($this->cookies as $cookie) {
            if($cookie->name != $name) {
                $t_arr[] = $cookie;
            }
		}
        $this->cookies = $t_arr;
	}
};

?>