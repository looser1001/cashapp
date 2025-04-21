<?php
$file = 'data.txt';
$current_modified = filemtime($file);
$last_modified = isset($_GET['last_modified']) ? intval($_GET['last_modified']) : 0;

$response = ['new_data' => false, 'last_modified' => $current_modified];

if ($current_modified > $last_modified) {
    $response['new_data'] = true;
}

header('Content-Type: application/json');
echo json_encode($response);
?>
