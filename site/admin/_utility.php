<?php

// return true if does, false if it doesn't, and null if something went wrong
function table_exists( $db, $the_table_name )
{
    if (empty($the_table_name))
        return null;

    // NOTE: there must be a better & more robust 
    //       way to do this, but this works for now
    $table_check_result = $db->query( "SHOW TABLES LIKE '$the_table_name'" );
    if ($table_check_result)
    {
        $row = $table_check_result->fetch_row();
        $found_table_name = $row[0];
        if ( $found_table_name == $the_table_name )
            $exists = true;
        else
            $exists = false;
        $table_check_result->close();
        return $exists;
    }
    else
    {
        return null;
    }
}

?>