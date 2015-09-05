<?php
    require "../../CONFIG.php";
    
    header('Content-Type: application/json');
    
    $db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (mysqli_connect_errno())
    {
        $result = " ";
    }
    else
    {
        $sql = "SELECT id,inputsource,content,fromphone,DATE_FORMAT(created, '%Y-%m-%dT%H:%i:%s0Z') as created,approved FROM $TABLE_NAME ORDER BY id DESC";
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