<?php
include ("/m23/inc/packages.php");
include ("/m23/inc/checks.php");
include ("/m23/inc/client.php");
include ("/m23/inc/capture.php");

$params = PKG_OptionPageHeader2("wmanx");

$elem["shared/packages-wordlist"]["type"]="text";
$elem["shared/packages-wordlist"]["description"]="Description:

";
$elem["shared/packages-wordlist"]["descriptionde"]="";
$elem["shared/packages-wordlist"]["descriptionfr"]="";
$elem["shared/packages-wordlist"]["default"]="";
$elem["wmanx/languages"]["type"]="text";
$elem["wmanx/languages"]["description"]="Description:


";
$elem["wmanx/languages"]["descriptionde"]="";
$elem["wmanx/languages"]["descriptionfr"]="";
$elem["wmanx/languages"]["default"]="Gaelg (Manx Gaelic)";
$elem["shared/packages-wordlist"]["type"]="text";
$elem["shared/packages-wordlist"]["description"]="Description:

";
$elem["shared/packages-wordlist"]["descriptionde"]="";
$elem["shared/packages-wordlist"]["descriptionfr"]="";
$elem["shared/packages-wordlist"]["default"]="";
$elem["wmanx/languages"]["type"]="text";
$elem["wmanx/languages"]["description"]="Description:
";
$elem["wmanx/languages"]["descriptionde"]="";
$elem["wmanx/languages"]["descriptionfr"]="";
$elem["wmanx/languages"]["default"]="Gaelg (Manx Gaelic)";
PKG_OptionPageTail2($elem);
?>
