<?php
    // Correct function is date_default_timezone_set
    date_default_timezone_set("Asia/Dhaka");

    $CurrentTime = time();
    
    // 'j' = day (no leading zero)
    // 'M' = short textual representation of month (May)
    // 'Y' = 4-digit year
    // 'g:i a' = 12-hour format:minutes am/pm
    $DateTime = date("j-M-Y g:i a", $CurrentTime);

    echo $DateTime;
?>