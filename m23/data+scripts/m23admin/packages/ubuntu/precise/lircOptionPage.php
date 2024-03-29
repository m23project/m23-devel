<?php
include ("/m23/inc/packages.php");
include ("/m23/inc/checks.php");
include ("/m23/inc/client.php");
include ("/m23/inc/capture.php");

$params = PKG_OptionPageHeader2("lirc");

$elem["lirc/install_devices"]["type"]="boolean";
$elem["lirc/install_devices"]["description"]="Create LIRC device nodes if they are not there?
 LIRC needs the /dev/lirc, /dev/lircd and /dev/lircm files in /dev.
 .
 
";
$elem["lirc/install_devices"]["descriptionde"]="Sollen die LIRC-Gerätedateien erstellt werden, wenn sie nicht vorhanden sind?
 LIRC benötigt die Dateien /dev/lirc, /dev/lircd und /dev/lircm in /dev.
";
$elem["lirc/install_devices"]["descriptionfr"]="Créer ces fichiers de périphériques s'ils n'existent pas déjà ?
 LIRC a besoin des périphériques /dev/lirc, /dev/lircd et /dev/lircm.
";
$elem["lirc/install_devices"]["default"]="true";
$elem["lirc/reconfigure"]["type"]="boolean";
$elem["lirc/reconfigure"]["description"]="Do you want to reconfigure LIRC?
 LIRC is already configured, reconfiguring it may overwrite the
 existing configuration in /etc/lirc/hardware.conf.
 .
 However, comments, LIRC_ARGS and other unknown code will be preserved.
";
$elem["lirc/reconfigure"]["descriptionde"]="Soll LIRC neu konfiguriert werden?
 LIRC ist schon konfiguriert, eine Neukonfiguration könnte die existierende Konfiguration in /etc/lirc/hardware.conf überschreiben.
 .
 Jedoch werden Kommentare, LIRC_ARGS und anderer unbekannter Code beibehalten.
";
$elem["lirc/reconfigure"]["descriptionfr"]="Faut-il reconfigurer LIRC ?
 Une configuration antérieure de LIRC existe déjà. Reconfigurer le paquet peut corrompre la configuration existante située dans /etc/lirc/hardware.conf.
 .
 Cependant, les commentaires, LIRC_ARGS et les parties inconnues seront conservés.
";
$elem["lirc/reconfigure"]["default"]="false";
$elem["lirc/take_care_of_old_config"]["type"]="note";
$elem["lirc/take_care_of_old_config"]["description"]="Old configuration files found
 Previous versions of this package didn't include any configuration
 file and expected users to create their own /etc/lircd.conf and
 /etc/lircmd.conf.
 .
 The new location for these files is /etc/lirc/.
 .
 File locations will be corrected but you should check that none of
 LIRC configuration files are left directly under /etc/.
";
$elem["lirc/take_care_of_old_config"]["descriptionde"]="Alte Konfigurationsdateien gefunden
 Vorhergehende Versionen dieses Pakets beinhalteten keine Konfigurationsdateien und erwarteten vom Benutzer, eigene Konfigurationen in /etc/lircd.conf und /etc/lircmd.conf anzulegen.
 .
 Der neue Ort dieser Dateien ist /etc/lirc/.
 .
 Dateiorte werden korrigiert, aber Sie sollten überprüfen, dass keine der Konfigurationsdateien von LIRC direkt unter /etc verblieben ist.
";
$elem["lirc/take_care_of_old_config"]["descriptionfr"]="Anciens fichiers de configuration présents
 Les versions antérieures de ce paquet ne comportaient aucun fichier de configuration. Les fichiers de configuration /etc/lircd.conf et /etc/lircmd.conf devaient être créés manuellement.
 .
 Ces fichiers se trouvent maintenant dans le répertoire /etc/lirc/.
 .
 L'emplacement des fichiers de configuration de LIRC va être corrigé mais vous devriez contrôler qu'aucun fichier de configuration de LIRC ne subsiste dans /etc/.
";
$elem["lirc/take_care_of_old_config"]["default"]="";
$elem["lirc/remote_driver"]["type"]="string";
$elem["lirc/remote_driver"]["description"]="Driver name for lircd:
 for internal use only

";
$elem["lirc/remote_driver"]["descriptionde"]="";
$elem["lirc/remote_driver"]["descriptionfr"]="";
$elem["lirc/remote_driver"]["default"]="";
$elem["lirc/remote_modules"]["type"]="string";
$elem["lirc/remote_modules"]["description"]="Needed kernel modules for LIRC to work:
 for internal use only

";
$elem["lirc/remote_modules"]["descriptionde"]="";
$elem["lirc/remote_modules"]["descriptionfr"]="";
$elem["lirc/remote_modules"]["default"]="";
$elem["lirc/remote_device"]["type"]="string";
$elem["lirc/remote_device"]["description"]="Device node for lircd:
 for internal use only

";
$elem["lirc/remote_device"]["descriptionde"]="";
$elem["lirc/remote_device"]["descriptionfr"]="";
$elem["lirc/remote_device"]["default"]="";
$elem["lirc/remote_lircd_conf"]["type"]="string";
$elem["lirc/remote_lircd_conf"]["description"]="Recommended configuration file for lircd:
 for internal use only

";
$elem["lirc/remote_lircd_conf"]["descriptionde"]="";
$elem["lirc/remote_lircd_conf"]["descriptionfr"]="";
$elem["lirc/remote_lircd_conf"]["default"]="";
$elem["lirc/transmitter_driver"]["type"]="string";
$elem["lirc/transmitter_driver"]["description"]="Driver name for lircd:
 for internal use only

";
$elem["lirc/transmitter_driver"]["descriptionde"]="";
$elem["lirc/transmitter_driver"]["descriptionfr"]="";
$elem["lirc/transmitter_driver"]["default"]="";
$elem["lirc/transmitter_modules"]["type"]="string";
$elem["lirc/transmitter_modules"]["description"]="Needed kernel modules for LIRC to work:
 for internal use only

";
$elem["lirc/transmitter_modules"]["descriptionde"]="";
$elem["lirc/transmitter_modules"]["descriptionfr"]="";
$elem["lirc/transmitter_modules"]["default"]="";
$elem["lirc/transmitter_device"]["type"]="string";
$elem["lirc/transmitter_device"]["description"]="Device node for lircd:
  for internal use only

";
$elem["lirc/transmitter_device"]["descriptionde"]="";
$elem["lirc/transmitter_device"]["descriptionfr"]="";
$elem["lirc/transmitter_device"]["default"]="";
$elem["lirc/transmitter_lircd_conf"]["type"]="string";
$elem["lirc/transmitter_lircd_conf"]["description"]="Recommended configuration file for lircd:
 for internal use only

";
$elem["lirc/transmitter_lircd_conf"]["descriptionde"]="";
$elem["lirc/transmitter_lircd_conf"]["descriptionfr"]="";
$elem["lirc/transmitter_lircd_conf"]["default"]="";
$elem["lirc/lircmd_conf"]["type"]="string";
$elem["lirc/lircmd_conf"]["description"]="Recommended configuration file for lircmd:
 for internal use only

";
$elem["lirc/lircmd_conf"]["descriptionde"]="";
$elem["lirc/lircmd_conf"]["descriptionfr"]="";
$elem["lirc/lircmd_conf"]["default"]="";
$elem["lirc/port"]["type"]="string";
$elem["lirc/port"]["description"]="Hint for lirc-modules-source:
 for internal use only

";
$elem["lirc/port"]["descriptionde"]="";
$elem["lirc/port"]["descriptionfr"]="";
$elem["lirc/port"]["default"]="";
$elem["lirc/irq"]["type"]="string";
$elem["lirc/irq"]["description"]="Hint for lirc-modules-source:
 for internal use only

";
$elem["lirc/irq"]["descriptionde"]="";
$elem["lirc/irq"]["descriptionfr"]="";
$elem["lirc/irq"]["default"]="";
$elem["lirc/timer"]["type"]="string";
$elem["lirc/timer"]["description"]="Hint for lirc-modules-source:
 for internal use only

";
$elem["lirc/timer"]["descriptionde"]="";
$elem["lirc/timer"]["descriptionfr"]="";
$elem["lirc/timer"]["default"]="";
$elem["lirc/blacklist"]["type"]="string";
$elem["lirc/blacklist"]["description"]="blacklisted kernel modules:
 for internal use only

";
$elem["lirc/blacklist"]["descriptionde"]="";
$elem["lirc/blacklist"]["descriptionfr"]="";
$elem["lirc/blacklist"]["default"]="";
$elem["lirc/cflags"]["type"]="string";
$elem["lirc/cflags"]["description"]="Hint for lirc-modules-source:
 for internal use only

";
$elem["lirc/cflags"]["descriptionde"]="";
$elem["lirc/cflags"]["descriptionfr"]="";
$elem["lirc/cflags"]["default"]="";
$elem["lirc/should-use-IntelliMouse"]["type"]="note";
$elem["lirc/should-use-IntelliMouse"]["description"]="IntelliMouse protocol preferred over IMPS/2
 You are currently using lircmd with the IMPS/2 protocol. This is not
 compatible with the method lircmd uses to simulate a mouse, so IntelliMouse
 support has been added and is now the preferred protocol.
 .
 You should update /etc/lirc/lircmd.conf and the configuration of any program
 which uses lircmd as a mouse to use the IntelliMouse protocol instead.
 .
 NOTE: gpm will refuse to use lircmd as a mouse with IMPS/2 protocol.
";
$elem["lirc/should-use-IntelliMouse"]["descriptionde"]="Das IntelliMouse-Protokoll sollte statt des IMPS/2-Protokolls verwendet werden.
 Sie verwenden momentan Lircmd mit dem IMPS/2-Protokoll. Dies ist nicht kompatibel mit der Methode, die Lircmd zur Simulation einer Maus verwendet, daher wurde Unterstützung für die IntelliMouse hinzugefügt und diese ist nun das bevorzugte Protokoll.
 .
 Sie sollten /etc/lirc/lircmd.conf und die Konfigurationen von allen Programmen, die Lircmd als Maus verwenden, aktualisieren, und stattdessen das IntelliMouse-Protokoll verwenden.
 .
 Hinweis: gpm wird sich weigern, Lircmd als Maus mit dem IMPS/2 Protokoll zu benutzen.
";
$elem["lirc/should-use-IntelliMouse"]["descriptionfr"]="Protocole IntelliMouse suggéré à la place d'IMPS/2
 Le programme lircmd est actuellement utilisé avec le protocole IMPS/2. Cela n'est pas compatible avec la méthode employée par lircmd pour simuler une souris. La gestion du protocole IntelliMouse a donc été ajoutée et il est recommandé de l'utiliser.
 .
 Vous devriez mettre à jour /etc/lirc/lircmd.conf, ainsi que la configuration des programmes utilisant lircmd comme souris, afin d'utiliser le protocole IntelliMouse.
 .
 Note : lircmd ne pourra pas être utilisé comme souris par gpm si vous choisissez IMPS/2.
";
$elem["lirc/should-use-IntelliMouse"]["default"]="";
$elem["lirc/remove_var-log-lircd"]["type"]="boolean";
$elem["lirc/remove_var-log-lircd"]["description"]="Delete /var/log/lircd?
 LIRC now uses syslog as a logging mechanism, so /var/log/lircd is no longer
 relevant.
";
$elem["lirc/remove_var-log-lircd"]["descriptionde"]="/var/log/lircd löschen?
 LIRC benutzt nun syslog als Protokollmechanismus, daher ist /var/log/lircd nicht mehr relevant.
";
$elem["lirc/remove_var-log-lircd"]["descriptionfr"]="Faut-il effacer /var/log/lircd ?
 LIRC utilise maintenant syslog pour gérer ses journaux. En conséquence, le fichier /var/log/lircd n'est plus utilisé.
";
$elem["lirc/remove_var-log-lircd"]["default"]="true";
$elem["lirc/remote"]["type"]="select";
$elem["lirc/remote"]["choices"][1]="None";
$elem["lirc/remote"]["description"]="Remote control configuration:
 If you choose a remote or transmitter, but already have a
 configuration file in /etc/lirc/lircd.conf, the existing
 file will be renamed to /etc/lirc/lircd.conf.dpkg-old and
 the community configurations loaded into
 /etc/lirc/lircd.conf.  If you have a
 /etc/lirc/lircd.conf.dpkg-old file already, it will not
 be overwritten and your current /etc/lirc/lircd.conf will
 be lost.

";
$elem["lirc/remote"]["descriptionde"]="";
$elem["lirc/remote"]["descriptionfr"]="";
$elem["lirc/remote"]["default"]="None";
$elem["lirc/transmitter"]["type"]="select";
$elem["lirc/transmitter"]["choices"][1]="None";
$elem["lirc/transmitter"]["description"]="IR transmitter, if present:
 IR transmitters can be used for controlling external devices.  Some
 devices are considered transceivers, with the ability to both send
 and receive.  Other devices require separate hardware to accomplish
 these tasks.

";
$elem["lirc/transmitter"]["descriptionde"]="";
$elem["lirc/transmitter"]["descriptionfr"]="";
$elem["lirc/transmitter"]["default"]="None";
$elem["lirc/dev_input_device"]["type"]="select";
$elem["lirc/dev_input_device"]["description"]="Custom event interface for your dev/input device:
 Many remotes that were previously supported by the lirc_gpio interface now
 need to be set up via the dev/input interface.  You will need to custom
 select your remote's event character device.  This can be determined by
 'cat /proc/bus/input/devices'.

";
$elem["lirc/dev_input_device"]["descriptionde"]="";
$elem["lirc/dev_input_device"]["descriptionfr"]="";
$elem["lirc/dev_input_device"]["default"]="/dev/lirc0";
$elem["lirc/start_lircd"]["type"]="string";
$elem["lirc/start_lircd"]["description"]="Hint for lirc:
 for internal use only

";
$elem["lirc/start_lircd"]["descriptionde"]="";
$elem["lirc/start_lircd"]["descriptionfr"]="";
$elem["lirc/start_lircd"]["default"]="true";
$elem["lirc/serialport"]["type"]="select";
$elem["lirc/serialport"]["choices"][1]="/dev/ttyS0";
$elem["lirc/serialport"]["description"]="Port your serial device is attached to:
 The UART (serial) driver is a low level driver that takes advantage
 of bit banging a character device.  This means that you can only use it
 with hardware serial devices.  It unfortunately does not work with USB
 serial devices.
";
$elem["lirc/serialport"]["descriptionde"]="";
$elem["lirc/serialport"]["descriptionfr"]="";
$elem["lirc/serialport"]["default"]="/dev/ttyS0";
PKG_OptionPageTail2($elem);
?>
