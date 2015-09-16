<?php

    if (is_numeric($_REQUEST["random"]) && !empty((int) $_REQUEST["random"])) {
        $random = (int) $_REQUEST["random"];
    } else {
        $random = false;
    }

?>