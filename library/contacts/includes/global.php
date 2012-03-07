<?php
// session_start();

$debug = 0; // Set to 1 for verbose debugging output

function do_get($url, $port=80, $headers=NULL) {
    $retarr = array();  // Return value

    $curl_opts = array(CURLOPT_URL => $url,
                     CURLOPT_PORT => $port,
                     CURLOPT_POST => false,
                     CURLOPT_SSL_VERIFYHOST => false,
                     CURLOPT_SSL_VERIFYPEER => false,
                     CURLOPT_RETURNTRANSFER => true);

    if ($headers) {
        $curl_opts[CURLOPT_HTTPHEADER] = $headers; 
    }
    $response = do_curl($curl_opts);
    if (! empty($response)) { 
        $retarr = $response; 
    }

    return $retarr;
}

function do_post($url, $postbody, $port=80, $headers=NULL) {
    $retarr = array();

    $curl_opts = array(CURLOPT_URL => $url,
                     CURLOPT_PORT => $port,
                     CURLOPT_POST => true,
                     CURLOPT_SSL_VERIFYHOST => false,
                     CURLOPT_SSL_VERIFYPEER => false,
                     CURLOPT_POSTFIELDS => $postbody,
                     CURLOPT_RETURNTRANSFER => true);

    if ($headers) { 
        $curl_opts[CURLOPT_HTTPHEADER] = $headers; 
    }

    $response = do_curl($curl_opts);
    
    if (! empty($response)) { 
        $retarr = $response; 
    }

    return $retarr;
}

function do_curl($curl_opts) {
    global $debug;

    $retarr = array();

    if (! $curl_opts) {
        return $retarr;
    }

    $ch = curl_init();
    if (! $ch) {
        return $retarr;
    }

    curl_setopt_array($ch, $curl_opts);

    curl_setopt($ch, CURLOPT_HEADER, true);

    if ($debug) {
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
    }

    ob_start();
    $response = curl_exec($ch);
    $curl_spew = ob_get_contents();
    ob_end_clean();

    if (curl_errno($ch)) {
        $errno = curl_errno($ch);
        $errmsg = curl_error($ch);
        curl_close($ch);
        unset($ch);
        return $retarr;
    }

    if ($debug) {
        $header_sent = curl_getinfo($ch, CURLINFO_HEADER_OUT);
    }

    $info = curl_getinfo($ch);

    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $header_size);
    $body = substr($response, $header_size );

    curl_close($ch);
    unset($ch);

    array_push($retarr, $info, $header, $body);
    return $retarr;
}

function M_get_curl($url,$cookie_path="",$postfileds="",$referrer="",$header=false,$httph="",&$last_url, $redir=true) {
    $last_url = $url;
    $agent = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1) ; .NET CLR 1.1.4322)";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_USERAGENT, $agent);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    if($referrer!="") {
        curl_setopt($ch, CURLOPT_REFERER, $referrer);
    }

    if($httph != "") {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httph);
    }
    
    if($cookie_path != "") {
        touch($cookie_path);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_path);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_path);
    }
    if($postfileds != "") {
        curl_setopt($ch, CURLOPT_POST, 1.1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$postfileds);
    }
    curl_setopt($ch, CURLOPT_HEADER, $header);

    $result = curl_exec ($ch);
    $code=curl_getinfo($ch,CURLINFO_HTTP_CODE);
    curl_close ($ch);
    /* 
    echo("url=$url<hr>PostFields=$postfileds<hr>Referrer=$referrer<hr>".nl2br(htmlspecialchars($result))."<hr>");
    */
    
    if(($code==302 ||$code==301) && $redir)	{
        $n_url=substr($result,strpos($result,"Location: ")+strlen("Location: "));
        $n_url=substr($n_url,0,strpos($n_url,"\r\n"));
        $result=$result."<br>".M_get_curl(trim($n_url),$cookie_path,"",$url,true,"",$last_url);
    }
    return $result;
}


function M_hidden_fileds($str)
{
    $post="";
    $str=str_replace("> <","><",$str);
    /*
    $str=str_replace("\n","",$str);
    $str=str_replace("  ","",$str);
    preg_match_all ('/<input.*?hidden.*?>/i', $str, $matches,PREG_PATTERN_ORDER);
    */
    
    preg_match_all ("/<input.*hidden.*?>/", $str, $matches,PREG_PATTERN_ORDER);
    
    /*
    print_r($matches);
    */
    
    foreach($matches[0] as $keyfield=>$valfield)
    {
        if(strstr($valfield,'name="'))
        {
            $nstr='name="';
            $name=substr($valfield,strpos($valfield,$nstr)+strlen($nstr));
            $name=substr($name,0,strpos($name,"\""));
        }
        else
        {
            $nstr='name=';
            $name=substr($valfield,strpos($valfield,$nstr)+strlen($nstr));
            $name=substr($name,0,strpos($name," "));
        }
        
        $vstr='value="';
        if(strpos($valfield,$vstr)===false)
        {
            $vstr="value='";
            if(strpos($valfield,$vstr)===false)
                $value = "";
            else
                $value=substr($valfield,strpos($valfield,$vstr)+strlen($vstr));
                $value=substr($value,0,strpos($value,"'"));
        }
        else
        {
            $value=substr($valfield,strpos($valfield,$vstr)+strlen($vstr));
            $value=substr($value,0,strpos($value,"\""));
        }
        
        $details[$name]=$value;
        if($post=="")
            $post="$name"."=".urlencode($value);
        else
            $post.="&".$name."=".urlencode($value);
    }

    return $post;
}

function json_pretty_print($json, $html_output=false)
{
    $spacer = '  ';
    $level = 1;
    $indent = 0;
    $pretty_json = '';
    $in_string = false;

    $len = strlen($json);

    for ($c = 0; $c < $len; $c++) {
        $char = $json[$c];
        switch ($char) {
            case '{':
            case '[':
                if (!$in_string) {
                    $indent += $level;
                    $pretty_json .= $char . "\n" . str_repeat($spacer, $indent);
                } 
                else {
                    $pretty_json .= $char;
                }
                break;
                
            case '}':
            case ']':
                if (!$in_string) {
                    $indent -= $level;
                    $pretty_json .= "\n" . str_repeat($spacer, $indent) . $char;
                } 
                else {
                    $pretty_json .= $char;
                }
                break;
                
            case ',':
                if (!$in_string) {
                    $pretty_json .= ",\n" . str_repeat($spacer, $indent);
                } 
                else {
                    $pretty_json .= $char;
                }
                break;
                
            case ':':
                if (!$in_string) {
                    $pretty_json .= ": ";
                } 
                else {
                    $pretty_json .= $char;
                }
                break;
                
            case '"':
                if ($c > 0 && $json[$c-1] != '\\') {
                    $in_string = !$in_string;
                }
                break;
                
            default:
                $pretty_json .= $char;
                break;
        }
    }

    return ($html_output) ? '<pre>' . htmlentities($pretty_json) . '</pre>' : $pretty_json . "\n";
}

?>