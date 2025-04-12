<?php
/* 
WeatherOS Data Display
Version 1.0.0
© 2025 OLSD Free Press. All rights reserved.
*/
require_once "db.php";

// Function to handle database errors
function handleDBError($query) {
    die();
}

// Get SHT41 data (temperature and humidity)
$sht41_sql = "SELECT temperature, humidity, timestamp FROM sht41 ORDER BY timestamp DESC LIMIT 10";
$sht41_result = mysqli_query($link, $sht41_sql);
if (!$sht41_result) {
    handleDBError($sht41_sql);
}

// Get BMP280 data (temperature and pressure)
$bmp280_sql = "SELECT temperature, pressure, timestamp FROM bmp280 ORDER BY timestamp DESC LIMIT 10";
$bmp280_result = mysqli_query($link, $bmp280_sql);
if (!$bmp280_result) {
    handleDBError($bmp280_sql);
}

// Get DSK data (value readings)
$dsk_sql = "SELECT value, timestamp FROM dsk ORDER BY timestamp DESC LIMIT 10";
$dsk_result = mysqli_query($link, $dsk_sql);
if (!$dsk_result) {
    handleDBError($dsk_sql);
}

// Get latest readings for summary display
$latest_sql = "
    SELECT 
        (SELECT temperature FROM sht41 ORDER BY timestamp DESC LIMIT 1) as sht41_temp,
        (SELECT humidity FROM sht41 ORDER BY timestamp DESC LIMIT 1) as humidity,
        (SELECT pressure FROM bmp280 ORDER BY timestamp DESC LIMIT 1) as pressure,
        (SELECT value FROM dsk ORDER BY timestamp DESC LIMIT 1) as dsk_value
";
$latest_result = mysqli_query($link, $latest_sql);
if (!$latest_result) {
    handleDBError($latest_sql);
}
$latest = mysqli_fetch_assoc($latest_result);

// Function to format date/time
function formatDateTime($datetime) {
    $date = new DateTime($datetime);
    return $date->format('M d, Y g:i A');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WeatherOS - Current Conditions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
            color: #333;
        }
        header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 20px;
        }
        h1 {
            color: #2c3e50;
        }
        .summary-box {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 30px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        .stat {
            text-align: center;
            flex-basis: 23%;
            min-width: 200px;
            margin: 10px 0;
        }
        .stat-value {
            font-size: 2.5em;
            font-weight: bold;
            color: #3498db;
        }
        .stat-unit {
            font-size: 0.8em;
            color: #7f8c8d;
        }
        .stat-label {
            margin-top: 5px;
            font-size: 1.2em;
            color: #2c3e50;
        }
        .data-section {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
            color: #2c3e50;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #7f8c8d;
            font-size: 0.9em;
        }
        @media (max-width: 768px) {
            .stat {
                flex-basis: 48%;
            }
        }
        @media (max-width: 480px) {
            .stat {
                flex-basis: 100%;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>WeatherOS - Current Conditions</h1>
        <p>Last updated: <?php echo date('F d, Y g:i A'); ?></p>
    </header>

    <div class="summary-box">
        <div class="stat">
            <div class="stat-value"><?php echo number_format($latest['sht41_temp'], 1); ?><span class="stat-unit">°C</span></div>
            <div class="stat-label">Temperature</div>
        </div>
        <div class="stat">
            <div class="stat-value"><?php echo number_format($latest['humidity'], 1); ?><span class="stat-unit">%</span></div>
            <div class="stat-label">Humidity</div>
        </div>
        <div class="stat">
            <div class="stat-value"><?php echo number_format($latest['pressure'], 1); ?><span class="stat-unit">hPa</span></div>
            <div class="stat-label">Pressure</div>
        </div>
        <div class="stat">
            <div class="stat-value"><?php echo number_format($latest['dsk_value']); ?></div>
            <div class="stat-label">DSK Reading</div>
        </div>
    </div>

    <div class="data-section">
        <h2>SHT41 Sensor - Temperature & Humidity</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Temperature (°C)</th>
                    <th>Humidity (%)</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($sht41_result)): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo number_format($row['temperature'], 1); ?> °C</td>
                    <td><?php echo number_format($row['humidity'], 1); ?>%</td>
                    <td><?php echo formatDateTime($row['timestamp']); ?></td>
                </tr>
                <?php endwhile; ?>
                <?php if (mysqli_num_rows($sht41_result) == 0): ?>
                <tr>
                    <td colspan="4">No data available</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="data-section">
        <h2>BMP280 Sensor - Temperature & Pressure</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Temperature (°C)</th>
                    <th>Pressure (hPa)</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($bmp280_result)): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo number_format($row['temperature'], 1); ?> °C</td>
                    <td><?php echo number_format($row['pressure'], 1); ?> hPa</td>
                    <td><?php echo formatDateTime($row['timestamp']); ?></td>
                </tr>
                <?php endwhile; ?>
                <?php if (mysqli_num_rows($bmp280_result) == 0): ?>
                <tr>
                    <td colspan="4">No data available</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="data-section">
        <h2>DSK Sensor - Readings</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Value</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($dsk_result)): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['value']; ?></td>
                    <td><?php echo formatDateTime($row['timestamp']); ?></td>
                </tr>
                <?php endwhile; ?>
                <?php if (mysqli_num_rows($dsk_result) == 0): ?>
                <tr>
                    <td colspan="3">No data available</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>© 2025 OLSD Free Press. All rights reserved. | WeatherOS Display v1.0.0</p>
    </div>
</body>
</html>