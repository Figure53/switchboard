<?php header('Content-Type: application/json; charset=utf-8'); ?>
<?php
    require "../../CONFIG.php";
    require BASEPATH . "/helpers/request_limit.php";
    require BASEPATH . "/helpers/request_offset.php";
    require BASEPATH . "/helpers/request_random.php";

    $db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!mysqli_connect_errno())
    {
        $db->set_charset("utf8");
        $db->query("SET NAMES utf8");
        $db->query("SET CHARACTER SET utf8");

        if ($random)
            $sql = "SELECT id,inputsource,content,fromphone,DATE_FORMAT(created, '%Y-%m-%dT%H:%i:%s0Z') as created,approved FROM $TABLE_NAME ORDER BY RAND() LIMIT ? ";
        else
            $sql = "SELECT id,inputsource,content,fromphone,DATE_FORMAT(created, '%Y-%m-%dT%H:%i:%s0Z') as created,approved FROM $TABLE_NAME ORDER BY id " . $order . " LIMIT ? OFFSET ? ";
        $statement = $db->prepare($sql);  
        if ($statement)
        {
            if ($random)
                $statement->bind_param('i', $limit);
            else
                $statement->bind_param('ii', $limit, $offset);
            if ($statement->execute())
            {
                $result = $statement->get_result();
                $rows = array();
                while ($row = $result->fetch_array(MYSQLI_ASSOC))
                {
                    array_push($rows,$row);
                }
                $result->close();
                echo json_encode($rows);
            }
            $statement->close();
        }
        mysqli_close($db);
    }
?>