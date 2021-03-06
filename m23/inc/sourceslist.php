<?PHP

/*$mdocInfo
 Author: Hauke Goos-Habermann (HHabermann@pc-kiel.de)
 Description: function to generate the sources.list for the client
$*/





/**
**name SRCLST_getAddToFile($sourceName)
**description Returns addToFile paremters from the given sources list as an associative array, where file name and file contents are seperated.
**parameter sourceName: The name of the package source list
**returns: Associative array with file name and file contents (e.g. [0] => Array ([file] => file1.txt, [text] => text1), [1] => Array ([file] => file2.txt, [text] => text2), ...)
**/
function SRCLST_getAddToFile($sourceName)
{
	/*
		The input lines have the folliwing format:
		#addToFile:<File name to store the text in>###line 1###line 2###line 3###line 4
	*/

	//Get all lines with addToFile parameter
	$lines = SRCLST_getParameter($sourceName, 'addToFile');
	$out = array();
	$i=0;

	foreach ($lines as $line)
	{
		//Newlines are represented by '###'
		$temp = explode('###',$line);
		//The first string in the array is the file name
		$out[$i]['file'] = $temp[0];
		//Remove the file name from the array
		array_shift($temp);
		//Combine all other elements to new lines (this is the text for the file)
		$out[$i]['text'] = implode("\n",$temp);
		$i++;
	}

	return($out);
}





/**
**name SRCLST_getRelease($name)
**description Gets a release from the sourceslist table.
**parameter name: the name of the package source list
**returns Release name of choosen sources list.
**/
function SRCLST_getRelease($name)
{
	CHECK_FW(CC_sourceslistname, $name);
	return(SRCLST_getValue($name, 'release'));
};





/**
**name SRCLST_genList($clientName)
**description generates the sources.list file for the client
**parameter clientName: the name of the client
**/
function SRCLST_genList($clientName)
{
	$allOptions = CLIENT_getAllOptions($clientName);

	$packageSource = $allOptions['packagesource'];

	//Add the Debian/Ubuntu extraDebs repository only, if it is not a halfSister distribution
	if ($allOptions['distr'] == 'halfSister')
		$addExtraDeb = "";
	else
		$addExtraDeb = "\ndeb http://".getServerIP()."/extraDebs/ ./";

	return(SRCLST_loadSourceList($packageSource).$addExtraDeb);
};





/**
**name SRCLST_saveArchitectures($sourceName, $archs)
**description Saves the architectures for package source list.
**parameter sourceName: the name of the package source list
**parameter archs: Associative array with the supported CPU architectures.
**/
function SRCLST_saveArchitectures($sourceName, $archs)
{
	$archs = implode('###',$archs);
	CHECK_FW(CC_sourceslistname, $sourceName, CC_sourceslistarchs, $archs);
	return(db_query("UPDATE `sourceslist` SET `archs` = '$archs' WHERE `name` = '$sourceName' LIMIT 1")); //FW ok
}





/**
**name SRCLST_saveList($name,$list,$description,$distr,$release)
**description saves the package source list
**parameter name: the name of the package source list
**parameter list: the list of sources as simple text
**parameter description: a descriptive text for the list
**parameter distr: the name of the distribution the list is for
**parameter release: the name of the release the list is for
**/
function SRCLST_saveList($name,$list,$description,$distr,$release="")
{
	$desktops = DISTR_getSelectedDesktopsStr();

	CHECK_FW(CC_sourceslistname, $name, CC_sourceslistdescription, $description, CC_sourceslistdistr, $distr, CC_sourceslistrelease, $release, CC_sourceslistdesktops, $desktops, CC_sourceslistlist, $list);

	//Make sure that sources lists with special characters are stored correctly.
	$list = CHECK_text2db($list);

	$sql="SELECT count(*) FROM `sourceslist` WHERE `name`='$name'";
	$result=db_query($sql); //FW ok

	$line=mysql_fetch_row($result);

	if ($line[0] > 0)
		$sql="UPDATE `sourceslist` SET `list` = '$list', `description` = '$description', `distr` = '$distr', `release` = '$release', `desktops` = '$desktops' WHERE `name` = '$name' LIMIT 1";
	else
		$sql="INSERT INTO `sourceslist` ( `name` , `list` , `description` , `distr` , `release` , `desktops`) VALUES ('$name','$list','$description','$distr','$release','$desktops')";

	$sourceslist="/m23/var/cache/m23apt/$distr/$name/sources.list";
	//delete sources.list to make the package cache refresh after
	if (file_exists($sourceslist))
		unlink($sourceslist);

	PKG_updateSourcesListAtAllClients($name);

	return(db_query($sql)); //FW ok
};





/**
**name SRCLST_querySourceslists($distr)
**description returns the result of the DB query after sourceslists for a special distribution
**parameter distr: the distribution the sources list is for or "*" for all distributions
**/
function SRCLST_querySourceslists($distr)
{
	if ($distr != "*")
	{
		CHECK_FW(CC_sourceslistdistr, $distr);
		$addQuery="WHERE distr='$distr'";
	}

	$sql = "SELECT name FROM `sourceslist` $addQuery ORDER BY name";

	$result = db_query($sql); //FW ok
	return($result);
}





/**
**name SRCLST_genSelection($selName, $first, $distr)
**description generates a HTML selection with the names of alls package sources
**parameter selName: the name of the selection
**parameter first: the package source that should be shown first
**parameter distr: the distribution the sources list is for or "*" for all distributions
**/
function SRCLST_genSelection($selName, $first, $distr)
{
	$result=SRCLST_querySourceslists($distr);

	$out="<select name=\"$selName\" size=\"1\">";

	if (strlen($first) > 0)
		$out.="<option>$first</option>";

	while ($line=mysql_fetch_row($result))
		if ($line[0] != $first)
			$out.="<option>".$line[0]."</option>";

	$out.="</select>";

	return($out);
};





/**
**name SRCLST_getValue($name,$var)
**description gets a value from the sourceslist table
**parameter name: the name of the package source list
**parameter var: the name of the table row
**/
function SRCLST_getValue($name,$var)
{
	if (($name == 'imaging') && ($var == 'release'))
	return('imaging');

	CHECK_FW(CC_sourceslistname, $name, "s", $var);

	$sql = "SELECT `$var` FROM `sourceslist` WHERE name='$name'";

	$result = db_query($sql); //FW ok

	$line = mysql_fetch_row($result);

	if ('list' == $var)
		$line[0] = CHECK_db2text($line[0]);

	return ($line[0]);
};





/**
**name SRCLST_loadSourceList($name)
**description loads and returnes the the package source list
**parameter name: the name of the package source list
**/
function SRCLST_loadSourceList($name)
{
	return(SRCLST_getValue($name,"list"));
};





/**
**name SRCLST_getDescription($name)
**description returnes the the package source description
**parameter name: the name of the package source list
**/
function SRCLST_getDescription($name)
{
	return(SRCLST_getValue($name,"description"));
};





/**
**name SRCLST_delete($name)
**description deletes package source
**parameter name: the name of the package source list
**/
function SRCLST_delete($name)
{
	CHECK_FW(CC_sourceslistname, $name);

	$sql = "DELETE FROM `sourceslist` WHERE `name` = '$name'";

	return(db_query($sql)); //FW ok
}





/**
**name SRCLST_checkList($sourceName)
**description checks a package info and returns the output of the OS package update function
**parameter sourceName: the name of the package source list
**/
function SRCLST_checkList($sourceName, $arch)
{
	$distr = SRCLST_getValue($sourceName,"distr");

	include_once("/m23/inc/distr/$distr/packages.php");

	//Check if the function for testing the package sources list exists.
	if (!function_exists('PKG_updatePackageInfo'))
	{
		//Show an error message, if it is not possible.
		include("/m23/inc/i18n/".$GLOBALS["m23_language"]."/m23base.php");
		MSG_showError($I18N_testingOfTheSourcesListNotPossible);
		return(false);
	}

	$logFile = PKG_updatePackageInfo($distr,$sourceName,true,$arch);

	//there was an error updating the package source
	if (!$logFile)
		return("");

	$FILE=fopen($logFile,"r");
	if (!$FILE)
		return("");

	$out = "";
	


	while (!feof($FILE))
		{
			$line = fgets($FILE,10000);

			$out .= nl2br(wordwrap($line));
		};

	fclose($FILE);

	return($out);
};





/**
**name SRCLST_packageInformationOlderThan($minutes,$distr,$sourcename)
**description checks if a package info is older than a selected amount of minutes
**parameter minutes: the amount of minutes the package information can be older to return true
**parameter distr: the short name of the distribution
**parameter sourceName: the name of the package source list
**/
function SRCLST_packageInformationOlderThan($minutes,$distr,$sourcename)
{
	$statusFile = "/m23/var/cache/m23apt/$distr/$sourcename/status";

	//clear the cache that caches informations about the access time of files
	clearstatcache();

	return ((!file_exists('/m23/etc/offlineMode')) &&
		((!file_exists($statusFile)) || (((time()-filectime($statusFile))/60) > $minutes)));
};





/**
**name SRCLST_getStorageFS($fs, $sourceName)
**description Returns a file systems that can be used to install the OS and to store data. A wished file system is given and an alternative FS is returned, if this FS is not supported.
**parameter fs: File system to probe.
**parameter sourceName: The name of the package source list
**returns: File systems that can be used to install the OS and to store data
**/
function SRCLST_getStorageFS($fs, $sourceName)
{
	if (in_array($fs,SRCLST_supportedFS($sourceName)) || ('linux-swap' == $fs) || ('auto' == $fs))
		return($fs);
	else
		return(SRCLST_alternativeFS($sourceName));
}





/**
**name SRCLST_supportedFS($sourceName)
**description Returns an array with file systems that supported by the OS.
**parameter sourceName: The name of the package source list
**returns: Array with file systems supported by the OS.
**/
function SRCLST_supportedFS($sourceName)
{
	$line = SRCLST_getParameter($sourceName, 'supportedFS');
	return(explode(', ',$line[0]));
}





/**
**name SRCLST_alternativeFS($sourceName)
**description Returns the alternative file system that is supported by the OS.
**parameter sourceName: The name of the package source list
**returns: File system.
**/
function SRCLST_alternativeFS($sourceName)
{
	$tmp = SRCLST_getParameter($sourceName, 'alternativeFS');
	return(isset($tmp[0]{1}) ? $tmp[0] : 'ext3');
}





/**
**name SRCLST_getParameter($sourceName, $parameter)
**description Returns special parameter(s) from the given sources list.
**parameter sourceName: The name of the package source list
**parameter parameter: The name of the parameter.
**returns: Values for the given parameter in an array.
**/
function SRCLST_getParameter($sourceName, $parameter)
{
	$list=SRCLST_loadSourceList($sourceName);
	$i=0;
	$out = array();
	$lines = explode("\n",trim(HELPER_grep($list,"#$parameter:")));
	
	foreach ($lines as $line)
	{
		$mM=explode("#$parameter:",$line);
		$out[$i++] = trim($mM[1]);
	}

	return($out);
};





/**
**name SRCLST_getMirror($sourceName)
**description returns the mirror from the sources list
**parameter sourceName: the name of the package source list
**returns URL to the mirror
**/
function SRCLST_getMirror($sourceName)
{
	$tmp = SRCLST_getParameter($sourceName, 'mirror');
	return($tmp[0]);
};





/**
**name SRCLST_getDesktopList($sourceName)
**description returnes an array with all supported desktops
**parameter sourceName: the name of the package source list
**/
function SRCLST_getDesktopList($sourceName)
{
	return(explode("###",SRCLST_getValue($sourceName,"desktops")));
};





/**
**name SRCLST_showDesktopsSel($sourceName,$selName,$first)
**description returnes a selections with all desktops supported by the sources list
**parameter sourceName: the name of the package source list
**parameter selName: the name of the selection
**parameter first: the desktop that should be shown first
**/
function SRCLST_showDesktopsSel($sourceName,$selName,$first)
{
	$desktops=SRCLST_getDesktopList($sourceName);
	sort($desktops);

	if (!in_array($first,$desktops))
		$first="";

	return(HTML_listSelection($selName,$desktops,$first));
};





/**
**name SRCLST_showAlternativeArchitectureSelection($sourceName, $wantedArch, $client)
**description Shows a list with available CPU architectures of the sources list, in case that the wanted architecture is not available in the sources list. The alternative architecture will be written to the arch option of the client.
**parameter sourceName: The name of the package source list.
**parameter wantedArch: The CPU architecture of the m23 client.
**parameter client: Name of the client.
**returns: A CPU architecture supported by the package source list.
**/
function SRCLST_showAlternativeArchitectureSelection($sourceName, $wantedArch, $client)
{
	if (!isset($sourceName{1}) || !isset($wantedArch{1}) || SRCLST_isArchAvailable($sourceName, $wantedArch))
		return($wantedArch);
	else
		{
			include("/m23/inc/i18n/".$GLOBALS["m23_language"]."/m23base.php");
			$allArchs = SRCLST_getArchitectures($sourceName);
			$arch = HTML_selection('SEL_showAlternativeArchitectureSelection', $allArchs, SELTYPE_selection);

			if (HTML_submit('BUT_showAlternativeArchitectureSelection',$I18N_select))
			{
				$options['arch'] = $arch;
				CLIENT_setAllOptions($client, $options);
			}
			else
			{
				MSG_showWarning($I18N_theSourcesListDoesntSupportTheClientArchitecturePleaseSelectAnother);
				HTML_showTableHeader();
				HTML_showTableRow($I18N_arch, SEL_showAlternativeArchitectureSelection, BUT_showAlternativeArchitectureSelection);
				HTML_showTableEnd();
			}
			return($arch);
		}
}





/**
**name SRCLST_isArchAvailable($sourceName, $arch)
**description Checks if a given architecture is supported by the sources list.
**parameter sourceName: the name of the package source list
**parameter arch: Architecture to check for.
**returns true, if the architecture is supported, false otherwise.
**/
function SRCLST_isArchAvailable($sourceName, $arch)
{
	$archs = SRCLST_getValue($sourceName,"archs");
	return(!(strpos($archs,$arch) === false));
}





/**
**name SRCLST_getArchitectures($sourceName)
**description Returnes a list of all CPU architectures supported by the sources list.
**parameter sourceName: the name of the package source list
**returns Associative array with the supported CPU architectures as variable AND key.
**/
function SRCLST_getArchitectures($sourceName)
{
	$archs = SRCLST_getValue($sourceName,"archs");

	if (isset($archs{1}))
	{
		$archs = explode('###', $archs);
		return(HELPER_array2AssociativeArray($archs));
	}
	else
		return(false);
}





/**
**name SRCLST_showEditor()
**description shows an editor for sources lists
**parameter poolName: if it is set, the editor shows a package download dialog for the selected pool
**/
function SRCLST_showEditor($poolName="")
{
	include("/m23/inc/i18n/".$GLOBALS["m23_language"]."/m23base.php");

	//Supported CPU architectures
	$archs = getArchList();
	$defaultCheckedArchs = $archs;
	$forceCheckedArchsReload = false;

	$sourcename=$_POST['sourcename'];
	$sourcelist=CHECK_db2text(trim($_POST['sourcelist']));
	$sourcedescr=trim($_POST['sourcedescr']);
	$distr=$_POST['distr'];
	$release = $_POST['release'];


	//loads a sources list
	if (isset($_POST['BUT_load']) || isset($_POST['BUT_deleteCancel']))
		{
			$sourcename = $_POST['SEL_name'];
			$sourcelist = trim(SRCLST_loadSourceList($sourcename));
			$sourcedescr = trim(SRCLST_getDescription($sourcename));
			$distr = SRCLST_getValue($sourcename,"distr");
			$release = SRCLST_getValue($sourcename,"release");
			$selectedDesktops = SRCLST_getDesktopList($sourcename);
			$tmpArch = SRCLST_getArchitectures($sourcename);
			if (!($tmpArch === false))
			{
				$defaultCheckedArchs = $tmpArch;
				$forceCheckedArchsReload = true;
			}
		};

	if (!is_array($selectedDesktops))
		$selectedDesktops=DISTR_getSelectedDesktopsArr();

	$checkedArchs = HTML_multiCheckBox('MUL_archs', $archs, $defaultCheckedArchs, $forceCheckedArchsReload);

	//save the source list
	if (isset($_POST['BUT_save']))
		{
			$sourcename=$_POST['ED_name'];
			SRCLST_saveList($sourcename, trim($sourcelist), trim($sourcedescr), $distr, $release);
			SRCLST_saveArchitectures($sourcename, $checkedArchs);
			CAPTURE_captureAll(0,"Edit package sources");
		};


	//delete a source list for real
	if (isset($_POST['BUT_deleteReal']))
		{
			SRCLST_delete($sourcename);

			MSG_showInfo($I18N_packageSource_was_deleted);
			echo("<br>");

			$sourcename="";
		};

	if (isset($_POST['BUT_test']))
		{
			if (!empty($sourcename))
				$testResult = SRCLST_checkList($sourcename, $checkedArchs[0]);
			else
				{
					MSG_showInfo($I18N_pleaseSavePackageListBeforeTest);
					$testResult = "";
					echo("<br>");
				};
		}
	else
		$testResult = "";
		
	
	if (!empty($poolName))
		{
			$packageList = $_POST[TA_packageList];
			$firstClient = $_POST[SEL_clientName];
			$clientArr = CLIENT_getNamesWithPackages();
			$firstClient = $_POST[SEL_clientName];
			$packageListsArr = CLIENT_getNamesWithPackages(true);

	
			//add packages from client
			if (isset($_POST[BUT_addFromClient]))
				$packageList.=" ".PKG_getClientPackages($firstClient, "", false);

			$firstPackageList=$_POST[ED_packageSourceName];
			if (empty($firstPackageList))
				$firstPackageList=$_POST[SEL_packageListName];

			//load packages list from DB
			if (isset($_POST[BUT_loadPackagesList]) || isset($_POST[BUT_addPackagesList]))
				{
					$packageListName=$_POST[SEL_packageListName];
					
					if ($packageListName == "basepackages_$release")
						{
							include_once("/m23/inc/distr/$distr/packages.php");
							if (function_exists(PKG_getDebootStrapBasePackages))
								$packageListNew = PKG_getDebootStrapBasePackages($release);
						}
					else
						$packageListNew = PKG_loadPackagesList($packageListName,false);
						
					if (isset($_POST[BUT_addPackagesList]))
						$packageList .= $packageListNew;
					else
						$packageList = $packageListNew;

					$firstPackageList = $packageListName;
				};

			//sort list and make packages unique
			$packageListArr=array_unique (explode(" ",$packageList));
			sort($packageListArr);
			$packageList=implode(" ", $packageListArr);

			//save current packages list
			if (isset($_POST[BUT_savePackagesList]))
				PKG_savePackagesList($firstPackageList,$packageList);

			if (isset($_POST[BUT_deletePackagesList]))
				{
					PKG_deletePackagesList($_POST[SEL_packageListName]);
					if ($_POST[SEL_packageListName] == $firstPackageList)
						$packageList="";

					$firstPackageList=false;
					$packageListsArr=CLIENT_getNamesWithPackages(true);
				};
		};

	//generate code for displaying the result of the package update
	if (strlen($testResult) > 0)
		$testResultHTML="
		<tr>
			<td colspan=\"2\">
				<hr>
				<span class=\"title\">
					<center>
						$I18N_test_result
					</center>
				</span>
				<br><br>
				$testResult
			</td>
		</tr>";
	else
		$testResultHTML="";

HTML_showTableHeader();

if (!isset($_POST['BUT_delete']))
	{
	//normal mode: edit, load, save the package sources
	echo("
		<tr>
			<td colspan=\"2\">
				<span class=\"title\">
					$I18N_packageSourceName: $sourcename
				</span>
			</td>
		</tr>
		<tr>
			<td valign=\"top\">
				<textarea name=\"sourcelist\" cols=\"75\" rows=\"30\">$sourcelist</textarea>
			</td>
			<td valign=\"top\">
					<span class=\"subhighlight\">$I18N_packageSourceName</span><br>
					".SRCLST_genSelection("SEL_name", $sourcename, "*")."
					<br><br>

					<input type=\"submit\" name=\"BUT_load\" value=\"$I18N_load\">
					<input type=\"submit\" name=\"BUT_delete\" value=\"$I18N_delete\">
<br><br>
<span class=\"subhighlight\">$I18N_distribution</span>
					<br>
					".DISTR_DistributionsSelections("distr",$distr)."
					<input type=\"submit\" name=\"BUT_refresh\" value=\"$I18N_select\"><br>");
					
					if (!empty($distr))
						{
							include_once("/m23/inc/distr/$distr/clientConfig.php");
							if (function_exists("$CLCFG_listReleases"))
								echo("<br><span class=\"subhighlight\">$I18N_release</span><br>".$CLCFG_listReleases("release",$release));
							else
								$release="";
						};

					echo("<br><br>
<span class=\"subhighlight\">$I18N_arch</span>".MUL_archs."<br><br>
<span class=\"subhighlight\">$I18N_packageSourceName</span><br>

					<input type=\"text\" name=\"ED_name\" size=\"20\" maxlength=\"100\" value=\"$sourcename\">
					<br><br>

<span class=\"subhighlight\">$I18N_description</span><br>
					<textarea name=\"sourcedescr\" cols=\"20\" rows=\"5\">
$sourcedescr
</textarea>
					<br><br>
					<input type=\"submit\" name=\"BUT_save\" value=\"$I18N_save\">
					<input type=\"submit\" name=\"BUT_test\" value=\"$I18N_test_it\">


				</center>

			</td>
		</tr>
		<tr>
			<td colspan=\"2\">
				".DISTR_getDesktopsCBList($distr,$selectedDesktops)."
			</td>
		</tr>
		$testResultHTML
");
}
else
	//delete the package source
{
	$sourcelist=SRCLST_loadSourceList($sourcename);
	$sourcedescr=SRCLST_getDescription($sourcename);

	//Re-include to set the correct variable in $I18N_should_packageSource_be_deleted
	include("/m23/inc/i18n/".$GLOBALS["m23_language"]."/m23base.php");

	echo("
		<tr>
			<td>
				<br>
				<center>
					$I18N_should_packageSource_be_deleted<br>
				</center>
			</td>
		</tr>

		<tr><td><hr></td></tr>

		<tr>
			<td>
			<center>
				<span class=\"subhighlight\">$I18N_description</span>
			</center>
			<br>
			".nl2br($sourcedescr)."
			</td>
		</tr>

		<tr><td><hr></td></tr>

		<tr>
			<td>
			<center>
				<span class=\"subhighlight\">$I18N_packageSources</span>
			</center>
			<br>
			".nl2br($sourcelist)."
			</td>
		</tr>

		<tr><td><hr></td></tr>

		<tr>
			<td>
				<br>
				<center>
					<input type=\"hidden\" name=\"SEL_name\" value=\"$sourcename\">
					<input type=\"submit\" name=\"BUT_deleteReal\" value=\"$I18N_yes\">
					<input type=\"submit\" name=\"BUT_deleteCancel\" value=\"$I18N_no\">
				</center>
			</td>
		</tr>


	");
}
echo("<input type=\"hidden\" name=\"sourcename\" value=\"$sourcename\">");

if (!empty($poolName))
{
	echo("<tr><td colspan=\"2\"><hr><td></tr>
	<tr>
	<td colspan=\"3\"><span class=\"title\">$I18N_packageList: </span></td>
	</tr>
	<tr>
		<td valign=\"top\">
			<textarea cols=\"70\" rows=\"17\" name=\"TA_packageList\">$packageList</textarea>
		</td>
		<td valign=\"top\">
			<span class=\"subhighlight\">$I18N_packageList</span><br>
			".HTML_listSelection("SEL_packageListName",$packageListsArr,$firstPackageList)."
			<br><br>

			<center>
			<input type=\"submit\" name=\"BUT_loadPackagesList\" value=\"$I18N_load\">
			<input type=\"submit\" name=\"BUT_addPackagesList\" value=\"$I18N_add\"><br>
			<input type=\"submit\" name=\"BUT_deletePackagesList\" value=\"$I18N_delete\"><br><br>
			</center>
			
			<span class=\"subhighlight\">$I18N_packageList</span><br>
			<INPUT type=\"text\" name=\"ED_packageSourceName\" size=\"20\" maxlength=\"75\" value=\"$firstPackageList\"><br>
			<input type=\"submit\" name=\"BUT_savePackagesList\" value=\"$I18N_save\"><br><br>
			
			<span class=\"subhighlight\">$I18N_addPackagesFromClient</span><br>
			".HTML_listSelection("SEL_clientName",$clientArr,$firstClient)."<br>
			<input type=\"submit\" name=\"BUT_addFromClient\" value=\"$I18N_add\"><br><br>
			
			<INPUT type=\"checkbox\" name=\"CB_downloadBasePackages\" value=\"yes\" checked>$I18N_downloadBasePackages<br>
		</td>
	</tr>
	<tr>
		<td colspan=\"2\" align=\"center\">
			<input type=\"submit\" name=\"BUT_step3\" value=\"$I18N_nextStep (".$I18N_poolStep["3download"].")\">
		</td>
	</tr>
	");
};

HTML_showTableEnd();
}





/**
**name SRCLST_getListnames($distr)
**description Returns an array that contains all sourceslist names
**parameter distr: the distribution the sources list is for or "*" for all distributions
**/
function SRCLST_getListnames($distr)
{
	$result=SRCLST_querySourceslists($distr);

	$i=0;

	while ($line=mysql_fetch_row($result))
		$out[$i++]=$line[0];

	return($out);
};





/**
**name SRCLST_cleanList($list)
**description Returns an array with all lines of the sources list that contain Debian sources
**parameter list: the contents of the sources list
**/
function SRCLST_cleanList($list)
{
	$i=0;
	$lines=explode("\n",$list);
	foreach ($lines as $line)
		{
			$line=trim($line);
			if (!empty($line) && (strpos($line,"#")!=0 || strpos($line,"#")===false))
				$out[$i++]=$line;
		};
	return($out);
};





/**
**name SRCLST_matchList($distr,$search)
**description Returns the name of the sources list that matches the searched sources list contents for the distribution or false
**parameter distr: the distribution to search the name of the sources list under
**parameter search: the contents of the sources list to search
**/
function SRCLST_matchList($distr,$search)
{
	$listNames=SRCLST_getListnames($distr);

	$search=SRCLST_cleanList($search);
	$samount=count($search);
	
	$found=false;

	foreach ($listNames as $listName)
	{
		$list=SRCLST_loadSourceList($listName);
		$list=SRCLST_cleanList($list);
		$lamount=count($list);

		if ($lamount != $samount)
			continue;

		$found=true;
		foreach ($search as $line)
		{
			if (!in_array($line,$list))
				{
					$found=false;
					break;
				}
		};

		if ($found)
			return($listName);
	};
	return(false);
};
?>
