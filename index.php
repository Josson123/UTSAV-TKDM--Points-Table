<?php
// Database configuration
$host = getenv('DB_HOST') ?: 'db';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: 'example';
$db   = getenv('DB_NAME') ?: 'utsav_db';


try {
    // Create connection
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
     echo "Database connected!"; 

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

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
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
    <link rel="icon" href="../Photos/YD_Logo_BG.png" type="image/png">
    <title>Utsav TKDM Points Table</title>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            margin-top: 90px;
            background-color: var(--body-color);
            background-image: url("../Photos/YD_Logo_BG.png"); /* path from index.php */
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
            border: 1px solid rgba(14, 163, 29, 1);
            padding: 8px;
            text-align: center;
            white-space: nowrap;
            background-color: rgba(10, 198, 23, 0.99);
        }

        /* Dark mode cells */
        body.dark .points-table th,
        body.dark .points-table td {
            border: 1px solid rgba(31, 45, 137, 1);
            background-color: rgba(6, 97, 195, 0.93);
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
            background-color: rgba(220, 18, 18, 1);
            font-weight: bold;
        }

        /* Dark mode total row */
        body.dark .points-table .total-row {
            background-color: rgba(57, 56, 56, 0.9);
        }
        
        .points-value {
            font-weight: bold;
            color: var(--text-color);
            padding: 5px;
            text-align: center;
            font-size: 14px;
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
    </style>
</head>
<body>
    <nav>
        <div class="nav-bar">
            <i class='bx bx-menu sidebarOpen' ></i>
              <img src="Photos/YD_Logo_BG.png" alt="Profile" class="profile-pic">
            <div class="menu">
            <span class="logo navLogo"><a href="index.php">UTSAV-TKDM ഫൊറോണ യുവദീപ്തി കലോത്സവം </a></span>

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
            </div>
        </div>
    </nav>
 
    <div class="container">
        <h1>Utsav TKDM - Points Table</h1>
        
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
                                <span class="points-value"><?php echo $current_points; ?></span>
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
    
    <script src="script.js"></script>
</body>
</html>
