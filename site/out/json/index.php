<?php header('Content-Type: application/json'); ?>
<?php
    require "../../CONFIG.php";
    
    if (is_numeric($_GET["limit"]) && !empty((int) $_GET["limit"])) {
      $limit = (int) $_GET["limit"];
    } else {
      $limit = 100;
    }
    
    if ($limit < 0) {
      # this way, we can use a negative limit to return ascending IDs
      # i.e. limit=100 returns [3,2,1] while limit=-100 returns [1,2,3]
      $limit = abs($limit);
      $order = "ASC";
    } else {
      $order = "DESC";
    }
    
    if (is_numeric($_GET["offset"]) && $_GET["offset"] > 0) {
      $offset = (int) $_GET["offset"];
    } else {
      $offset = 0;
    }
    
    $db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (mysqli_connect_errno())
    {
        $result = " ";
    }
    else
    {
        $sql = "SELECT id,inputsource,content,fromphone,DATE_FORMAT(created, '%Y-%m-%dT%H:%i:%s0Z') as created,approved FROM $TABLE_NAME ORDER BY id " . $order . " LIMIT " . $limit . " OFFSET " . $offset;
        $result = $db->query($sql);
        $rows = array();
        for ($i=0; $i < $result->num_rows; $i++) { 
          $row = $result->fetch_array(MYSQLI_ASSOC);
          array_push($rows,$row);
        }
        echo json_encode($rows);
        $result->close();
        mysqli_close($db);
    }

?>