<?php
/**
 * @(#) csvutils.php
 * @author Hasan hasan@improsys.com
 * @history
 * created  : Hasan : Date :24/04/2009 
 * @version 1.0
 * @purpose: For useing with qq.php, Temporary CSV creating & management insted of $_SESSION[] var
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


Class CsvUtils {

	var $savevar = array();
	var $csvfilename = '';
	
	function CsvUtils($filename){
		$this->csvfilename = $filename;
		$this->savevar['ts']="";
		$this->savevar['rnadurl']="";
		$this->savevar['uname']="";
		$this->savevar['pass']="";
		$this->savevar['cookie']="";
		$this->savevar['imgpath']="";
		$this->savevar['postarr']="";
	}
	
	function csv_write_file($stringtowite){
		if (!$handle = fopen($this->csvfilename, 'w')) {
			 print "Cannot open file ($this->csvfilename)";
			 exit;
		}
		if (!fwrite($handle, $stringtowite)) 
		{
			print "Cannot write to file ($this->csvfilename)";
			exit;
		}
		fclose($handle);
	}

	function csv_set_value($key, $value){
		$this->savevar[$key] = $value;
		$ret = "";
		$ret .= $this->savevar["ts"]."|";
		$ret .= $this->savevar["rnadurl"]."|";
		$ret .= $this->savevar["uname"]."|";
		$ret .= $this->savevar["pass"]."|";
		$ret .= $this->savevar["cookie"]."|";
		$ret .= $this->savevar["imgpath"]."|";
		$ret .= $this->savevar["postarr"]."\r\n";
		return $ret;
	}

	function csv_get_value($key){
		if (!$handle = fopen($this->csvfilename, 'r')) {
			 print "Cannot open file ($this->csvfilename)";
			 exit;
		}
		$contents = fread($handle, filesize($this->csvfilename));
		if ($contents == False)  {
			 print "Cannot open file for read ($this->csvfilename)";
			 exit;
		}
		for($c = 0; $c <= 6; $c++) {
			$line[$c] = ($c < 6) ? substr($contents,0,strpos($contents,"|")) : $contents;
			$nrl = substr($contents,strpos($contents,"|")+strlen("|"));
			$contents = $nrl;

		}
		$this->savevar["ts"] = $line[0];
		$this->savevar["rnadurl"] = $line[1];
		$this->savevar["uname"] = $line[2];
		$this->savevar["pass"] = $line[3];
		$this->savevar["cookie"] = $line[4];
		$this->savevar["imgpath"] = $line[5];
		$this->savevar["postarr"] = $line[6];
		fclose($handle);
		return $this->savevar[$key];
	}
};

function csv2array($csv, $byIndex = false, $delimiter = ',', $enclosure = '"', $escape = '\\', $terminator = "\n") {
    $r = array();
    $rows = explode($terminator,trim($csv));
    $names = array_shift($rows);
    $names = getcsvstr($names, $delimiter, $enclosure, $escape);
    $nc = count($names);
    foreach ($rows as $row) {
        if (trim($row)) {
            $values = getcsvstr($row, $delimiter, $enclosure, $escape);
            if (!$values) {
                $values = array_fill(0,$nc,null); 
            }
            $cn = count($names);
            $cv = count($values);
            $co = $cn - $cv;
            if($co < 0) {
                $values = array_slice($values, 0, $co);
            } else if($co > 0) {
                for($i = 0; $i < $co; $i++) {
                    $values[] = "";
                }
            }
            // echo "<pre>".print_r($values, true)."<pre>";
            // exit();
            if(!$byIndex) {
                $r[] = array_combine($names, $values);
            } else {
                $r[] = $values;
            }
        }
    }

    return $r;
}

function getcsvstr($csvstr, $delimiter = ',', $enclosure = '"', $escape = '\\') {
    $arr = explode($delimiter, $csvstr);
    foreach($arr as &$elem) {
        $elem = stripslashes(str_replace('"', '', $elem));
    }
    return $arr;
}
?>