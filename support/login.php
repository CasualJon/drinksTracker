<?php
  if (!isset($_SESSION)) session_start();
  require __DIR__.'/connection.php';

  $stmt = $mysqli->stmt_init();
  $stmt->prepare("SELECT * FROM person_data WHERE pd_name=?");
  $stmt->bind_param("s", $_POST['login']);
  $stmt->execute();
  $resultSet = $stmt->get_result();
  $stmt->close();

  if ($resultSet->num_rows < 1) {
    $resultSet->free();
    exit;
  }
  $user = $resultSet->fetch_assoc();
  $resultSet->free();

  if (password_verify($_POST['pass'], $user['pd_password'])) {
    $_SESSION['user_authorized'] = $user['pd_id'];
    $_SESSION['user_name'] = $user['pd_name'];
    $_SESSION['user_email'] = $user['pd_email'];
    $_SESSION['user_hash'] = $user['pd_hash'];
  }
?>
