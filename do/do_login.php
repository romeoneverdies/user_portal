<?php


function login() {
  global $err;
  if(isset($_POST['username']) and isset($_POST['password'])) {
    $con=ldap_connect(__ldap_server__) or die("Could not connect to LDAP Server");
    if($con) {
    ldap_set_option($con, LDAP_OPT_PROTOCOL_VERSION, 3);
      echo "connecting...\n";
        if($bind = ldap_bind($con,__ldap_proxy_acc__.','.__ldap_base_dn__,__ldap_proxy_accpwd__)) {
        $att= array("cn","uid");
        $sr = ldap_search($con,__ldap_base_dn__,"uid=".$_POST['username'],$att);
        $info = ldap_get_entries($con, $sr);
       
        
        if ($info['count'] ==1) {
          $dn = ldap_get_dn($con,ldap_first_entry($con,$sr));
          if ($bind=ldap_bind($con,$dn,$_POST['password'])) {
            echo "login success";  
            $err="loginsuccess";
            $sr = ldap_search($con,__ldap_domain_dn__.','.__ldap_base_dn__,"uid=".$_POST['username'],$att);
            $info = ldap_get_entries($con, $sr);
            
            $_SESSION['nom']=$info[0]['cn'][0];
            $_SESSION['uid']=$info[0]['uid'][0];
            $_SESSION['secured']="yes";
           
          } else { $err="loginfail"; echo "login failed\n";echo ldap_error($con); }
        
        
        } else { $err="usernotfound\n";}
      } else  error_log("Portal error: bind failed\n"); 
    }  else error_log("Portal error: did not connect to ldap\n");
  } else { $err="missingcredentials"; }
  
}

function logout() {


session_unset();
session_destroy();
 
}
  
  

?>

