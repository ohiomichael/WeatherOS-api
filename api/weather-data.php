<?php
/* 
WeatherOS API
Version 0.0.0
© 2025 OLSD Free Press. All rights reserved.
*/
require_once "../db.php";

header('Content-Type: application/json');
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === "POST"){
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    if ($data === null) {
        // Handle invalid JSON
        echo json_encode(['error' => 'Invalid JSON data']);
        http_response_code(400); // Bad Request
        die;
    }

    if($data['sensor'] === "sht41"){
        // Validate Temperature (Celsius)
        if (empty(trim($data["temperature"]))) {
            $errors["temperature"] = "Invalid temperature. None provided.";
        } elseif (!preg_match('/^[-+]?\d+(\.\d+)?$/',trim($data["temperature"]))) {
            $errors["temperature"] ="Invalid temperature. Must be a valid number.";
        } else {
            $temperature = floatval(trim($data["temperature"]));
        }

        // Validate Humidity Level
        if (empty(trim($data["humidity"]))) {
            $errors['humidity'] = "Invalid humidity. None provided.";
        } elseif (!preg_match('/^\d+(\.\d+)?$/', trim($data["humidity"]))) {
            $errors['humidity'] = "Invalid humidity. Must be a number (e.g., 50, 65.7).";
        } else {
            $humidity = floatval(trim($data["humidity"]));
        }

        if (empty($errors)){
            $insert_sql = "INSERT INTO sht41 (temperature, humidity) VALUES (?, ?);";
            $insert_stmt = mysqli_prepare($link, $insert_sql);
            mysqli_stmt_bind_param($insert_stmt, "dd", $temperature, $humidity);
            if (mysqli_stmt_execute($insert_stmt)){
                http_response_code(200);
                die();
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Could not process request']);
                die();
            }
        }
    }

    elseif($data['sensor'] === "bmp280"){
        // Validate Temperature (Celsius)
        if (empty(trim($data["temperature"]))) {
            $errors["temperature"] = "Invalid temperature. None provided.";
        } elseif (!preg_match('/^[-+]?\d+(\.\d+)?$/',trim($data["temperature"]))) {
            $errors["temperature"] ="Invalid temperature. Must be a valid number.";
        } else {
            $temperature = floatval(trim($data["temperature"]));
        }
        
        // Validate Barometric Pressure (hPa)
        if(empty(trim($data["pressure"]))){
            $errors['pressure'] = "Invalid pressure. None provided.";
        } elseif(!preg_match('/^\d+(\.\d+)?$/', trim($data["pressure"]))){
            $errors['pressure'] = "Invalid pressure. Must be a positive number (e.g., 1013.25).";
        } else{
            $pressure = floatval(trim($data["pressure"]));
        }

        if (empty($errors)){
            $insert_sql = "INSERT INTO bmp280 (temperature, pressure) VALUES (?, ?);";
            $insert_stmt = mysqli_prepare($link, $insert_sql);
            mysqli_stmt_bind_param($insert_stmt, "dd", $temperature, $pressure);
            if (mysqli_stmt_execute($insert_stmt)){
                http_response_code(200);
                die();
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Could not process request']);
                die();
            }
        }
   } elseif($data['sensor'] === "dsk"){
        
        // Validate value ADC Reading (0–65535)
        if (empty(trim($data["value"]))) {
            $errors['value'] = "Invalid value reading. None provided.";
        } elseif (!preg_match('/^\d+$/', trim($data["value"]))) {
            $errors['value'] = "Invalid value reading. Must be a number between 0 and 65535.";
        } else {
            $value = intval(trim($data["value"]));
            if ($value < 0 || $value > 65535) {
                $errors['value'] = "Invalid value reading. Must be between 0 and 65535.";
            }
        }

        if (empty($errors)){
            $insert_sql = "INSERT INTO dsk (value) VALUES (?);";
            $insert_stmt = mysqli_prepare($link, $insert_sql);
            mysqli_stmt_bind_param($insert_stmt, "i", $value);
            if (mysqli_stmt_execute($insert_stmt)){
                http_response_code(200);
                die();
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Could not process request']);
                die();
            }
        }
} else {
        echo json_encode(['error' => 'Sensor not supported']);
        http_response_code(400); // Bad Request
        die;
   }

} else{
    echo json_encode(['error' => 'Invalid JSON data']);
    http_response_code(400); // Bad Request
    die;
}

echo json_encode($errors);
?>