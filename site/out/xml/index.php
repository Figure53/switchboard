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
        $sql = "SELECT id,inputsource,content,fromphone,DATE_FORMAT(created, '%Y-%m-%dT%H:%i:%s0Z') as created,approved FROM $TABLE_NAME ORDER BY id DESC";
        $result = $db->query($sql);
        for ($i=0; $i < $result->num_rows; $i++) {
          echo "  <row>\n";
          $row = $result->fetch_array(MYSQLI_ASSOC);
          foreach($row as $key => $value) {
            $safeValue = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            echo "    <{$key}>{$safeValue}</{$key}>\n";
          }
          echo "  </row>\n";
        }
        $result->close();
        mysqli_close($db);
    }

?>
</switchboard>