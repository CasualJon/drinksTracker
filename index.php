<?php
  //Session start
  if (!isset($_SESSION)) session_start();
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login']) && isset($_POST['pass'])) require './support/login.php';
  }

  //If logged in already, forward to review page
  if (isset($_SESSION['user_authorized'])) {
    header('location: review.php');
    exit;
  }
  elseif (isset($_COOKIE['drinks_tracker'])) {
    require './support/cookie_validate.php';
    if (isset($_SESSION['user_authorized'])) {
      header('location: review.php');
      exit;
    }
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

    <title>WDT Log In</title>
  </head>
  <body>
    <div class="container">
      <div class="row">
        <div class="col-md-12">


          <div class="form">
            <h1 class="login-title">Weekly Tracker</h1>

            <form action="index.php" method="post" autocomplete="off">
              <div class="field-wrap">
                <label>Name</label>
                <input type="text" required autocomplete="off" name="login" autofocus />
              </div>

              <div class="field-wrap">
                <label>Password</label>
                <input type="password" required autocomplete="off" name="pass" />
              </div>

              <div class="text-right">
                <button type="submit" class="btn btn-dark">
                  <i class="fas fa-arrow-right fa-3x"></i>
                </button>
              </div>
            </form>
          </div> <!-- /form -->

        </div> <!-- /col -->
      </div> <!-- /row -->
    </div> <!-- /container -->

    <?php include './assets/js/universal_js.html'; ?>
    <script type="text/javascript" src="./assets/js/index.js"></script>
  </body>
</html>
