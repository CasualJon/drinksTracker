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
        $stmt->bind_param('is', $_SESSION['user_authorized'], $_POST['arguments'][0]);
        $stmt->execute();
        $resultSet = $stmt->get_result();
        if ($resultSet->num_rows > 0) {
          while ($row = $resultSet->fetch_assoc()) array_push($result, $row);
        }
        $resultSet->free();

        //Return personal data preferences as the final element
        $stmt->prepare("SELECT pd_low, pd_med, pd_high, pd_max FROM person_data WHERE pd_id=?");
        $stmt->bind_param("i", $_SESSION['user_authorized']);
        $stmt->execute();
        $resultSet = $stmt->get_result();
        if ($resultSet->num_rows < 1) {
          $limits['pd_low'] = NULL;
          $limits['pd_med'] = NULL;
          $limits['pd_high'] = NULL;
          $limits['pd_max'] = NULL;
        }
        else $limits = $resultSet->fetch_assoc();
        $resultSet->free();
        array_push($result, $limits);
        $stmt->close();
        break;

      case 'fetch_historical_aggregates':
        $target_start = new DateTime($_POST['arguments'][0], new DateTimeZone('America/Chicago'));
        $one_week_interval = new DateInterval('P7D');
        $target_end = new DateTime($_POST['arguments'][0], new DateTimeZone('America/Chicago'));
        $target_end = $target_end->add($one_week_interval);
        $stmt = $mysqli->stmt_init();
        $stmt->prepare("SELECT COUNT(cl_id) AS cnt FROM consumption_log WHERE cl_person=? AND cl_datetime >= ? AND cl_datetime < ?");

        for ($i = 0; $i < $_POST['arguments'][1]; $i++) {
            $str_start = $target_start->format('Y-m-d');
            $str_end = $target_end->format('Y-m-d');

            $stmt->bind_param('iss', $_SESSION['user_authorized'], $str_start, $str_end);
            $stmt->execute();
            $resultSet = $stmt->get_result();
            if ($resultSet->num_rows < 1) array_push($result, NULL);
            else {
              $row = $resultSet->fetch_assoc();
              array_push($result, [$str_start, $str_end, $row['cnt']]);
            }
            $resultSet->free();

            $target_start = $target_start->add($one_week_interval);
            $target_end = $target_end->add($one_week_interval);
        }
        $stmt->close();
        break;

      case 'update_personal_limits':
        $blue = NULL; $orange = NULL; $red = NULL; $max = NULL;
        if (!empty($_POST['arguments'][0])) {
          $blue = $_POST['arguments'][0];
          $orange = $_POST['arguments'][1];
          $red = $_POST['arguments'][2];
          $max = $_POST['arguments'][3];
        }

        $stmt = $mysqli->stmt_init();
        $stmt->prepare('UPDATE person_data SET pd_low=?, pd_med=?, pd_high=?, pd_max=? WHERE pd_id=?');
        $stmt->bind_param('iiiii', $blue, $orange, $red, $max, $_SESSION['user_authorized']);
        $result['outcome'] = $stmt->execute();
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
