<?php

    if (is_numeric($_REQUEST["offset"]) && $_REQUEST["offset"] > 0) {
        $offset = (int) $_REQUEST["offset"];
    } else {
        $offset = 0;
    }

?>