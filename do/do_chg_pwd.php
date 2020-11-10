<?php

function change_password() {
global $url,$err;


  $con=ldap_connect(__ldap_server__) or error_log("Could not connect to LDAP Server");
  if($con) {
    ldap_set_option($con, LDAP_OPT_PROTOCOL_VERSION, 3);
    echo "connected...\n";
    if($bind = ldap_bind($con,__ldap_proxy_acc__.','.__ldap_base_dn__,__ldap_proxy_accpwd__)) {
      $att= array("cn","uid");
      $sr = ldap_search($con,__ldap_base_dn__,"uid=".$_SESSION['uid'],$att);
      $info = ldap_get_entries($con, $sr);
       
        
      if ($info['count'] ==1) {
        $dn = ldap_get_dn($con,ldap_first_entry($con,$sr));
          
        if ($bind=ldap_bind($con,$dn,$_POST['oldpass'])) {
          $att= array("userPassword","cn","uid","mail");
          $sr = ldap_search($con,__ldap_base_dn__,"uid=".$_SESSION['uid'],$att);
          $info = ldap_get_entries($con,$sr);
          if ($info['count'] ==1) {
            if(!empty($_POST['newpass'])) {
               if(strlen($_POST['newpass']) > 9)  {
               $pass= portal_encrypt_password($_POST['newpass']);
            
                $dn = ldap_get_dn($con,ldap_first_entry($con,$sr));
             
                $entry= array('userPassword'=>"{CRYPT}".$pass[1],'shadowLastChange'=> floor(date("U") / 86400) );

                if( ldap_mod_replace($con,$dn,$entry) ) {
                  $err="chgpwdsuccess";
                } else {
                  $ldaperror = ldap_error($con);
                  $errno = ldap_errno($con);
                  echo "LDAP ERR ".$errno." - ".$ldaperror."\n";
                }
              } else $err="passtooshort";
             echo "bob!";
            } else $err="emptypasswd";
           ldap_unbind($con);
          } else $err="usernotfound";
        } else $err="oldcredfail";
      } else $err="usernotfound";
     
    } else  error_log("Portal error: bind failed\n"); 
  }  else error_log("Portal error: did not connect to ldap\n");

}





?>
