<?php
    require "../../CONFIG.php";
    //require BASEPATH . "/third-party/twilio-php/Services/Twilio.php";

    $content = $_REQUEST['Body'];
    $fromphone = $_REQUEST['From'];
    // TODO: bail if these aren't set

    $db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (mysqli_connect_errno())
    {
        $result = "sorry, something went wrong";
    }
    else
    {
        $statement = $db->prepare("INSERT INTO $TABLE_NAME (inputsource, content, fromphone) VALUES (?, ?, ?)");
        if ($statement)
        {
            $inputsource = "twilio";
            $statement->bind_param( 'sss', $inputsource, $content, $fromphone ); 
            if ($statement->execute())
                $result = "thanks!";
            else
                $result = "sorry, something went wrong";
            $statement->close();
        }
        else
        {
            $result = "sorry, something went wrong";
        }
        mysqli_close($db);
    }
?>
<?php
header("Content-Type: text/xml");
echo '<?xml version="1.0" encoding="utf-8"?>';
?>
<Response>
    <Message>
        <?php echo $result; ?>
    </Message>
</Response>
