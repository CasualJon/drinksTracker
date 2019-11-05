<?php
  if (!isset($_SESSION)) session_start();
  if (!isset($_SESSION['user_authorized'])) {
    http_response_code(403);
    exit;
  }
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Personal utility to help track drinks per week.">
    <meta name="author" content="Jon Cyrus">

    <?php include './assets/css/universal_css.html'; ?>
    <link rel="stylesheet" type="text/css" href="./assets/css/style.css">

    <title>Weekly Drinks Tracker</title>
  </head>
  <body>
    <?php echo "REVIEW PAGE!<br>"; var_dump($_SESSION); ?>
    <br>
    <a href="./logout.php">Logout</a>
    <?php include './assets/js/universal_js.html'; ?>
  </body>
</html>
