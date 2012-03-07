<?php
 
function smarty_modifier_debug()
{
	if(empty($_SESSION['debug']) || !DEBUG)
		return '';

	$SqlLog =  '<table cellpadding="6" style="width:100%" cellspacing="0" border="0" id="SqlLog" >		
		<thead>
		<tr style="background-color:#545454;color:#FFFFFF;text-align: left;" ><th>Sr. Nr</th><th>Query</th><th style="text-align: right" >Error</th><th style="text-align: right" >Affected</th><th style="text-align: right" >Num. rows</th><th style="text-align: right" >Took (ms)</th></tr>
		</thead>
	<tbody>
	';
	$m=1;
	$Took = 0;
	foreach($_SESSION['debug'] as $key => $value) {	 
		$SqlLog .= '<tr>
					<td  valign="top">'.$m++.'</td>
					<td  valign="top">'.$value['Query'].'</td>
					<td  valign="top">'.$value['Error'].'</td>
					<td style="text-align: right">'.$value['Affected'].'</td>
					<td style="text-align: right">'.$value['Num'].'</td>
					<td style="text-align: right">'.$value['Took'].'</td>
			</tr>';
			$Took = $Took+$value['Took'];
	}
	 
	$SqlLog .='<caption><b>(default) '.count($_SESSION['debug']).' queries took '.ceil($Took).' ms</b></caption>	</tbody></table>
	 
	';

	return $SqlLog;
}

?>