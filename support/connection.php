<?php
  /* Database connection settings */
  $host = "localhost";
  $dbUId = "";
  $pass = "";
  $db = "";

  //Check to see if $mysql already exists, instantiate if not
  if (!isset($mysqli)) {
    $mysqli = new mysqli($host,$dbUId,$pass,$db) or die($mysqli->connect_error);
    if (!$mysqli->set_charset("utf8mb4")) {
      $db_issue = "Database error loading UTF8 character set.<br> We're working on it, but please feel free to reach out and let us know about Error Code #".$mysqli->errno;
    }
  }

  //Validate connection
  if ($mysqli->connect_error != NULL) {
    $db_issue = "<b>Database connection failed.</b><br> We're working on it, but please feel free to reach out and let us know about Connection Error Code #".$mysqli->connect_errno;
  }
?>
