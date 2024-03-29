<?

define('EXTRA_DEBS_DIRECTORY',"/m23/data+scripts/extraDebs/");


/**
**name PKGBUILDER_showDialog()
**description Shows a dialog for creating Debian packages from archives.
**/
function PKGBUILDER_showDialog()
{
	if (!is_dir(EXTRA_DEBS_DIRECTORY))
		mkdir(EXTRA_DEBS_DIRECTORY);

	PKGBUILDER_listFiles();
	PKGBUILDER_showUploadDialog();
}





/**
**name PKGBUILDER_showUploadDialog()
**description Shows a dialog for uploading the tar files with checking of the file extension.
**/
function PKGBUILDER_showUploadDialog()
{
	include("/m23/inc/i18n/".$GLOBALS["m23_language"]."/m23base.php");

	echo("<br><span class=\"title\">$I18N_uploadedTarFileForCenversion</span>");
	HTML_showTableHeader();
	echo("<tr><td>");
	if (HTML_submit("BUT_uploadTar",$I18N_uploadTarFile))
	{
		if (preg_match("/(tar.bz2$|tb2$|tar.gz$|tgz$)/i",$_FILES['FILE_uploadTar']['name']) < 1)
				MSG_showError($I18N_uploadedFileNotATarFile);
		elseif (move_uploaded_file($_FILES['FILE_uploadTar']['tmp_name'], EXTRA_DEBS_DIRECTORY.$_FILES['FILE_uploadTar']['name']))
			MSG_showInfo($I18N_tarFileUploadedSucessfully);
		else
			MSG_showError($I18N_errorUploadingTarFile);
	}

	echo("<input name=\"FILE_uploadTar\" type=\"file\">".BUT_uploadTar."<br>$I18N_maxUploadFileSize: ".HELPER_maxPhpUploadSize());
	echo("</td></tr>");
	HTML_showTableEnd();
}





/**
**name PKGBUILDER_listFiles()
**description Shows a dialog of all files in EXTRA_DEBS_DIRECTORY with possibillity to create Debian packages from tar files and to delete files.
**/
function PKGBUILDER_listFiles()
{
	include("/m23/inc/i18n/".$GLOBALS["m23_language"]."/m23base.php");

	HTML_submit("BUT_reload",$I18N_refresh);
	if (HTML_submit("BUT_recreatePackageIndex",$I18N_recreatePackageIndex))
	{
		PKGBUILDER_tar2deb(false);
	}
	

	$pressedAction=array_keysSearch($_POST,"/^BUT_action/");
	if (!($pressedAction === false))
	{
		$dumpActionFilename=explode("?",$pressedAction);

		$fileName=EXTRA_DEBS_DIRECTORY.base64_decode($dumpActionFilename[2]);
		
		switch ($dumpActionFilename[1])
		{
			case "d":
				unlink($fileName);
				PKGBUILDER_tar2deb(false);
				break;
			case "c":
				echo("<br><span class=\"title\">$I18N_tar2debConversionStatus</span>");
				HTML_showTableHeader();
				echo("<pre>");
				PKGBUILDER_tar2deb($fileName);
				echo("</pre>");
				HTML_showTableEnd();
				break;
		}
	}


	echo("<br><span class=\"title\">$I18N_fileList</span>");
	HTML_showTableHeader();

	echo("<tr>
	<td><span class=\"subhighlight\">$I18N_fileName</span></td>
	<td><span class=\"subhighlight\">$I18N_fileSize</span></td>
	<td><span class=\"subhighlight\">$I18N_fileChangeTime</span></td>
	<td><span class=\"subhighlight\">$I18N_action</span></td>
	</tr>
	");
	
	$i=0;
	
	//scan the directory for files
	$dir=opendir(EXTRA_DEBS_DIRECTORY);
	while ($fileName = readdir($dir))
	{
		if ($fileName != "." && $fileName != "..")
			$files[$i++]=$fileName;
	}
	//close the directory handle
	closedir($dir);
	sort($files);


	foreach ($files as $fileName)
	{
		if ($colorBlue)
			{
				$color=' bgcolor="#A4D9FF" bordercolor="#A4D9FF"';
				$colorBlue = false;
			}
		else
			{
				$color='';
				$colorBlue = true;
			}

		if (preg_match("/(tar.bz2$|tb2$|tar.gz$|tgz$)/i",$fileName) > 0)
		{
			//it's a tar file => convert or do nothing
			$htmlName="BUT_action?d?".base64_encode($fileName);
			HTML_submitDefine("$htmlName",$I18N_delete);
			
			$htmlName2="BUT_action?c?".base64_encode($fileName);
			HTML_submitDefine($htmlName2,$I18N_convertToDeb);
			$code=constant($htmlName)." ".constant($htmlName2);
		}
		elseif (preg_match("/Packages(.bz2$|.gz$|)/",$fileName) > 0)
		{
			//Debian package index file (Packages, Packages.gz, Packages.bz2)
			$code=$I18N_packageIndexFile;
		}
		else
		{
			//all other files (Debian packages etc.) can be deleted
			$htmlName="BUT_action?d?".base64_encode($fileName);
			HTML_submitDefine("$htmlName",$I18N_delete);
			$code=constant($htmlName);
		}
		
		$fullPath = EXTRA_DEBS_DIRECTORY.$fileName;
		echo("<tr$color>
		<td>$fileName</td>
		<td>".filesize($fullPath)."</td>
		<td>".date($DATE_TIME_FORMAT,filectime($fullPath))."</td>
		<td>".$code."</td>
		</tr>");
	}
	
	echo("
	<tr>
		<td colspan=\"4\"><hr></td>
	</tr>
	<tr>
		<td colspan=\"4\" align=\"right\">".BUT_recreatePackageIndex." ".BUT_reload."</td>
	</tr>");

	HTML_showTableEnd();
};





/**
**name array_keysSearch($array,$expr)
**description Searches for a key in an associative array that matches a regular expression.
**parameter array: Array to search in.
**parameter expr: Regular expression for searching the keys (e.g. "/^BUT_action/").
**returns: The first found key that matches the expression or false if no matching key was found.
**/
function array_keysSearch($array,$expr)
{
	foreach (array_keys($array) as $key)
	{
		if (preg_match($expr,$key) > 0)
			return($key);
	}
	
	return(false);
}





/**
**name PKGBUILDER_tar2deb($tarFile)
**description Converts a tar file (with bzip2 or gzip compression) into a Debian package.
**parameter tarFile: Name of the tar file to convert or "false" if only the package index should be updated.
**/
function PKGBUILDER_tar2deb($tarFile)
{
	if ($tarFile === false)
		$cmd="
		. /mdk/m23Debs/bin/m23Deb.inc
		cd ".EXTRA_DEBS_DIRECTORY."
		makePackages
		";
	else
		$cmd="
		. /mdk/m23Debs/bin/m23Deb.inc
		cd ".EXTRA_DEBS_DIRECTORY."
		convertTarToDeb $tarFile
		makePackages
		";

	SERVER_runInBackground("tar2deb_".urlencode($tarFile),$cmd,HELPER_getApacheUser(),false);
}
?>