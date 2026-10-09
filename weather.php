<?php
session_start();
$user_name = $_SESSION['user_name'] ?? $_SESSION['username'] ?? "Trisha Shah";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Weather Module - Disaster Portal</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
        body { 
            background: linear-gradient(rgba(15, 23, 42, 0.90), rgba(15, 23, 42, 0.95)), 
                        url('https://images.unsplash.com/photo-1592210454359-9043f067919b?q=80&w=1920&auto=format&fit=crop') no-repeat center center fixed;
            background-size: cover;
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #0f172a;
            padding: 15px 35px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .navbar .brand { font-size: 22px; font-weight: 700; color: #ffffff; text-decoration: none; }
        .nav-links { display: flex; align-items: center; gap: 22px; }
        .nav-links a { color: #cbd5e1; text-decoration: none; font-size: 15px; font-weight: 500; }
        .nav-links a:hover, .nav-links a.active { color: #38bdf8; }
        .btn-dashboard { background: #dc2626; color: white !important; padding: 8px 18px; border-radius: 6px; font-weight: bold; }

        .container { max-width: 800px; margin: 40px auto; padding: 0 20px; flex: 1; }

        .weather-card {
            background: rgba(30, 41, 59, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 16px;
            padding: 30px;
            backdrop-filter: blur(12px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        }

        .card-title {
            text-align: center;
            font-size: 22px;
            font-weight: 700;
            color: #38bdf8;
            margin-bottom: 20px;
        }

        .search-row {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }

        .search-row input {
            flex: 1;
            padding: 12px 16px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(15, 23, 42, 0.8);
            color: #fff;
            font-size: 15px;
        }

        .search-row button {
            padding: 12px 24px;
            background: #0284c7;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
        }

        .search-row button:hover { background: #0369a1; }

        .status-msg {
            font-size: 13px;
            color: #94a3b8;
            margin-bottom: 20px;
            text-align: center;
        }

        .current-weather {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .city-name { font-size: 26px; font-weight: 700; color: #f8fafc; }
        .temp-display { font-size: 52px; font-weight: 800; color: #ffffff; margin: 10px 0; }
        .weather-desc { font-size: 16px; color: #cbd5e1; }

        .details-row {
            display: flex;
            justify-content: space-around;
            margin: 20px 0 30px 0;
            font-size: 15px;
            color: #cbd5e1;
        }

        .forecast-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 15px;
            text-align: center;
            color: #f8fafc;
        }

        .forecast-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 12px;
        }

        .forecast-card {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 10px;
            padding: 12px;
            text-align: center;
        }

        .forecast-date { font-size: 12px; font-weight: 600; color: #94a3b8; margin-bottom: 6px; }
        .forecast-temp { font-size: 18px; font-weight: 700; color: #38bdf8; margin: 6px 0; }
        .forecast-desc { font-size: 11px; color: #cbd5e1; }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="brand">Disaster Portal</a>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="disasters.php">Disasters</a>
            <a href="weather.php" class="active">Live Weather</a>
            <a href="helplines.php">Emergency Helplines</a>
            <a href="feedback.php">Feedback</a>
            <a href="dashboard.php" class="btn-dashboard">Dashboard</a>
        </div>
    </nav>

    <div class="container">
        <div class="weather-card">
            <h2 class="card-title">🌤️ Real-Time Weather Alert System</h2>
            
            <div class="search-row">
                <input type="text" id="cityInput" placeholder="Enter city name..." value="London">
                <button onclick="getWeatherBySearch()">Search</button>
            </div>
            
            <div id="gpsStatus" class="status-msg">Detecting location...</div>

            <div class="current-weather">
                <div id="cityName" class="city-name">--</div>
                <div id="weatherDesc" class="weather-desc">--</div>
                <div id="temperature" class="temp-display">--°C</div>
            </div>

            <div class="details-row">
                <div>💧 Humidity: <span id="humidity">--</span>%</div>
                <div>💨 Wind Speed: <span id="windSpeed">--</span> km/h</div>
            </div>

            <h3 class="forecast-title">5-Day Emergency Weather Forecast</h3>
            <div id="forecastGrid" class="forecast-grid"></div>
        </div>
    </div>

<script>
    function getWmoInfo(code) {
        const map = {
            0: "Clear Sky", 1: "Mainly Clear", 2: "Partly Cloudy", 3: "Overcast",
            45: "Foggy", 48: "Rime Fog", 51: "Light Drizzle", 53: "Moderate Drizzle",
            55: "Dense Drizzle", 61: "Slight Rain", 63: "Moderate Rain", 65: "Heavy Rain",
            80: "Rain Showers", 81: "Moderate Showers", 82: "Violent Showers",
            95: "Thunderstorm", 96: "Thunderstorm with Hail"
        };
        return map[code] || "Cloudy";
    }

    async function fetchWeatherData(lat, lon, labelName = "Your Location") {
        const status = document.getElementById('gpsStatus');
        try {
            status.textContent = "Fetching weather updates...";
            
            // Fetch reverse geocoding if needed
            if (labelName === "Your Location") {
                try {
                    const geoRes = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`);
                    const geoData = await geoRes.json();
                    labelName = geoData.address.city || geoData.address.town || geoData.address.state || "Your Location";
                } catch(e) {}
            }

            // Fetch live weather + 5-day daily forecast
            const weatherRes = await fetch(`https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current=temperature_2m,relative_humidity_2m,weather_code,wind_speed_10m&daily=weather_code,temperature_2m_max&timezone=auto`);
            const data = await weatherRes.json();

            // Populate current weather UI
            document.getElementById('cityName').textContent = labelName;
            document.getElementById('temperature').textContent = `${Math.round(data.current.temperature_2m)}°C`;
            document.getElementById('weatherDesc').textContent = getWmoInfo(data.current.weather_code);
            document.getElementById('humidity').textContent = data.current.relative_humidity_2m;
            document.getElementById('windSpeed').textContent = data.current.wind_speed_10m;
            status.textContent = "📍 Live weather updated successfully";

            // Populate 5-Day Forecast Grid
            const forecastGrid = document.getElementById('forecastGrid');
            forecastGrid.innerHTML = "";
            const daily = data.daily;

            for (let i = 0; i < Math.min(5, daily.time.length); i++) {
                const dateStr = new Date(daily.time[i]).toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
                const desc = getWmoInfo(daily.weather_code[i]);
                const maxTemp = Math.round(daily.temperature_2m_max[i]);

                forecastGrid.innerHTML += `
                    <div class="forecast-card">
                        <div class="forecast-date">${dateStr}</div>
                        <div class="forecast-temp">${maxTemp}°C</div>
                        <div class="forecast-desc">${desc}</div>
                    </div>
                `;
            }
        } catch (err) {
            status.textContent = "⚠️ Failed to fetch weather data. Check internet connection.";
        }
    }

    async function getWeatherBySearch() {
        const city = document.getElementById('cityInput').value.trim() || "London";
        const status = document.getElementById('gpsStatus');
        status.textContent = `Searching for "${city}"...`;

        try {
            const geoRes = await fetch(`https://geocoding-api.open-meteo.com/v1/search?name=${encodeURIComponent(city)}&count=1&format=json`);
            const geoData = await geoRes.json();

            if (geoData.results && geoData.results[0]) {
                const loc = geoData.results[0];
                fetchWeatherData(loc.latitude, loc.longitude, `${loc.name}, ${loc.country || ''}`);
            } else {
                status.textContent = `❌ Location "${city}" not found.`;
            }
        } catch(e) {
            status.textContent = "⚠️ Search error. Please try again.";
        }
    }

    // On Load: Try GPS with 3-second timeout fallback to London
    window.addEventListener('DOMContentLoaded', () => {
        let fallbackTriggered = false;

        const fallbackToDefault = () => {
            if (!fallbackTriggered) {
                fallbackTriggered = true;
                getWeatherBySearch();
            }
        };

        const timeout = setTimeout(fallbackToDefault, 3000);

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    clearTimeout(timeout);
                    if (!fallbackTriggered) {
                        fetchWeatherData(pos.coords.latitude, pos.coords.longitude);
                    }
                },
                () => {
                    clearTimeout(timeout);
                    fallbackToDefault();
                },
                { timeout: 3000 }
            );
        } else {
            fallbackToDefault();
        }
    });
</script>

</body>
</html>