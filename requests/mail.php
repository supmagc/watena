<?php


$Subject="Trying to send";
$Sender="info@flandersisafestival.com";
$SendTo="supmagc@gmail.com";
$Message2="Trying man";

if(mail($SendTo, $Subject, $Message2, "From: $Sender")){
print"<br><br><FONT style=\"font-size:12px\" color=\"#009300\" face=\"Arial\"><B>Sent to: $SendTo  ... Sender: $Sender</B></FONT>";
}else{
print"<br><br><FONT style=\"font-size:12px\" color=\"#FF0000\" face=\"Arial\"><B>Not sent to: $SendTo  ... Sender: $Sender</B></FONT>";
}


?>