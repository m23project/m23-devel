<?php
include ("/m23/inc/packages.php");
include ("/m23/inc/checks.php");
include ("/m23/inc/client.php");
include ("/m23/inc/capture.php");

$params = PKG_OptionPageHeader2("arno-iptables-firewall");

$elem["arno-iptables-firewall/title"]["type"]="title";
$elem["arno-iptables-firewall/title"]["description"]="";
$elem["arno-iptables-firewall/title"]["descriptionde"]="";
$elem["arno-iptables-firewall/title"]["descriptionfr"]="";
$elem["arno-iptables-firewall/title"]["default"]="";
$elem["arno-iptables-firewall/config-ext-if"]["type"]="string";
$elem["arno-iptables-firewall/config-ext-if"]["description"]="";
$elem["arno-iptables-firewall/config-ext-if"]["descriptionde"]="";
$elem["arno-iptables-firewall/config-ext-if"]["descriptionfr"]="";
$elem["arno-iptables-firewall/config-ext-if"]["default"]="";
$elem["arno-iptables-firewall/dynamic-ip"]["type"]="boolean";
$elem["arno-iptables-firewall/dynamic-ip"]["description"]="";
$elem["arno-iptables-firewall/dynamic-ip"]["descriptionde"]="";
$elem["arno-iptables-firewall/dynamic-ip"]["descriptionfr"]="";
$elem["arno-iptables-firewall/dynamic-ip"]["default"]="true";
$elem["arno-iptables-firewall/services-tcp"]["type"]="string";
$elem["arno-iptables-firewall/services-tcp"]["description"]="";
$elem["arno-iptables-firewall/services-tcp"]["descriptionde"]="";
$elem["arno-iptables-firewall/services-tcp"]["descriptionfr"]="";
$elem["arno-iptables-firewall/services-tcp"]["default"]="";
$elem["arno-iptables-firewall/services-udp"]["type"]="string";
$elem["arno-iptables-firewall/services-udp"]["description"]="";
$elem["arno-iptables-firewall/services-udp"]["descriptionde"]="";
$elem["arno-iptables-firewall/services-udp"]["descriptionfr"]="";
$elem["arno-iptables-firewall/services-udp"]["default"]="";
$elem["arno-iptables-firewall/restart"]["type"]="boolean";
$elem["arno-iptables-firewall/restart"]["description"]="";
$elem["arno-iptables-firewall/restart"]["descriptionde"]="";
$elem["arno-iptables-firewall/restart"]["descriptionfr"]="";
$elem["arno-iptables-firewall/restart"]["default"]="true";
$elem["arno-iptables-firewall/nat"]["type"]="boolean";
$elem["arno-iptables-firewall/nat"]["description"]="";
$elem["arno-iptables-firewall/nat"]["descriptionde"]="";
$elem["arno-iptables-firewall/nat"]["descriptionfr"]="";
$elem["arno-iptables-firewall/nat"]["default"]="false";
$elem["arno-iptables-firewall/config-int-if"]["type"]="string";
$elem["arno-iptables-firewall/config-int-if"]["description"]="";
$elem["arno-iptables-firewall/config-int-if"]["descriptionde"]="";
$elem["arno-iptables-firewall/config-int-if"]["descriptionfr"]="";
$elem["arno-iptables-firewall/config-int-if"]["default"]="";
$elem["arno-iptables-firewall/config-int-net"]["type"]="string";
$elem["arno-iptables-firewall/config-int-net"]["description"]="";
$elem["arno-iptables-firewall/config-int-net"]["descriptionde"]="";
$elem["arno-iptables-firewall/config-int-net"]["descriptionfr"]="";
$elem["arno-iptables-firewall/config-int-net"]["default"]="";
$elem["arno-iptables-firewall/config-int-nat-net"]["type"]="string";
$elem["arno-iptables-firewall/config-int-nat-net"]["description"]="";
$elem["arno-iptables-firewall/config-int-nat-net"]["descriptionde"]="";
$elem["arno-iptables-firewall/config-int-nat-net"]["descriptionfr"]="";
$elem["arno-iptables-firewall/config-int-nat-net"]["default"]="";
$elem["arno-iptables-firewall/icmp-echo"]["type"]="boolean";
$elem["arno-iptables-firewall/icmp-echo"]["description"]="";
$elem["arno-iptables-firewall/icmp-echo"]["descriptionde"]="";
$elem["arno-iptables-firewall/icmp-echo"]["descriptionfr"]="";
$elem["arno-iptables-firewall/icmp-echo"]["default"]="false";
$elem["arno-iptables-firewall/debconf-wanted"]["type"]="boolean";
$elem["arno-iptables-firewall/debconf-wanted"]["description"]="";
$elem["arno-iptables-firewall/debconf-wanted"]["descriptionde"]="";
$elem["arno-iptables-firewall/debconf-wanted"]["descriptionfr"]="";
$elem["arno-iptables-firewall/debconf-wanted"]["default"]="true";
PKG_OptionPageTail2($elem);
?>