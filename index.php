<?php 
session_start();


if (isset($_SERVER['HTTPS']) and !empty($_SERVER['HTTPS'])) {


echo "<html>\n  <head>\n    <link rel=\"stylesheet\" type=\"text/css\" href=\"style.css\">\n  </head>\n  <body>\n";

echo "<h1>Bienvenu sur la page de service de example.com</h1>\n";
echo "<h3>vous etes connecter sur: ".$_SERVER['HTTP_HOST']."</h3><hr>\n\n";




if(isset($_GET['dest'])) {
switch($_GET['dest']) {
  case "chgpwd": change_password();break;
  case "resetpwd": reset_password();break;
  case "login": login();break;
  default: welcome();  
  
}

} else welcome(); 


echo "\n  </body>\n</html>\n";

} else header("location: https://".$_SERVER['HTTP_HOST']);


// *********** functions **************






function welcome(){

if(isset($_SESSION['secured'])) 
secured_welcome() ;
else
unsecured_welcome() ;

}



function get_certificate() {
  if ( !empty($_SESSION['uid']) ){
    if ( validate_certificate() == "expired" ) {
       // generate a new certificate
       exec('/usr/bin/ca_tools -u -o -c -w '.$_SESSION['uid'].' 2>&1', $out);
      if (file_exists('/var/www/html/secure/users/'.$_SESSION['uid'].'.zip') )    {
        echo "<a href=\"https:\/\/".$_SERVER['HTTP_HOST']."/secure/users/".$_SESSION['uid'].".zip\">t&eacute;l&eacute;charger votre certificat usager</a>";
      }
   
    }
    else {
     
      if ( file_exists('/var/www/html/secure/users/'.$_SESSION['uid'].'.zip')) {
        echo "<a href=\"https:\/\/".$_SERVER['HTTP_HOST']."/secure/users/".$_SESSION['uid'].".zip\">t&eacute;l&eacute;charger votre certificat usager</a>";
      } else { 
      exec('/usr/bin/ca_tools -u  -o -c -w '.$_SESSION['uid'].' 2>&1', $out); 
    //  print_r($out);
        echo "<a href=\"https:\/\/".$_SERVER['HTTP_HOST']."/secure/users/".$_SESSION['uid'].".zip\">t&eacute;l&eacute;charger votre certificat usager</a>";
      }
      
    }
  }
  else echo "vous n'etes pas authentifier pour t&eacute;l&eacute;charger: ".$_SESSION['uid'];
 
}



function validate_certificate() {
 $date = explode("=",exec("openssl x509 -enddate -noout -in /ca/intermediate/certs/users/`ls -1 /ca/intermediate/certs/users/ | grep ".$_SESSION['uid']."_  | tail -n 1`"));
 
 if(strtotime($date[1]) < time()) 
  return "expired";
 else return strtotime($date[1]); 
  
}


function secured_welcome() {
  
$services= array("Carnet d'adresse LDAP","Serveur de fichier NAS");
//,"Imprimante Laser(appeler avant)","Courriel Interne","liste de films/series","machines virtuelles","calendrier";  




echo "<p>Bonjour ".$_SESSION['nom']."<br>\n";



echo "<p class=error>si vous avez re&ccedil;u une erreur<br>\n svp t&eacute;l&eacute;chargez et installez la Chaine de certificat suivante: ";
echo  "<a href=\"https:\/\/".$_SERVER['HTTP_HOST']."/ca-chain.cert.pem\">ca-chain</a> </p>\n";

echo "<ul>\n";

echo "<li><a href=\"https:\/\/".$_SERVER['HTTP_HOST']."/index.php?dest=chgpwd\">Changer votre mot de passe reseau</a></li>";
echo "<li>"; 
get_certificate();
echo "</li>\n</ul>\n";
echo "<a href=\"https://".$_SERVER['HTTP_HOST']."/do.php?action=logout\">Sortir</a>\n";

echo "<h3>Services disponibles</h3>\n<ul>";

foreach ($services as $serv) { echo "<li>".$serv."</li>";}
echo "</ul>";

}
function unsecured_welcome() {


echo "si vous avez re&ccedil;u une erreur svp t&eacute;l&eacute;chargez et installez la Chaine de certificat suivante:<br>" ;
echo  "<a href=\"https:\/\/".$_SERVER['HTTP_HOST']."/ca-chain.cert.pem\">ca-chain</a> <br>";

echo "<ul>";
//echo "<li>Demander un acces au reseau example.com.</li>";
echo "<li><a href=\"?dest=resetpwd\">R&eacute;initialiser votre mot de passe.</a></li>";
echo "<li><a href=\"https:\/\/".$_SERVER['HTTP_HOST']."/index.php?dest=login\">Ouvrir une session</a></li>";
echo "</ul>";

  
}


# ****************** #
#      FORMS         #
# ****************** #

// **********login 
function login() {
  
  if ((isset($_GET['err'])) and ($_GET['err'] == "loginsuccess")) { show_err($_GET['err']); }
  else {
    echo "<div class=content><form action=\"do.php?action=login&dest=login\" method=POST>\n";
    echo "<div class=err>"; show_err($_GET['err']); echo "</div>\n";
    echo "<div class=row><label for=username>Usager:</label><input type=text name=username></div>\n".
         "<div class=row><label for=password>Mot de passe:</label><input type=password name=password></div>\n".
         "<div class=row><input type=submit value=Envoyer></div>\n";
    echo "</form></div>\n";
 }
}


//*********** pwd change ********
function change_password() {
  
  if ((isset($_GET['err'])) and ($_GET['err'] == "chgpwdsuccess")) { show_err($_GET['err']); }
  else {
    echo "<div class=content><form action=\"do.php?action=chgpwd&dest=chgpwd\" method=POST>\n";
    echo "<div class=err>"; show_err($_GET['err']); echo "</div>\n";
    echo "<div class=row><label for=oldpass>Ancien mot de passe</label><input type=password name=oldpass></div>\n".
         "<div class=row><label for=newpass>Nouveau mot de passe:</label><input type=password name=newpass></div>\n".
   //      "<div class=row><label for=cfrmpass>Confirm&eacute; le nouveau mot de passe:</label><input type=password name=cfrmpass></div>\n".
         "<div class=row><input type=submit value=Envoyer></div>\n";
    echo "</form></div>\n";
 }
}



//*********** pwd reset ********

function reset_password() {

 
  if ((isset($_GET['err'])) and ($_GET['err'] == "successemail")) { show_err($_GET['err']); }
  else {


  
  echo "<div class=content><form action=do.php?action=resetpwd&dest=resetpwd method=POST>\n";
   echo "<div class=err>"; show_err($_GET['err']); echo "</div>\n";
  echo "<div class=row><label for=email>Courriel:</label><input type=text name=email></div>\n".
       "<div class=row><input type=submit value=Envoyer></div>\n".
       "</form></div>\n";
  }


}


// errors 

function show_err($error="success") {
  
    switch ($error) {
      case "emailnotfound": echo "Courriel non trouver"; break; 
      case "emailnotvalid": echo "Courriel non valide"; break; 
      case "noemail": echo "Vous devez specifier un courriel"; break; 
      case "successemail" : echo "Un courriel va &ecirc;tre envoy&eacute;.\nSVP v&eacute;rifier votre boite de courriel.<br>\n<a href=\"https:\/\/".$_SERVER['HTTP_HOST']."/index.php\">Rertourner a la page d'acceuil</a>\n";break;
      case "loginsuccess": header("location: https:\/\/".$_SERVER['HTTP_HOST']."/index.php");break;
      case "chgpwdsuccess": echo "Mot de passe chang&eacute; avec succes!<br>\n<a href=\"https:\/\/".$_SERVER['HTTP_HOST']."/index.php\">Rertourner a la page d'acceuil</a>\n";break;
      case "usernotfound": echo "Usager non trouver";break;
      case "loginfail": echo "L'ouverture de session as &eacute;chou&eacute;";break;
      case "oldcredfail": echo "Les anciens usag&eacute; ou mot de passe ont &eacute;chou&eacute;";break;
      case "missingcredentials": echo "Vous devez specifier un usag&eacute et un mot de passe"; break; 
      
      //case "": echo "";break;
      
    }
  
}







?>
