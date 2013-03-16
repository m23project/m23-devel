<?php
include ("/m23/inc/packages.php");
include ("/m23/inc/checks.php");
include ("/m23/inc/client.php");
include ("/m23/inc/capture.php");

$params = PKG_OptionPageHeader2("backup-manager");

$elem["backup-manager/backup-repository"]["type"]="string";
$elem["backup-manager/backup-repository"]["description"]="Archives location:
 Please enter the name of the directory where backup-manager will
 store the generated archives.
 .
 The size of archives may be rather important so you should store them
 on a disk with enough available space.
";
$elem["backup-manager/backup-repository"]["descriptionde"]="Archivplatz:
 Bitte geben Sie den Namen des Verzeichnisses an, in dem backup-manager die erstellten Archive speichern soll.
 .
 Die Archive können durchaus ziemlich groß werden, sie sollten daher auf einer Partition mit genügend Platz gespeichert werden.
";
$elem["backup-manager/backup-repository"]["descriptionfr"]="Dépôt des archives :
 Veuillez indiquer le répertoire où backup-manager sauvegardera les archives.
 .
 La taille des archives pourrait devenir importante, il est donc recommandé de choisir un emplacement possédant suffisamment d'espace disponible.
";
$elem["backup-manager/backup-repository"]["default"]="/var/archives";
$elem["backup-manager/filetype"]["type"]="select";
$elem["backup-manager/filetype"]["choices"][1]="tar";
$elem["backup-manager/filetype"]["choices"][2]="tar.gz";
$elem["backup-manager/filetype"]["choices"][3]="tar.bz2";
$elem["backup-manager/filetype"]["choices"][4]="tar.lz";
$elem["backup-manager/filetype"]["choices"][5]="zip";
$elem["backup-manager/filetype"]["description"]="Archives storage format:
";
$elem["backup-manager/filetype"]["descriptionde"]="Wählen Sie das Format der Archivnamen:
";
$elem["backup-manager/filetype"]["descriptionfr"]="Format des archives :
";
$elem["backup-manager/filetype"]["default"]="tar.gz";
$elem["backup-manager/dump_symlinks"]["type"]="boolean";
$elem["backup-manager/dump_symlinks"]["description"]="Follow symlinks?
 The tar, tar.gz and tar.bz2 filetypes may dereference the symlinks in
 generated archives.
 .
 Enabling this feature will dump the files pointed by symlinks and
 is likely to generate huge archives.
";
$elem["backup-manager/dump_symlinks"]["descriptionde"]="Symbolischen Links folgen?
 Beim Generieren der Archive könnten bei den Dateitypen tar, tar.gz und tar.bz2 symbolische Links dereferenziert werden.
 .
 Das Aktivieren dieses Features wird durch das Sichern der durch die symbolischen Links referenzierten Dateien wahrscheinlich große Archive erzeugen.
";
$elem["backup-manager/dump_symlinks"]["descriptionfr"]="Suivre les liens symboliques ?
 Les types de fichier tar, tar.gz et tar.bz2 pourraient perdre les références des liens symboliques dans les archives créées.
 .
 L'activation de cette fonctionnalité intégrera les fichiers pointés par les liens symboliques ce qui augmentera la taille des archives créées.
";
$elem["backup-manager/dump_symlinks"]["default"]="false";
$elem["backup-manager/blacklist"]["type"]="string";
$elem["backup-manager/blacklist"]["description"]="Directories to skip in archives:
 Please enter a space-separated list of directories which should not be
 archived.
";
$elem["backup-manager/blacklist"]["descriptionde"]="Bitte geben Sie die Verzeichnisse an, die nicht archiviert werden sollen:
 Bitte geben Sie eine durch Leerzeichen getrennte Liste jener Verzeichnisse an, die nicht gesichert werden sollen.
";
$elem["backup-manager/blacklist"]["descriptionfr"]="Répertoires à exclure des archives :
 Veuillez indiquer la liste des répertoires qui ne seront pas archivés, chaque élément séparé par des espaces.
";
$elem["backup-manager/blacklist"]["default"]="/var/archives";
$elem["backup-manager/name-format"]["type"]="select";
$elem["backup-manager/name-format"]["choices"][1]="long";
$elem["backup-manager/name-format"]["choicesde"][1]="lange";
$elem["backup-manager/name-format"]["choicesfr"][1]="long";
$elem["backup-manager/name-format"]["description"]="Archives name format:
 Files generated by backup-manager may use different file naming
 conventions.
 .
 The long format is \"host-full-path-to-directory.tar.gz\" while the short
 format only uses the last directory name. For instance, /home/me
 would be named me.tar.gz.
";
$elem["backup-manager/name-format"]["descriptionde"]="Wählen Sie das Format der Archivnamen:
 Von backup-manager erstellte Dateien können verschiedene Dateinamenkonventionen haben.
 .
 Das lange Format schaut wie folgt aus: »rechner-ganzer-verzeichnisnamen.tar.gz«.Das kurze Format nimmt dafür nur den letzten Verzeichnisnamen: /home/ich zum Beispiel würde als ich.tar.gz gesichert werden.
";
$elem["backup-manager/name-format"]["descriptionfr"]="Format de nommage des archives :
 Les fichiers générés par backup-manager peuvent être nommés selon deux formats.
 .
 Le format long correspond à « hôte-chemin-complet.tar.gz » alors que le format court n'utilise que le nom du répertoire parent. Par exemple /home/me sera nommé me.tar.gz.
";
$elem["backup-manager/name-format"]["default"]="long";
$elem["backup-manager/time-to-live"]["type"]="string";
$elem["backup-manager/time-to-live"]["description"]="Age of kept archives (days):
 Please choose the number of days backup-manager will keep the files
 before purging them. Combining several directories and a large number of
 days for keeping them may lead to huge archives.
";
$elem["backup-manager/time-to-live"]["descriptionde"]="Alter aufzubewahrender Archive (in Tagen):
 Sie müssen eine Anzahl an Tagen angeben, die backup-manager die Dateien behält bevor sie gelöscht werden. Beachten Sie dabei aber, dass Sie bei einer großen Anzahl an Tagen unter Umständen sehr viele große Archive erhalten.
";
$elem["backup-manager/time-to-live"]["descriptionfr"]="Durée de vie des archives (jours) :
 Veuillez choisir le nombre de jours pendant lesquels les fichiers seront conservés. Une combinaison de nombreux répertoires et d'un grand nombre de jours impliquera des archives de grande taille.
";
$elem["backup-manager/time-to-live"]["default"]="5";
$elem["backup-manager/directories"]["type"]="string";
$elem["backup-manager/directories"]["description"]="Directories to backup:
 Please enter a space-separated list of all the directories you want
 to backup.
 .
 You should rather to enter several subdirectories instead of the
 parent in order to have more pertinent files in your backup repository.
 .
 For instance, \"/home/user1 /home/user2 /home/user3\" is more appropriate
 than \"/home\" alone.
";
$elem["backup-manager/directories"]["descriptionde"]="Verzeichnisse, die gesichert werden sollen:
 Bitte geben Sie eine durch Leerzeichen getrennte Liste jener Verzeichnisse an, die gesichert werden sollen.
 .
 Beachten Sie, dass es besser ist mehrere Unterverzeichnisse anstatt eines einzelnen Oberverzeichnisses anzugeben. Dadurch können Sie eindeutigere Dateien(-namen) in ihrem Backup-Depot erhalten.
 .
 Zum Beispiel ist »/home/user1 /home/user2 /home/user3« besser geeignet als nur »/home«.
";
$elem["backup-manager/directories"]["descriptionfr"]="Répertoires à archiver :
 Veuillez indiquer la liste des répertoires à archiver (séparés par des espaces).
 .
 Il est préférable d'indiquer plusieurs sous-répertoires plutôt que leur répertoire parent afin d'avoir des fichiers plus pertinents dans le dépôt.
 .
 Par exemple, « /home/user1 /home/user2 /home/user3 » est plus approprié que « /home » uniquement.
";
$elem["backup-manager/directories"]["default"]="/etc /home";
$elem["backup-manager/burning-enabled"]["type"]="boolean";
$elem["backup-manager/burning-enabled"]["description"]="Enable automatic burning?
 Archives may be burnt on a CDR/CDRW/DVD media.
 .
 Using this feature requires a writable media to be present at the running
 time.
";
$elem["backup-manager/burning-enabled"]["descriptionde"]="Wollen Sie automatisches Brennen aktivieren?
 Archive können auf ein CDR/CDRW/DVD-Medium gebrannt werden.
 .
 Das Aktivieren dieses Features erfordert, dass zur Laufzeit ein schreibbares Medium verfügbar ist.
";
$elem["backup-manager/burning-enabled"]["descriptionfr"]="Faut-il activer la gravure automatique ?
 Les archives peuvent être gravées sur des CD-ROM, CD-RW ou DVD.
 .
 Les supports à graver doivent être présent au moment de l'exécution.
";
$elem["backup-manager/burning-enabled"]["default"]="false";
$elem["backup-manager/burning-device"]["type"]="string";
$elem["backup-manager/burning-device"]["description"]="Device to use for burning data:
";
$elem["backup-manager/burning-device"]["descriptionde"]="Geben Sie das Gerät zum Brennen an:
";
$elem["backup-manager/burning-device"]["descriptionfr"]="Périphérique de gravure à utiliser :
";
$elem["backup-manager/burning-device"]["default"]="/dev/cdrom";
$elem["backup-manager/burning-maxsize"]["type"]="string";
$elem["backup-manager/burning-maxsize"]["description"]="Maximum size of your media (MB):
";
$elem["backup-manager/burning-maxsize"]["descriptionde"]="Maximale Größe der Medien (MB):
";
$elem["backup-manager/burning-maxsize"]["descriptionfr"]="Taille maximale du support (Mo) :
";
$elem["backup-manager/burning-maxsize"]["default"]="650";
$elem["backup-manager/burning-method"]["type"]="select";
$elem["backup-manager/burning-method"]["choices"][1]="CDRW";
$elem["backup-manager/burning-method"]["choices"][2]="CDR";
$elem["backup-manager/burning-method"]["choices"][3]="DVD";
$elem["backup-manager/burning-method"]["choicesde"][1]="CD-RW";
$elem["backup-manager/burning-method"]["choicesde"][2]="CD-R";
$elem["backup-manager/burning-method"]["choicesde"][3]="DVD";
$elem["backup-manager/burning-method"]["choicesfr"][1]="CDRW";
$elem["backup-manager/burning-method"]["choicesfr"][2]="CDR";
$elem["backup-manager/burning-method"]["choicesfr"][3]="DVD";
$elem["backup-manager/burning-method"]["description"]="Burning method:
 When burning data, backup-manager will try to burn the whole archives
 repository. If it does not fit in the media, it will try to burn only the
 daily generated archives.
 .
 The CDRW/DVD-RW methods will first blank the media and then burn the data.
 .
 The CDR/DVD method will only burn the data, assuming that the media is empty
 (or that the disc does not need formatting, like DVD+RW).
";
$elem["backup-manager/burning-method"]["descriptionde"]="Brennmethode:
 Beim Brennen der Daten versucht backup-manager das gesamte Archiv-Depot zu brennen. Falls das nicht auf ein Sicherungsmedium passen sollte wird versucht, nur das täglich generierte Archiv zu sichern.
 .
 Die CDRW/DVD-RW-Methoden löschen zuerst das Medium und brennen anschließend die Daten.
 .
 Die CDR/DVD-Methode brennt nur die Daten und nimmt an, dass das Sicherungsmedium leer ist (oder nicht formatiert werden muss, wie DVD+RW).
";
$elem["backup-manager/burning-method"]["descriptionfr"]="Méthode de gravure :
 Lors de la gravure, backup-manager va essayer de graver tout le contenu du dépôt. Si le volume est trop important pour le support choisi, seules les archives du jour seront gravées.
 .
 Les méthodes « CD-RW » et « DVD » comportent une phase d'effacement du support, suivie d'une phase de gravure.
 .
 La méthode « CD-ROM » comporte seulement une phase de gravure, ce qui suppose que le support présent soit vierge (ou que le support ne nécessite pas de formatage, tel que les DVD+RW).
";
$elem["backup-manager/burning-method"]["default"]="CDRW";
$elem["backup-manager/want_to_upload"]["type"]="boolean";
$elem["backup-manager/want_to_upload"]["description"]="Enable backup-manager's uploading system?
 Archives may be uploaded to remote hosts using ftp or ssh.
 .
 Using this feature requires valid ftp or ssh account on remote hosts.
";
$elem["backup-manager/want_to_upload"]["descriptionde"]="Möchten Sie das Upload-System von backup-manager aktivieren?
 Archive können auf einen entfernten Rechner via FTP oder SSH hochgeladen werden.
 .
 Das Aktivieren dieses Features erfordert einen gültigen FTP- oder SSH-Account am entfernten Rechner.
";
$elem["backup-manager/want_to_upload"]["descriptionfr"]="Faut-il activer le système d'envoi automatique ?
 Les archives peuvent être téléchargées vers des hôtes distants via FTP ou SSH.
 .
 Cette fonctionnalité requiert un compte FTP ou SSH valide sur les hôtes distants.
";
$elem["backup-manager/want_to_upload"]["default"]="false";
$elem["backup-manager/transfert_mode"]["type"]="select";
$elem["backup-manager/transfert_mode"]["choices"][1]="scp";
$elem["backup-manager/transfert_mode"]["description"]="Transfer mode to use:
 The \"ftp\" transfer mode requires a valid FTP account on remote hosts.
 .
 The \"scp\" mode requires a valid SSH account on remote hosts.
 SSH Key authentication is used to establish the connection.
";
$elem["backup-manager/transfert_mode"]["descriptionde"]="Der zu verwendende Transfermodus:
 Der »ftp«-Transfermodus erfordert auf dem entfernten Rechner einen gültigen FTP-Account.
 .
 Der »scp«-Transfermodus erfordert auf dem entfernten Rechner einen gültigen SSH-Account. Es kommt dabei SSH-Schlüsselauthentifizierung zum Einsatz.
";
$elem["backup-manager/transfert_mode"]["descriptionfr"]="Mode de transfert :
 Le mode de transfert « ftp » requiert un compte FTP valide sur les hôtes distants.
 .
 Le mode de transfert « scp » requiert un compte SSH valide sur les hôtes distants. La connexion est établie avec authentification par clef SSH.
";
$elem["backup-manager/transfert_mode"]["default"]="scp";
$elem["backup-manager/upload-hosts"]["type"]="string";
$elem["backup-manager/upload-hosts"]["description"]="Remote hosts list:
 Please enter a space-separated list of hosts (IP or FQDN) where archives will
 be uploaded.
";
$elem["backup-manager/upload-hosts"]["descriptionde"]="Liste der entfernten Rechner:
 Bitte geben sie eine durch Leerzeichen getrennte Liste jener Rechner (IP oder FQDN) an, auf die Archive hochgeladen werden sollen.
";
$elem["backup-manager/upload-hosts"]["descriptionfr"]="Liste des hôtes distants :
 Veuillez indiquer une liste d'hôtes, séparés par des espaces (adresses IP ou noms pleinement qualifiés), où les archives seront envoyées.
";
$elem["backup-manager/upload-hosts"]["default"]="";
$elem["backup-manager/upload-user-scp"]["type"]="string";
$elem["backup-manager/upload-user-scp"]["description"]="SSH user's login:
 For the scp transfer mode to be possible, a SSH account will be used. The SSH login
 to use is required as well as the path to the private key.
 .
 Remote hosts must have the user's public key listed in their authorized_keys files
 (see ssh-keygen(1) for details).
";
$elem["backup-manager/upload-user-scp"]["descriptionde"]="Geben Sie den Benutzernamen für den SSH-Login an:
 Um den SCP-Transfer zu ermöglichen, wird ein SSH-Account benutzt. Sowohl der SSH-Login als auch der Pfad zum privaten Schlüssel werden benötigt.
 .
 Beachten Sie, dass dieser Benutzer auf dem lokalen Host einen SSH-Schlüssel besitzen muss, und am entfernten Host muss der öffentliche Schlüssel (public key) des Benutzers in der authorized_keys-Datei vorhanden sein (siehe man ssh-keygen für Details).
";
$elem["backup-manager/upload-user-scp"]["descriptionfr"]="Identifiant SSH :
 Pour que le mode de transfert scp soit possible, un compte SSH est nécessaire. Vous devez indiquer l'identifiant du compte SSH à utiliser ainsi que le chemin vers la clef privée.
 .
 La clef publique de l'utilisateur doit être présente dans le fichier « authorized_keys » des hôtes distants (voir ssh-keygen(1) pour les détails).
";
$elem["backup-manager/upload-user-scp"]["default"]="bmngr";
$elem["backup-manager/upload-user-ftp"]["type"]="string";
$elem["backup-manager/upload-user-ftp"]["description"]="FTP user's login:
 Please enter the FTP user to use for uploading files to remote hosts.
";
$elem["backup-manager/upload-user-ftp"]["descriptionde"]="Geben Sie den Benutzernamen für den FTP-Login an:
 Geben Sie den FTP-Benutzer für das Hochladen der Archive auf entfernte Rechner an.
";
$elem["backup-manager/upload-user-ftp"]["descriptionfr"]="Utilisateur FTP :
 Veuillez entrez un identifiant ftp à utiliser pour l'envoi des fichiers sur les hôtes distants.
";
$elem["backup-manager/upload-user-ftp"]["default"]="";
$elem["backup-manager/upload-key"]["type"]="string";
$elem["backup-manager/upload-key"]["description"]="SSH private key file:
 Despite of the ftp transfer mode, ssh doesn't require a password. The
 authentication is based on the SSH key.
 .
 Don't forget to add the user's public key to the remote host's
 authorized_keys file (see ssh-keygen(1) for details about ssh key
 authentication).
";
$elem["backup-manager/upload-key"]["descriptionde"]="Private SSH-Schlüsseldatei:
 Im Gegensatz zum FTP-Transfer-Modus benötigt ssh kein Passwort. Die Authentifizierung funktioniert via SSH-Schlüssel.
 .
 Vergessen Sie nicht den öffentlichen Schlüssel (public-key) des Benutzers in der authorized_keys-Datei am entfernten Host einzutragen (siehe ssh-keygen(1) für Details über SSH-Schlüssel-Authentifizierung).
";
$elem["backup-manager/upload-key"]["descriptionfr"]="Fichier de la clef privée SSH :
 Contrairement au mode de transfert « ftp », le mode « scp » ne requiert pas de mot de passe. L'authentification est basée sur une clef SSH.
 .
 Il est nécessaire d'ajouter la clef publique de l'identifiant dans les fichiers « authorized_keys » des hôtes distants (voir ssh-keygen(1) pour les détails).
";
$elem["backup-manager/upload-key"]["default"]="";
$elem["backup-manager/upload-passwd"]["type"]="password";
$elem["backup-manager/upload-passwd"]["description"]="FTP user's password:
 Enter the password of the FTP user to use for uploading files to remote
 hosts.
";
$elem["backup-manager/upload-passwd"]["descriptionde"]="Passwort des FTP-Benutzers:
 Geben Sie das Passwort des FTP-Benutzers für das Hochladen von Dateien auf entfernte Rechner bekannt.
";
$elem["backup-manager/upload-passwd"]["descriptionfr"]="Mot de passe FTP :
 Veuillez indiquer le mot de passe de l'identifiant FTP à utiliser pour envoyer les fichiers sur les hôtes distant.
";
$elem["backup-manager/upload-passwd"]["default"]="";
$elem["backup-manager/upload-dir"]["type"]="string";
$elem["backup-manager/upload-dir"]["description"]="Remote host's repository:
 Please enter where - on the remote hosts - archives should be stored.
 .
 If backup-manager is installed on those hosts, it is recommended to use a
 subdirectory of its archive repository so that even uploaded archives will be purged
 when needed.
";
$elem["backup-manager/upload-dir"]["descriptionde"]="Depot des entfernten Rechners:
 Bitte geben Sie an, wo die Archive auf dem entfernten Rechner gespeichert werden sollen.
 .
 Wenn backup-manager auf diesen Rechnern installiert ist, empfiehlt es sich, ein Unterverzeichnis von deren Archiv-Depots zu verwenden, so dass hochgeladene Verzeichnisse bei Bedarf gelöscht werden können.
";
$elem["backup-manager/upload-dir"]["descriptionfr"]="Dépôt sur l'hôte distant :
 Veuillez entrer l'emplacement, sur les hôtes distants, où les archives seront envoyées.
 .
 Si backup-manager est installé sur ces hôtes distants, il est recommandé d'utiliser un sous-répertoire de son dépôt d'archives ; ainsi, même les archives téléchargées seront purgées lorsque nécessaire.
";
$elem["backup-manager/upload-dir"]["default"]="/var/archives/uploads";
$elem["backup-manager/cron_frequency"]["type"]="select";
$elem["backup-manager/cron_frequency"]["choices"][1]="never";
$elem["backup-manager/cron_frequency"]["choices"][2]="daily";
$elem["backup-manager/cron_frequency"]["choices"][3]="weekly";
$elem["backup-manager/cron_frequency"]["choicesde"][1]="nie";
$elem["backup-manager/cron_frequency"]["choicesde"][2]="täglich";
$elem["backup-manager/cron_frequency"]["choicesde"][3]="wöchentlich";
$elem["backup-manager/cron_frequency"]["choicesfr"][1]="jamais";
$elem["backup-manager/cron_frequency"]["choicesfr"][2]="quotidienne";
$elem["backup-manager/cron_frequency"]["choicesfr"][3]="hebdomadaire";
$elem["backup-manager/cron_frequency"]["description"]="CRON frequency:
 Although backup-manager is designed to make daily archives, it can be run
 less frequently, like once a week or even once a month.
 .
 Note that you can also choose not to run backup-manager at all with CRON.
";
$elem["backup-manager/cron_frequency"]["descriptionde"]="CRON-Häufigkeit:
 Obwohl backup-manager für tägliche Archive gemacht wurde, kann es auch mit geringerer Häufigkeit wie z.B. wöchentlich oder monatlich verwendet werden.
 .
 Beachten Sie, dass Sie auch wählen können, backup-manager überhaupt nicht mit CRON auszuführen.
";
$elem["backup-manager/cron_frequency"]["descriptionfr"]="Fréquence de la tâche périodique de cron :
 Bien que backup-manager soit conçu pour générer des archives quotidiennes, il peut être lancé moins fréquemment, par exemple une fois par semaine ou par mois.
 .
 Vous pouvez également choisir de ne pas lancer automatiquement backup-manager.
";
$elem["backup-manager/cron_frequency"]["default"]="never";
$elem["backup-manager/repo_user"]["type"]="string";
$elem["backup-manager/repo_user"]["description"]="Owner user of the repository:
 For security reason, the repository where archives will be stored is accessible 
 by a specific user.
 .
 The repository and archives inside will be readable and writeable by this user.
";
$elem["backup-manager/repo_user"]["descriptionde"]="
 Aus Sicherheitsgründen ist das Depot, in dem die Archive gesichert werden, nur für einen spezifischen Benutzer zugänglich.
 .
 Das Depot und die Archive darin werden für diesen Benutzer lesbar und schreibbar sein.
";
$elem["backup-manager/repo_user"]["descriptionfr"]="Utilisateur propriétaire du dépôt :
 Pour des raisons de sécurité, le dépôt où les archives sont sauvegardées n'est accessible que par un identifiant spécifique.
 .
 Le dépôt et les archives qui y sont contenues ne seront accessibles en lecture/écriture que par cet utilisateur.
";
$elem["backup-manager/repo_user"]["default"]="root";
$elem["backup-manager/repo_group"]["type"]="string";
$elem["backup-manager/repo_group"]["description"]="Owner group of the repository:
 For security reason, the repository where archives will be stored is accessible 
 by a specific group.
 .
 The repository and archives inside will be readable and writeable by this group.
";
$elem["backup-manager/repo_group"]["descriptionde"]="Benutzergruppe des Depots:
 Aus Sicherheitsgründen ist das Depot, in dem die Archive gesichert werden, nur für eine spezifische Gruppe zugänglich.
 .
 Das Depot und die Archive darin werden für diese Gruppe lesbar und schreibbar sein.
";
$elem["backup-manager/repo_group"]["descriptionfr"]="Groupe propriétaire du dépôt :
 Pour des raisons de sécurité, le dépôt où les archives sont sauvegardées est accessible par un groupe spécifique.
 .
 Le dépôt et les archives qui y sont contenues ne seront accessibles en lecture/écriture que par ce groupe.
";
$elem["backup-manager/repo_group"]["default"]="root";
$elem["backup-manager/cron_d_remove_deprecated"]["type"]="boolean";
$elem["backup-manager/cron_d_remove_deprecated"]["description"]="Remove deprecated file /etc/cron.d/backup-manager?
 In previous version, backup-manager uses /etc/cron.d directory but this directory is not 
 handled by anacron. Thus, it is not possible to run backup-manager's job asynchronously, if 
 cron.d is used.
 .
 In order to let anacron handle backup-manager's job, the following CRON subdirectories are used:
 /etc/cron.daily, /etc/cron.weekly and /etc/cron.monthly, depending on the chosen frequency.
 .
 As jobs handled by the cron.d subdirectory may be skipped if the system is not running,
 it is recommanded to use one of those directories instead.
";
$elem["backup-manager/cron_d_remove_deprecated"]["descriptionde"]="Löschen der missbilligten Datei /etc/cron.d/backup-manager?
 Backup-Manager hat in älteren Versionen das Verzeichnis /etc/cron.d benutzt. Dieses Verzeichnis wird aber nicht von anacron durchsucht. Daher ist es nicht möglich, die Aufträge von backup-manager asynchron auszuführen, wenn /etc/cron.d eingesetzt wird.
 .
 Damit anacron die Aufträge von backup-manager ausführen kann werden folgende Verzeichnisse benutzt: /etc/cron.daily, /etc/cron.weekly und /etc/cron.monthly, abhängig von der gewählten Häufigkeit.
 .
 Da Aufträge im /etc/cron.d Verzeichnis übergangen werden könnten, wenn das System nicht läuft ist es ratsam, eines dieser Verzeichnisse zu benutzen.
";
$elem["backup-manager/cron_d_remove_deprecated"]["descriptionfr"]="Faut-il effacer le fichier obsolète /etc/cron.d/backup-manager ?
 Dans sa version précédente, backup-manager utilisait le répertoire /etc/cron.d mais ce répertoire n'est pas géré par anacron. Ainsi, il n'est pas possible d'exécuter les tâches de backup-manager de manière asynchrone si cron.d est utilisé.
 .
 Afin qu'anacron puisse gèrer les tâches de backup-manager, les répertoires /etc/cron.daily, /etc/cron.weekly ou /etc/cron.monthly seront utilisés, en fonction de la fréquence choisie.
 .
 Comme les tâches gérées dans le sous-répertoire cron.d peuvent ne pas être lancées si le système est arrêté, il est recommandé d'utiliser un de ces répertoires à la place.
";
$elem["backup-manager/cron_d_remove_deprecated"]["default"]="false";
$elem["backup-manager/enable_encryption"]["type"]="boolean";
$elem["backup-manager/enable_encryption"]["description"]="Encrypt archives?
 If you don't trust the physical device where you store your data, you may want
 to be sure that your archives won't be accessible by a malicious user.
 .
 Backup Manager can encrypt your archives with GPG, that means that you will
 need a GPG identity to use that feature.
";
$elem["backup-manager/enable_encryption"]["descriptionde"]="Archive verschlüsseln?
 Falls das Gerät, auf dem Sie Ihre Daten speichern, nicht vertrauenswürdig ist, können Sie sicherstellen, dass Ihre Archive für übel gesinnte Benutzer nicht zugänglich sind. 
 .
 Backup-Manager kann Ihre Archive mit GPG verschlüsseln. Um dieses Feature zu verwenden, benötigen Sie eine GPG-Identität.
";
$elem["backup-manager/enable_encryption"]["descriptionfr"]="Faut-il chiffrer les archives ?
 Si vous ne faites pas totalement confiance aux périphériques physiques, vous pourriez avoir besoin d'être rassuré que vos archives ne seront pas accessibles à un utilisateur malveillant.
 .
 Backup Manager peut chiffrer vos archives avec GPG, ce qui signifie qu'une identité GPG est nécessaire pour utiliser cette fonction.
";
$elem["backup-manager/enable_encryption"]["default"]="false";
$elem["backup-manager/encryption_recipient"]["type"]="string";
$elem["backup-manager/encryption_recipient"]["description"]="GPG recipient:
 You have to set the recipient for which the archive is encrypted. A valid 
 specification is a short or long key id, or a descriptive name, as explained 
 in the gpg man page. 
 .
 The public key for this identity must be in the key ring of the user running gpg, 
 which may be root in most of the cases.
";
$elem["backup-manager/encryption_recipient"]["descriptionde"]="GPG-Empfänger:
 Sie müssen den Empfänger angeben, für den das Archiv verschlüsselt wird. Eine gültige Angabe ist eine kurze oder lange Schlüssel-ID oder ein beschreibender Name, wie in der GPG-Handbuchseite erklärt wird.
 .
 Der öffentliche Schlüssel für diese Identität muss im Schlüsselring des Benutzers sein, der gpg ausführt, was in den meisten Fällen root sein dürfte.
";
$elem["backup-manager/encryption_recipient"]["descriptionfr"]="Identité GPG :
 Vous devez définir l'identité avec laquelle l'archive sera chiffrée. Une identité valide peut être définie par une clé (« id ») courte ou longue ou une description du nom, tel qu'expliqué dans la page de manuel de GPG.
 .
 La clé publique de cette identité doit être située dans le trousseau de l'utilisateur exécutant GPG, soit « root » dans la plupart des cas.
";
$elem["backup-manager/encryption_recipient"]["default"]="";
PKG_OptionPageTail2($elem);
?>