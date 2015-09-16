<?php

    if (is_numeric($_REQUEST["limit"]) && !empty((int) $_REQUEST["limit"])) {
        $limit = (int) $_REQUEST["limit"];
    } else {
        $limit = 1;
    }
    
    if ($limit < 0) {
        # this way, we can use a negative limit to return ascending IDs
        # i.e. limit=100 returns [3,2,1] while limit=-100 returns [1,2,3]
        $limit = abs($limit);
        $order = "ASC";
    } else {
        $order = "DESC";
    }

?>