<?php
  //Log out process, unsets and destroys session variables
  if (!isset($_SESSION)) session_start();
  session_unset();
  session_destroy();
  if (isset($_COOKIE['drinks_tracker'])) {
    unset($_COOKIE['drinks_tracker']);
  }
  setcookie("drinks_tracker", "", time() - 3600, NULL, NULL, TRUE, TRUE);
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php include './assets/css/universal_css.html'; ?>
    <link rel="stylesheet" type="text/css" href="./assets/css/style.css">

    <title>WDT Log Out</title>
  </head>
  <body>
    <h1>Logged Out!</h1>
    <a href="./index.php">Home</a>


    <?php include './assets/js/universal_js.html'; ?>
  </body>
</html>
