<?php
    require "../CONFIG.php";
    require BASEPATH . "/admin/_utility.php";
    //require BASEPATH . "/third-party/twilio-php/Services/Twilio.php";
    
    $db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (mysqli_connect_errno())
    {
        $error = "Unable to connect to database.";
    }
    else
    {
        $db->set_charset("utf8");
        $db->query("SET NAMES utf8");
        $db->query("SET CHARACTER SET utf8");
        
        $exists = table_exists( $db, $TABLE_NAME );
        if (is_null($exists) || !$exists)
        {
            header("Location: " . HOST . "/admin/settings/");
            mysqli_close($db);
            die();
        }

        // BASIC VARIABLE SETUP
        $message_id = $_POST['message_id'];
        $message_submit = $_POST['submit'];
        if ($message_submit == "approve")
            $message_mark = 1;
        else if ($message_submit == "reject")
            $message_mark = -1;
        $message_show_approved = $_REQUEST['approved'];
        $message_show_used = $_REQUEST['used'];
        $page = $_REQUEST['page'];
        if (empty($page))
            $page = 1;
        if ($page < 1)
            $page = 1;
        
        // QUERY TOTAL MESSAGES
        $result = $db->query( "SELECT COUNT(*) FROM $TABLE_NAME" );
        $row = $result->fetch_row();
        $total_messages = $row[0];
        $result->close();
        
        // MORE VARIABLE SETUP
        $total_pages = ceil( $total_messages / 25 );
        if ($page > $total_pages)
            $page = $total_pages;
        $prev_page = $page - 1;
        $next_page = $page + 1;
        $offset = ($page - 1) * 25;
        if ($offset < 0)
            $offset = 0;
        
        // UPDATE INDIVIDUAL MESSAGE APPROVAL IF NECESSRY
        if (!empty($message_id) && !is_null($message_mark))
        {
            $statement = $db->prepare("UPDATE $TABLE_NAME SET approved=? WHERE id=?");
            if ($statement)
            {
                $statement->bind_param( 'is', $message_mark, $message_id );
                if ( $statement->execute() )
                {
                    if ($message_mark == 1)
                        $message_approval_message = "Approved message ID $message_id";
                    else
                        $message_info_message = "Rejected message ID $message_id";
                }
                else
                {
                    $error = "Unable to update approval for message ID $message_id";
                }
                $statement->close();
            }
            else
            {
                $error = "Unable to prepare update for database.";
            }
        }
        
        // QUERY NUMBER OF APPROVED MESSAGES
        $result = $db->query( "SELECT COUNT(*) FROM $TABLE_NAME WHERE approved != 0" );
        $row = $result->fetch_row();
        $total_approved_messages = $row[0];
        $result->close();

        // QUERY NUMBER OF USED MESSAGES
        $result = $db->query( "SELECT COUNT(*) FROM $TABLE_NAME WHERE used != 0" );
        $row = $result->fetch_row();
        $total_used_messages = $row[0];
        $result->close();
        
        // QUERY LIST OF MESSAGES FOR THIS PAGE (optionally filtering by approval)
        if (!is_null($message_show_approved))
            $query_statement = $db->prepare("SELECT * FROM $TABLE_NAME WHERE approved = ? ORDER BY created DESC,id DESC LIMIT 25 OFFSET ?");
        elseif (!is_null($message_show_used))
            $query_statement = $db->prepare("SELECT * FROM $TABLE_NAME WHERE used = ? ORDER BY created DESC,id DESC LIMIT 25 OFFSET ?");
        else
            $query_statement = $db->prepare("SELECT * FROM $TABLE_NAME ORDER BY created DESC,id DESC LIMIT 25 OFFSET ?");
        if ($query_statement)
        {
            if (!is_null($message_show_approved))
                $query_statement->bind_param( 'ii', $message_show_approved, $offset ); 
            elseif (!is_null($message_show_used))
                $query_statement->bind_param( 'ii', $message_show_used, $offset );
            else
                $query_statement->bind_param( 'i', $offset ); 
            if ( !$query_statement->execute() )
            {
                $error = "Unable to query database.";
            }
        }
        else
        {
            $error = "Unable to prepare query of database.";
        }
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
        <li <?php if (is_null($message_show_approved) && is_null($message_show_used)) echo "class=\"active\""; ?>><a href="<?php echo HOST ?>/admin/">All</a></li>
        <li <?php if (!is_null($message_show_approved) && $message_show_approved == 0) echo "class=\"active\""; ?>><a href="<?php echo HOST ?>/admin/?approved=0">Pending</a></li>
        <li <?php if ($message_show_approved == 1) echo "class=\"active\""; ?>><a href="<?php echo HOST ?>/admin/?approved=1">Approved</a></li>
        <li <?php if ($message_show_approved == -1) echo "class=\"active\""; ?>><a href="<?php echo HOST ?>/admin/?approved=-1">Rejected</a></li>
        <li <?php if ($message_show_used == 1) echo "class=\"active\""; ?>><a href="<?php echo HOST ?>/admin/?used=1">Used</a></li>
        <li <?php if (!is_null($message_show_used) && $message_show_used == 0) echo "class=\"active\""; ?>><a href="<?php echo HOST ?>/admin/?used=0">Unused</a></li>
        <li><a href="<?php echo HOST ?>/admin/settings/">Settings</a></li>
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
    
    if (!empty($message_approval_message))
    {
        echo "<div class=\"alert alert-success\" role=\"alert\">$message_approval_message</div>";
    }
    
    if (!empty($message_info_message))
    {
        echo "<div class=\"alert alert-info\" role=\"alert\">$message_info_message</div>";
    }
?>    
    <div class="row">
        <div class="col-sm-6">
            <p><b>&nbsp;<?php echo $total_approved_messages ?> approved, <?php echo $total_used_messages ?> used, <?php echo $total_messages ?> total</b></p>
        </div>
      
        <div class="col-sm-6 text-right">          
            <p><?php require BASEPATH . "/admin/_pager.php" ?></p>
        </div>
    </div>
    
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Created</th>
                <th>Source</th>
                <th>From Phone</th>
                <th>Content</th>
                <th>Approved?</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
<?php
        $query_statement->store_result();
        if ($query_statement->num_rows == 0)
        {
            echo "<tr><td colspan=\"7\">No results found.</td></tr>";
        }
        else
        {
            $query_statement->bind_result($row_id, $row_created, $row_inputsource, $row_content, $row_fromphone, $row_approved, $row_used);
            
            while ($query_statement->fetch())
            {
                $content = htmlspecialchars($row_content, ENT_NOQUOTES, 'UTF-8');
              
                echo "<tr>";
                echo "<td>" . $row_id . "</td>";
                echo "<td>" . $row_created . "</td>";
                echo "<td>" . $row_inputsource . "</td>";
                echo "<td>" . $row_fromphone . "</td>";
                echo "<td>" . $content . "</td>";
                if ( $row_approved == 0 )
                    echo "<td> </td>";
                else if ( $row_approved > 0 )
                    echo "<td> <img src=\"img/check.png\"/> </td>";
                else
                    echo "<td> <img src=\"img/x.png\"/> </td>";
                echo "<td>";
                echo "    <form class=\"form-inline\" action=\"" . HOST . "/admin/\" method=\"POST\" target=\"_self\">";
                echo "    <input type=\"hidden\" name=\"page\" value=\"$page\" />";
                if (!is_null($message_show_approved))
                    echo "    <input type=\"hidden\" name=\"approved\" value=\"$message_show_approved\" />";
                echo "    <input type=\"hidden\" name=\"message_id\" value=\"$row_id\" />";
                if ( $row_approved == 0 )
                {
                    echo "    <input class=\"form-control btn-success\" type=\"submit\" name=\"submit\" value=\"approve\" />";
                    echo "    <input class=\"form-control btn-danger\" type=\"submit\" name=\"submit\" value=\"reject\" />";
                }
                else if ( $row_approved < 0 )
                {
                    echo "    <input class=\"form-control btn-success\" type=\"submit\" name=\"submit\" value=\"approve\" />";
                }
                else if ( $row_approved > 0 )
                {
                    echo "    <input class=\"form-control btn-danger\" type=\"submit\" name=\"submit\" value=\"reject\" />";
                }
                echo "    </form>";
                echo "</td>";
                echo "</tr>\n";
            }
            echo "<tr> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> </tr>";
        }
      
        $query_statement->close();
        mysqli_close($db);
?>
        </tbody>
    </table>
    
    <div class="row">
        <div class="text-center">          
            <p><?php require BASEPATH . "/admin/_pager.php" ?></p>
            <p>Made with &hearts; in Baltimore</p>
        </div>
    </div>
</div><!-- /.container -->    

<?php require BASEPATH . "/admin/_footer.php" ?>