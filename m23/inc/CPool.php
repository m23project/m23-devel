<?php

class CPool extends CChecks
{
	private $poolName = null, $poolDir = null;





	public function __construct($poolName = null, $poolType = null, $poolArch = null)
	{
		if (($poolName !== null) && CPoolLister::poolExists($poolName))
			$this->setPoolName($poolName);
		else
		{
			if (($poolType !== null) && ($poolArch !== null))
				$this->createBasicPool($poolName, $poolType, $poolArch);
		}
	}





/**
**name CPool::getConvertPackagesToRepositoryLogName()
**description Returns the full file name of the convert packages to repository log file.
**returns: Name the log file.
**/
	public function getConvertPackagesToRepositoryLogName()
	{
		return($this->getPoolDir().'/convertPackagesToRepository.log');
	}





/**
**name CPool::getConvertPackagesToRepositoryLogNewLines()
**description Gets the last (new) lines of the (growing) convert packages to repository log file.
**returns: UTF8-encoded new lines of the log file.
**/
	public function getConvertPackagesToRepositoryLogNewLines()
	{
		return(HELPER_getNewLogLines($this->getConvertPackagesToRepositoryLogName(),'getConvertPackagesToRepositoryLogNewLines'.$this->getPoolName()));
	}





/**
**name CPool::isConvertPackagesToRepositoryRunning()
**description Checks if the conversation of downloaded packages to a repository is running.
**returns: true, if it is running, otherwise false.
**/
	public function isConvertPackagesToRepositoryRunning()
	{
		return(SERVER_runningInBackground('convertPackagesToRepository'));
	}


	public function convertPackagesToRepository()
	{
		$poolDir = $this->getPoolDir();
		$serverIP = getServerIP();
		$poolName = $this->getPoolName();
		$logFile = $this->getConvertPackagesToRepositoryLogName();

		$cmds = 'cd "'.$poolDir.'"
		find . -iname "*.deb" -type f -exec mv {} . \; > "'.$logFile.'"
		
		rm *.gif *.htm* Packages.* 2> /dev/null
		touch tempmkpackages

		dpkg-scanpackages -m . tempmkpackages | grep -v "Depends: $" > Packages
		gzip -c Packages > Packages.gz
		bzip2 -k Packages

		rm tempmkpackages

		apt-ftparchive release -o APT::FTPArchive::Release::Origin=m23 -o APT::FTPArchive::Release::Suite=stable .  > Release

		chmod 755 -R .
		';

		SERVER_runInBackground('convertPackagesToRepository', $cmds, 'root',true);
	
		//write sources list for this pool
		$sourceslist = "#mirror: http://$serverIP/pool/$poolName
deb http://$serverIP/pool/$poolName ./\n";
	
		$this->setPoolSourceslist($sourceslist);
	}



/**
**name CPool::convertPackagesToRepository()
**description Generates a package source from packages stored in one directory.
**/
	public function convertPackagesToRepositoryALT()
	{
		$poolDir = $this->getPoolDir();
		$serverIP = getServerIP();
		$release = $this->getPoolRelease();
		$archivPath = "archivs/";
		$logFile = $this->getConvertPackagesToRepositoryLogName();
		$poolName = $this->getPoolName();
	
		//write sources list for this pool
		$sourceslist = "#mirror: http://$serverIP/pool/$poolName
deb http://$serverIP/pool/$poolName/ $release main non-free contrib\n";
	
		$this->setPoolSourceslist($sourceslist);
	
		//making links from the existing distr directory to all known releases
		include_once("/m23/inc/distr/debian/clientConfig.php"); //needed for CLCFG_getDebianReleasesGeneric
		$linkReleases="";
		foreach (CLCFG_getDebianReleasesGeneric("debian") as $linkRelease)
			$linkReleases.="echo 'ln -s `sudo find $poolDir/dists -maxdepth 1 -mindepth 1 -type d` $poolDir/dists/$linkRelease'\n";
	
		$cmds="
		cd $poolDir
		echo \"cd $poolDir\"
		rm -f $logFile
		find $archivPath/ -type f | grep deb\$ | awk '{system(\"reprepro -Vb . includedeb $release \"$0\" 2>&1 | tee -a $logFile\")}'
		find /mdk/m23Debs/debs -type f | grep deb\$ | grep -v woody | awk '{system(\"reprepro -Vb . includedeb $release \"$0\" 2>&1 | tee -a $logFile\")}'
		sudo rm -f archivs/*
		$linkReleases
	
		#Fix the Packages files
		for pfile in `find \"$poolDir\" | grep \"/Packages\$\"`
		do
			#e.g. /m23/data+scripts/pool/mb/dists/etch/main/binary-i386
			dir=`dirname \$pfile`
			cd \"\$dir\"
			echo \"dir: \$dir\"
			
			#remove all empty Depends lines from Packages
			sed '/Depends: \$/d' Packages > Packages2
			
			#Check if there were lines removed
			diff -q Packages Packages2
			if [ \$? -eq 0 ]
			then
				echo \"No changes in \$dir\"
				continue
			fi
		
			#move Packages
			cat Packages2 > Packages
			rm Packages.gz Packages2
		
			#Create compressed version
			gzip -c Packages > Packages.gz
		
			#calculate sizes and MD5 sums of Packages and the compressed Packages.gz
			m=`md5sum Packages | cut -d' ' -f1`
			s=`find Packages -printf \"%s\"`
			mgz=`md5sum Packages.gz | cut -d' ' -f1`
			sgz=`find Packages.gz -printf \"%s\"`
		
			#jump to the directory that stores the global Releases file with sizes and hashes
			#e.g. /m23/data+scripts/pool/mb/dists/etch
			cd ..
			cd ..
			releaseDir=`pwd`
			echo \"releaseDir: \$releaseDir\"
		
			#Remove the release directory from the complete path
			# e.g. main/binary-i386
			strippedReleaseDir=`echo \$dir | sed \"s*\$releaseDir**g\" | sed \"s*/**\"`
			echo \"strippedReleaseDir: \$strippedReleaseDir\"
		
			#convert / to \/ for sed's syntax
			strippedReleaseDirSED=`echo \$strippedReleaseDir | sed 's#\/#\\\/#g'`
		
			#remove the lines that contain the Packages and Packages.gz entries for 
			sed \"/\$strippedReleaseDirSED/d\" Release > /tmp/Release
			echo \" \$m \$s \$strippedReleaseDir/Packages\" >> /tmp/Release
			echo \" \$mgz \$sgz \$strippedReleaseDir/Packages.gz\" >> /tmp/Release
		
			cat /tmp/Release > Release
			rm /tmp/Release
		done
	
		#fixing code to correct Release files that are missing the information stored in conf/distributions
		for releasefile in `find \"$poolDir\" | grep Release | grep \"/dists/\"`
		do
			if [ `grep -c Origin: \"\$releasefile\"` -eq 0 ]
			then
				echo \"fixing: \$releasefile\"
				cat \"$poolDir/conf/distributions\" > /tmp/fixedRelease
				echo \"MD5Sum:\" >> /tmp/fixedRelease
				cat  \"\$releasefile\" >> /tmp/fixedRelease
				cat /tmp/fixedRelease > \"\$releasefile\"
				#rm /tmp/fixedRelease
			fi
		done
		";
	
		SERVER_runInBackground('convertPackagesToRepository', $cmds,HELPER_getApacheUser(),true);
	}





/**
**name CPool::isDownloadRunning()
**description Checks if a download of packages to the pool is running.
**returns: true, if download is running, otherwise false.
**/
	public function isDownloadRunning()
	{
		return(SERVER_runningInBackground('downloadPoolPackages'));
	}





/**
**name CPool::getDownloadLogNewLines()
**description Gets the last (new) lines of the (growing) download log file.
**returns: UTF8-encoded new lines of the log file.
**/
	public function getDownloadLogNewLines()
	{
		return(HELPER_getNewLogLines($this->getPoolDir().'/aptDownload.log','getDownloadLogNewLines'.$this->getPoolName()));
	}





/**
**name CPool::getDownloadLogContents()
**description Gets the contents of the download log file.
**returns: Contents of the download log file.
**/
	public function getDownloadLogContents()
	{
		return(HELPER_getFileContents($this->getPoolDir().'/aptDownload.log'));
	}





/**
**name CPool::getPoolImportedFromSourceslist()
**description Gets the complete sourceslist that was used to download the packages into the pool.
**returns: Sourceslist that was used to download the packages into the pool.
**/
	public function getPoolImportedFromSourceslist()
	{
		return($this->getProperty('importedFromImportedFromSourceslist'));
	}





/**
**name CPool::setPoolImportedFromSourceslist($importedFromImportedFromSourceslist)
**description Sets the complete sourceslist that was used to download the packages into the pool.
**parameter importedFromImportedFromSourceslist: Complete sourceslist that was used to download the packages into the pool.
**/
	public function setPoolImportedFromSourceslist($importedFromImportedFromSourceslist)
	{
		include("/m23/inc/i18n/".$GLOBALS["m23_language"]."/m23base.php");

		$importedFromImportedFromSourceslist = trim($importedFromImportedFromSourceslist);

		if (empty($importedFromImportedFromSourceslist))
			$this->addErrorMessage($I18N_sourcesListIsEmpty);
		else
			$this->setProperty('importedFromImportedFromSourceslist', $importedFromImportedFromSourceslist);
	}





/**
**name CPool::hasPoolDownloadBasePackages()
**description Checks, if base packages should be downloaded.
**returns: true, if base packages should be downloaded otherwise false.
**/
	public function hasPoolDownloadBasePackages()
	{
		return($this->getProperty('downloadBasePackages') == 'yes');
	}





/**
**name CPool::setPoolDownloadBasePackages($downloadBasePackages)
**description Sets, if base packages should be downloaded.
**parameter downloadBasePackages: true, when base packages should be downloaded otherwise false.
**/
	public function setPoolDownloadBasePackages($downloadBasePackages)
	{
		if ($downloadBasePackages === true)
			$downloadBasePackages = 'yes';
		else
			$downloadBasePackages = 'no';
		
		$this->setProperty('downloadBasePackages', $downloadBasePackages);
	}





/**
**name CPool::getPoolImportedPackageList()
**description Gets the list of packages that were downloaded (or have to be downloaded) into the pool.
**returns: PackageList of the pool.
**/
	public function getPoolImportedPackageList()
	{
		return($this->getProperty('importedPackageList'));
	}





/**
**name CPool::setPoolImportedPackageList($packageList)
**description Sets the list of packages that were downloaded (or have to be downloaded) into the pool.
**parameter packageList: PackageList of the pool.
**/
	public function setPoolImportedPackageList($packageList)
	{
		include("/m23/inc/i18n/".$GLOBALS["m23_language"]."/m23base.php");

		$packageList = trim($packageList);

		if (empty($packageList))
			$this->addErrorMessage($I18N_packagesListIsEmpty);
		else
			$this->setProperty('importedPackageList', $packageList);
	}





/**
**name CPool::getPoolDistribution()
**description Gets the distribution value of the pool.
**returns: Distribution of the pool.
**/
	public function getPoolDistribution()
	{
		return($this->getProperty('distribution'));
	}





/**
**name CPool::setPoolDistribution($distribution)
**description Sets the distribution value of the pool.
**parameter distribution: Distribution of the pool.
**/
	public function setPoolDistribution($distribution)
	{
		$this->setProperty('distribution', $distribution);
	}





/**
**name CPool::createBasicPool($poolName, $poolType, $poolArch)
**description Sets the name, type and architecture of the pool and creates the pool directory.
**parameter poolName: Name of the pool.
**parameter poolType: Type of the pool (POOL_TYPE_CD or CPoolLister::POOL_TYPE_DOWNLOAD).
**parameter poolArch: Architecture of the pool (POOL_ARCH_I386 or CPoolLister::POOL_ARCH_AMD64).
**/
	public function createBasicPool($poolName, $poolType, $poolArch)
	{
		if (CPoolLister::poolExists($poolName))
		{
			$this->addErrorMessage('POOL existiert schon');
			return(false);
		}
		else
		{
			$this->setPoolName($poolName);
			$this->setPoolType($poolType);
			$this->setPoolArch($poolArch);
		}
	}





/**
**name CPool::getPoolDir()
**description Gets the directory of the pool.
**returns: Directory of the pool.
**/
	public function getPoolDir()
	{
		if ($this->poolDir === null)
			die('ERROR: No pool directory set.');
		return($this->poolDir);
	}





/**
**name CPool::setPoolName($poolName)
**description Sets the name of the pool and create the pool directory.
**parameter poolName: Name of the pool.
**/
	public function setPoolName($poolName)
	{
		if ($this->checkPoolName($poolName))
		{
			$this->poolName = $poolName;

			$this->poolDir = CPoolLister::POOL_DIR.'/'.$this->poolName;

			if (!is_dir($this->getPoolDir()))
				SERVER_multiMkDir($this->getPoolDir(), 0700);
			return(true);
		}
		else
			return(false);
		
	}





/**
**name CPool::getPoolName()
**description Gets the name of the pool.
**parameter returnEmptyIfNull: Returns an empty string, if $this->poolName is null.
**returns: Name of the pool.
**/
	public function getPoolName($returnEmptyIfNull = false)
	{
		if ($this->poolName === null)
		{
			if ($returnEmptyIfNull)
				return('');
			else
			{
				print_r2(debug_backtrace());
				die('ERROR: No pool name set.');
			}
		}
		return($this->poolName);
	}





/**
**name CPool::getPoolSourceslist()
**description Gets the sourceslist value of the pool.
**returns: Sourceslist of the pool.
**/
	public function getPoolSourceslist()
	{
		return($this->getProperty('sourceslist'));
	}





/**
**name CPool::setPoolSourceslist($sourceslist)
**description Sets the sourceslist value of the pool.
**parameter sourceslist: Sourceslist of the pool.
**/
	public function setPoolSourceslist($sourceslist)
	{
		$this->setProperty('sourceslist', $sourceslist);
	}





/**
**name CPool::getPoolDescription()
**description Gets the description value of the pool.
**returns: Description of the pool.
**/
	public function getPoolDescription()
	{
		return($this->getProperty('description'));
	}





/**
**name CPool::setPoolDescription($description)
**description Sets the description value of the pool.
**parameter description: Description of the pool.
**/
	public function setPoolDescription($description)
	{
		$this->setProperty('description', $description);
	}
	



/**
**name CPool::getPoolRelease()
**description Gets the release value of the pool.
**returns: Release of the pool.
**/
	public function getPoolRelease()
	{
		return($this->getProperty('release'));
	}





/**
**name CPool::setPoolRelease($release)
**description Sets the release value of the pool.
**parameter release: Release of the pool.
**/
	public function setPoolRelease($release)
	{
		$this->setProperty('release', $release);
	}





/**
**name CPool::getPoolType()
**description Gets the type value of the pool.
**returns: Type of the pool.
**/
	public function getPoolType()
	{
		return($this->getProperty('type'));
	}





/**
**name CPool::setPoolType($type)
**description Sets the type value of the pool.
**parameter type: Type of the pool (POOL_TYPE_CD or CPoolLister::POOL_TYPE_DOWNLOAD).
**/
	public function setPoolType($type)
	{
		if ((CPoolLister::POOL_TYPE_CD == $type) || (CPoolLister::POOL_TYPE_DOWNLOAD == $type))
			return($this->setProperty('type', $type));
		else
			die('ERROR: Invalid pool type: '.$type);
	}





/**
**name CPool::getPoolArch()
**description Gets the architecture value of the pool.
**returns: Architecture of the pool.
**/
	public function getPoolArch()
	{
		return($this->getProperty('arch'));
	}





/**
**name CPool::setPoolArch($arch)
**description Sets the architecture value of the pool.
**parameter arch: Architecture of the pool (POOL_ARCH_I386 or CPoolLister::POOL_ARCH_AMD64).
**/
	public function setPoolArch($arch)
	{
		if ((CPoolLister::POOL_ARCH_I386 == $arch) || (CPoolLister::POOL_ARCH_AMD64 == $arch))
			return($this->setProperty('arch', $arch));
		else
			die('ERROR: Invalid pool arch: '.$arch);
	}





/**
**name CPool::setProperty($property, $value)
**description Writes the contents of a property file
**parameter property: name of the pool property
**parameter value: value to write in the pool property file
**/
	private function setProperty($property, $value)
	{
		$poolFile = $this->getPoolDir()."/$property.m23pool";
		$file = fopen($poolFile,"w");
		fwrite($file,$value);
		fclose($file);
	}





/**
**name CPool::getProperty($poolName,$var)
**description Reads the contents of a property file
**parameter property: Name of the pool property
**returns Contents of a property file
**/
	private function getProperty($property)
	{
		if ($this->poolDir === null)
			return("");
		else
		{
			$poolFile = $this->getPoolDir()."/$property.m23pool";
			if (file_exists($poolFile))
				{
					$file = fopen($poolFile,"r");
					$out = fread($file,100000);
					fclose($file);
					return($out);
				}
			else
				return("");
		}
	}





/**
**name CPool::getPoolSize()
**description Calculates the disk usage of a pool.
**returns Size of the pool in MB
**/
	public function getPoolSize()
	{
		if ($this->poolDir !== null)
		{
			$size=exec("cd ".$this->getPoolDir()."; du | tail -n1");
			$size/=1024;
			return(sprintf("%.2f",$size));
		}
		else
			return(0);
	}





/**
**name CPool::destroyPool($poolName = null)
**description Deletes the pool
**parameter poolName: Name of the pool (can optionally be set here)
**/
	public function destroyPool($poolName = null)
	{
		include("/m23/inc/i18n/".$GLOBALS["m23_language"]."/m23base.php");

		if ($poolName !== null)
			$this->setPoolName($poolName);

		// Try to delete the pool
		exec('sudo rm -r '.$this->getPoolDir(), $outArray, $retCode);

		if ($retCode === 0)
			$this->addInfoMessage($I18N_poolDeleted);
		else
			$this->addErrorMessage($I18N_poolCouldNotBeDeleted);

		// Make the pool invalid
		$this->poolName = null;
		$this->poolDir = null;
	}





/**
**name CPool::preparePool()
**description Generates the needed configuration file for reprepro.
**/
	function preparePool()
	{
		$release = $this->getPoolRelease();			// release: release of the distribution (e.g. sarge)
		$distr = $this->getPoolDistribution();
		$arch = $this->getPoolArch();				// arch: CPU architecture for the packages
		$poolName = $this->getPoolName();

		$confDir = $this->getPoolDir()."/conf";
		
		if (!is_dir($confDir))
			mkdir($confDir);
	
		$version = DISTR_releaseVersionTranslator($release);
	
		$confFile=fopen("$confDir/distributions","w");
		fwrite($confFile,"Origin: $distr
Label: $poolName
Suite: $release
Codename: $release
Version: $version
Architectures: $arch source
Components: main non-free contrib
Description: m23 pool
");

	fclose($confFile);

	}



/**
**name CPool::stopDownloadToPool()
**description Stops the download of packages to the pool.
**/
	function stopDownloadToPool()
	{
		include("/m23/inc/i18n/".$GLOBALS["m23_language"]."/m23base.php");
		$this->addInfoMessage($I18N_downloadAborted);

		SERVER_deleteFile($this->getPoolDir().'/lock');

		SERVER_killBackgroundJob('downloadPoolPackages');
	}





/**
**name CPool::startDownloadToPool()
**description Checks, if all pre-requirements for downloading packages to the pool are satisfied. Then starts the distribution specific download routine.
**returns true, if the download was started otherwise false.
**/
	function startDownloadToPool()
	{
		include("/m23/inc/i18n/".$GLOBALS["m23_language"]."/m23base.php");

		$this->preparePool();
	
		// Check, if there is a packages include file and the function for downloading packages to the pool for this distribution is existing
		if (file_exists('/m23/inc/distr/'.$this->getPoolDistribution().'/packages.php'))
			{
				include_once('/m23/inc/distr/'.$this->getPoolDistribution().'/packages.php');
	
				if (!function_exists('PKG_downloadPool'))
					$this->addErrorMessage($I18N_distrHasNoFunctionToDownloadPackages);
			}
		else
			$this->addErrorMessage($I18N_thisDistributionHasNoPackageFunctionsDefined);
	
		// Check, if there are errors (some can be generated by set functions)
		if (!$this->hasErrors())
		{
			// Add the list of GUI-selected packages
			$packagesArr[0] = $this->getPoolImportedPackageList();
	
			// Maybe add the list of distribution specific base packages
			if ($this->hasPoolDownloadBasePackages())
			{
				include_once('/m23/inc/distr/'.$this->getPoolDistribution().'/packages.php');
				if (function_exists('PKG_getDebootStrapBasePackages'))
					$packagesArr[1] = PKG_getDebootStrapBasePackages($this->getPoolRelease());
			}
	
			// Start download of the packages
			PKG_downloadPool($this->getPoolDir(), $this->getPoolImportedFromSourceslist(), $packagesArr, $this->getPoolArch());
			$this->addInfoMessage($I18N_packageDownloadStarted);
	
			return(true);
		}
		else
			return(false);
	}










/**
**name POOL_createExtendedPackageIndex($poolName)
**description creates the Packages* index files for the pool
**parameter poolName: name of the pool
**/
function POOL_createExtendedPackageIndex($poolName)
{
	include("/m23/inc/i18n/".$GLOBALS["m23_language"]."/m23base.php");

	$logFile = $this->getConvertPackagesToRepositoryLogName();
/**if (file_exists("/m23/tmp/makePoolPackages.sh"))
		return(false);*/
		
	if (SERVER_runningInBackground("m23poolBuilder"))
		return(false);

	$poolDir = $this->getPoolDir();
	
	//set correct permissions, remove old log file and copy m23 packages
	exec("sudo chmod 755 $poolDir -R
	rm -f $logFile
	sudo rm -f -r $poolDir/pool/main/m23
	cp /mdk/m23Debs/debs $poolDir/pool/main/m23 -r 2>&1 | tee -a $logFile
	");

	//open the pool builder script file, executing this file will produce the necessary Packages files
/**$pbfile=fopen("/m23/tmp/makePoolPackages.sh","w");*/

	$serverIP=getServerIP();
	//write sources list for this pool
	$sourceslist="#mirror: http://$serverIP/pool/$poolName\n";

	//write BASH header and set the user to the Apache user after the script start
	$cmds="
	echo \"$I18N_packageIndexCreationStarted\" 2>&1 | tee -a $logFile
	";

	//get the releases (woody, sarge, sid)
	$pin = popen("cd $poolDir	
	find dists/ -type d -maxdepth 2 | cut -d'/' -f2 | sort -u","r");
	while ($release = fgets($pin))
	{
		$release=trim($release);
		if (empty($release))
			continue;
		
		//all branches seperated by a blank
		$branches="";

		//get the branches for a release (main, contrib, ...)
		$pin2 = popen("cd $poolDir
		find dists/$release/ -type d -maxdepth 2 | cut -d'/' -f3 | sort -u","r");
		while ($branch = fgets($pin2))
		{
			$branch=trim($branch);
			if (empty($branch))
				continue;

			$branches.="$branch ";

			//directory that stores the Debian packages
			$debDir="pool/$branch";
			//directory that will store Packages*
			$listDir="dists/$release/$branch/binary-i386";

			//write the script for one release + branch
			$cmds.="cd $poolDir
			for from in `find | grep '%'`
			do
				to=`echo \$from | sed 's/%3a/./g'`
				mv -f \$from \$to
			done
			rm tempmkpackages -f
			touch tempmkpackages 2>&1 | tee -a $logFile
			mkdir -p $listDir
			chmod 755 $listDir
			rm $listDir/Packages.bz2 -f 2>&1 | tee -a $logFile
			dpkg-scanpackages $debDir tempmkpackages | grep -v \"Depends: $\" > $listDir/Packages 2>&1 | tee -a $logFile
			gzip -c $listDir/Packages > $listDir/Packages.gz 2>&1 | tee -a $logFile
			bzip2 -k $listDir/Packages 2>&1 | tee -a $logFile
			rm tempmkpackages -f
			";
		};

		//write sources.list entry for this release and with all braches
		$sourceslist.="deb http://$serverIP/pool/$poolName/ $release $branches\n";

		pclose($pin2);
	};

	pclose($pin);

	//delete the script after execution
	$cmds.="
	echo \"$I18N_packageIndexCreationFinished\" 2>&1 | tee -a $logFile
	rm -f /m23/tmp/makePoolPackages.sh\n";

	POOL_setProperty($poolName,"sourceslist",$sourceslist);

/**
	//execute the script in a screen. screen is started as "root", but the script is executed as the Apache user
	exec("chmod +x /m23/tmp/makePoolPackages.sh
	sudo screen -dmS m23poolBuilder su ".HELPER_getApacheUser()." -c /m23/tmp/makePoolPackages.sh");
	*/

	SERVER_runInBackground("m23poolBuilder",$cmds,HELPER_getApacheUser(),true);
}


/**
**name CPool::poolTypeSelection()
**description shows buttons for selecting the type of pool and returns the pressed button
**/
	public function poolTypeSelection()
	{
		include("/m23/inc/i18n/".$GLOBALS["m23_language"]."/m23base.php");
	
		$out=$_POST['poolType'];
		if (isset($_POST['BUT_cdPool']))
			$out="cd";
		elseif (isset($_POST['BUT_downloadPool']))
			$out="dl";
	
		if (empty($out))
			{
				echo "<INPUT type=\"submit\" name=\"BUT_cdPool\" value=\"$I18N_CDPool\">
				<INPUT type=\"submit\" name=\"BUT_downloadPool\" value=\"$I18N_downloadPool\">";
			}
	
		echo("<input type=\"hidden\" name=\"poolType\" value=\"$out\">");
	
		return($out);
	}




}

?>