<?php header('Content-Type: application/xml'); ?>
<?php echo '<?xml version="1.0" encoding="utf-8"?>'; ?>

<switchboard>
<?php
    require "../../CONFIG.php";

    $db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (mysqli_connect_errno())
    {
        $result = " ";
    }
    else
    {
        $sql = "SELECT id,inputsource,content,fromphone,DATE_FORMAT(created, '%Y-%m-%dT%H:%i:%s0Z') FROM $TABLE_NAME ORDER BY id DESC";
        $result = $db->query($sql);
        for ($i=0; $i < $result->num_rows; $i++) { 
          echo "  <row>\n";
          $row = $result->fetch_row();
          $id = htmlspecialchars($row[0], ENT_QUOTES, 'UTF-8');
          $source = htmlspecialchars($row[1], ENT_QUOTES, 'UTF-8');
          $content = htmlspecialchars($row[2], ENT_QUOTES, 'UTF-8');
          $fromphone = htmlspecialchars($row[3], ENT_QUOTES, 'UTF-8');
          $created = htmlspecialchars($row[4], ENT_QUOTES, 'UTF-8');
          echo "    <id>{$id}</id>\n";
          echo "    <source>{$source}</source>\n";
          echo "    <content>{$content}</content>\n";
          echo "    <fromphone>{$fromphone}</fromphone>\n";
          echo "    <created>{$created}</created>\n";
          echo "  </row>\n";
        }
        $result->close();
        mysqli_close($db);
    }

?>
</switchboard>