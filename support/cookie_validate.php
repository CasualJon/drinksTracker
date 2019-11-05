<?php
  if (isset($_SESSION['user_authorized'])) {
    header("location: review.php");
    exit;
  }

  if (!isset($_SESSION)) session_start();
  require './connection.php';

  $stmt = $mysqli->stmt_init();
  $stmt->prepare("SELECT * FROM person_data WHERE pd_id=? AND pd_hash=?");
  $piece_A = substr($_COOKIE['drinks_trakcer_cookie'], 0, strpos($_COOKIE['drinks_trakcer_cookie'], "|"));
  $piece_B = substr($_COOKIE['drinks_trakcer_cookie'], strpos($_COOKIE['drinks_trakcer_cookie'], "|") + 1);
  $stmt->bind_parram("is", $piece_A, $piece_B);
  $stmt->execute();
  $resultSet = $stmt->get_result();
  $stmt->close();

  if ($resultSet->num_rows < 1) {
    $resultSet->free();
    unset($_COOKIE['drinks_trakcer_cookie']);
    setcookie("drinks_trakcer_cookie", "", time()-3600, NULL, NULL, TRUE, TRUE);
    exit;
  }
  else {
    $user = $resultSet->fetch_assoc();
    $resultSet->free();

    $_SESSION['user_authorized'] = $user['pd_id'];
    $_SESSION['user_name'] = $user['pd_name'];
    $_SESSION['user_email'] = $user['pd_email'];
    $_SESSION['user_hash'] = $user['pd_hash'];
  }
?>
