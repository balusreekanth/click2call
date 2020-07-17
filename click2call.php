<?php

/**
 * Created by Sreekanth Balu
 * Date: 16/07/12020
 * Email: balusreekanth@gmail.com
 * Time: 12:54 PM
 */


/**
* Licence:
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */



// please replcae site key with your google recaptch site secret
define("RECAPTCHA_V3_SECRET_KEY", '6Lf0GLIZAAANANaG0xTXNGBx_BxO8FF6pfplZOG0');


//define which method to use. Only one should be true.
define("CALL_FILES", "TRUE");
define("AMI_METHOD", "FALSE");

//define which extension to call
define("DEST_EXT", "2020");


//define host port and credentials  for AMI
define("AST_HOST", "127.0.0.1");
define("AST_PORT","5038");
define("AST_USER", "demouser");
define("AST_SECRET", "2dDf9KFXcNNZwp");


//define  where to place call files before moving to asterisk outgoing spool directory
//this will avoid asterisk reading files incorrectly
define("CALL_DIR", "/tmp/");
//define  wait for answer time  before call fails
define("CALL_WAITTIME","40");
//define  call priority
define("CALL_PRIORITY", "1");
//define  maximum number of call retry
define("CALL_RETRY", "2");
//define  caller name to identify at receiving side
define("DISPLAY_CALL_ID", "NO");
//define in which context call should be placed
define("CALL_CONTEXT", "click2call");




//Probably You do not want to modify below lines

if (isset($_POST['phone']) && $_POST['phone']) {
    $phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
} else {

//Replace location to be redirected if request is not valid
 header('location: click2call.html');
    exit;
}

$token = $_POST['token'];
$action = $_POST['action'];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('secret' => RECAPTCHA_V3_SECRET_KEY, 'response' => $token)));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
$arrResponse = json_decode($response, true);


if($arrResponse["success"] == '1' && $arrResponse["action"] == $action && $arrResponse["score"] >= 0.5) {


$caller_id = "from-web";
$phone = $_REQUEST['phone'];

if (DISPLAY_CALL_ID=='YES'){
$caller_id = $phone;
}

if (defined(AMI_METHOD) && defined(CALL_FILES) && (AMI_METHOD=='TRUE') &&(CALL_FILES=='FALSE'))
 {

$ast_socket = fsockopen(AST_HOST, AST_PORT, $errnum, $errdesc) or die(Header( "Location: click2call.html"));
fputs($ast_socket, "Action: login\r\n");
fputs($ast_socket, "Events: off\r\n");
fputs($ast_socket, "Username: ".AST_USER."\r\n");
fputs($ast_socket, "Secret: ".AST_SECRET."\r\n\r\n");
fputs($ast_socket, "Action: originate\r\n");
fputs($ast_socket, "Channel: SIP/".DEST_EXT."\r\n");
fputs($ast_socket, "WaitTime: ".CALL_WAITTIME."\r\n");
fputs($ast_socket, "CallerId: ".$caller_id."\r\n");
fputs($ast_socket, "Exten: s\r\n");
fputs($ast_socket, "Variable:phone=$phone\r\n");
fputs($ast_socket, "Context: ".CALL_CONTEXT."\r\n");
fputs($ast_socket, "Priority: ".CALL_PRIORITY."\r\n\r\n");
fputs($ast_socket, "Action: Logoff\r\n\r\n");

echo "<br>";
echo "Calling  now....Redirecting in 10 seconds";
require 'cc_redirect.php';
sleep(3);
fclose($ast_socket);
}




if (defined(AMI_METHOD) && defined(CALL_FILES) && (AMI_METHOD=='FALSE') &&(CALL_FILES=='TRUE'))

{


$tmpfile = CALL_DIR.$phone.".call";
$ast_out_dir  = '/var/spool/asterisk/outgoing/';
fopen($tmpfile, "w");
$content = "Channel: SIP/".DEST_EXT."\n";
$content .= "Callerid:<" .$caller_id.">\n";
$content .= "WaitTime:".CALL_WAITTIME."\n";
$content .= "Context:" .CALL_CONTEXT."\n";
$content .= "Extension: s\n";
$content .= "Priority: 1\n";
$content .= "Set: phone={$phone}\n";

//Do not write or create the call file directly in the outgoing directory

echo file_put_contents($tmpfile, $content, FILE_TEXT | LOCK_EX);
rename($tmpfile, $ast_out_dir . pathinfo($tmpfile, PATHINFO_BASENAME));

//create a safe  context  in dialplan example:
//[click2call]
//exten => s,1,Dial(SIP/yourtrunk/${phone})
echo "<br>";
echo "Calling  now....Redirecting in 10 seconds";
require 'cc_redirect.php';

}



} else {

    // do not process ,probably spam
}

?>
