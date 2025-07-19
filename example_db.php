<?php
// Connect to MySQL persistant DB backend
define('PRIMARY_DB_SERVER', 'localhost');
define('PRIMARY_DB_USERNAME', '');
define('PRIMARY_DB_PASSWORD', '');
define('PRIMARY_DB_SCHEMA', 'WeatherOS');

$link = mysqli_connect(PRIMARY_DB_SERVER, PRIMARY_DB_USERNAME, PRIMARY_DB_PASSWORD, PRIMARY_DB_SCHEMA);
?>