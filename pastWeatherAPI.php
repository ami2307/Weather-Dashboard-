<?php
// Configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "city_weather";
$api_key = "b9042ec5d9a26c6e11c152ed3cf8ec90";
$city_name = "Chelmsford";

// Latitude and Longitude for the city (Chelmsford, UK in this case)
$lat = 51.7356; // Replace with the correct latitude for your city
$lon = 0.4686;  // Replace with the correct longitude for your city

// Constants for table structure
const ID_LENGTH = 6;
const PRESSURE_LENGTH = 6;
const HUMIDITY_LENGTH = 3;

// Function to fetch weather data from the OpenWeatherMap API
function fetch_weather_data($api_key, $lat, $lon, $timestamp) {
    $url = "https://api.openweathermap.org/data/2.5/onecall/timemachine?lat=$lat&lon=$lon&dt=$timestamp&appid=$api_key";
    $json_data = @file_get_contents($url);
    if ($json_data === false) {
        throw new Exception("Failed to fetch weather data");
    }
    $response_data = json_decode($json_data);
    if ($response_data === null || isset($response_data->cod) && $response_data->cod !== 200) {
        throw new Exception("Failed to fetch weather data");
    }
    return $response_data;
}

// Function to create a database if it doesn't exist
function create_database($servername, $username, $password, $dbname) {
    $conn = new mysqli($servername, $username, $password);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
    if ($conn->query($sql) !== true) {
        throw new Exception("Error creating database: " . $conn->error);
    }
    if ($conn->close() === false) {
        throw new Exception("Error closing connection: " . $conn->error);
    }
}

// Function to create a table if it doesn't exist
function create_table($servername, $username, $password, $dbname, $city_name) {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    $sql = "CREATE TABLE IF NOT EXISTS $city_name(
        id INT(" . ID_LENGTH . ") UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        Day_of_Week VARCHAR(15),
        Day_and_Date VARCHAR(20),
        Weather_Condition VARCHAR(50),
        Weather_Icon VARCHAR(100),
        Temperature DECIMAL(5, 2