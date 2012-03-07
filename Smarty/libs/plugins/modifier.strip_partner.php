<?php

function smarty_modifier_strip_partner($number)
{
	
	if(empty($number)) return;
	if(strpos($number, '-') === false) return $number;

	list($partner, $number) = @split("-", $number);
	return trim($number);
}