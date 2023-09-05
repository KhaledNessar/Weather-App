<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="css/styles.css">
        <link rel="icon" href="http://openweathermap.org/img/wn/10d@2x.png"/>
        <link
            rel="stylesheet"
            href="//fonts.googleapis.com/css?family=Just+Another+Hand"
        />
        <title>Weather App</title>
    </head>
    <body>


        <h1 class="title">Weather Forecast</h1>

        <div class="box">
            <form method="post" action="index.php" class="form">
                Weather in:
                <input type="text" name="cityName">
                <input type="submit" value="Search" name="submit">
            </form>
        </div>

        <?php
        if (isset($_POST["submit"])) {
            $city = $_POST["cityName"];
            $apiUrl = "https://api.openweathermap.org/data/2.5/forecast?q=$city&units=metric&appid=abb21b96237441179709d635f51a8f80";

            $json_data = file_get_contents($apiUrl);
            $city_data = json_decode($json_data);

            // Check if the 'city' key exists in the response data to avoid errors
            if (isset($city_data->city)) {
                $city_list = count($city_data->list);

                echo '<div class="city_title">';
                echo '<h1>', $city_data->city->name, '(', $city_data->city->country,')</h1>';
                echo '</div>';

                echo '<div class="container">';

                for ($i = 0; $i < $city_list; $i++) {
                    $city_time = explode(" ", $city_data->list[$i]->dt_txt);

                    if ($city_time[1] == '15:00:00') {
                        echo '<div class="item">';
                        echo '<h1>', $city_data->list[$i]->dt_txt, '</h1>';
                        $iconName = $city_data->list[$i]->weather[0]->icon;
                        $iconLink = "http://openweathermap.org/img/wn/" . $iconName . "@2x.png";

                        echo "<img src='$iconLink'>";

                        // General information about the weather
                        echo '<h2>Temperature</h2>';
                        echo '<p><strong>Current:</strong>', $city_data->list[$i]->main->temp, '&deg; C</p>';
                        echo '<p><strong>Minimum:</strong>', $city_data->list[$i]->main->temp_min, '&deg; C</p>';
                        echo '<p><strong>Maximum:</strong>', $city_data->list[$i]->main->temp_max, '&deg; C </p>';

                        // Something about the air
                        echo '<h2>Air</h2>';
                        echo '<p><strong>Humidity:</strong>', $city_data->list[$i]->main->humidity, '%</p>';
                        echo '<p><strong>Pressure:</strong>', $city_data->list[$i]->main->pressure, 'hpa</p>';

                        // Some info about the wind
                        echo '<h2>Wind</h2>';
                        echo '<p><strong>Speed:</strong>', $city_data->list[$i]->wind->speed, 'm/s</p>';
                        echo '<p><strong>Orientation:</strong>', $city_data->list[$i]->wind->deg, '&deg; </p>';

                        // Description
                        echo '<h2>The weather</h2>';
                        echo '<p><strong>Description:</strong>', $city_data->list[$i]->weather[0]->description, '</p>';
                        echo '</div>';
                    }
                }
                echo '</div>';
            } else {
                echo '<div class="error">City not found</div>';
            }
        }
        ?>

    </body>
</html>