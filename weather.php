<?php
// 1. NÓGRÁD VÁRMEGYEI HELYSZÍNEK KOORDINÁTÁI
$varosok = [
    "Salgótarján" => [48.10, 19.80],
    "Balassagyarmat" => [48.07, 19.29],
    "Pásztó" => [47.92, 19.66],
    "Szécsény" => [48.08, 19.51],
    "Bátonyterenye" => [47.98, 19.83],
    "Rétság" => [47.92, 19.14],
    "Hollókő" => [47.99, 19.58],
    "Bánk" => [47.92, 19.17],
    "Nógrádszakál" => [48.19, 19.53],
    "Tar" => [47.95, 19.74],
    "Somoskő" => [48.17, 19.85],
    "Rónabánya" => [48.12, 19.89],
    "Ipolytarnóc" => [48.24, 19.62],
    "Kozárd" => [47.92, 19.63],
    "Cserhát" => [47.91, 19.46],
    "Börzsöny" => [47.93, 18.92],
    "Mátra" => [47.87, 19.92]
];

$lats = implode(',', array_column($varosok, 0));
$lons = implode(',', array_column($varosok, 1));

// API lekérés
$url = "https://api.open-meteo.com/v1/forecast?latitude=$lats&longitude=$lons&current=temperature_2m,weather_code,relative_humidity_2m,wind_speed_10m,wind_direction_10m,pressure_msl&hourly=temperature_2m,weather_code&daily=temperature_2m_max,temperature_2m_min&timezone=auto&forecast_days=2";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$response = curl_exec($ch);
curl_close($ch);

$json_for_js = json_encode(json_decode($response, true));
?>

<style>
    /* IDŐJÁRÁS DOBOZ - FIXÁLT DESIGN */
    .weather-box {
        position: relative; 
        /* 3px margó körben a kérésednek megfelelően */
        margin: 3px; 
        /* A szélességből levonjuk a kétoldali 3-3px-et */
        width: calc(100% - 6px); 
        background: rgba(26, 26, 26, 0.98); 
        border-top: 3px solid #45489a; 
        border-bottom: 1px solid #333;
        color: white; 
        padding: 12px; 
        font-family: 'Open Sans', sans-serif;
        box-sizing: border-box;
        border-radius: 4px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        z-index: 10;
    }
    
    .w-main { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px; }
    .w-temp { font-size: 22px; font-weight: 800; line-height: 1; color: #fff; }
    .w-minmax { font-size: 10px; color: #fec107; font-weight: 600; display: block; margin-top: 4px; }
    .w-details { font-size: 9px; color: #bbb; margin-top: 6px; line-height: 1.4; }
    .fo-kep { width: 42px; height: 42px; border-radius: 6px; object-fit: cover; border: 1px solid #444; }
    
    .w-hours { 
        display: flex; 
        justify-content: space-between; 
        border-top: 1px solid #333; 
        padding-top: 8px; 
        margin-top: 8px; 
        gap: 2px;
    }
    .w-hour-item { text-align: center; font-size: 9px; flex: 1; }
    .w-hour-item img { width: 18px; height: 18px; display: block; margin: 2px auto; }
    
    .city-select {
        width: 100%; background: #1a1a1a; color: #fec107; border: 1px solid #45489a;
        border-radius: 4px; padding: 4px; font-size: 10px; margin-top: 10px; cursor: pointer;
        font-weight: bold; outline: none;
    }

    /* Képernyő magasság korrekció */
    @media (max-height: 750px) {
        .weather-box { padding: 8px; }
        .w-hours { padding-top: 5px; margin-top: 5px; }
        .w-temp { font-size: 18px; }
    }
</style>

<div class="weather-box">
    <div class="w-main">
        <div>
            <span class="w-temp" id="main-temp">--°C</span>
            <span class="w-minmax" id="main-minmax">-- / --</span>
            <div class="w-details">
                🌬️ <span id="main-wind">--</span> km/h (<span id="main-dir">--</span>°) | 💧 <span id="main-hum">--</span>%<br>
                ⏲️ <strong><span id="main-press">--</span> hPa</strong>
            </div>
        </div>
        <img src="" id="main-img" class="fo-kep">
    </div>
    
    <div class="w-hours" id="hourly-forecast"></div>

    <select class="city-select" id="citySelector" onchange="updateWeather(this.value)">
        <?php $i=0; foreach($varosok as $nev=>$k): ?>
            <option value="<?= $i++ ?>"><?= $nev ?></option>
        <?php endforeach; ?>
    </select>
</div>

<script>
    const weatherData = <?= $json_for_js ?>;

    function getCustomWeatherImg(code, hour) {
        const path = window.location.pathname;
        const prefix = path.includes('/index/') ? "../img/weather_img/" : "img/weather_img/";
        const isNight = (hour >= 19 || hour < 6);
        let img = "";

        if (isNight) {
            img = (code <= 1) ? "ejszakatiszta.jpg" : "felhosejszaka.jpg";
        } else {
            if (code === 0) img = "nap.jpg";
            else if (code <= 2) img = "naposfelhos.jpg";
            else if (code === 3 || (code >= 45 && code <= 48)) img = "felhos.jpg";
            else if ([66, 67, 83, 85, 86].includes(code)) img = "havaseso.jpg";
            else if (code >= 51 && code <= 65) img = "esos.jpg";
            else if (code >= 71 && code <= 77) img = "havas.jpg";
            else if (code >= 80 && code <= 82) img = "zapor.jpg";
            else if (code >= 95) img = "szeles.jpg";
            else img = "felhos.jpg";
        }
        return prefix + img;
    }

    function updateWeather(idx) {
        const data = Array.isArray(weatherData) ? weatherData[idx] : weatherData;
        if (!data || !data.current) return;

        const cur = data.current;
        const hr = data.hourly;
        const dy = data.daily;
        const now = new Date();
        const currentHour = now.getHours();

        // Fő adatok
        document.getElementById('main-temp').innerText = Math.round(cur.temperature_2m) + "°C";
        document.getElementById('main-minmax').innerText = `↓ ${Math.round(dy.temperature_2m_min[0])}° / ↑ ${Math.round(dy.temperature_2m_max[0])}°`;
        document.getElementById('main-wind').innerText = Math.round(cur.wind_speed_10m);
        document.getElementById('main-dir').innerText = cur.wind_direction_10m;
        document.getElementById('main-hum').innerText = cur.relative_humidity_2m;
        document.getElementById('main-press').innerText = Math.round(cur.pressure_msl);
        document.getElementById('main-img').src = getCustomWeatherImg(cur.weather_code, currentHour);

        // Órás bontás
        const hDiv = document.getElementById('hourly-forecast');
        hDiv.innerHTML = '';

        const currentHourString = now.getHours().toString().padStart(2, '0') + ":00";
        let startIndex = hr.time.findIndex(t => t.includes(currentHourString));
        if (startIndex === -1) startIndex = currentHour;

        for (let i = 1; i <= 5; i++) {
            const offset = i * 2;
            const targetIdx = startIndex + offset;
            if (hr.time[targetIdx]) {
                const hourLabel = hr.time[targetIdx].substring(11, 13);
                hDiv.innerHTML += `
                    <div class="w-hour-item">
                        <div>${hourLabel}h</div>
                        <img src="${getCustomWeatherImg(hr.weather_code[targetIdx], parseInt(hourLabel))}">
                        <div><strong>${Math.round(hr.temperature_2m[targetIdx])}°</strong></div>
                    </div>`;
            }
        }
    }
    updateWeather(0);
</script>