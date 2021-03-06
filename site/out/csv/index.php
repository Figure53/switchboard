<?php header('Content-Type: text/csv; charset=utf-8'); ?>
id,inputsource,content,fromphone,date,approved,used
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

        if (!empty($_REQUEST['type']))
            $type = $_REQUEST['type'];
        else 
            $type = "all";
        switch ($type)
        {
            case 'all':
                if ($random)
                    $sql = "SELECT id,inputsource,content,fromphone,DATE_FORMAT(created, '%Y-%m-%dT%H:%i:%s0Z') as created,approved,used FROM $TABLE_NAME ORDER BY RAND() LIMIT ? ";
                else
                    $sql = "SELECT id,inputsource,content,fromphone,DATE_FORMAT(created, '%Y-%m-%dT%H:%i:%s0Z') as created,approved,used FROM $TABLE_NAME ORDER BY id " . $order . " LIMIT ? OFFSET ? ";
                break;
            case 'pending':
                if ($random)
                    $sql = "SELECT id,inputsource,content,fromphone,DATE_FORMAT(created, '%Y-%m-%dT%H:%i:%s0Z') as created,approved,used FROM $TABLE_NAME WHERE approved = 0 ORDER BY RAND() LIMIT ? ";
                else
                    $sql = "SELECT id,inputsource,content,fromphone,DATE_FORMAT(created, '%Y-%m-%dT%H:%i:%s0Z') as created,approved,used FROM $TABLE_NAME WHERE approved = 0 ORDER BY id " . $order . " LIMIT ? OFFSET ? ";
                break;
            case 'rejected':
                if ($random)
                    $sql = "SELECT id,inputsource,content,fromphone,DATE_FORMAT(created, '%Y-%m-%dT%H:%i:%s0Z') as created,approved,used FROM $TABLE_NAME WHERE approved = -1 ORDER BY RAND() LIMIT ? ";
                else
                    $sql = "SELECT id,inputsource,content,fromphone,DATE_FORMAT(created, '%Y-%m-%dT%H:%i:%s0Z') as created,approved,used FROM $TABLE_NAME WHERE approved = -1 ORDER BY id " . $order . " LIMIT ? OFFSET ? ";
                break;
            case 'unused':
                if ($random)
                    $sql = "SELECT id,inputsource,content,fromphone,DATE_FORMAT(created, '%Y-%m-%dT%H:%i:%s0Z') as created,approved,used FROM $TABLE_NAME WHERE used = 0 ORDER BY RAND() LIMIT ? ";
                else
                    $sql = "SELECT id,inputsource,content,fromphone,DATE_FORMAT(created, '%Y-%m-%dT%H:%i:%s0Z') as created,approved,used FROM $TABLE_NAME WHERE used = 0 ORDER BY id " . $order . " LIMIT ? OFFSET ? ";
                break;
            case 'approved':
            default:
                if ($random)
                    $sql = "SELECT id,inputsource,content,fromphone,DATE_FORMAT(created, '%Y-%m-%dT%H:%i:%s0Z') as created,approved,used FROM $TABLE_NAME WHERE approved = 1 ORDER BY RAND() LIMIT ? ";
                else
                    $sql = "SELECT id,inputsource,content,fromphone,DATE_FORMAT(created, '%Y-%m-%dT%H:%i:%s0Z') as created,approved,used FROM $TABLE_NAME WHERE approved = 1 ORDER BY id " . $order . " LIMIT ? OFFSET ? ";
                break;
        }

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
                while ($row = $result->fetch_array(MYSQLI_ASSOC))
                {
                    foreach($row as $key => $value) {
                        $safeValue = htmlspecialchars($value, ENT_NOQUOTES, 'UTF-8');
                        if ($key == "content")
                            echo "\"$safeValue\"";
                        else
                            echo "$safeValue";
                        if ($key != "used")
                            echo ",";
                    }
                    echo "\n";

                    $id = $row['id'];
                    $used = $row['used'];
                    if ($used == false)
                    {
                        $set_used = $db->prepare("UPDATE $TABLE_NAME SET used = 1 WHERE id = ?");
                        if ($set_used)
                        {
                            $set_used->bind_param( 'i', $id );
                            $set_used->execute();
                            $set_used->close();
                        }
                    }
                }
                $result->close();
            }
            $statement->close();
        }
        mysqli_close($db);
    }
?>
