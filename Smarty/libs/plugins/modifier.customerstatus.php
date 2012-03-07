<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty status modifier plugin
 *
 * Type:     modifier<br>
 * Name:     status<br>
 * Purpose:  change status
 * @link http://smarty.php.net/manual/en/language.modifier.wordwrap.php
 *          wordwrap (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @param string
 * @param integer
 * @param string
 * @param boolean
 * @return string
 */
function smarty_modifier_customerstatus($status, $operation = '')
{
	$customerstatus = '';
	# Order 
	switch($status)
	{
		case '1':  # Not Processed
		case '2':  # Process (API)
		case '11': # Process (Manual) 
			$customerstatus = tr('customer_status', 'pending');
			if(strtolower($operation) == 'transfer' && $status != '1')
				$customerstatus = tr('customer_status', 'transfer_progress');
		break;
		case '3': # Declined
		case '8': # Cancelled
		case '10': # Transfered
		case '9': # Expired
			$customerstatus = tr('customer_status', 'cancelled');
		break;
		case '4': # Awaiting
			$customerstatus = tr('customer_status', 'awaiting_information');
		break;
		case '5': # Awaiting confirmation
			$customerstatus = tr('customer_status', 'customer_confirmation');
		break;
		case '6': # Processed
			$customerstatus = tr('customer_status', 'processed');
			if(strtolower($operation) == 'transfer')
				$customerstatus = tr('customer_status', 'transfer_completed');
			else if(strtolower($operation) == 'register')
				$customerstatus = tr('customer_status', 'registered');
 		break;
		case '7': # Failed
			$customerstatus = tr('customer_status', 'failed');
			if(strtolower($operation) == 'transfer')
				$customerstatus = tr('customer_status', 'transfer_failed');				
		break;
		case '12': # Awaiting documents
			$customerstatus = tr('customer_status', 'formal_documents');
		break;
		case '13': # Awaiting EPP code
			$customerstatus = tr('customer_status', 'awaiting_epp_code');
		break;
	}
	echo $customerstatus;
}

?>
