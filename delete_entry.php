<?php
$file = 'data.txt';
$entryToDeleteEncoded = $_POST['entry'] ?? '';
$entryToDelete = base64_decode($entryToDeleteEncoded);

if (!empty($entryToDelete) && file_exists($file)) {
    $fileContent = file_get_contents($file);
    $entries = explode("------------------------\n", trim($fileContent));
    $updatedContent = '';
    $deleted = false;

    foreach ($entries as $entry) {
        if (trim($entry) === trim($entryToDelete)) {
            $deleted = true;
        } else {
            $updatedContent .= $entry . "\n------------------------\n";
        }
    }

    if ($deleted) {
        file_put_contents($file, trim($updatedContent));
        echo "Entry deleted successfully.";
    } else {
        echo "Error: Entry not found.";
    }
} else {
    echo "Error: Invalid entry or data file not found.";
}
?>