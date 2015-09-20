<?php header('Content-Type: text/plain; charset=utf-8'); ?>
<?php
    require "../../CONFIG.php";
    require BASEPATH . "/helpers/request_limit.php";
    require BASEPATH . "/helpers/request_offset.php";
    require BASEPATH . "/helpers/request_random.php";

    $db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (mysqli_connect_errno())
    {
        echo "Error: could not connect to database.";
    }
    else
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
                    $sql = "SELECT id, content, used FROM $TABLE_NAME ORDER BY RAND() LIMIT ? ";
                else
                    $sql = "SELECT id, content, used FROM $TABLE_NAME ORDER BY id " . $order . " LIMIT ? OFFSET ? ";
                break;
            case 'pending':
                if ($random)
                    $sql = "SELECT id, content, used FROM $TABLE_NAME WHERE approved = 0 ORDER BY RAND() LIMIT ? ";
                else
                    $sql = "SELECT id, content, used FROM $TABLE_NAME WHERE approved = 0 ORDER BY id " . $order . " LIMIT ? OFFSET ? ";
                break;
            case 'rejected':
                if ($random)
                    $sql = "SELECT id, content, used FROM $TABLE_NAME WHERE approved = -1 ORDER BY RAND() LIMIT ? ";
                else
                    $sql = "SELECT id, content, used FROM $TABLE_NAME WHERE approved = -1 ORDER BY id " . $order . " LIMIT ? OFFSET ? ";
                break;
            case 'unused':
                if ($random)
                    $sql = "SELECT id, content, used FROM $TABLE_NAME WHERE used = 0 ORDER BY RAND() LIMIT ? ";
                else
                    $sql = "SELECT id, content, used FROM $TABLE_NAME WHERE used = 0 ORDER BY id " . $order . " LIMIT ? OFFSET ? ";
                break;
            case 'approved':
            default:
                if ($random)
                    $sql = "SELECT id, content, used FROM $TABLE_NAME WHERE approved = 1 ORDER BY RAND() LIMIT ? ";
                else
                    $sql = "SELECT id, content, used FROM $TABLE_NAME WHERE approved = 1 ORDER BY id " . $order . " LIMIT ? OFFSET ? ";
                break;
        }

        $statement = $db->prepare($sql);
        if ($statement)
        {
            if ($random)
                $statement->bind_param( 'i', $limit );
            else
                $statement->bind_param( 'ii', $limit, $offset );
            if ($statement->execute())
            {
                $result = $statement->get_result();

                while ($row = $result->fetch_row())
                {
                    $id = $row[0];
                    $content = $row[1];
                    $used = $row[2];
                    $content = trim(preg_replace('/\s+/', ' ', $content));
                    $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
                    echo $id . " (" . $used . "): " . $content . "\n";

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
            else
            {
                echo "Error: could not execute statement.";
            }
            $statement->close();
        }
        else
        {
            echo "Error: could not prepare statement.";
        }

        mysqli_close($db);
    }
?>