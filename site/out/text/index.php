<?php
    require "../../CONFIG.php";
    require BASEPATH . "/helpers/request_limit.php";
    require BASEPATH . "/helpers/request_offset.php";

    $db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (mysqli_connect_errno())
    {
        echo "Error: could not connect to database.";
    }
    else
    {
        if (!empty($_REQUEST['type']))
            $type = $_REQUEST['type'];
        else 
            $type = "all";
        switch ($type)
        {
            case 'approved':
                $sql = "SELECT content FROM $TABLE_NAME WHERE approved = 1 ORDER BY id " . $order . " LIMIT ? OFFSET ? ";
                break;
            case 'pending':
                $sql = "SELECT content FROM $TABLE_NAME WHERE approved = 0 ORDER BY id " . $order . " LIMIT ? OFFSET ? ";
                break;
            case 'rejected':
                $sql = "SELECT content FROM $TABLE_NAME WHERE approved = -1 ORDER BY id " . $order . " LIMIT ? OFFSET ? ";
                break;
            default:
                $sql = "SELECT content FROM $TABLE_NAME ORDER BY id " . $order . " LIMIT ? OFFSET ? ";
                break;
        }

        $statement = $db->prepare($sql);
        if ($statement)
        {
            $statement->bind_param( 'ii', $limit, $offset );
            if ($statement->execute())
            {
                $result = $statement->get_result();

                while ($row = $result->fetch_row())
                {
                    $content = $row[0];
                    $content = trim(preg_replace('/\s+/', ' ', $content));
                    $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
                    echo $content . "\n";
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