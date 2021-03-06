<?php

/*$mdocInfo
 Author: Daniel Kasten (DKasten@pc-kiel.de), Hauke Goos-Habermann (hauke@goos-habermann.de)
 Description: different checks for validation of email, ip, netmasks, etc.
$*/

/*
	Variable checking functions
	Copyright (C) 2005-2009 Hauke Goos-Habermann

	This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or any later version.

	This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

	You should have received a copy of the GNU General Public License along with this program; see the file COPYING.  If not, write to
	the Free Software Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
*/


define('CONF_ALLOWEDCHARACTERS','#?.,+*=@�������\!@:\-/_ )(');
define('CC_clientname','S64');
define('CC_client',CC_clientname);
define('CC_language','s5');
define('CC_status','i');
define('CC_statusOrEmpty','ie');
define('CC_firstpw','sn');
define('CC_rootpassword',0);
define('CC_installdate','i');
define('CC_dns2','pe');
define('CC_dns1','pe');
define('CC_gateway','p');
define('CC_netmask','p');
define('CC_ip','p');
define('CC_mac','An12');
define('CC_email','ee');
define('CC_familyname','s40');
define('CC_forename','s40');
define('CC_name',CC_forename);
define('CC_office','se');
define('CC_id','i');
define('CC_lastmodify','i');
define('CC_groupname','s200');
define('CC_groupnameOrEmpty','Ae');
define('CC_biggerEqualSmaler','V>=<');
define('CC_package','sn255');
define('CC_packagestatus','sn2');
define('CC_packageOrEmpty','se255');
define('CC_packageselectionname','sn255');
define('CC_jobstatus','sn10');
define('CC_jobstatusOrEmpty','se10');
define('CC_packagestatusOrEmpty','se10');
define('CC_nochecknow',0);
define('CC_packagesize','i');
define('CC_packagepriority','i');
define('CC_packageid','i');
define('CC_packagesourcename','sn255');
define('CC_capturestep','i');
define('CC_page','sn100');
define('CC_packageversion','sn255');
define('CC_packageproxy','se');
define('CC_packageport','ie');
define('CC_partitionpath','sn');
define('CC_partitionnr','i');
define('CC_partitionstart','i');
define('CC_partitionend','i');
define('CC_partitiontype','se');
define('CC_partitionfs','sn');
define('CC_partitionamount','i');
define('CC_partitionsize','i');
define('CC_remotevarip','sn255');
define('CC_remotevarvar','sn255');
define('CC_sourceslistname','sn255');
define('CC_sourceslistdistr','sn255');
define('CC_sourceslistrelease','sn255');
define('CC_sourceslistdescription','se');
define('CC_sourceslistlist',0);
define('CC_sourceslistdesktops','se');
define('CC_sourceslistarchs','sn');
define('CC_clientpreferencesname','sn255');
define('CC_clientpreferencesvar','sn255');
define('CC_clientpreferencesvalue','sn');
define('CC_vmsoftware','i');
define('CC_memory',0);
define('CC_hd',0);
define('CC_partitions',0);
define('CC_cpu',0);
define('CC_mhz',0);
define('CC_netcards',0);
define('CC_graficcard',0);
define('CC_soundcard',0);
define('CC_isa',0);
define('CC_dmi',0);
define('CC_dhcpbootimage','s');
define('CC_options',0);
define('CC_vmrunonhost','i');
define('CC_vmrole','i');
define('CC_vmvisualpassword',0);
define('CC_vmvisualurl',0);
define('CC_debconfVarName','s255');
define('CC_debconfVarType','s255');
define('CC_debconfValue',0);
define('CC_statusBarName','sn255');
define('CC_percent','f');
define('CC_statusBarText','sn');
define('CC_statusBarType','sn4');
define('CC_statusBarID','i');
define('CC_poolName','An255');





/**
**name CHECK_safeFilename($fileName)
**description Make sure, the file/directory name is safe and doesn't contain evil characters.
**parameter fileName: File/directory name to make safe.
**returns The safe made file/directory name.
**/
function CHECK_safeFilename($fileName)
{
	return(trim($fileName, "\x00..\x1F."));
}





/**
**name CHECK_text2db($val,$like)
**description Makes a text safe for using it in the database.
**parameter val: Text to use;
**parameter like: Set to true if the text should be used as the LIKE parameter.
**returns The safe made text.
**/
function CHECK_text2db($val,$like = false)
{
	if (get_magic_quotes_gpc())
		$val = stripslashes($val);

	$val = mysql_real_escape_string($val);
	if ($like)
		$val = addcslashes($val, "%_");

	return((string)$val);
}





/**
**name CHECK_db2text($val)
**description Converts a string from the DB format to a normal string.
**parameter val: String to use.
**returns The safe made text.
**/
function CHECK_db2text($val)
{
	$val=(string)$val;

	$val = stripslashes($val);

	return($val);
}





/**
**name CHECK_FW()
**description Variable checking firewall, that checks a bunch of variables if they contain only valid characters.
**parameter List of parameters, where the first of two is the checking parameter and the second the value to check.
**/
function CHECK_FW()
{
	$amount = func_num_args();
	
	if (func_get_arg(0) === true)
	{
		$startI = 1;
		$returnNoDie = true;
	}
	else
	{
		$startI = 0;
		$returnNoDie = false;
	}

	for($i = $startI; $i < $amount; $i+=2)
	{
		$typeS = func_get_arg($i);
		$val = func_get_arg($i + 1);
		
		if (empty($val))
			continue;

		//Store debuging info for CHECK_letFWDie
		$GLOBALS['CHECK_FW_DEBUG_typeS'] = $typeS;
		$GLOBALS['CHECK_FW_DEBUG_val'] = $val;

		switch ($typeS{0})
		{
			case "i":
				//i:	integer and empty value disallowed
				//ie:	integer and empty value allowed
				return(CHECK_int($val, $typeS{1} == "e", $returnNoDie));
				break;

			case "f":
				//f:	float and empty value disallowed
				//fe:	float and empty value allowed
				return(CHECK_float($val, $typeS{1} == "e", $returnNoDie));
				break;

			case "a":
				//a:	string only with characters (no numbers, no special characters) and unlimited length and NO empty allowed
				//ae:	string only with characters (no numbers, no special characters) and unlimited length and empty allowed
				//an10:	string only with characters (no numbers, no special characters) and 10 characters maximum and NO empty allowed
				//ae10:	string only with characters (no numbers, no special characters) and 10 characters maximum and empty allowed
				return(CHECK_strAlpha($val, (isset($typeS{2}) ? substr($typeS,2) : 0), $typeS{1} == "e", $returnNoDie));
				break;

			case "A";
				//A:	string only with characters (no special characters) and unlimited length and NO empty allowed
				//Ae:	string only with characters (no special characters) and unlimited length and empty allowed
				//An10:	string only with characters (no special characters) and 10 characters maximum and NO empty allowed
				//Ae10:	string only with characters (no special characters) and 10 characters maximum and empty allowed
				return(CHECK_strAlphaNum($val, (isset($typeS{2}) ? substr($typeS,2) : 0), $typeS{1} == "e", $returnNoDie));
				break;

			case "s":
				//s:	string with allowed characters or numbers and unlimited length but no empty string allowed
				//se:	string with allowed characters or numbers and unlimited length AND empty string allowed
				//sn10:	string with allowed characters or numbers and maximum length of 10 and NO empty string allowed
				return(CHECK_str($val,(isset($typeS{2}) ? substr($typeS,2) : 0), $typeS{1} == "e", $returnNoDie));
				break;

			case "p":
				//p:	valid IPv4 address or netmask allowed
				//pe:	valid IPv4 address or netmask or empty allowed
				if ((($typeS{1} != "e") && empty($val)) || !checkIP($val))
				{
					if ($returnNoDie)
						return(false);
					else
						CHECK_letFWDie("CHECK_FW error: IP invalid!");
				}
				break;

			case "e":
				//e:	valid eMail address allowed
				//ee:	valid eMail address or empty allowed
				if (empty($val))
				{
					if ($typeS{1} != "e")
					{
						if ($returnNoDie)
							return(false);
						else
							CHECK_letFWDie("CHECK_FW error: eMail invalid!");
					}
				}
				elseif (!checkEmail($val))
				{
					if ($returnNoDie)
						return(false);
					else
						CHECK_letFWDie("CHECK_FW error: eMail invalid!");
				}
				break;

			case "v":
				//vabc:	valid are only a,b,c
				if (@strpos($typeS, $val, 1) === false)
				{
					if ($returnNoDie)
						return(false);
					else
						CHECK_letFWDie("CHECK_FW error: Character not found in the list of allowed characters!");
				}
				break;

			case "V":
				//Vabc:	valid are only a,b,c or empty string
				if ( isset($val{0}) && (strpos($typeS, $val, 1) === false))
				{
					if ($returnNoDie)
						return(false);
					else
						CHECK_letFWDie("CHECK_FW error: Character not found in the list of allowed characters!");
				}
				break;

			case 0:
				//0:	all allowed
				break;

			default:
				CHECK_letFWDie("FW error: Unknown command \"$typeS\"");
		}
	}
}





/**
**name CHECK_int($val,$allowEmpty=false, $returnNoDie=false)
**description Checks if the input value is an integer and shuts down the application if not.
**parameter val: Input value to check.
**parameter allowEmpty: Set to true if you want to allow empty strings.
**parameter returnNoDie: Set to true if you want to return (instead of aborting the program) when an error in the input is found.
**returns The input value if it's an integer or false on an error.
**/
function CHECK_int($val, $allowEmpty=false, $returnNoDie=false)
{
	if (is_numeric($val))
		return((int)$val);
	elseif ($allowEmpty && !isset($val{0}))
		return((string)"");

	if ($returnNoDie)
		return(false);
	else
		CHECK_letFWDie("CHECK_FW error: No integer!");
};





/**
**name CHECK_float($val, $allowEmpty=false, $returnNoDie=false)
**description Checks if the input value is a float number and shuts down the application if not.
**parameter val: Input value to check.
**parameter allowEmpty: Set to true if you want to allow empty strings.
**parameter returnNoDie: Set to true if you want to return (instead of aborting the program) when an error in the input is found.
**returns The input value if it's a float number or false on an error.
**/
function CHECK_float($val, $allowEmpty=false, $returnNoDie=false)
{
	//set language of language specific functions to german
	//doesn't work for me:
	setlocale (LC_ALL, 'de_DE@euro', 'de_DE', 'de', 'ge');
	$val = str_replace (",",".",$val);
	if (is_numeric($val))
		return((float)$val);
	elseif ($allowEmpty && !isset($val{0}))
		return((string)"");

	if ($returnNoDie)
		return(false);
	else
		CHECK_letFWDie("CHECK_FW error: No float!");
};





/**
**name CHECK_strAlpha($val, $maxlen=0, $allowEmpty=false, $returnNoDie=false)
**description Checks if the input value is a string that contains only characters and shuts down the application if not.
**parameter val: Input value to check.
**parameter maxlen: The maximal length of the the string or 0 if the string length doesn't matter.
**parameter allowEmpty: Set to true if you want to allow empty strings.
**parameter returnNoDie: Set to true if you want to return (instead of aborting the program) when an error in the input is found.
**returns The input value if it contains only characters or stops (or false, if $returnNoDie is true) the program on an error.
**/
function CHECK_strAlpha($val, $maxlen=0, $allowEmpty=false, $returnNoDie=false)
{
	//set language of language specific functions to german
	setlocale(LC_CTYPE, "german");
	if (ctype_alpha($val) && (($maxlen==0) || (!isset($val{$maxlen}))))
		return((string)$val);
	elseif ($allowEmpty && !isset($val{0}))
		return((string)"");

	if ($returnNoDie)
		return(false);
	else
		CHECK_letFWDie("CHECK_FW error: No alpha!");
};





/**
**name CHECK_strAlphaNum($val, $maxlen=0, $allowEmpty=false, $returnNoDie=false)
**description Checks if the input value is a string that contains only characters and digits and shuts down the application if not.
**parameter val: Input value to check.
**parameter maxlen: The maximal length of the the string or 0 if the string length doesn't matter.
**parameter allowEmpty: Set to true if you want to allow empty strings.
**parameter returnNoDie: Set to true if you want to return (instead of aborting the program) when an error in the input is found.
**returns The input value if it contains only characters and digits or stops (or false, if $returnNoDie is true) the program on an error.
**/
function CHECK_strAlphaNum($val, $maxlen=0, $allowEmpty=false, $returnNoDie=false)
{

	//set language of language specific functions to german
	setlocale(LC_CTYPE, "german");
	if (ctype_alnum($val) && (($maxlen==0) || (!isset($val{$maxlen}))))
		return((string)$val);
	elseif ($allowEmpty && !isset($val{0}))
		return((string)"");

	if ($returnNoDie)
		return(false);
	else
		CHECK_letFWDie("CHECK_FW error: No alpha or numeric!");
};





/**
**name CHECK_letFWDie($dieMessage)
**description Lets the variable checking firewall die with error message and info why and where it stopped executing the script.
**parameter dieMessage: Message to show if the script should be stopped.
**/
function CHECK_letFWDie($dieMessage)
{
	$msg = "
	<h2>Variable firewall error</h2>
	<ul>
		<li>Error message: $dieMessage</li>
		<li>Check type: \"$GLOBALS[CHECK_FW_DEBUG_typeS]\"</li>
		<li>Value: \"$GLOBALS[CHECK_FW_DEBUG_val]\"</li>
	</ul>
	<h3>Trace</h3>
	<ol>";
	foreach (debug_backtrace() as $debug)
	{
		if (strpos($debug['function'],"CHECK_") === false)
			$msg.="\n<li>$debug[function](".implode(",",$debug['args']).")@$debug[file]:$debug[line]</li>";
	}
	$msg .= "\n</ol>\n";
	
// Make time hacking harder
//	usleep(rand(1253345,8235235));
	
	die($msg);
}





/**
**name CHECK_str($val,$maxlen=0,$allowEmpty=false, $returnNoDie=false)
**description Checks if the input string only contains valid characters and is not longer than the maximum length and shuts down the application if not.
**parameter val: String value to check.
**parameter maxlen: The maximal length of the the string or 0 if the string length doesn't matter.
**parameter allowEmpty: Set to true if you want to allow empty strings.
**parameter returnNoDie: Set to true if you want to return (instead of aborting the program) when an error in the input is found.
**returns The input string or stops the program on an error.
**/
function CHECK_str($val, $maxlen=0, $allowEmpty=false, $returnNoDie=false)
{
	$val=(string)$val;
	//set language of language specific functions to german
	setlocale(LC_CTYPE, "german");

	//the value must be shorter than $malen
	//and only some letters are allowed
	if ((($maxlen==0) || (!isset($val{$maxlen}))) && (preg_match("![^-'[:alpha:][:digit:]".CONF_ALLOWEDCHARACTERS." \t\n\r]!", $val)==0))
		return((string)$val);
	elseif ($allowEmpty && !isset($val{0}))
		return((string)"");

	if ($returnNoDie)
		return(false);
	else
		CHECK_letFWDie("CHECK_FW error: Not allowed characters found!");
};





/**
**name countLinesInFile($dateiname)
**description counts the lines of a file, return value is the amount of lines
**parameter dateiname: file name
**/
function countLinesInFile($dateiname)
 {
	$fp = fopen("$dateiname","r");
	$counter = 0;
	while( ! feof($fp) )
		{
			fgets($fp,1024);
			$counter++;
		}
	//fclose("$dateiname");
	return $counter;
 }





/**
**name checkIP($string)
**description checks if an ip is valid
**parameter string: ip value to check
**returns true if IP is valid, else false
**/
function checkIP($string)
{
	if (ip2longSafe($string) !== false)
		return(true);
	else
		return(false);
}





/**
**name checkMAC($mac)
**description Checks if a MAC address is valid.
**parameter mac: MAC address to test.
returns: true, if the input is a valid MAC otherwise false.
**/
function checkMAC($mac)
{
	return(preg_match('/^[a-fA-F0-9]{12}$/', $mac) === 1);
}





/**
**name checkNetmask($string)
**description checks if a netmask is valid
**parameter string: netmask value to check
**returns true if netmask is valid, else false
**/
function checkNetmask($string)
{
	$a = ip2longSafe($string);

	if (($a === false) || ($a === 0) || ($a == 4294967295))
		return (false);

	if ($a !== false)
	{
		$i = 31;
		while ($a > 0)
		{
			$a = $a - pow(2, $i);
			$i --;
		}

		if ($a == 0)
			return(true);
		else
			return(false);
	}
}





/**
**name checkEmail($string)
**description checks if a email address is valid, returns 1 if it is a valid netmask otherwise 0
**parameter string: email address value to check
**/
function checkEmail($string)
{
	preg_match("/.+@.+\.[a-z0-9]+/i",$string,$tempvar);
	if( !empty($tempvar[0]) )
		return(true);

	return(false);
}





/**
**name checkFQDN($string)
**description Checks if a string contains only characters that are allowed in a FQDN.
**parameter string: string to check for special characters
**/
function checkFQDN($string)
{
	preg_match("/[^a-z0-9_.-]+/i",$string,$tempvar);
	if( !empty($tempvar[0]) )
		return(0);

	return(1);
}





/**
**name checkNormalKeys($string)
**description checks if a string doesn't contain any special letters, returns 1 if it doesn't contain special characters otherwise 0
**parameter string: string to check for special characters
**/
function checkNormalKeys($string)
{
	preg_match("/[^a-z0-9_]+/i",$string,$tempvar);
	if( !empty($tempvar[0]) )
		return(0);

	return(1);
}



function CHECK_diskFree()
{
	$df = disk_free_space("/");
};
 ?>
