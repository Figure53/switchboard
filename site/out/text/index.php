<?php
    require "../../CONFIG.php";

    $db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (mysqli_connect_errno())
    {
        $result = " ";
    }
    else
    {
        if (!empty($_GET['type']))
          $type = $_GET['type'];
        else 
          $type = "all";
        
        switch ($type) {
          case 'approved':
            $sql = "SELECT content FROM $TABLE_NAME WHERE approved = 1 ORDER BY id DESC LIMIT 1";
            break;
          case 'pending':
            $sql = "SELECT content FROM $TABLE_NAME WHERE approved = 0 ORDER BY id DESC LIMIT 1";
            break;
          default:
            $sql = "SELECT content FROM $TABLE_NAME ORDER BY id DESC LIMIT 1";
            break;
        }
        $result = $db->query($sql);
        $row = $result->fetch_row();
        $content = htmlspecialchars($row[0], ENT_QUOTES, 'UTF-8');
        $result->close();
        mysqli_close($db);
    }

?>
<?php echo $content; ?>