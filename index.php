<?php
session_start();

/*****************************************************************************
/*Copyright (C) 2006 Tony Iha Kazungu
/*****************************************************************************
Hotel Management Information System (HotelMIS Version 1.0), is an interactive system that enables small to medium
sized hotels take guests bookings and make hotel reservations.  It could either be uploaded to the internet or used
on the hotel desk computers.  It keep tracks of guest bills and posting of receipts.  Hotel reports can alos be
produce to make work of the accounts department easier.

This program is free software; you can redistribute it and/or modify it under the terms
of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License,
or (at your option) any later version.
This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with this program;
if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA or 
check for license.txt at the root folder
/*****************************************************************************
For any details please feel free to contact me at taifa@users.sourceforge.net
Or for snail mail. P. O. Box 938, Kilifi-80108, East Africa-Kenya.
/*****************************************************************************/
error_reporting(E_ALL & ~E_NOTICE);
ob_start();
include_once ("queryfunctions.php");
include_once ("functions.php");

if (!empty($_POST["login"])){

	switch($_POST["login"]){
		case "Login":
			Login();
			break;
		case "Logout":
			echo "<center><font color=\"#0033CC\"><b>Session successful ended.</b></font></center>";
			setcookie("data_login","",time()-60);
			//ob_start();
			session_unset();
			session_destroy();
			break;	
	}
}

//don't know what this does.
if (!empty($_POST["request"])&&$_POST["request"]=="submit"){
	header("Location: reservation.php");
}

function Login(){
	$username=$_POST['username'];
	$password=$_POST['password'];
	
	if($username && $password) {
	  $conn=($GLOBALS["___mysqli_ston"] = mysqli_connect(HOST, USER, PASS)) or die ("Whoops");    // Connect to the database, or if connection fails print error message.
	  $password = md5($password);          // encode submited password with MD5 encryption and store it back in the same variable. If not on a windows box, I suggest you use crypt()
	  $sql = "select * from users where loginname='$username'";   // query statment that gets the username/password from 'login' where the username is the same as the one you submited
	  $r = ((mysqli_query($GLOBALS["___mysqli_ston"], "USE " . constant('DB'))) ? mysqli_query($GLOBALS["___mysqli_ston"], $sql) : false);  // Execute Query
	  
	  // if no rows for that database come up, redirect.
	  if(!mysqli_num_rows($r)){
			((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
			header("Location: index.php");  // This is the redirection, notice it uses $SCRIPT_NAME which is a predefined variable with the name of the script in it.
		}else{
			$passed=@($GLOBALS["___mysqli_ston"] = mysqli_connect(HOST, USER, PASS));
		}
		
		mysqli_select_db($GLOBALS["___mysqli_ston"], constant('DB'));
		if (!$passed) {
			//echo 'Could not connect: ' . mysql_error();
			echo "<center><font color=\"#FF0000\"><b>Invalid User Name or Password</b></font></center>";     
			$_SESSION["logged"]=0;
			$_SESSION["userid"]="";
		}else{
			//$sql="select pass('$password') as pass, fname, sname from users";
			$sql="select pass, fname, sname, loginname, userid from users where loginname='$username'";
			$password=mkr_query($sql,$passed);
			$password=mysqli_fetch_array($password);
			$_SESSION["employee"]=$password['fname'] ." ". $password['sname'];
			$_SESSION["loginname"]=$password['loginname'];
			$_SESSION["userid"]=$password['userid'];
			$password=$password['pass'];		
			//******************************************************************
			//*Not the best option but produce the required results - unencrypted password saved to a cookie
			//******************************************************************
			setcookie("data_login","$username $password",time()+60*30);  // Set the cookie named 'candle_login' with the value of the username (in plain text) and the password (which has been encrypted and serialized.)
			$_SESSION["logged"]=1;
			// set variable $msg with an HTML statement that basically says redirect to the next page. The reason we didn't use header() is that using setcookie() and header() at the sametime isn't 100% compatible with all browsers, this is more compatible.
			$msg = "<meta http-equiv=\"Refresh\" content=\"0;url=./index.php\">"; //put index.php
		}
	}else{
		echo "<center><font color=\"#FF0000\"><b>Enter your UserName and Password to login on to the system</b></font></center>";
		$_SESSION["logged"]=0;
	}
	if($msg) echo $msg;  //if $msg is set echo it, resulting in a redirect to the next page.
	//}
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="css/new.css" rel="stylesheet" type="text/css">
<title>Hotel Management Information System</title>

<script type="text/javascript">
<!--
var request;
var dest;

function loadHTML(URL, destination){
    dest = destination;
	if (window.XMLHttpRequest){
        request = new XMLHttpRequest();
        request.onreadystatechange = processStateChange;
        request.open("GET", URL, true);
        request.send(null);
    } else if (window.ActiveXObject) {
        request = new ActiveXObject("Microsoft.XMLHTTP");
        if (request) {
            request.onreadystatechange = processStateChange;
            request.open("GET", URL, true);
            request.send();
        }
    }
}

function processStateChange(){
    if (request.readyState == 4){
        contentDiv = document.getElementById(dest);
        if (request.status == 200){
            response = request.responseText;
            contentDiv.innerHTML = response;
        } else {
            contentDiv.innerHTML = "Error: Status "+request.status;
        }
    }
}

function loadHTMLPost(URL, destination){
    dest = destination;
	if (window.XMLHttpRequest){
        request = new XMLHttpRequest();
        request.onreadystatechange = processStateChange;
        request.open("POST", URL, true);
        request.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");
      	request.setRequestHeader("Content-length", parameters.length);
      	request.setRequestHeader("Connection", "close");
		request.send("good");
    } else if (window.ActiveXObject) {
        request = new ActiveXObject("Microsoft.XMLHTTP");
        if (request) {
            request.onreadystatechange = processStateChange;
            request.open("POST", URL, true);
            request.send();
        }
    }
}

//-->	 
</script>
</head>

<body>
<form action="index.php" method="post" enctype="multipart/form-data">

<table width="102%"  border="0" cellpadding="1" bgcolor="#66CCCC" align="center">
  <tr valign="top">
    <td width="18%" bgcolor="#FFFFFF">
	<table width="100%" border="0" cellpadding="1" cellspacing="5">
	  <tr>
    <td bgcolor="#66CCCC">
		<table cellspacing=0 cellpadding=0 width="100%" align="left" bgcolor="#FFFFFF">
      <tr><td align="center"><a href="index.php"><img src="images/titanic1.gif" width="70" height="74" border="0"/><br>Home</a></td></tr>
	  <tr><td width="110"> Username:<br><input name="username" type="text" width="10"></input> </td></tr>
      <tr><td> Password: <br><input name="password" type="password" width="10"></input></td></tr>
      <tr>
        <td align="center">

		<?php signon(); ?>		

		</td>
		</tr>
	  </table></td></tr>
		<?php require_once("menu_header.php"); ?>	

    <tr><td align="center"><div onclick="loadHTML('futures.php','RequestDetails')" style="cursor:pointer"><h2>Futures</h2></div></td></tr>		
    </table>
	</td>
    
    <td width="82%" bgcolor="#FFFFFF"><table width="100%"  border="0" cellpadding="1">
      <tr>
        <td align="center"></td>
      </tr>
      <tr>
        <td>
		<H4>HOTEL MANAGEMENT INFORMATION SYSTEMS</H4> </td>
      </tr>
      <tr>
        <td><div id="Requests">
		</div></td>
		
      </tr>
	  <tr bgcolor="#66CCCC" >
        <td align="left">
		<div id="RequestDetails"></div>
		</td>
      </tr>
    </table></td>
  </tr>
  <tr><td><a href="www.php.net" target="_blank"><img src="images/php-power-white.gif" width="88" height="31" border="0" /></a><a href="www.mysql.com" target="_blank"><img src="images/powered-by-mysql-88x31.png" width="88" height="31" border="0" /></a></td>
  <td>TaifaTech Networks &copy; 2006. Vers 1.0 <a href="http://sourceforge.net"><img src="http://sflogo.sourceforge.net/sflogo.php?group_id=172638&amp;type=1" width="88" height="31" border="0" alt="SourceForge.net Logo" /></a></td>
  </tr>
</table>
</form>
</body>
</html>