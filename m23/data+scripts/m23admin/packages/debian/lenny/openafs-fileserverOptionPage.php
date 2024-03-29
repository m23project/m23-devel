<?php
include ("/m23/inc/packages.php");
include ("/m23/inc/checks.php");
include ("/m23/inc/client.php");
include ("/m23/inc/capture.php");

$params = PKG_OptionPageHeader2("openafs-fileserver");

$elem["openafs-fileserver/thiscell"]["type"]="string";
$elem["openafs-fileserver/thiscell"]["description"]="Cell this server serves files for:
 AFS fileservers belong to a cell.  They have the key for that cell's
 Kerberos service and serve volumes into that cell.  Normally, this cell is
 the same cell as the workstation's client belongs to.
";
$elem["openafs-fileserver/thiscell"]["descriptionde"]="Zelle, fÃŒr die dieser Server Dateien ausliefert:
 AFS-Dateiserver gehÃ¶ren zu einer Zelle. Die Server haben den SchlÃŒssel fÃŒr den Kerberos-Service der Zelle und stellen Volumes fÃŒr die Zelle bereit. Normalerweise ist die Zelle identisch mit der des Clients.
";
$elem["openafs-fileserver/thiscell"]["descriptionfr"]="Cellule pour laquelle ce serveur est un serveur de fichiers :
 Les serveurs de fichiers AFS appartiennent à une cellule. Ils possèdent la clé pour le service Kerberos de cette cellule et y mettent à disposition des volumes. Normalement, cette cellule est la même que celle à laquelle appartient le client.
";
$elem["openafs-fileserver/thiscell"]["default"]="";
$elem["openafs-fileserver/bosconfig_moved"]["type"]="boolean";
$elem["openafs-fileserver/bosconfig_moved"]["description"]="Upgrading will move files to new locations; continue?
 Between Openafs 1.1 and Openafs 1.2, several files moved.  In particular,
 files in /etc/openafs/server-local have been distributed to other
 locations.  The BosConfig file is now located in /etc/openafs and the
 other files are located in /var/lib/openafs.  If you continue with this
 upgrade, these files will be moved.  You should use the bos restart
 command to reload your servers.  Any configuration changes made before
 you do so will be lost.
";
$elem["openafs-fileserver/bosconfig_moved"]["descriptionde"]="Upgraden wird Dateien an neue Orte verschieben; Fortfahren?
 Zwischen Openafs 1.1 und Openafs 1.2, wurden einige Dateien verschoben. Besonders Dateien in /etc/openafs/server-local sind betroffen. Die BosConfig-Datei ist nun unter /etc/openafs zu finden und alle anderen Dateien sind unter /var/lib/openafs. Wenn Sie mit dem Upgrade fortfahren, werden diese Dateien verschoben. Sie sollten den bos restart Befehl verwenden, um Ihre Server neuzustarten. Jede KonfigurationsÃ€nderung, die Sie davor machen, wird verloren gehen.
";
$elem["openafs-fileserver/bosconfig_moved"]["descriptionfr"]="Faut-il procéder au déplacement de fichiers requis pour la mise à jour ?
 Entre les versions 1.1 et 1.2 d'OpenAFS, de nombreux fichiers ont été déplacés. Les fichiers de /etc/openafs/server-local ont notamment été répartis sur d'autres emplacements. Le fichier BosConfig est désormais placé dans /etc/openafs et les autres fichiers sont dans /var/lib/openafs. Si vous poursuivez la mise à jour, ces fichiers seront déplacés. Vous devez utiliser la commande « bos restart » pour redémarrer vos serveurs. Toutes les modifications de configuration que vous ferez avant d'avoir effectué ces opérations seront perdues.
";
$elem["openafs-fileserver/bosconfig_moved"]["default"]="true";
$elem["openafs-fileserver/alpha-broken"]["type"]="note";
$elem["openafs-fileserver/alpha-broken"]["description"]="OpenAFS file server probably does not work!
 You are running the OpenAFS file server package on an alpha.  This
 probably doesn't work; the DES code is flaky on the alpha, along with the
 threaded file server.  Likely, the fileserver will simply fail to start,
 but if it does load, data corruption may result.  You have been warned.
";
$elem["openafs-fileserver/alpha-broken"]["descriptionde"]="Der OpenAFS-Dateiserver wird wahrscheinlich nicht funktionieren!
 Sie benutzen den OpenAFS-Dateiserver auf einer Alpha-Maschine. Das funktioniert wahrscheinlich nicht; der DES-Code unter Alpha zusammen mit dem Dateiserver im Thread-Modus ist fehlerhaft. Wahrscheinlich wird der Dateiserver einfach nicht starten, aber wenn er gestartet wird, kÃ¶nnte Datenverlust die Folge sein. Sie wurden gewarnt.
";
$elem["openafs-fileserver/alpha-broken"]["descriptionfr"]="Le serveur de fichiers OpenAFS ne fonctionne probablement pas
 Vous utilisez le paquet du serveur de fichier OpenAFS sur une plateforme alpha. Cela ne fonctionne probablement pas ; le code DES est défectueux sur ces plateformes de même que le serveur de fichiers à processus légers. Il est probable que le serveur refusera tout simplement de démarrer. Cependant, s'il démarre quand même, des corruptions de données peuvent avoir lieu.
";
$elem["openafs-fileserver/alpha-broken"]["default"]="";
PKG_OptionPageTail2($elem);
?>
