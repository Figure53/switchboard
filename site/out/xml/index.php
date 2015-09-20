<?php header('Content-Type: application/xml; charset=utf-8'); ?>
<?php echo '<?xml version="1.0" encoding="utf-8"?>'; ?>

<switchboard>
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
            $sql = "SELECT id,inputsource,content,fromphone,DATE_FORMAT(created, '%Y-%m-%dT%H:%i:%s0Z') as created,approved,used FROM $TABLE_NAME ORDER BY RAND() LIMIT ? ";
        else
            $sql = "SELECT id,inputsource,content,fromphone,DATE_FORMAT(created, '%Y-%m-%dT%H:%i:%s0Z') as created,approved,used FROM $TABLE_NAME ORDER BY id " . $order . " LIMIT ? OFFSET ? ";
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
                    echo "  <row>\n";
                    foreach($row as $key => $value) {
                        $safeValue = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                        echo "    <{$key}>{$safeValue}</{$key}>\n";
                    }
                    echo "  </row>\n";

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
</switchboard>