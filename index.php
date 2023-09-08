<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/styles.css">
    <link
            rel="stylesheet"
            href="//fonts.googleapis.com/css?family=Just+Another+Hand"
    />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Weather App</title>
</head>
<body>


<h1 class="title">Weather Forecast For Today</h1>

<div class="box">
    <form method="post" action="index.php" class="form">

        <div class="search-container">
            <input type="text" name="cityName" placeholder="Please enter a city name" required><i class="fa-solid fa-magnifying-glass"></i></input>
        </div>

        <button type="submit" name="submit" class="button">Submit</button>
    </form>
</div>


<?php
require(__DIR__ . '/config.php');

global $apiKey;

if (isset($_POST["submit"])) {
    $city = $_POST["cityName"];
    $apiUrl = "https://api.openweathermap.org/data/2.5/forecast?q=$city&units=metric&appid=$apiKey";

// Use the "get_headers" function to check the HTTP response headers
    $response_headers = get_headers($apiUrl);

    if ($response_headers[0] === 'HTTP/1.1 404 Not Found') {
        echo '<div class="error-message">City not found</div>';
    } else {
        try {
            $json_data = file_get_contents($apiUrl);

            if ($json_data === false) {
                throw new Exception('Failed to retrieve data from the API.');
            }

            $city_data = json_decode($json_data);

            if ($city_data === null || !isset($city_data->city)) {
                throw new Exception("City not found");
            }

            $city_data = json_decode($json_data);

            // Check if the 'city' key exists in the response data to avoid errors
            if (isset($city_data->city)) {
                $city_list = count($city_data->list);

                echo '<div class="city_title">';
                echo '<h1>', $city_data->city->name, '(', $city_data->city->country, ')</h1>';
                echo '</div>';

                echo '<div class="container">';

                $currentTimestamp = time(); // Aktuelle Serverzeit in Unix-Timestamp erhalten
                $today = date("d/m/y", $currentTimestamp); // Heutiges Datum

                for ($i = 0, $count = 0; $i < $city_list && $count < 5; $i++) {
                    $timestamp = strtotime($city_data->list[$i]->dt_txt);
                    $forecastDate = date("d/m/y", $timestamp);
                    $formattedTime = date("H:i", $timestamp);

                    if ($timestamp >= $currentTimestamp) {
                        echo '<div class="item">';
                        echo '<h1>', $forecastDate, ' ', $formattedTime, '</h1>';
                        $iconName = $city_data->list[$i]->weather[0]->icon;
                        $iconLink = "https://openweathermap.org/img/wn/" . $iconName . "@2x.png";

                        echo "<img alt='weather icons' src='$iconLink'>";

                        // General information about the weather
                        echo '<h2>Temperature</h2>';
                        echo '<p><strong>Current: </strong>', $city_data->list[$i]->main->temp, '&deg; C</p>';
                        echo '<p><strong>Minimum: </strong>', $city_data->list[$i]->main->temp_min, '&deg; C</p>';
                        echo '<p><strong>Maximum: </strong>', $city_data->list[$i]->main->temp_max, '&deg; C </p>';

                        // Something about the air
                        echo '<h2>Air</h2>';
                        echo '<p><strong>Humidity: </strong>', $city_data->list[$i]->main->humidity, '%</p>';
                        echo '<p><strong>Pressure: </strong>', $city_data->list[$i]->main->pressure, 'hpa</p>';

                        // Some info about the wind
                        echo '<h2>Wind</h2>';
                        echo '<p><strong>Speed: </strong>', $city_data->list[$i]->wind->speed, 'm/s</p>';
                        echo '<p><strong>Orientation: </strong>', $city_data->list[$i]->wind->deg, '&deg; </p>';

                        // Description
                        echo '<h2>The weather</h2>';
                        echo '<p><strong>Description: </strong>', $city_data->list[$i]->weather[0]->description, '</p>';
                        echo '</div>';
                        $count++;
                    }
                }
                echo '</div>';
            } else {
                echo '<div class="error">City not found</div>';
            }

        } catch (Exception $e) {
            echo 'Message: ' . $e->getMessage();
        }

    }
}

?>

</body>
</html>