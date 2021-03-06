<?php
  if (!isset($_SESSION)) session_start();
  if (!isset($_SESSION['user_authorized'])) {
    header('location: index.php');
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
    <link rel="stylesheet" href="./assets/css/style.css" type="text/css">
    <link rel="stylesheet" href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" type="text/css">

    <title>Weekly Drinks Tracker</title>
  </head>
  <body>
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <h1>
            <?php echo $_SESSION['user_name']; ?>
            <button class="userSettings" type="button" data-toggle="modal" data-target="#userSettingsModal" onclick="launchUserSettings()"><i class="fas fa-user-cog"></i></button>
          </h1>
          <h3>Count for current week: <span id="drink_count"></span></h3>
          <hr>
        </div> <!-- /col-md-12 -->
      </div> <!-- /row -->

      <div class="row">
        <div class="col-md-12">
          <button type="button" class="btn btn-outline-dark btn-lg" id="addDrinkBtn">
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
        <div class="col-md-6 text-center">
          <div id="previous_month_data">
            <span class="carolina-blue"><i class="fa fa-circle-notch fa-3x fa-spin"></i></span>
          </div>
        </div> <!-- /col-md-6 -->
      </div> <!-- /row -->

      <br><hr>
      <div class="row">
        <div class="col-md-12 text-center">
          <h4><a href="./logout.php">Logout</a></h4>
        </div> <!-- /col-md-12 -->
      </div> <!-- /row -->
    </div> <!-- /container -->

    <!-- Edit Personal Settings Modal -->
    <div class="modal fade" id="userSettingsModal" tabindex="-1" role="dialog" aria-labelledby="userSettingsModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title" id="userSettingsModalLabel"><?php echo $_SESSION['user_name']; ?>'s Personal Setttings</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form>
              <div class="form-group">
                <!-- Blue threshold -->
                <h5 class="modal-title"><span class="carolina-blue">Blue</span> threshold <=</h5>
                <div class="row">
                  <div class="col-md-12">
                    <input type="number" class="form-control" id="blueWeekly">
                  </div> <!-- /column -->
                </div> <!-- /row -->

                <!-- Orange threshold -->
                <h5 class="modal-title"><span class="gc-orange">Orange</span> threshold >=</h5>
                <div class="row">
                  <div class="col-md-12">
                    <input type="number" class="form-control" id="orangeWeekly">
                  </div> <!-- /column -->
                </div> <!-- /row -->

                <!-- Red threshold -->
                <h5 class="modal-title"><span class="wi-red">Red</span> threshold >=</h5>
                <div class="row">
                  <div class="col-md-12">
                    <input type="number" class="form-control" id="redWeekly">
                  </div> <!-- /column -->
                </div> <!-- /row -->
                <br>

                <!-- Max Per Week -->
                <h5 class="modal-title">Max/Week</h5>
                <div class="row">
                  <div class="col-md-12">
                    <input type="number" class="form-control" id="maxWeekly">
                  </div> <!-- /column -->
                </div> <!-- /row -->

              </div> <!-- /form-group -->
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-dismiss="modal">Exit</button>
            <button type="button" class="btn btn-primary" onclick="validatePersonalizations()">Save</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Historical Add Modal -->
    <div class="modal fade" id="historicalAddModal" tabindex="-1" role="dialog" aria-labelledby="historicalAddModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title" id="historicalAddModalLabel">Add past drinks</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form>
              <div class="form-group">
                <!-- How many drinks -->
                <h5 class="modal-title">How many drinks?</h5>
                <div class="row">
                  <div class="col-md-12">
                    <input type="number" class="form-control" id="numberOfDrinks">
                  </div> <!-- /column -->
                </div> <!-- /row -->
                <br>

                <!-- What date -->
                <h5 class="modal-title">Date of drink(s)?</h5>
                <div class="row">
                  <div class="col-md-12">
                    <input id="dateToAddDrink" width="184" placeholder="yyyy-mm-dd">
                  </div> <!-- /column -->
                </div> <!-- /row -->

              </div> <!-- /form-group -->
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-dismiss="modal">Exit</button>
            <button type="button" class="btn btn-primary" onclick="validateHistoricalAdd()">Save</button>
          </div>
        </div>
      </div>
    </div>

    <?php include './assets/js/universal_js.html'; ?>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript" src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js"></script>
    <script type="text/javascript" src="./assets/js/review.js"></script>
  </body>
</html>
