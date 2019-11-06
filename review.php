<?php
  if (!isset($_SESSION)) session_start();
  if (!isset($_SESSION['user_authorized'])) {
    http_response_code(403);
    exit;
  }

  //Fetch current weekly count info as the default
  require './support/connection.php';
  //Set a cookie to help later logins
  if (!isset($_COOKIE['drinks_tracker'])) {
    //604800 = 1 week
    setcookie('drinks_tracker', $_SESSION['user_authorized'].'|'.$_SESSION['user_hash'], time()+604800, NULL, NULL, TRUE, TRUE);
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
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <h1><?php echo $_SESSION['user_name']; ?></h1>
          <h3>Count for current week: <span id="drink_count"></span></h3>
          <hr>
        </div> <!-- /col-md-12 -->
      </div> <!-- /row -->

      <div class="row">
        <div class="col-md-12">
          <button type="button" class="btn btn-outline-dark btn-lg" onclick="addDrink()">
            <i class="fas fa-plus fa-2x"></i>
            <i class="fas fa-wine-glass-alt fa-2x"></i>
          </button>

          <span class="push-right">
            <button type="button" class="btn btn-outline-danger" onclick="whoopsies()" id="undo_button" hidden>
              <i class="fas fa-undo"></i>
            </button>
          </span>
        </div> <!-- /col-md-12 -->
      </div> <!-- /row -->

      <br>
      <div class="row">
        <div class="col-md-6">
          <div id="current_week_barchart">
            <span class="carolina-blue"><i class="fa fa-circle-notch fa-3x fa-spin"></i></span>
          </div>
        </div> <!-- /col-md-6 -->
        <div class="col-md-6">
        </div> <!-- /col-md-6 -->
      </div> <!-- /row -->

      <br><hr>
      <div class="row">
        <div class="col-md-12 text-center">
          <h4><a href="./logout.php">Logout</a></h4>
        </div> <!-- /col-md-12 -->
      </div> <!-- /row -->
    </div> <!-- /container -->

    <?php include './assets/js/universal_js.html'; ?>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript" src="./assets/js/review.js"></script>
  </body>
</html>
