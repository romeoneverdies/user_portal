<?php

function reset_password() {
global $url,$err;




  if ( isset($_POST['email']) and !empty($_POST['email'])) {

    if (preg_match('/^.*@.*\..*$/',$_POST['email'],$matches)==1) {


      $con=ldap_connect(__ldap_server__) or error_log("Could not connect to LDAP Server");
      if($con) {
        ldap_set_option($con, LDAP_OPT_PROTOCOL_VERSION, 3);
        echo "connected...\n";
        if($bind = ldap_bind($con,__ldap_proxy_acc__.','.__ldap_base_dn__,__ldap_proxy_accpwd__)) {
          $att= array("userPassword","cn","uid","mail");
          $sr = ldap_search($con,__ldap_base_dn__,"mail=".$matches[0],$att);
          $info = ldap_get_entries($con,$sr);
          if ($info['count'] ==1) {
  
            $pass= portal_encrypt_password(gen_password());
            gen_pwd_email($pass[0],$info[0]['mail'][0]); 
 
            $dn = ldap_get_dn($con,ldap_first_entry($con,$sr));
             
             $entry= array('userPassword'=>"{CRYPT}".$pass[1]);

            if( ldap_mod_replace($con,$dn,$entry) ) {
              $err="successemail";
            } else {
              $ldaperror = ldap_error($con);
              $errno = ldap_errno($con);
              echo "LDAP ERR ".$errno." - ".$ldaperror."\n";
            }
          
           
          } else $err="emailnotfound";
          ldap_unbind($con);
        } else echo "bind failed";
      } else echo "we are not connected: ".$con;
    } else $err="emailnotvalid";
  } else $err= "noemail";
}






?>
