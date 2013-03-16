<?php
ob_start();

//Include the files needed for getting the language of the interface only
include('/m23/inc/checks.php');
include('/m23/inc/db.php');
include('/m23/inc/remotevar.php');
include('/m23/inc/capture.php');
include('/m23/inc/message.php');
include_once('/m23/inc/CMessageManager.php');
include_once('/m23/inc/CChecks.php');
include_once('/m23/inc/CClient.php');
include_once('/m23/inc/CIPRanges.php');
include_once('/m23/inc/CIPManagement.php');
include_once('/m23/inc/CClientLister.php');
include_once('/m23/inc/CPoolLister.php');
include_once('/m23/inc/CPool.php');
include_once('/m23/inc/CPoolGUI.php');

if (file_exists('/m23/inc/m23shared/m23shared.php')) include_once('/m23/inc/m23shared/m23shared.php');

session_start();
echo('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">');

dbConnect();

/***************
** Get the language of the interface
****************/
//set language cookie
if (isset($_GET['BUT_lang']))
	{
		setcookie ( "m23language", $_GET['LB_language'], time()+60*60*24*30);
		RMV_set("m23language", $_GET['LB_language']);
	};

//fetch language from remote vars
$templang=RMV_get("m23language");

//try remote var
if (!empty($templang))
	$GLOBALS['m23_language']=$templang;
	else
	//try to read language cookie
	if (!empty($HTTP_COOKIE_VARS["m23language"]))
		$GLOBALS['m23_language']=$HTTP_COOKIE_VARS["m23language"];
		else
		//try to get language from url
		if (!empty($_GET['m23_language']))
			$GLOBALS['m23_language']=$_GET['m23_language'];
			else
			//try language listbox
			if (!empty($_GET['LB_language']))
				$GLOBALS['m23_language']=$_GET['LB_language'];
				else
				//try session (defunct)
				if (!empty($_SESSION['m23language']))
					$GLOBALS['m23_language']=$_SESSION['m23language'];
					else
					//if nothing is found use english
					$GLOBALS['m23_language']="en";

//overwrite all language settings during screenshot making
if (file_exists("/m23/tmp/screenshot.lang"))
	{
		$file=fopen("/m23/tmp/screenshot.lang","r");
		$GLOBALS['m23_language']=fgets($file,3);
		fclose($file);
	};

//Include the language file
include("/m23/inc/i18n/".$GLOBALS['m23_language']."/m23base.php");

//Log out the current user
if ($_GET['logout'] == 1)
{
	$_SESSION = array();
	if (isset($_COOKIE[session_name()]))
    	setcookie(session_name(), '', time()-42000, '/');
	session_destroy();
	header("WWW-Authenticate: Basic realm=\"m23\"");
	header("HTTP/1.0 401 Unauthorized");
	echo("$I18N_userWasLoggedOut

	<meta http-equiv=\"refresh\" content=\"1; URL=/m23admin/index.php\">
	");
	exit(0);
}

include("/m23/inc/packages.php");
include("/m23/inc/fdisk.php");
include("/m23/inc/hwinfo.php");
include("/m23/inc/client.php");
include("/m23/inc/help.php");
include("/m23/inc/dhcp.php");
include("/m23/inc/preferences.php");
include("/m23/inc/i18n.php");
include("/m23/inc/plugin.php");
include("/m23/inc/update.php");
include("/m23/inc/distr.php");
include("/m23/inc/sourceslist.php");
include("/m23/inc/groups.php");
include("/m23/inc/html.php");
include("/m23/inc/massTools.php");
include("/m23/inc/burn.php");
include("/m23/inc/server.php");
include("/m23/inc/pool.php");
include("/m23/inc/menu.php");
include("/m23/inc/helper.php");
include("/m23/inc/ldap.php");
include("/m23/inc/edit.php");
include("/m23/inc/backup.php");
include("/m23/inc/assimilate.php");
include("/m23/inc/raidlvm.php");
include("/m23/inc/packageBuilder.php");
include("/m23/inc/scredit.php");
include("/m23/inc/vm.php");
include("/m23/inc/cron.php");
include("/m23/inc/version.php");
include_once('/m23/inc/imaging.php');
include("/m23/inc/mail.php");
include("/m23/inc/serverBackup.php");
include("/m23/inc/halfSister.php");
include("/m23/inc/m23RemoteAdministrationService.php");

CAPTURE_load();

$page2="isset";

$m23_page=$_POST['page'];
if (empty($m23_page))
	$m23_page=$_GET['page'];

switch($m23_page)
 {
  /* CLIENTS */
	case 'clientsoverview':
		$page = "clients/clients_overview.php" ;
		break;

	case 'addclient':
		$page = "clients/add_client.php" ;
		break;

	case 'addvmclient':
		$page = ($_SESSION['m23Shared'] === true ? "misc/support.php" : "clients/add_vmclient.php");
		break;

	case 'editclient':
		$page = "clients/edit_client.php" ;
		break;

	case 'deleteclient':
		$page = "clients/delete_client.php" ;
		break;

	case 'recoverclient':
		$page = "clients/recover_client.php" ;
		break;

	case 'rescueclient':
		$page = "clients/rescue_client.php" ;
		break;

	case 'clientaddtogroup':
		$page = "clients/client_addtogroup.php" ;
		break;

	case 'clientdetails':
		$page = "clients/client_details.php" ;
		break;

	case 'changeJobs':
		$page = "clients/client_changeJobs.php" ;
		break;

	case 'fdisk':
		$page = "clients/client_partition.php" ;
		break;

	case 'clientdistr':
		$page = "clients/client_distr.php" ;
		break;

	case 'clientstatus':
		$page = "clients/client_status.php" ;
		break;

	case 'clientlog':
		$page = "clients/client_log.php" ;
		break;

	case 'clientpackages':
		$page = "clients/client_packages.php" ;
		break;
		
	case 'clientinfo':
		$page = "clients/client_infoPage.php" ;
		break;

	case 'clientdebug':
		$page = "clients/client_debug.php" ;
		break;

	case 'backup':
		$page = ($_SESSION['m23Shared'] === true ? "misc/support.php" : "clients/client_backup.php");
		break;

	case 'assimilate':
		$page = ($_SESSION['m23Shared'] === true ? "misc/support.php" : "clients/client_assimilate.php");
		break;

	case 'createImage':
		$page = ($_SESSION['m23Shared'] === true ? "misc/support.php" : "clients/client_createImage.php");
		break;

  /* GROUPS */
	case 'groupsoverview':
		$page = "groups/groups_overview.php" ;
		break;

	case 'groupdetails':
		$page = "groups/group_details.php" ;
		break;

	case 'creategroup':
		$page = "groups/create_group.php" ;
		break;
		
	case 'groupactions':
		 $page = "groups/group_actions.php" ;
		break;
		

	/* PACKAGES */
	case 'installpackages':
		$page = "packages/install_packages.php" ;
		break;
	case 'updatepackages':
		$page = "packages/update_packages.php" ;
		break;
	case 'poolBuilder':
		$page = "packages/poolBuilder.php" ;
		break;
	case 'scriptEditor':
		$page = ($_SESSION['m23Shared'] === true ? "misc/support.php" : "packages/scriptEditor.php");
		break;
	case 'packageBuilder':
		$page = ($_SESSION['m23Shared'] === true ? "misc/support.php" : "packages/packageBuilder.php");
		break;
	case 'grouppackages':
		$page = "clients/group_packages.php" ;
		break;
	case 'addpackages':
		$page = "clients/add_packages.php" ;
		break;
	case 'removepackages':
		$page = "clients/remove_packages.php" ;
		break;
	case 'clientsourceslist':
		$page = "packages/client_sourceslist.php";
		break;
	case 'showCurrentWorkPHP':
		$page = "packages/showCurrentWorkPHP.php";
		break;


	/* MASSTOOLS */
	case 'massInstall':
		$page = "masstools/massInstall.php" ;
		break;

	/* TOOLS */
	case 'makeBootDisk':
		$page = ($_SESSION['m23Shared'] === true ? "misc/support.php" : "tools/makeBootDisk.php");
		break;
		
	case 'makeBootCD':
		$page = ($_SESSION['m23Shared'] === true ? "misc/support.php" : "tools/makeBootCD.php");
		break;

	/* SERVER */
	case 'm23RemoteAdministrationService':
		$page = ($_SESSION['m23Shared'] === true ? "misc/support.php" : "server/m23RemoteAdministrationService.php");
		break;

	case 'htaccess':
		$page = ($_SESSION['m23Shared'] === true ? "misc/support.php" : "server/htaccess.php");
		break;

	case 'update':
		$page = ($_SESSION['m23Shared'] === true ? "misc/support.php" : "server/update.php");
		break;

	case 'serverStatus':
		$page = ($_SESSION['m23Shared'] === true ? "misc/support.php" : "server/serverStatus.php");
		break;

	case 'manageGPGKeys':
		$page = ($_SESSION['m23Shared'] === true ? "misc/support.php" : "server/manageGPGKeys.php");
		break;

	case 'capture':
		$page = ($_SESSION['m23Shared'] === true ? "misc/support.php" : "server/capture.php");
		break;

	case 'calculator':
		$page = "server/calc.php";
		break;

	case 'support':
		$page = "misc/support.php" ;
		break;

	case 'serverSettings':
		$page = ($_SESSION['m23Shared'] === true ? "misc/support.php" : "server/serverSettings.php") ;
		break;
	
	case 'm23sharedAdmin':
		$page = ((function_exists("m23SHARED_init") && !isset($_SESSION['m23Shared'])) ? "m23shared/m23sharedAdmin.php" : "misc/support.php");
		break;

	case 'bafh':
		$page = "misc/bafh.php" ;
		break;

	case 'customerCenter':
		$page = ($_SESSION['m23Shared'] !== true ? "misc/support.php" : "m23shared/customerCenter.php") ;
		break;

	case 'serverBackup':
		$page = ($_SESSION['m23Shared'] === true ? "misc/support.php" : "server/serverBackup.php");
		break;

	case 'serverBackupList':
		$page = ($_SESSION['m23Shared'] === true ? "misc/support.php" : "server/serverBackupList.php");
		break;

	case 'downloadVBoxAddons':
		$page = ($_SESSION['m23Shared'] === true ? "misc/support.php" : "server/downloadVBoxAddons.php");
		break;

	case 'ldapSettings':
		$page = ($_SESSION['m23Shared'] === true ? "misc/support.php" : "server/ldapSettings.php");
		break;
		
	case 'ipManagement':
		$page = ($_SESSION['m23Shared'] === true ? "misc/support.php" : "server/ipManagement.php");
		break;

	case 'server_daemonsAndPrograms':
		$page = ($_SESSION['m23Shared'] === true ? "misc/support.php" : "server/daemonsAndPrograms.php");
		break;

	case 'manageImageFiles':
		$page = "server/manageImageFiles.php";
		break;

  /* PLUGINS */
	case 'plginstall':
		$page = ($_SESSION['m23Shared'] === true ? "misc/support.php" : "misc/plginstall.php");
		break;

	case 'plgoverview':
		$page = ($_SESSION['m23Shared'] === true ? "misc/support.php" : "misc/plgoverview.php");
		break;

	case 'helpViewer':
		$page = "misc/helpViewer.php" ;
		break;

	case 'manViewer':
		$page = "misc/manViewer.php" ;
		break;

	case 'developersPlayground':
		$page = "misc/developersPlayground.php" ;
		break;

	default:
		$page2 = "";
		break;

 }

 if (empty($page2))
 {
 $page2=PLG_isPluginSelected("/m23/data+scripts/m23admin/server/",$page);
 if (!empty($page2))
	$page=$page2;
 else
 $page2=PLG_isPluginSelected("/m23/data+scripts/m23admin/clients/",$page);
 if (!empty($page2))
	$page=$page2;
 else
 $page2=PLG_isPluginSelected("/m23/data+scripts/m23admin/groups/",$page);
 if (!empty($page2))
	$page=$page2;
 else
 $page2=PLG_isPluginSelected("/m23/data+scripts/m23admin/packages/",$page);
 if (!empty($page2))
	$page=$page2;
 else
 $page2=PLG_isPluginSelected("/m23/data+scripts/m23admin/tools/",$page);
 if (!empty($page2))
	$page=$page2;
 else
 	$page="misc/welcome.php";
 }

?>

<html>
<head>
	<title>m23 - Admin (<? echo($m23_page);?>)</title>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
	<meta http-equiv="expires" content="0">
	<link rel="stylesheet" type="text/css" href="css/index.css">
	<link rel="stylesheet" type="text/css" href="css/jQuery.css">
	<link rel="stylesheet" type="text/css" href="/js/syntaxhighlighter_3.0.83/styles/shCoreDefault.css">
	<script src="packages/codepress/codepress.js" type="text/javascript"></script>
	<script type="text/javascript" src="/js/jquery.min.js"></script>
	<script type="text/javascript" src="/js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="/js/syntaxhighlighter_3.0.83/scripts/shCore.js"></script>
	<script type="text/javascript" src="/js/syntaxhighlighter_3.0.83/scripts/shBrushBash.js"></script>
</head>

 <body bgcolor="#596374">
<?
	HTML_esel();
	HTML_questionnaire($_GET['disableQuestionnaire'] == 1);
?>

<table align="left" cellspacing="0"  cellpadding="5" border="0">
	<tr>
		<td colspan="2" align="center">
			<table class="box" align="center" id="ID_headHeight">
				<tr>
					<td width="900">
						<?php include("head/head.php"); ?>
					</td>
				<tr>
			</table>
		</td>
	</tr>
	<tr>
		<td valign="top" width="150">
			<table class="box" valign="top">
				<tr>
					<td align="left">
						<?php include("menu/menu.php"); ?>
					</td>
				<tr>
			</table>
		</td>
		<td valign="top">
			<table class="box" align="center" <?php CAPTURE_showTableWith(); ?>>
				<tr>
					<td valign="top" align="center"><br>
						<?php
							HTML_showPagePrintButton();
							include("$page");
						?>
					</td>
					<?php CAPTURE_showMarker(); ?>
				</tr>
			</table>
		</td>
	</tr>
</table>
</body>

</html>
<? ob_end_flush(); ?>