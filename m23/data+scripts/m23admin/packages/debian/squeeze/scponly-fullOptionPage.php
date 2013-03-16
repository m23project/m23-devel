<?php
include ("/m23/inc/packages.php");
include ("/m23/inc/checks.php");
include ("/m23/inc/client.php");
include ("/m23/inc/capture.php");

$params = PKG_OptionPageHeader2("scponly-full");

$elem["scponly/chroot"]["type"]="boolean";
$elem["scponly/chroot"]["description"]="Install the chrooted binary /usr/sbin/scponlyc SUID root?
 If you want scponly to chroot into the user's home directory prior to
 doing its work, the scponly binary has to be installed in
 /usr/sbin/scponlyc and has to have the suid-root-bit set.
 .
 This could lead (in the worst case) to a remotely exploitable root hole.
 If you don't need the chroot- functionality, don't install the file.
";
$elem["scponly/chroot"]["descriptionde"]="Chrooted /usr/sbin/scponlyc mit »suid«-root-Rechten installieren?
 Wenn Sie wollen, dass scponly in das home-Verzeichnis des Benutzers per chroot wechlset, bevor es seine Arbeit aufnimmt, muss scponly als /usr/sbin/scponlyc installiert und das »suid«-root-Recht gesetzt werden.
 .
 Dies könnte (im schlimmsten Fall) zu einer Sicherheitslücke führen, die es einem entfernen Angreifer erlaubt »root«-Rechte zuerlangen. Wenn Sie diese Funktionalität nicht benötigen, installieren sie diese Datei nicht.
";
$elem["scponly/chroot"]["descriptionfr"]="Installer /usr/sbin/scponlyc pour un environnement fermé d'exécution ?
 Si vous souhaitez que scponly s'exécute dans un environnement restreint au répertoire racine de l'utilisateur avant d'exécuter son travail, le binaire scponly doit être installé dans /usr/bin/scponlyc et doit avoir le bit suid positionné (autorisations d'accès en 4755, exécution avec les droits du super-utilisateur).
 .
 Cela pourrait entraîner (dans le pire des cas) un accès privilégié exploitable à distance. Si vous n'avez pas besoin que le programme s'exécute dans un environnement fermé (fonctionnalité « chroot »), vous ne devriez pas installer le fichier.
";
$elem["scponly/chroot"]["default"]="false";
$elem["scponly/fullwarning"]["type"]="note";
$elem["scponly/fullwarning"]["description"]="Potential security hazard
 WARNING: this package was compiled with rsync, unison and SVN
 support, which can be a potential SECURITY hazard if not configured
 correctly! Please read the file /usr/share/doc/scponly-full/SECURITY!
";
$elem["scponly/fullwarning"]["descriptionde"]="Mögliche Gefährdung der Sicherheit
 WARNUNG: Dieses Paket wurde mit rsync-, unison- und SVN-Unterstützung kompiliert, was zu einer möglichen SICHERHEITSGEFAHR führt, falls es nicht richtig konfiguriert wird! Bitte lesen Sie die Datei /usr/share/scponly-full/SECURITY!
";
$elem["scponly/fullwarning"]["descriptionfr"]="Possible problème de sécurité
 Ce paquet a été compilé avec la gestion de rsync, unison et SVN, ce qui peut constituer un risque pour la sécurité du système si cela n'est pas configuré correctement. Veuillez consulter le fichier /usr/share/doc/scponly-full/SECURITY pour plus d'informations.
";
$elem["scponly/fullwarning"]["default"]="";
PKG_OptionPageTail2($elem);
?>
