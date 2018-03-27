<?php

	session_start();

	$tr = "";

	$sqluser = "hellouser";
	$sqlpass = "d3317a710fe5029ad06a8c20539f";
	$sqldb = "helloworld";
	$sqlhost = "mysql.hello.lan";

	$link = mysqli_connect($sqlhost, $sqluser, $sqlpass, $sqldb);

	if (!$link) {
		echo "Error: Unable to connect to MySQL." . PHP_EOL;
		echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
		echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
		exit;
	}

	$q = "SHOW TABLES LIKE 'user'";
	$r = mysqli_query($link, $q); 
	if($r->num_rows == 0) {
		$q = "CREATE TABLE user ( id INT AUTO_INCREMENT PRIMARY KEY, email VARCHAR(255) NOT NULL, pass VARCHAR(32) NOT NULL ); ";
		mysqli_query($link, $q);
	}
	

	if ($_REQUEST["mfs"] == "1") {
		$_email = mysqli_real_escape_string ($link , $_REQUEST["email"] );
		$_pass = mysqli_real_escape_string ($link , $_REQUEST["pass"] );
		$mds = md5($_REQUEST["pass"]);

		switch (strtolower($_REQUEST["action"])) {
			case "logout" :
				$_SESSION["logged"] = 0;
				unset ($_SESSION["email"]);
				break;
			case "login":
				$q = "SELECT COUNT(*) FROM user WHERE email='$_email' AND pass='$mds'" ;
				$r = mysqli_query ($link, $q); 
				$m = $r->fetch_array();

				if ($m[0] == "1") {
					$_SESSION["logged"] = 1;
					$_SESSION["email"] = $_REQUEST["email"];
				} else {
					$tr = "Login failed ... <Br>";
					$_SESSION["logged"] = 0;
					unset ($_SESSION["email"]);
				}
				break;

			case "register":
				$q = "SELECT COUNT(*) AS NR FROM user WHERE email='$_email'";
				$r = mysqli_query ($link, $q); 
				$m = $r->fetch_array();

				if ($m[0] == "0") {
					$q = "INSERT INTO user SET email='$_email', pass='$mds'";
					mysqli_query ($link, $q); 
					$tr = "You are registered, try to log in with your credentials ... <br>";
				} else {
					$tr = "Register failed ... <br>";
				}

				break;

			default :
				$tr = "Error occured ... <Br>"; 
		}
	}

	$t = "";
	
	$t .= "<p>";
	$t .= "<div align=center>";
	$t .= $tr;
	$t .= "</div>";
	$t .= "</p>";

	if ($_SESSION["logged"] != "1") {
		$t .= "<p>";
		$t .= "<div align=center>";
		$t .= "<form method=post>";
		$t .= "<input type=hidden name=mfs value=1>";
		$t .= "<table>";
		$t .= "<tr><td>E-mail: </td><td><input required type=text name=email maxlength=254></td></tr>";
		$t .= "<tr><td>Password: </td><td><input required type=password name=pass maxlength=32></td></tr>";
		$t .= "<tr><td colspan=2><br></td></tr>";
		$t .= "<tr><td><input type=submit name='action' value='Login'>&nbsp;<input type=submit name='action' value='Register'></td></tr>";
		$t .= "</table>";
		$t .= "</form>";

		$t .= "</div>";
		$t .= "</p>";
	} else {
		$t .= "<p>";
		$t .= "<div align=center>";
		$t .= "<form method=post>";
		$t .= "<input type=hidden name=mfs value=1>";
		$t .= "<tr><td>Hello ".$_SESSION["email"]."<br></td></tr>";
		$t .= "<tr><td><input type=submit name='action' value='Logout'></td></tr>";
		$t .= "</table>";
		$t .= "</form>";

		$t .= "</div>";
		$t .= "</p>";
	}

	echo $t;
?>

