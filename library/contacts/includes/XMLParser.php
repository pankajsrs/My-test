<?php
/**
 * @(#) XMLParser.php
 * @author Sumon sumon@improsys.com
 * @history
 *          created  : Md.Nuruzzaman Iran : Date : 09-09-2008
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

class xml  {
    var $parser;
	var $currentTag;
	var $address=array();
	var $i;
	var $getaddress;
	var $getFname;
	var $getLname;
	var $startPoint;
	var $tmpAddress;
	
    function xml() 
    {
        $this->parser = xml_parser_create();

        xml_set_object($this->parser, $this);
        xml_set_element_handler($this->parser, "tag_open", "tag_close");
        xml_set_character_data_handler($this->parser, "cdata");
    }
	
	function collectName()
	{
		for($j=0;$j<count($this->address['email']);$j++)
		{
		  if((trim($this->address['name'][$j]))=="")
		  {
		   	$this->address['name'][$j] = substr($this->address['email'][$j],0,strpos($this->address['email'][$j],"@"));
		  }	
		}	
	}
	
    function parse($data) 
    {
	
        $data=eregi_replace(">"."[[:space:]]+"."< ",">< ",$data);
		$this->i=0;
		xml_parse($this->parser, $data);
		$this->collectName();
		return $this->address;
    }

    function tag_open($parser, $tag, $attributes) 
    {
		if(!strcmp($tag , "ADDRESS"))
		{
			$this->currentTag = "ADDRESS";
			$this->getAddress = true;
		}
		else if(!strcmp($tag , "LASTNAME"))
		{
			$this->currentTag = "LASTNAME";
			$this->getLname = true;
		}
		else if(!strcmp($tag , "FIRSTNAME"))
		{
			$this->currentTag = "FIRSTNAME";
			$this->getFname = true;
		}
		/*else if(!strcmp($tag , "NAME"))
		{
			$this->currentTag = "NAME";
			$this->getLname = true;
		}*/
		else
			$this->currentTag = "Blank";
    }

    function cdata($parser, $cdata) 
    {
        if(!strcmp($this->currentTag , "ADDRESS") && $this->getAddress)
		{
			$this->address['email'][$this->i]= $cdata;
			$this->getAddress = false;
		}
		else if(!strcmp($this->currentTag , "FIRSTNAME") && $this->getFname)
		{
			$this->address['name'][$this->i] = $cdata;
			$this->getFname = false;
		}
		else if(!strcmp($this->currentTag , "LASTNAME") && $this->getLname)
		{
			$temp = $this->address['name'][$this->i];
			$this->address['name'][$this->i] = $temp." ".$cdata;
			$this->getLname = false;
		}
		/*else if(!strcmp($this->currentTag , "NAME") && $this->getLname)
		{
			$temp = $this->address['name'][$this->i-1];
			$this->address['name'][$this->i-1] = $cdata;
			$this->getLname = false;
		}*/
		
    }

    function tag_close($parser, $tag) 
    {
        if(!strcmp($tag , "CONTACT"))
		{
			if(!isset($this->address['email'][$this->i]))
				{
					$this->address['email'][$this->i]="";
				}
			$this->i++;
		}
		
    }

} // end of class xml

?> 