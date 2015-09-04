<?php
    require "../../../CONFIG.php";

    // Example IFTTT SMS -> Maker recipe:
    //
    // URL: http://example.com/switchboard/in/ifttt/sms/
    // Method: POST
    // Content Type: application/x-www-form-urlencoded
    // Body: content={{Message}}&from={{From}}

    $content = $_POST['content'];
    $fromphone = $_POST['from'];
    // TODO: bail if these aren't set

    // for debugging:
    //file_put_contents( 'debug' . time() . '.log', var_export( $_POST, true ));

    $db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (mysqli_connect_errno())
    {
        $result = "connection error";
    }
    else
    {
        $statement = $db->prepare("INSERT INTO $TABLE_NAME (inputsource, content, fromphone) VALUES (?, ?, ?)");
        if ($statement)
        {
            $inputsource = "ifttt";
            $statement->bind_param( 'sss', $inputsource, $content, $fromphone );
            if ($statement->execute())
                $result = "success";
            else
                $result = "sql failure";
            $statement->close();
        }
        else
        {
            $result = "something went wrong";
        }
        mysqli_close($db);
    }
?>
