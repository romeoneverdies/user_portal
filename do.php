<?php
session_start();


include "do/do_functions.php";

define('__ldap_server__','ldaps://localhost');
define('__ldap_base_dn__','dc=example,dc=com');
define('__ldap_domain_dn__','sambaDomainName=WORKGROUP');
define('__ldap_proxy_acc__','cn=someadmin,ou=people');

define('__password_file__','/etc/portal.secret'); // its better to "chmod u+rw,go-rwx" this file ... 
define('__ldap_proxy_accpwd__',read_password());

$url=null;
$err =null;

// uncomment the following line and go to do.php ONCE to encrypt your admin password 
//write_password('<your admin/proxy password>','<your key>') ;

include "do/do_rst_pwd.php";
include "do/do_login.php";
include "do/do_chg_pwd.php";




if(isset($_GET['action'])) {
switch($_GET['action']) {
 case "resetpwd": reset_password();set_url(); break;
 case "chgpwd": change_password();set_url();break;
 case "login": login();set_url();header($url); break;
 case "logout": logout();set_url(); header($url);break;
 default: echo "ERR: action is not valid";
}


} else echo "no target provided";
// comment/uncomment for debug purpose. 
//echo "<br>".$url;
if (!empty($url))
  header($url);





function set_url() {
  global $url,$err;
 
  $url = "location: https:\/\/".$_SERVER['HTTP_HOST']."/index.php"; 
  if (count($_GET) > 1) {
 
    $url .= "?";
    $first=true;
 
    foreach($_GET as $a=>$val) {
	
    	if ($a != "action") {
    	  if ($first == false) $url .= "&"; else $first=false;
        $url .= $a."=".$val; 
    	}
	
    }
    if(!empty($err)) $url.="&err=".$err;
  } else if(!empty($err)) $url.="?err=".$err;
  
   
}

?>
