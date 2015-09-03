<?php
    require "../../CONFIG.php";

    $db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (mysqli_connect_errno())
    {
        $result = " ";
    }
    else
    {
        $sql = "SELECT content FROM $TABLE_NAME ORDER BY id DESC LIMIT 1";
        $result = $db->query($sql);
        $row = $result->fetch_row();
        $content = htmlspecialchars($row[0], ENT_QUOTES, 'UTF-8');
        $result->close();
        mysqli_close($db);
    }

?>
<?php echo '<?xml version="1.0" encoding="utf-8"?>'; ?>

<switchboard>
  <row>
    <source></source>
    <content><?php echo $content; ?></content>
    <fromphone></fromphone>
  </row>
</switchboard>