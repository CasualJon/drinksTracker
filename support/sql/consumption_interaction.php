<?php
  require '../connection.php';
  if (!isset($_SESSION)) session_start();
  if (!isset($_SESSION['user_authorized'])) {
    http_response_code(403);
    exit;
  }

  header('Content-Type: application/json');
  $result = array();

  if (!isset($_POST['functionname'])) $result['error'] = 'No function name provided.';
  if (!isset($_POST['arguments'])) $result['error'] = 'No function arguments provided.';

  if (!isset($result['error'])) {
    switch ($_POST['functionname']) {
      case 'fetch_within_range':
        $stmt = $mysqli->stmt_init();
        $stmt->prepare('SELECT cl_id, cl_datetime FROM consumption_log WHERE cl_person=? and cl_datetime >= ?');
        $stmt->bind_param("is", $_SESSION['user_authorized'], $_POST['arguments'][0]);
        $stmt->execute();
        $resultSet = $stmt->get_result();
        if ($resultSet->num_rows > 0) {
          while ($row = $resultSet->fetch_assoc()) array_push($result, $row);
        }
        $resultSet->free();
        $stmt->close();
        break;

      case 'add_drink':
        $now =  date('Y-m-d H:i:s');
        $stmt = $mysqli->stmt_init();
        $stmt->prepare('INSERT INTO consumption_log (cl_person, cl_datetime) VALUES (?, ?)');
        $stmt->bind_param('is', $_SESSION['user_authorized'], $now);
        $result['outcome'] = $stmt->execute();
        $stmt->close();
        break;

      case 'remove_last':
        $stmt = $mysqli->stmt_init();
        $stmt->prepare('SELECT cl_id FROM consumption_log WHERE cl_person=? ORDER BY cl_id DESC LIMIT 1');
        $stmt->bind_param('i', $_SESSION['user_authorized']);
        $stmt->execute();
        $resultSet = $stmt->get_result();

        if ($resultSet->num_rows < 1) {
          $resultSet->free();
          $stmt->close();
          $result['error'] = 'Failed to retrieve ID of last drink added.';
          break;
        }

        $cl_data = $resultSet->fetch_assoc();
        $resultSet->free();
        $stmt->prepare('DELETE FROM consumption_log WHERE cl_id=?');
        $stmt->bind_param('i', $cl_data['cl_id']);
        $result['outcome'] = $stmt->execute();
        $stmt->close();
        break;

      default:
        $result['error'] = "Well, shit.";
        break;
    }
  }

    echo json_encode($result);
?>
