<?
	if (isset($_GET['m23clientID']))
		$id = $_GET['m23clientID'];
	else
		$id = $_POST['m23clientID'];

	CHECK_FW(CC_id, $id);

	$clientO = new CClient($id);

	HTML_showTitle($I18N_currentWorkPHP.': '.$clientO->getClientName());
	
	
	
	
	
	/**
**name HTML_sourceViewer($htmlName, $code, $heightlighting)
**description Creates a source viewer area that polls source code from a given URL.
**parameter htmlNames: Name of the source viewer.
**parameter code: The source code to show.
**parameter heightlighting:
**/
function HTML_sourceViewer($htmlName, $code, $heightlighting)
{
	include("/m23/inc/i18n/".$GLOBALS["m23_language"]."/m23base.php");
	$const = 'BUT_'.$htmlName.'Refresh';

	include_once('/m23/inc/geshi/geshi.php');
	

	$geshi = new GeSHi($code, $heightlighting);
	$geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS,5);
	$geshi->set_header_type(GESHI_HEADER_DIV);
	$geshi->enable_classes();
	
	echo('<style type="text/css"><!--'.$geshi->get_stylesheet().'--></style>');

	define($htmlName,'
	<p>
	'.$geshi->parse_code().'
	</p>');
}
	
	
	
	
	
	
$I18N_currentWorkPHPContents = 'Aktueller Inhalt der work.php';
$I18N_wgetWorkPHPGettingCode = 'wget-Befehl zum Holen der work.php';

	HTML_showFormHeader();
	HTML_setPage('showCurrentWorkPHP');
	
	HTML_submitDefine('BUT_refresh', $I18N_refresh);
	HTML_sourceViewer('SRC_currentWorkPHP', $clientO->getClientCurrentWorkPHP(), 'bash');
	HTML_logArea('TA_wgetCode', 100, 1, 'wget "'.$clientO->getClientWorkPHPURL().'" -O /tmp/work.sh; less /tmp/work.sh');


	HTML_showTableHeader();
	echo('
	<tr>
		<td><span class="titlesmal">'.$I18N_wgetWorkPHPGettingCode.'</span></td>
		<td>'.TA_wgetCode.'
	</tr>
	<tr>
		<td colspan="2"><span class="titlesmal">'.$I18N_currentWorkPHPContents.'</span></td>
	</tr>
	<tr>
		<td colspan="2" bgcolor="#f8f8f8" style="padding:10px">'.SRC_currentWorkPHP.HTML_hiddenVar('m23clientID', $id).'</td>
	</tr>');
	HTML_showTableEnd();
	echo(BUT_refresh);

	HTML_showFormEnd();
	echo($clientO->getBackToDetailsLink('criticalStatus'));
	help_showhelp('showCurrentWorkPHP');
?>
