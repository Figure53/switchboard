<?php
	require "../../CONFIG.php";
	$content = $_POST['content'];
	$content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');

	if (!empty($content))
	{
		$db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	    if (mysqli_connect_errno())
	    {
	        $result = "sorry, something went wrong";
	    }
	    else
	    {
	    	$db->set_charset("utf8");
	        $db->query("SET NAMES utf8");
	        $db->query("SET CHARACTER SET utf8");

	        $statement = $db->prepare("INSERT INTO $TABLE_NAME (inputsource, content) VALUES (?, ?)");
	        if ($statement)
	        {
	            $inputsource = "webform";
	            $statement->bind_param( 'ss', $inputsource, $content ); 
	            if ($statement->execute())
	                $result = $content;
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
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    
    <title></title>
    
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
    
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>

	<div class="container">
		<div class="row">
			<div class="col-md-1">
			</div>
			<div class="col-md-10">
			<?php echo "<form class=\"form-horizontal\" action=\"" . HOST . "/in/form/\" method=\"POST\" target=\"_self\">"; ?>
					<div class="form-group">
						<label class="switchboard-prompt text-right" for="content"><?php echo $form_prompt; ?></label>
						<input class="switchboard-ok btn-default btn-lg" type="submit" name="submit" value="ok" />
						<input class="switchboard-content" id="content" name="content" type="text" maxlength="45" />
					</div>
				</form>
			</div>
			<div class="col-md-1">
			</div>
		</div>

		<div class="row">
			<div class="col-md-2">
			</div>
			<div class="col-md-8">
				<p id="switchboard-result" class="text-center switchboard-result">
				<?php echo $result; ?>
				</p>
			</div>
			<div class="col-md-2">
			</div>
		</div>
	</div>

	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

    <script>
		$(document).ready(function() {
	        $("#switchboard-result").fadeOut(3000);
		});
	</script>
</body>
</html>
