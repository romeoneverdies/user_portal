<?php







function portal_encrypt_password($password){
  

  
$hash = crypt($password,'$6$'.$salt);

  return  array($password,$hash);
  
}



function gen_password() {
$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789%$';
    $count = strlen($chars);
$pass = ''; 


    for ($i = 0; $i < 10; $i++) {
      $index = rand(0, $count - 1);
      $pass .= substr($chars, $index, 1);
    }  
return $pass;
}


function gen_pwd_email($password,$to) {

$headers = "From: donotreply@svdx.net". "\r\n";
$subj = "Subject: Reinitialisation de mot de passe SVDX.net";

$msg = "Bonjour\nEn reponse a votre requete voici le nouveau mot de passe\n\n";
$msg.= $password."\n\n";
 
$msg.= "Bien a vous\n\nAdministrateur SVDX.net\n\n";
$msg = wordwrap($msg,70);
mail($to,$subj,$msg,$headers);
echo $msg;

} 

function read_password() {
  
  
  $passfile =file(__password_file__);
   if(!empty($passfile)) {
   
  return  openssl_decrypt(base64_decode($passfile[0]),'AES-256-CBC',base64_decode($passfile[1]));
  }
  else return Null;
}

function write_password($password,$key) {
  

$pf = fopen(__password_file__,"w");

$str=base64_encode(openssl_encrypt($password,'AES-256-CBC',$key))."\n".base64_encode($key);
  fwrite($pf,$str);
  fclose($pf);
}

 

?>
