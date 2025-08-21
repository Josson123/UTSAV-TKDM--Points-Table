<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Handle logout
if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "utsavtkdm";

try {
    // Create connection
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch all units
    $units_query = "SELECT unit_slno, unit_name FROM units ORDER BY unit_slno";
    $units_stmt = $pdo->prepare($units_query);
    $units_stmt->execute();
    $units = $units_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all events
    $events_query = "SELECT Event_slno, Event_name FROM events ORDER BY Event_slno";
    $events_stmt = $pdo->prepare($events_query);
    $events_stmt->execute();
    $events = $events_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all points data
    $points_query = "SELECT unit_slno, Event_slno, Points FROM points";
    $points_stmt = $pdo->prepare($points_query);
    $points_stmt->execute();
    $points_data = $points_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Create a points lookup array for quick access
    $points_lookup = array();
    foreach ($points_data as $point) {
        $points_lookup[$point['unit_slno']][$point['Event_slno']] = $point['Points'];
    }

} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle AJAX requests for updating points
if (isset($_POST['action']) && $_POST['action'] === 'update_single_point') {
    header('Content-Type: application/json');
    
    $unit_slno = intval($_POST['unit_slno']);
    $event_slno = intval($_POST['event_slno']);
    $points = intval($_POST['points']);
    
    try {
        $pdo->beginTransaction();
        
        if ($points > 0) {
            // Check if record exists
            $check_query = "SELECT Points FROM points WHERE unit_slno = ? AND Event_slno = ?";
            $check_stmt = $pdo->prepare($check_query);
            $check_stmt->execute([$unit_slno, $event_slno]);
            $existing = $check_stmt->fetch();
            
            if ($existing) {
                // Update existing record
                $update_query = "UPDATE points SET Points = ? WHERE unit_slno = ? AND Event_slno = ?";
                $update_stmt = $pdo->prepare($update_query);
                $update_stmt->execute([$points, $unit_slno, $event_slno]);
            } else {
                // Insert new record
                $insert_query = "INSERT INTO points (unit_slno, Event_slno, Points) VALUES (?, ?, ?)";
                $insert_stmt = $pdo->prepare($insert_query);
                $insert_stmt->execute([$unit_slno, $event_slno, $points]);
            }
        } else {
            // Delete record if points = 0
            $delete_query = "DELETE FROM points WHERE unit_slno = ? AND Event_slno = ?";
            $delete_stmt = $pdo->prepare($delete_query);
            $delete_stmt->execute([$unit_slno, $event_slno]);
        }
        
        // Update total points for the unit
        $update_total_query = "UPDATE units SET total_points = (
            SELECT COALESCE(SUM(p.Points), 0) 
            FROM points p 
            WHERE p.unit_slno = ?
        ) WHERE unit_slno = ?";
        $update_total_stmt = $pdo->prepare($update_total_query);
        $update_total_stmt->execute([$unit_slno, $unit_slno]);
        
        // Get updated total
        $total_query = "SELECT total_points FROM units WHERE unit_slno = ?";
        $total_stmt = $pdo->prepare($total_query);
        $total_stmt->execute([$unit_slno]);
        $new_total = $total_stmt->fetchColumn();
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Points updated successfully',
            'new_total' => $new_total
        ]);
        
    } catch (Exception $e) {
        $pdo->rollback();
        echo json_encode([
            'success' => false, 
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- ===== CSS ===== -->
    <link rel="stylesheet" href="style.css">
        
    <!-- ===== Boxicons CSS ===== -->
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <link rel="icon" href="Photos/YD_Logo_BG.png" type="image/png">
    <title>Utsav TKDM Points Table</title>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            margin-top: 90px;
            background-color: var(--body-color);
            background-image: url("Photos/YD_Logo_BG.png"); /* path from index.php */
            background-size: cover;   /* make it cover the screen*/
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh; full height
        }
        
        .container {
            background-color: rgba(255, 255, 255, 0.85); /* Translucent white */
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow-x: auto;
        }

        /* Dark mode container */
        body.dark .container {
            background-color: rgba(36, 37, 38, 0.85); /* Translucent dark */
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        }
        
        h1 {
            color: var(--search-text);
            text-align: center;
            margin-bottom: 30px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        body.dark h1 {
            color: var(--text-color);
        }
        
        .points-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1200px;
            background-color: rgba(8, 230, 64, 0.86);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            border-radius: 8px;
            overflow: hidden;
        }

        /* Dark mode table */
        body.dark .points-table {
            background-color: rgba(36, 37, 38, 0.9);
        }
        
        .points-table th,
        .points-table td {
            border: 1px solid rgba(221, 221, 221, 0.3);
            padding: 8px;
            text-align: center;
            white-space: nowrap;
            background-color: rgba(234, 232, 238, 0.1);
        }

        /* Dark mode cells */
        body.dark .points-table th,
        body.dark .points-table td {
            border: 1px solid rgba(255, 255, 255, 0.1);
            background-color: rgba(3, 123, 252, 0.27);
        }
        
        .points-table th {
            background-color: rgba(76, 175, 80, 0.9);
            color: white;
            font-weight: bold;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        /* Dark mode headers */
        body.dark .points-table th {
            background-color: rgba(64, 112, 244, 0.9);
        }
        
        .points-table .unit-header {
            background-color: rgba(76, 175, 80, 0.9);
            color: white;
            font-weight: bold;
            font-size: 12px;
            min-width: 80px;
            text-align: center;
        }

        /* Dark mode unit headers */
        body.dark .points-table .unit-header {
            background-color: rgba(64, 112, 244, 0.9);
        }
        
        .points-table .event-name {
            background-color: rgba(248, 249, 250, 0.9);
            font-weight: bold;
            text-align: left;
            padding-left: 12px;
            position: sticky;
            left: 0;
            z-index: 5;
            font-size: 11px;
            min-width: 200px;
            color: var(--search-text);
        }

        /* Dark mode event names */
        body.dark .points-table .event-name {
            background-color: rgba(36, 37, 38, 0.9);
            color: var(--text-color);
        }
        
        .points-table .total-row {
            background-color: rgba(233, 236, 239, 0.9);
            font-weight: bold;
        }

        /* Dark mode total row */
        body.dark .points-table .total-row {
            background-color: rgba(57, 56, 56, 0.9);
        }
        
        .table-controls {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .points-table input[type="number"] {
            width: 50px;
            border: 1px solid #ddd;
            background: transparent;
            text-align: center;
            font-size: 12px;
            padding: 2px;
            transition: all 0.3s ease;
        }
        
        .points-table input[type="number"]:focus {
            background-color: #fff3cd;
            outline: 2px solid #ffc107;
            border-color: #ffc107;
        }
        
        .points-table input[type="number"].changed {
            background-color: #d4edda;
            border-color: #28a745;
        }
        
        .points-table input[type="number"].saving {
            background-color: #f8d7da;
            border-color: #dc3545;
        }
        
        .summary {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .summary-stats {
            background-color: #e7f3ff;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #007bff;
        }
        
        .status-message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            display: none;
        }
        
        .status-message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .status-message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #0056b3;
        }
        
        .btn:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }
        
        .auto-save-indicator {
            display: inline-block;
            margin-left: 10px;
            padding: 5px 10px;
            background-color: #28a745;
            color: white;
            border-radius: 3px;
            font-size: 12px;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .auto-save-indicator.show {
            opacity: 1;
        }

        .nav-profile {
           margin-top: 20px;
           margin-left: -1cm;
           margin-right:100px;
        }

        .profile-pic {
            width: 40px;
            height: 70px;
            /* border-radius: 10%;   makes it round */
            object-fit: cover;    /* prevents stretching */
            /* border: 2px solid #fff; optional border */
            cursor: pointer;
        }
        .my-table {
           width: 100%;
           height: 300px;
           border: 2px solid black;
           background-image: url("Photos/YD_Logo_BG.png");
           background-size: cover;      /* scale image to cover */
           background-repeat: no-repeat; /* prevent tiling */
           background-position: center; /* keep centered */
           color: white; /* text color for contrast */
        }

        /* User info and logout styles */
       .user-info {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-left: 10%;
    position: fixed;
}

.user-dropdown {
    position: relative;
}

.user-welcome {
    color: var(--text-color);
    font-size: 14px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

body.dark .user-welcome {
    background: rgba(36, 37, 38, 0.8);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.user-welcome:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

body.dark .user-welcome:hover {
    background: rgba(36, 37, 38, 0.9);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.dropdown-arrow {
    transition: transform 0.3s ease;
}

.user-dropdown.active .dropdown-arrow {
    transform: rotate(180deg);
}

.dropdown-menu {
    position: absolute;
    top: 100%;
    right: 0;
    margin-top: 8px;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(15px);
    border-radius: 8px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.2);
    min-width: 180px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
    z-index: 1000;
}

body.dark .dropdown-menu {
    background: rgba(36, 37, 38, 0.95);
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
}

.user-dropdown.active .dropdown-menu {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.dropdown-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    color: var(--text-color);
    cursor: pointer;
    transition: all 0.3s ease;
    border-radius: 6px;
    margin: 4px;
    font-size: 14px;
}

.dropdown-item:hover {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;
}

body.dark .dropdown-item:hover {
    background: rgba(220, 53, 69, 0.2);
    color: #ff6b6b;
}

.dropdown-item i {
    font-size: 16px;
}
        
       .logout-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(5px);
    animation: fadeIn 0.3s ease-out;
}

.logout-modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.3);
    text-align: center;
    min-width: 350px;
    animation: slideIn 0.3s ease-out;
}

body.dark .logout-modal-content {
    background: rgba(36, 37, 38, 0.95);
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
}

.logout-modal h3 {
    color: var(--text-color);
    margin-bottom: 15px;
    font-size: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.logout-modal h3 i {
    color: #dc3545;
    font-size: 24px;
}

.logout-modal p {
    color: var(--text-color);
    margin-bottom: 25px;
    opacity: 0.8;
    font-size: 16px;
}

.logout-modal-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
}

.modal-btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
    min-width: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.modal-btn.confirm {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
}

.modal-btn.confirm:hover {
    background: linear-gradient(135deg, #c82333, #bd2130);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
}

.modal-btn.cancel {
    background: linear-gradient(135deg, #6c757d, #5a6268);
    color: white;
}

.modal-btn.cancel:hover {
    background: linear-gradient(135deg, #5a6268, #545b62);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
}

/* Animation keyframes */
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translate(-50%, -60%);
    }
    to {
        opacity: 1;
        transform: translate(-50%, -50%);
    }
}

/* Show modal */
.logout-modal.show {
    display: block !important;
}
    </style>
</head>
<body>
    <nav>
        <div class="nav-bar">
            <i class='bx bx-menu sidebarOpen' ></i>
              <img src="Photos/YD_Logo_BG.png" alt="Profile" class="profile-pic">
            <div class="menu">
            <span class="logo navLogo"><a href="index.php">UTSAV-TKDM ഫൊറോണാ യുവദീപ്തി കലോത്സവം </a></span>
                
                    
                

                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="appeals.php">Appeals</a></li>
                    <li><a href="services.php">Services</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </div>

            <div class="darkLight-searchBox">
                
                <div class="dark-light">
                    <i class='bx bx-moon moon'></i>
                    <i class='bx bx-sun sun'></i>
                </div>

                <div class="searchBox">
                   <div class="searchToggle">
                    <i class='bx bx-x cancel'></i>
                    <i class='bx bx-search search'></i>
                   </div>

                    <div class="search-field">
                        <input type="text" placeholder="Search...">
                        <i class='bx bx-search'></i>
                    </div>
                </div>

                
                <div class="user-info">
                   <div class="user-dropdown">
                     <div class="user-welcome" onclick="toggleUserDropdown()">
                     <i class='bx bx-user'></i>
                             Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!
                     <i class='bx bx-chevron-down dropdown-arrow'></i>
                  </div>
                   <div class="dropdown-menu" id="userDropdownMenu">
                <div class="dropdown-item" onclick="showLogoutModal()">
                <i class='bx bx-log-out'></i>
                <span>Logout</span>
            </div>
        </div>
    </div>
</div>
            </div>
        </div>
    </nav>

    <!-- Logout Confirmation Modal -->
    <div id="logoutModal" class="logout-modal">
        <div class="logout-modal-content">
            <h3><i class='bx bx-log-out'></i> Confirm Logout</h3>
            <p>Are you sure you want to logout?</p>
            <div class="logout-modal-buttons">
                <button class="modal-btn cancel" onclick="hideLogoutModal()">
                    <i class='bx bx-x'></i> Cancel
                </button>
                <button class="modal-btn confirm" onclick="confirmLogout()">
                    <i class='bx bx-check'></i> Logout
                </button>
            </div>
        </div>
    </div>
 
    <div class="container">
        <h1>Utsav TKDM - Points Table</h1>
        
        <div id="statusMessage" class="status-message"></div>
        
        <div class="table-controls">
            <div class="summary-stats">
                <strong>Statistics:</strong><br>
                Total Units: <?php echo count($units); ?><br>
                Total Events: <?php echo count($events); ?><br>
                Total Cells: <?php echo count($units) * count($events); ?>
                <span id="autoSaveIndicator" class="auto-save-indicator">Auto-saved!</span>
            </div>
            
            <div>
                <button type="button" id="saveAllBtn" class="btn">Save All Changes</button>
            </div>
        </div>
        
        <table class="points-table">
            <thead>
                <tr>
                    <th style="position: sticky; left: 0; z-index: 15;">Event Name</th>
                    <?php foreach ($units as $unit): ?>
                        <th class="unit-header"><?php echo htmlspecialchars($unit['unit_name']); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $event): ?>
                    <tr data-event-id="<?php echo $event['Event_slno']; ?>">
                        <td class="event-name"><?php echo htmlspecialchars($event['Event_name']); ?></td>
                        <?php 
                        foreach ($units as $unit): 
                            $current_points = isset($points_lookup[$unit['unit_slno']][$event['Event_slno']]) 
                                            ? $points_lookup[$unit['unit_slno']][$event['Event_slno']] 
                                            : 0;
                        ?>
                            <td>
                                <input type="number" 
                                       class="points-input"
                                       data-unit-id="<?php echo $unit['unit_slno']; ?>"
                                       data-event-id="<?php echo $event['Event_slno']; ?>"
                                       data-original-value="<?php echo $current_points; ?>"
                                       value="<?php echo $current_points; ?>" 
                                       min="0" 
                                       max="999">
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td class="total-column"><strong>Total</strong></td>
                    <?php foreach ($units as $unit): ?>
                        <?php 
                        $unit_total = 0;
                        foreach ($events as $event):
                            $current_points = isset($points_lookup[$unit['unit_slno']][$event['Event_slno']]) 
                                            ? $points_lookup[$unit['unit_slno']][$event['Event_slno']] 
                                            : 0;
                            $unit_total += $current_points;
                        endforeach;
                        ?>
                        <td class="total-column">
                            <strong class="unit-total" data-unit-id="<?php echo $unit['unit_slno']; ?>"><?php echo $unit_total; ?></strong>
                        </td>
                    <?php endforeach; ?>
                </tr>
            </tbody>
        </table>
    </div>

    <script>
        // Global variables to track changes
        let changedCells = new Set();
        let saveTimeout;
        let savingCells = new Set(); // Track cells currently being saved

        // Logout modal functionality
        function showLogoutModal() {
            document.getElementById('logoutModal').style.display = 'block';
        }

        function hideLogoutModal() {
            document.getElementById('logoutModal').style.display = 'none';
        }

        function confirmLogout() {
            window.location.href = 'index.php?logout=true';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('logoutModal');
            if (event.target == modal) {
                hideLogoutModal();
            }
        }

        // ESC key to close modal
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                hideLogoutModal();
            }
        });

        // Add event listeners when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            const pointsInputs = document.querySelectorAll('.points-input');
            
            pointsInputs.forEach(input => {
                // Store original value
                input.dataset.originalValue = input.value;
                
                // Add event listeners
                input.addEventListener('input', handlePointsChange);
                input.addEventListener('blur', handlePointsBlur);
            });
        });

        function handlePointsChange(e) {
            const input = e.target;
            const currentValue = parseInt(input.value) || 0;
            const originalValue = parseInt(input.dataset.originalValue) || 0;
            
            // Mark cell as changed if value differs from original
            if (currentValue !== originalValue) {
                input.classList.add('changed');
                changedCells.add(input);
            } else {
                input.classList.remove('changed');
                changedCells.delete(input);
            }
            
            // Update row total immediately
            updateRowTotal(input);
            
            // Clear any existing auto-save timeout and set new one
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(() => {
                if (changedCells.size > 0) {
                    autoSavePoints();
                }
            }, 2000); // Auto-save after 2 seconds of inactivity
        }

        function handlePointsBlur(e) {
            const input = e.target;
            // Clear the auto-save timeout to prevent double saving
            clearTimeout(saveTimeout);
            
            // Trigger immediate save when user leaves the field
            if (input.classList.contains('changed') && !savingCells.has(input)) {
                saveIndividualPoint(input);
            }
        }

        function updateRowTotal(input) {
            const unitId = input.dataset.unitId;
            const unitColumn = input.closest('td').cellIndex; // Get column index
            
            // Find all inputs in this unit's column
            const table = input.closest('table');
            const rows = table.querySelectorAll('tbody tr:not(.total-row)');
            let total = 0;
            
            rows.forEach(row => {
                const cellInput = row.cells[unitColumn].querySelector('.points-input');
                if (cellInput) {
                    const value = parseInt(cellInput.value) || 0;
                    total += value;
                }
            });
            
            // Update the total in the total row
            const totalRow = table.querySelector('.total-row');
            const totalCell = totalRow.cells[unitColumn].querySelector('.unit-total');
            if (totalCell) {
                totalCell.textContent = total;
            }
        }

        function saveIndividualPoint(input) {
            if (!input.classList.contains('changed') || savingCells.has(input)) return;
            
            const unitId = input.dataset.unitId;
            const eventId = input.dataset.eventId;
            const points = parseInt(input.value) || 0;
            
            // Add to saving set to prevent duplicate saves
            savingCells.add(input);
            
            // Mark as saving
            input.classList.add('saving');
            input.disabled = true;
            
            // Create FormData
            const formData = new FormData();
            formData.append('action', 'update_single_point');
            formData.append('unit_slno', unitId);
            formData.append('event_slno', eventId);
            formData.append('points', points);
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Remove from saving set
                savingCells.delete(input);
                
                if (data.success) {
                    // Update original value and remove changed class
                    input.dataset.originalValue = points;
                    input.classList.remove('changed', 'saving');
                    input.disabled = false;
                    
                    // Update total in the database total column if needed
                    if (data.new_total !== undefined) {
                        const unitId = input.dataset.unitId;
                        const totalCell = document.querySelector(`.unit-total[data-unit-id="${unitId}"]`);
                        if (totalCell) {
                            totalCell.textContent = data.new_total;
                        }
                    }
                    
                    // Remove from changed cells set
                    changedCells.delete(input);
                    
                    // Show success indicator
                    showAutoSaveIndicator();
                    showMessage('Points saved successfully', 'success');
                } else {
                    showMessage('Error saving points: ' + data.message, 'error');
                    input.classList.remove('saving');
                    input.disabled = false;
                }
            })
            .catch(error => {
                // Remove from saving set on error
                savingCells.delete(input);
                console.error('Error:', error);
                showMessage('Network error occurred', 'error');
                input.classList.remove('saving');
                input.disabled = false;
            });
        }

        function autoSavePoints() {
            // Save all changed cells
            const changedArray = Array.from(changedCells);
            changedArray.forEach(input => {
                saveIndividualPoint(input);
            });
        }

        function showAutoSaveIndicator() {
            const indicator = document.getElementById('autoSaveIndicator');
            indicator.classList.add('show');
            setTimeout(() => {
                indicator.classList.remove('show');
            }, 2000);
        }

        function showMessage(message, type) {
            const messageDiv = document.getElementById('statusMessage');
            messageDiv.className = 'status-message ' + type;
            messageDiv.textContent = message;
            messageDiv.style.display = 'block';
            
            setTimeout(() => {
                messageDiv.style.display = 'none';
            }, 3000);
        }

        // Save All button functionality
        document.getElementById('saveAllBtn').addEventListener('click', function() {
            if (changedCells.size === 0) {
                showMessage('No changes to save', 'success');
                return;
            }
            
            this.disabled = true;
            this.textContent = 'Saving...';
            
            autoSavePoints();
            
            setTimeout(() => {
                this.disabled = false;
                this.textContent = 'Save All Changes';
            }, 1000);
        });

        // Prevent form submission on Enter key
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && e.target.classList.contains('points-input')) {
                e.preventDefault();
                e.target.blur(); // This will trigger the blur event and save
            }
        });
        

function toggleUserDropdown() {
    const dropdown = document.querySelector('.user-dropdown');
    const isActive = dropdown.classList.contains('active');
    
    // Close all dropdowns first
    document.querySelectorAll('.user-dropdown.active').forEach(d => {
        d.classList.remove('active');
    });
    
    // Toggle current dropdown
    if (!isActive) {
        dropdown.classList.add('active');
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const userDropdown = document.querySelector('.user-dropdown');
    if (!userDropdown.contains(event.target)) {
        userDropdown.classList.remove('active');
    }
});

// Close dropdown on escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        document.querySelector('.user-dropdown').classList.remove('active');
    }
});

</script>
    
    <script src="script.js"></script>
</body>
</html>