<a href="?page=<?php 
    echo $prev_page;
    if (!is_null($message_show_approved))
        echo "&approved=$message_show_approved";
    if (!is_null($message_show_used))
        echo "&used=$message_show_used";?>">&larr; Previous</a>

| Page <?php echo $page ?> of <?php echo $total_pages ?> | 

<a href="?page=<?php 
    echo $next_page;
    if (!is_null($message_show_approved))
        echo "&approved=$message_show_approved";
    if (!is_null($message_show_used))
        echo "&used=$message_show_used";?>">Next &rarr;</a>