<?PHP

/*$mdocInfo
 Author: Hauke Goos-Habermann (HHabermann@pc-kiel.de)
 Description: Helper functions that did not fit into another include file.
$*/





/**
**name HELPER_getNewLogLines($log, $sessionPrefix)
**description Gets the last (new) lines of a (growing) log file.
**parameter log: Name of the log file.
**parameter sessionPrefix: Prefix for storing the last read line number in the session.
**returns UTF8-encoded new lines of the log file.
**/
function HELPER_getNewLogLines($log, $sessionPrefix)
{
	$out = '';

	//Check for the log
	if (file_exists($log))
	{
		//Read the log into an array (each line as an array entry)
		$lines = file($log);
		$lastEntry = (count($lines));

		//Check for valid last line number
		if (!isset($_SESSION[$sessionPrefix]['lastLogLine']))
			$_SESSION[$sessionPrefix]['lastLogLine'] = 0;

		//Give out the new lines that were added since the last call
		for ($i = $_SESSION[$sessionPrefix]['lastLogLine']; $i < $lastEntry; $i++)
			$out .= utf8_encode($lines[$i]);

		//Update the last shown line number
		$_SESSION[$sessionPrefix]['lastLogLine'] = $lastEntry;
	}

	return($out);
}





/**
**name HELPER_rmRecursive($dir)
**description Removes a directory with sub-directories and contained files.
**parameter: dir: Full path to the directory.
**/
function HELPER_rmRecursive($dir)
{
	//Run thru the files an directories of the current directory
	foreach(glob($dir . '/*') as $fileOrDir)
	{
		//Check if it is a file => delete it
		if(file_exists($fileOrDir))
			unlink($fileOrDir);
		else
		//Check if it is a directory => run another instance of HELPER_rmRecursive
			HELPER_rmRecursive($fileOrDir);
	}
	//Remove the directory itself
	@rmdir($dir);
}





/**
**name HELPER_showBAfH()
**description Shows the German BAfH excuse of the day.
**/
function HELPER_showBAfH()
{
	include_once('/m23/inc/bafh_ausreden.php');
	$numExcuse = count($ausredenSammlung);
	$firstTime = 1335045600;
	$oneDay = 60 * 60 * 24;
	$now = time();
	$daysPassed = intval(($now - $firstTime) / $oneDay);
	$index = $daysPassed % $numExcuse;

	echo("
	<br>
	<table style=\"max-width:100%; border-width:9px; border-color:orange; border-style:double; padding:5px;\">
		<tr>
			<td width=105px>
				<img src=\"/gfx/rabe_links100.png\" alt = \"Rabe links\">
			</td>
			<td style=\"vertical-align:middle\">
				<i>Die BAfH (Bastard Administrator from Hell)-Ausrede des Tages lautet:</i><br>
				<p><span style=\"font-size:larger\"><i><b>".$ausredenSammlung[$index]."</b></i></span></p>
				<p align=\"right\"><span style=\"font-size:x-small\">CC-BY Florian Schiel<p>
			</td>
			<td width=105px>
				<img src=\"/gfx/rabe_rechts100.png\" alt = \"Rabe rechts\">
			</td>
		</tr>
	</table>
	");
}





/**
**name HELPER_ucrc32($in)
**description Returns the unsigned crc32 sum of an input value.
**parameter: in: Input to crc.
**returns Unsigned crc32 sum of an input value.
**/
function HELPER_ucrc32($in)
{
	return(sprintf("%u", crc32($in)));
}





/**
**name HELPER_md5x5($in)
**description Hashes an input value 5 times with MD5.
**parameter: in: Input to hash.
**returns Hashed value.
**/
function HELPER_md5x5($in)
{
	return(md5(md5(md5(md5(md5($in))))));
}





/**
**name HELPER_netmaskCalculator($nm)
**description Converts a short netmask (e.g. 24 for 255.255.255.0) into the decimal notation.
**parameter: nm: The netmask to convert. If a netmask in decimal notation is given, no conversation is done.
**returns Netmask in decimal notation.
**/
function HELPER_netmaskCalculator($nm)
{
	//Check if it is a decimal netmask (http://de.wikipedia.org/wiki/Classless_Inter-Domain_Routing#Eine_.C3.9Cbersicht)
	if (is_numeric($nm))
	{
		//Generate a valid binary netmask
		$bin = str_repeat(1, $nm);
		$bin .= str_repeat(0, 32-$nm);

		//Split it into octets
		$dec = bindec($bin);
		$d = $dec % 256;
		$c = ($dec /= 256) % 256;
		$b = ($dec /= 256) % 256;
		$a = ($dec /= 256) % 256;

		//Build the netmask in decimal notation
		$nm = "$a.$b.$c.$d";
	}

	return($nm);
}





/**
**name HELPER_importAllIntoPOST()
**description Imports all values into the $_POST array in case that there are too much array keys for the normal processing.
**/
function HELPER_importAllIntoPOST()
{
	foreach (explode("&", file_get_contents("php://input")) as $postline)
	{
		$tmp = explode("=", $postline);
		$_POST[$tmp[0]] = $tmp[1];
	}
}





/**
**name HELPER_randomUsername($length)
**description Generates a random username with a given length.
**parameter length: Length of the username to create.
**returns The random username.
**/
function HELPER_randomUsername($length)
{
	$out='';

	//All characters
	$allowedChars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

	//Add the wished amount of characters to the string
	for ($i=0; $i < $length; $i++)
	{
		//Add numbers after the first char has been added.
		if ($i == 1)
			$allowedChars .= "0123456789";
		$out .= $allowedChars[(mt_rand(0,(strlen($allowedChars)-1)))];
	}
	return($out);
}





/**
**name HELPER_createSelfSignedCAAndServerCertificate($CADn, $serverDn, $password, $expirationDate)
**description Creates a selfsigned CA and a server certificate.
**parameter CADn: Information about the CA.
**parameter serverDn: Information about the server.
**parameter password: Password for the private server key.
**parameter expirationDate: Expiration of the certificates in days.
**returns Associative array with the certificate of the CA, the certificate and private key of the server.
**/
function HELPER_createSelfSignedCAAndServerCertificate($CADn, $serverDn, $password, $expirationDate)
{
	/*
	$serverDn = array(
		"countryName" => "ZZ",
		"stateOrProvinceName" => "Z-State",
		"localityName" => "Z-Stadt",
		"organizationName" => "Z-Firma",
		"organizationalUnitName" => "Z-Abteilung",
		"commonName" => "server.zarafa.localhost",
		"emailAddress" => "wez@z.com"
	);

	$CADn = array(
		"countryName" => "DE",
		"stateOrProvinceName" => "Somerset",
		"localityName" => "Glastonbury",
		"organizationName" => "The Brain Room Limited",
		"organizationalUnitName" => "PHP Documentation Team",
		"commonName" => "Wez Furlong",
		"emailAddress" => "wez@example.com"
	);
	*/

	//Generate the CA
	$CAPrivkey = openssl_pkey_new();
	$CACSR = openssl_csr_new($CADn, $CAPrivkey);
	$cacert = openssl_csr_sign($CACSR, null, $CAPrivkey, $expirationDate);
	openssl_x509_export($cacert, $out['cacertPEM']);

	//Create the 
	$serverPrivkey = openssl_pkey_new();
	$serverCSR = openssl_csr_new($serverDn, $serverPrivkey);
	$sscert = openssl_csr_sign($serverCSR, $cacert, $CAPrivkey, $expirationDate);
	openssl_x509_export($sscert, $out['serverCert']);
	openssl_pkey_export($serverPrivkey, $out['serverPrivateKey'], $password);	
	openssl_pkey_export($serverPrivkey, $out['serverPrivateKeyUnencrypted']);

	return($out);
}





/**
**name HELPER_arrayReOrderKeynumbers($inArray)
**description Changes all keys of the input array to simple ascending numbers, if the key of the inpur array is a number (if not, the key will be left unchanged). The order of the keys is preserved.
**parameter inArray: The input array.
**returns New array with the ascending key numbers.
**/
function HELPER_arrayReOrderKeynumbers($inArray)
{
	//Change the keys to simple numbers
	$i = 0;
	foreach ($inArray as $key => $val)
	{
		if (is_numeric($key))
			$out[$i++] = $val;
		else
			$out[$key] = $val;
	}

	return($out);
}





/**
**name HELPER_arrayInsertBeforeKeynumber($inArray, $beforeKeynumber, $val)
**description Inserts a value into an array (that has simple numbers as keys) before a given key.
**parameter inArray: The input array.
**parameter beforeKeynumber: The key number the value should be inserted before.
**parameter val: The value that should be inserted.
**returns New array with the new value inserted.
**/
function HELPER_arrayInsertBeforeKeynumber($inArray, $beforeKeynumber, $val)
{
	//Make the key of the value we want to insert the new value before a bit bigger
	$inArray[$beforeKeynumber.'.2'] = $inArray[$beforeKeynumber];
	//Make the key of the new value a bit smaler
	$inArray[$beforeKeynumber.'.1'] = $val;
	//Remove the old entry
	unset($inArray[$beforeKeynumber]);

	//Sort the array
	ksort($inArray);

	return(HELPER_arrayReOrderKeynumbers($inArray));
}





/**
**name HELPER_arrayInsertAfterKeynumber($inArray, $afterKeynumber, $val)
**description Inserts a value into an array (that has simple numbers as keys) after a given key.
**parameter inArray: The input array.
**parameter afterKeynumber: The key number the value should be inserted after.
**parameter val: The value that should be inserted.
**returns New array with the new value inserted.
**/
function HELPER_arrayInsertAfterKeynumber($inArray, $afterKeynumber, $val)
{
	//Insert the value in the array with a new key that will be (after sorting) after the choosen key
	$inArray[$afterKeynumber.'.1'] = $val;

	//Sort the array
	ksort($inArray);

	return(HELPER_arrayReOrderKeynumbers($inArray));
}





/**
**name HELPER_m23Array2Array($m23Array)
**description Converts an m23 array to a normal array.
**parameter m23Array: The m23 array to convert. The m23 array is a 2D array, that consists of keys build of a parameter names combined with a parameter number. Parameter names with the same parameter number belong together. (e.g. [command0] => format, [path0] => /dev/md0, [fs0] => ext4, ...)
**returns A normal array, that may be edited more easyly. (e.g [0] => Array([command] => format, [path] => /dev/md0, [fs] => ext4 ))
**/
function HELPER_m23Array2Array($m23Array)
{
	foreach($m23Array as $param => $val)
	{
		/*
			Split the parameter (e.g. path0) into parameter name and parameter number:
			Example: $param="path0" => $allCommandNr[0]="path0", $allCommandNr[1]="path0", $allCommandNr[2]="0"
		*/
		preg_match('/([^0-9]+)([0-9]+)/', $param, $allCommandNr);

		//Check if the parameter number is set or if it is a variable that does not belong to a parameter group (e.g. job_amount)
		if (is_numeric($allCommandNr[2]))
			$array[$allCommandNr[2]][$allCommandNr[1]]=$val;
		else
		//Mark parameters not belonging to a parameter group with 'a'
			$array['a'][$param]=$val;
	}
	return($array);
}





/**
**name HELPER_array2m23Array($array)
**description Converts a normal array to an m23 array.
**parameter array: A normal array, that may be edited more easyly. (e.g [0] => Array([command] => format, [path] => /dev/md0, [fs] => ext4 ))
**returns The m23 array, that is a 2D array, that consists of keys build of a parameter names combined with a parameter number. Parameter names with the same parameter number belong together. (e.g. [command0] => format, [path0] => /dev/md0, [fs0] => ext4, ...)
**/
function HELPER_array2m23Array($array)
{
	//Run thru the normal array and get the parameter numbers and an array consisting of the parameter names and the values
	foreach($array as $paramNr => $paramNameVal)
	{
		//Run thru the parameter names and the values
		foreach($paramNameVal as $paramName => $val)
		{
			//If the parameter number is 'a' the parameter does not belong to a parameter group (=== is needed, because 0 == a)
			if ($paramNr === 'a')
				$m23array[$paramName] = $val;
			else
			//Otherwise the parameter will be stored under the key the is build of the paramete name combined with a parameter number.
				$m23array["$paramName$paramNr"] = $val;
		}
	}
	return($m23array);
}





/**
**name print_r2($in)
**description Function like print_r, but sorts the entries, if the input is an array and converts newlines to HTML breaks.
**parameter in: Value to print.
**/
function print_r2($in)
{
	if (is_array($in))
		ksort($in);

	print(nl2br(print_r($in,true)));
}





/**
**name HELPER_debugBacktraceToFile($file)
**description Writes/Appends debug information about all calling functions and parameters into a file.
**parameter file: File name with full path, where the debug information should be stored.
**/
function HELPER_debugBacktraceToFile($file)
{
	$msg = "";

	//Open the file for appending
	$fp = fopen($file,"a");

	foreach (debug_backtrace() as $debug)
		fputs($fp, "$debug[file]:$debug[line]\n==> $debug[function](".implode(",",$debug['args']).")\n\n");

	fclose($fp);
}





/**
**name HELPER_getRemoteFileContents($url, $storeFile, $refreshTime)
**description Downloads a file if it is not older than a given time and returns its contents.
**parameter url: The URL where to download the file from.
**parameter storeFile: The file name to store the download in.
**parameter refreshTime: The time in minutes the file is downloaded again.
**parameter forceOverwrite: Set to true if the file should be overwritten even if the new file is epmty.
**returns The contents of the files from chache or from download or false if no file could be found.
**/
function HELPER_getRemoteFileContents($url, $storeFile, $refreshTime, $forceOverwrite = true)
{
	$tempDir = "/m23/tmp/HELPER_getRemoteFileContents";
	@mkdir($tempDir);
	$filePath = "$tempDir/$storeFile";

	//Download the file if it doesn't exist or is too old
	if ((!is_file($filePath)) || ((time() - filemtime($filePath)) / 60) > $refreshTime)
	{
		//Download the file
		system("wget \"$url\" -O $filePath.temp -t1 -T5");
	}

	if (file_exists("$filePath.temp") && ((filesize("$filePath.temp") > 0) || $forceOverwrite))
		rename("$filePath.temp", $filePath);

	if (file_exists($filePath))
	{
		$file=fopen($filePath,"r");
		$text=fread($file,200000);
		fclose($file);
		return($text);
	}
	else
		return(false);
}





/**
**name HELPER_passGenerator($length,$amount = 1)
**description Generates semi-random passwords via pwgen or DB_genPassword.
**parameter length: The length of the passwords.
**parameter amount: The amount of passwords to generate.
**returns Array with the generated passwords if $amount > 1 or the password string directly if $amount = 1.
**/
function HELPER_passGenerator($length, $amount = 1)
{
	//check if pwgen is installed
	if (isProgrammInstalled("pwgen"))
		$passwords = MASS_passGenerator($firstPasswordLength,"pwgen",$length);
	else
		$passwords = MASS_passGenerator($firstPasswordLength,"random",$length);

	if ($amount == 1)
		return($passwords[0]);
	else
		return($passwords);
}





/**
**name HELPER_array2AssociativeArray($in)
**description Copies the values of an array as keys AND values to a new assiciative array.
**parameter in: Input array.
**returns Associative array with equal keys and values.
**/
function HELPER_array2AssociativeArray($in)
{
	$out = array();
	foreach($in as $val)
		$out[$val] = $val;

	return($out);
}





/**
**name HELPER_randomMAC()
**description Generates a random MAC address.
**returns Random MAC address in the format XX:XX:XX:XX:XX:XX (e.g. 70:c4:d4:49:6e:27).
**/
function HELPER_randomMAC()
{
	$mac = "00";
	for ($i = 0; $i < 5; $i++)
		$mac .= ":".substr(md5(rand()),0,2);
	return($mac);
}



/**
**name HELPER_generateSalt($length)
**description Generates a random salt string.
**parameter length: Length of the salt.
**returns Random salt of given length.
**/
function HELPER_generateSalt($length)
{
	/*
		This is based on the GPL code found at
		https://svn.linux.ncsu.edu/svn/cls/trunk/kscfg/kickstart-8.0/index.php
	*/
	$salt = "";

	//create a salt with $length characters
	for ($i=1; $i<=$length; $i++)
	{
		do
			{
				mt_srand((double)microtime()*1000000);
				//generate random numbers that are representing characters
				$num = mt_rand(46,122);
			}
		while ( ( $num > 57 && $num < 65 ) || ( $num > 90 && $num < 97 ) );

		//add the single random charactes until 6 are appended
		$salt = $salt.chr($num);
	}

	return($salt);
}





/**
**name HELPER_grubMd5Crypt($password)
**description Encrypts a password to the MD5 hash as expected by grub.
**parameter password: Password to encrypt.
**parameter length: Length of the salt.
**returns Encrypted password in grub style or false if MD5 hash function isn't available.
**/
function HELPER_grubMd5Crypt($password, $length=6, $algo="CRYPT_MD5")
{
	$salt = HELPER_generateSalt($length);

	//check if crypt supports MD5
	if ( constant($algo) == 1)
		return(crypt($password, '$1$'.$salt.'$'));
	else
		return(false);
}





/**
**name HELPER_listFilesInDir($dirname)
**description Lists all files in a directory and returns an array with all file names.
**parameter dirname: Name of the directory.
**returns Array with all file names.
**/
function HELPER_listFilesInDir($dirname)
{
	$dir=opendir($dirname);
	$i=0;

	while ($fileName = readdir($dir))
	if ($fileName != "." && $fileName != ".." && is_file("$dirname/$fileName"))
	{
		$out[$i] = $fileName;
		$i++;
	}

	if (count($out) > 1)
		sort($out);

	//close the directory handle
	closedir($dir);
	return($out);
}





/**
**name HELPER_getTimeZones()
**description Searches for all time zones.
**parameter country: two letter country name that is used to select a timezone if none is set with $first.
**returns Array with all time zones.
**/
function HELPER_getTimeZones($country = "")
{
	//get all timezones with country and city name
	exec("cd /usr/share/zoneinfo/posix/ ; find . -mindepth 1 | sort | sed 's/\.\///g'",$timezones);

	if (!empty($country))
	{
		switch ($country)
		{
			case "de":	$first = array("Europe/Berlin"); break;
			case "en":	$first = array("Europe/London"); break;
			case "fr":	$first = array("Europe/Paris"); break;
			default	 :	$first = array("UTC");
		};
		return(array_merge($first,$timezones));
	}
	else
	return($timezones);
};





/**
**name HELPER_calcMBSize($number,$from=0,$trunc=false)
**description calculates the size in MB from a given input that can be a GB value or measured in %
**parameter number: the number to convert
**parameter from: if number is a percent value, the output will be the percentage of the from value
**parameter trunc: set to true, if the output value should be trunced
**/
function HELPER_calcMBSize($number,$from=0,$trunc=false)
{
	//calculate the GB value
	if (!(stristr($number,"gb") === FALSE))
		{
			$number = preg_replace("/[gG][bB]/","",$number);
			$out = $number * 1024;
		}
		
	//calculate the percent value of
	elseif (!(stristr($number,"%") === FALSE))
		{
			//try to transform, may be there is a GB inside
			$from = HELPER_calcMBSize($from);
			
			$number = str_replace("%","",$number);
			$out = ($from * $number) / 100;
		}
	else
		$out = $number;

	//trunc the float number
	if ($trunc)
		$out = sprintf("%.0f",$out);

	return($out);
};





/**
**name HELPER_grep($string,$search,$cut="\n")
**description returnes all lines from $string seperated by $cut that contain $search
**parameter string: the text, that should be searched
**parameter search: the string to be searched
**parameter cut: seperator for the input and output lines
**/
function HELPER_grep($string,$search,$cut="\n")
{
	$parts=explode($cut,$string);
	$out="";

	for ($i=0; $i < count($parts); $i++)
		if (!(strpos($parts[$i],$search) === false))
			$out.=$cut.trim($parts[$i]);

	return($out);
};





/**
**name HELPER_getFdiskMountPoints($excludeExtra=true)
**description Returnes an array with all mount points listed in /etc/fstab
**parameter excludeExtra: set to true, if you want to exclude /proc and /sys from the array
**returns Found mount points as array keys and values.
**/
function HELPER_getFdiskMountPoints($excludeExtra=true)
{
	$extraGrep=" | grep -v '/proc$' | grep -v '/sys$' ";

	$cmd="grep -v \"^#\" /etc/fstab | awk '{print $2}' | grep '/' | grep -v \"^/$\" | sort";

	if ($excludeExtra)
		$cmd.=$extraGrep;

	$pipe=popen($cmd,"r");
	if ($pipe === FALSE)
		return("");

	while ($line=fgets($pipe))
	{
		$line = trim($line);
		$out[$line] = $line;
	}

	pclose($pipe);

	return($out);
}





/**
**name HELPER_getApacheUser()
**description returnes the name of the Apache user
**/
function HELPER_getApacheUser()
{
	return(exec("whoami"));
};





/**
**name HELPER_getApacheGroup()
**description returnes the group of the Apache user
**/
function HELPER_getApacheGroup()
{
	return(exec("id -gn"));
};





/**
**name HELPER_putFileContents($fileName, $contents)
**description Writes data to a file.
**parameter fileName: name of the file to write
**parameter contents: Text or data that should be written to the file.
**returns Error code from fwrite.
**/
function HELPER_putFileContents($fileName, $contents)
{
	$file=fopen($fileName,"w");
	$ret = fwrite($file,$contents);
	fclose($file);
	return($ret);
};




/**
**name HELPER_getFileContents($fileName)
**description returnes the contents of a file (the file is read to a maximum of 5 MB)
**parameter fileName: name of the file to read
**/
function HELPER_getFileContents($fileName)
{
	if (!file_exists($fileName))
		return(false);
		
	$file=fopen($fileName,"r");
	$text=fread($file,5242880); //max file size = 5MB
	fclose($file);
	return($text);
};





/**
**name HELPER_showFileContents($fileName)
**description Shows the contents of a file (the file is read to a maximum of 5 MB)
**parameter fileName: name of the file to read
**/
function HELPER_showFileContents($fileName)
{
	if (!file_exists($fileName))
		return(false);
		
	$file=fopen($fileName,"r");
	while ($line=fgets($file))
		echo($line);
	fclose($file);
	return(true);
};





/**
**name HELPER_getFileContents($fileName)
**description Returnes the maximum file upload size allowed by php.ini.
**/
function HELPER_maxPhpUploadSize()
{
	return(exec("grep '\(post_max_size\|upload_max_filesize\)' /etc/php4/apache/php.ini | cut -d' ' -f3 | sort -b -r | head -1"));
}

?>