<?php
include ("/m23/inc/packages.php");
include ("/m23/inc/checks.php");
include ("/m23/inc/client.php");
include ("/m23/inc/capture.php");

$params = PKG_OptionPageHeader2("iogerman");

$elem["shared/packages-ispell"]["type"]="text";
$elem["shared/packages-ispell"]["description"]="Description:

";
$elem["shared/packages-ispell"]["descriptionde"]="";
$elem["shared/packages-ispell"]["descriptionfr"]="";
$elem["shared/packages-ispell"]["default"]="";
$elem["iogerman/languages"]["type"]="text";
$elem["iogerman/languages"]["description"]="Description:
";
$elem["iogerman/languages"]["descriptionde"]="";
$elem["iogerman/languages"]["descriptionfr"]="";
$elem["iogerman/languages"]["default"]="deutsch (Old German -tex mode-), deutsch (Old German 8 bit)";
PKG_OptionPageTail2($elem);
?>
