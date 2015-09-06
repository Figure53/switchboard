<?php
    require "../../CONFIG.php";
    require BASEPATH . "/admin/_utility.php";
    //require BASEPATH . "/third-party/twilio-php/Services/Twilio.php";
    
    $db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (mysqli_connect_errno())
    {
        $error = "Unable to connect to database.";
    }
    else
    {
        $exists = table_exists( $db, $TABLE_NAME );
        if (is_null($exists))
            $error = "Unable to check for table named '$TABLE_NAME'.";

        if (!is_null($exists))
        {
            $table_action = $_POST['table_action'];
            if ($table_action == 'create')
            {
                $sql = "CREATE TABLE IF NOT EXISTS `$TABLE_NAME` (
                          `id` int NOT NULL AUTO_INCREMENT,
                          `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                          `inputsource` text,
                          `fromphone` text,
                          `content` text,
                          `approved` tinyint(3) NOT NULL DEFAULT '0',
                          `used` tinyint(3) NOT NULL DEFAULT '0',
                          PRIMARY KEY (id)
                        ) DEFAULT CHARSET=utf8";
            }
            else if ($table_action == 'drop')
            {
                $sql = "DROP TABLE IF EXISTS `$TABLE_NAME`";
            }

            if (!empty($sql))
            {
                $statement = $db->prepare($sql);
                if ($statement)
                {
                    if ( $statement->execute() )
                    {
                        $sql_result = "Success!";
                    }
                    else
                    {
                        $error = "Unable to execute SQL statement.";
                    }
                }
                else
                {
                    $error = "Unable to prepare SQL statement.";
                }
                $statement->close();  
            }
        }

        mysqli_close($db);
    }
?>
<?php require BASEPATH . "/admin/_header.php" ?>
    
<div class="container">
  <nav class="navbar navbar-inverse ">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="<?php echo HOST ?>/admin/">Switchboard</a>
    </div>
    <div id="navbar" class="collapse navbar-collapse">
      <ul class="nav navbar-nav">
        <li><a href="<?php echo HOST ?>/admin/">All</a></li>
        <li><a href="<?php echo HOST ?>/admin/?approved=0">Pending</a></li>
        <li><a href="<?php echo HOST ?>/admin/?approved=1">Approved</a></li>
        <li class="active"><a href="<?php echo HOST ?>/admin/settings/">Settings</a></li>
      </ul>
    </div><!--/.nav-collapse -->
  </nav>
</div>

<div class="container">

<?php
    if (!empty($error))
    {
        echo "<div class=\"alert alert-danger\" role=\"alert\">
              <span class=\"glyphicon glyphicon-exclamation-sign\" aria-hidden=\"true\"></span>
              <span class=\"sr-only\">Error:</span>$error</div>";
    }
?>

<?php if ($exists): ?>
    <div class="row">
        <div class="col-md-12">
            <h2>Inputs</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <h3>Twilio</h3>
            <p>
                Input: enabled
            </p>
            <p>
                <code><?php echo HOST ?>/in/twilio/</code>
            </p>
        </div>
        <div class="col-md-4">
            <h3>IFTTT</h3>
            <p>
                Input: enabled
            </p>
            <p>
                <code><?php echo HOST ?>/in/ifttt/sms/</code>
            </p>
        </div>
        <div class="col-md-4">

            <h3>Pusher</h3>

        </div>
    </div>

    <hr/>

    <div class="row">
        <div class="col-md-12">
            <h2>Outputs</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <h3>Text</h3>
            <p>
                <a href="<?php echo HOST ?>/out/text/" target="_blank"><?php echo HOST ?>/out/text/</a>
            </p>
        </div>
        <div class="col-md-4">
            <h3>JSON</h3>
            <p>
                <a href="<?php echo HOST ?>/out/json/" target="_blank"><?php echo HOST ?>/out/json/</a>
            </p>
        </div>
        <div class="col-md-4">
            <h3>XML</h3>
            <p>
                <a href="<?php echo HOST ?>/out/xml/" target="_blank"><?php echo HOST ?>/out/xml/</a>
            </p>
        </div>
    </div>

    <hr/>

<?php endif; ?>

    <div class="row">
    <div class="col-md-12">

        <h2>Database</h2>

<?php 
    if (is_null($exists))
    {
        echo "<p>Error checking for existance of the '$TABLE_NAME' table.</p>";
    }
    else
    {
        if (empty($sql_result))
        {
            echo "<form class=\"form-inline\" action=\"" . HOST . "/admin/settings/\" method=\"POST\" target=\"_self\">";
            if (!$exists)
            {
                echo "<p><b>'$TABLE_NAME' table is missing</b></p>";
                echo "<input type=\"hidden\" name=\"table_action\" value=\"create\" />";
                echo "<input class=\"form-control btn-success\" type=\"submit\" name=\"submit\" value=\"CREATE TABLE\" />";
            }
            else
            {
                echo "<p><b>'$TABLE_NAME' table exists</b></p>";
                echo "<input type=\"hidden\" name=\"table_action\" value=\"drop\" />";
                echo "<input class=\"form-control btn-danger\" type=\"submit\" name=\"submit\" value=\"DROP TABLE\" />";
            }
            echo "</form>";
        }
        else
        {
            echo "<p><b>action:</b> $table_action </p>";
            echo "<p><b>sql:</b></p> <p><pre>$sql</pre> </p>";
            echo "<p><b>result:</b> $sql_result </p>";
            echo "<p><a href=\"" . HOST . "/admin/settings/\">Edit settings</a> or go to <a href=\"" . HOST . "/admin/\">dashboard</a>.</p>";
        }
    }
?>
    
    </div>
    </div><!-- .row -->

</div><!-- /.container -->    
    
<?php require BASEPATH . "/admin/_footer.php" ?>