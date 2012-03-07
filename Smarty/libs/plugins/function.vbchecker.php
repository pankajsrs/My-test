<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.vbchecker.php
 * Type:     function
 * Name:     vbchecker
 * Purpose:  check string length and outputs a response
 * -------------------------------------------------------------
 */

 function smarty_function_vbchecker($params, &$smarty)
{
    $length = strlen($params['vbstring']);
    $output = "Your sentence is too long. Shorten It!";
    if($length < 50)
        $output = "Your sentence is just right!";
   
    return $output;
}
?>