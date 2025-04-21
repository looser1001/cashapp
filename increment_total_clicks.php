<?php
$click_counter_file = 'total_clicks.txt';
$click_count = (int)@file_get_contents($click_counter_file);
$click_count++;
file_put_contents($click_counter_file, (string)$click_count);
?>