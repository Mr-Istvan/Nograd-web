<?php
// --- KÖZÖS ADATKEZELŐ BLOKK ---
$cacheFile = 'data/weather_24.json';
$refreshInterval = 3600;

$varosok = [
    "Salgótarján" => [48.10, 19.80], "Balassagyarmat" => [48.07, 19.29], "Pásztó" => [47.92, 19.66],
    "Szécsény" => [48.08, 19.51], "Bátonyterenye" => [47.98, 19.83], "Rétság" => [47.92, 19.14],
    "Hollókő" => [47.99, 19.58], "Bánk" => [47.92, 19.17], "Nógrádszakál" => [48.19, 19.53],
    "Tar" => [47.95, 19.74], "Somoskő" => [48.17, 19.85], "Rónabánya" => [48.12, 19.89],
    "Ipolytarnóc" => [48.24, 19.62], "Kozárd" => [47.92, 19.63], "Cserhát" => [47.91, 19.46],
    "Börzsöny" => [47.93, 18.92], "Mátra" => [47.87, 19.92]
];

if (!file_exists($cacheFile) || (time() - filemtime($cacheFile) > $refreshInterval)) {
    $lats = implode(',', array_column($varosok, 0));
    $lons = implode(',', array_column($varosok, 1));
    // weather_code hozzáadva a daily paraméterekhez a 6 napos előrejelzés ikonjaihoz
    $url = "https://api.open-meteo.com/v1/forecast?latitude=$lats&longitude=$lons&current=temperature_2m,weather_code,relative_humidity_2m,wind_speed_10m,pressure_msl&daily=temperature_2m_max,temperature_2m_min,weather_code&timezone=auto";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $res = curl_exec($ch);
    curl_close($ch);

    if ($res) {
        if (!is_dir('data')) mkdir('data', 0777, true);
        file_put_contents($cacheFile, $res);
    }
}

$data = json_decode(file_get_contents($cacheFile), true);
// --- KÖZÖS BLOKK VÉGE ---
?>

<style>
    .weather-mobile-mini, #weather-modal { display: none; }

    @media (max-width: 767px) {
        .weather-mobile-mini {
            display: flex; flex-direction: column; position: fixed; 
            right: 10px; top: 10px; z-index: 999999;
            background: rgba(15, 23, 42, 0.95); border: 1px solid rgba(56, 189, 248, 0.3);
            border-radius: 10px; padding: 10px 10px; color: #fff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            cursor: pointer;
            animation: pulse-border 3s infinite;
        }

        #weather-modal {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: #0f172a; z-index: 1000000; color: white; 
            overflow-y: auto; padding: 20px; font-family: sans-serif;
        }

        .city-card {
            background: rgba(30, 41, 59, 0.5); border-radius: 16px; padding: 15px;
            margin-bottom: 12px; border: 1px solid rgba(255,255,255,0.05);
        }
        .city-card.active { border-color: #facc15; background: rgba(56, 189, 248, 0.1); }

        .card-details {
            display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 12px;
            border-top: 1px solid rgba(255,255,255,0.1); margin-top: 5px; padding-top: 15px;
        }
        .card-details i { color: #facc15; width: 18px; }

        .daily-forecast-container {
            display: flex; justify-content: space-between; margin-top: 15px;
            border-top: 1px solid rgba(255,255,255,0.1); padding-top: 15px;
        }
        .daily-item {
            display: flex; flex-direction: column; align-items: center; gap: 5px;
        }
        .daily-day { font-size: 12px; font-weight: bold; color: #facc15; text-transform: uppercase;}
        .daily-icon { width: 30px; height: 30px; background: white; border-radius: 6px; padding: 2px;}
        .daily-temps { font-size: 11px; font-weight: bold; }
        .temp-min { color: #38bdf8; }
        .temp-max { color: #ef4444; }
        
        /* Hover effektus a kattintható felső sorhoz */
        .clickable-header {
            transition: background 0.2s;
            border-radius: 10px;
        }
        .clickable-header:active {
            background: rgba(255,255,255,0.1);
        }
    }

    @keyframes pulse-border {
        0% { box-shadow: 0 0 0 0 rgba(56, 189, 248, 0.4); }
        70% { box-shadow: 0 0 0 6px rgba(56, 189, 248, 0); }
        100% { box-shadow: 0 0 0 0 rgba(56, 189, 248, 0); }
    }
</style>

<div class="weather-mobile-mini" onclick="window.openWeatherList()">
    <div style="font-size:10px; color:#facc15; font-weight:800; text-transform:uppercase;" id="mw-name">Betöltés...</div>
    <div id="s-date" style="font-size: 12px; color: #e6c50b; margin-bottom: 5px;"></div>
    <div style="display:flex; align-items:center; gap:8px;">
        <img src="" id="mw-img" style="height:35px; border-radius:6px; background:white;">
        <span style="font-size:28px; font-weight:800;" id="mw-temp">--°</span>
    </div>
</div>

<div id="weather-modal">
    <!-- FEJLÉC: Dátum, Nap, és a Bezárás gomb egy sorban -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap: nowrap;">
        <h2 style="color:#facc15; margin:0; font-size: 18px;">Helyszínek</h2>
        
        <div style="display:flex; align-items:center; gap:8px;">
            <span id="m-date-time" style="color: #a3e635; font-size: 13px; background: rgba(255,255,255,0.05); padding: 5px 8px; border-radius: 5px; border: 1px solid rgba(255,255,255,0.1);"></span>
            <span id="m-day" style="color: #facc15; font-size: 15px; font-weight: 800; text-transform: uppercase;"></span>
        </div>

        <div onclick="window.closeWeatherList()" style="font-size:35px; color:#ef4444; line-height:1; cursor:pointer;">&times;</div>
    </div>
    
    <div id="m-list-content"></div>
</div>

<script>
(function() {
    const rawData = <?= json_encode($data) ?>;
    const cityNames = <?= json_encode(array_keys($varosok)) ?>;

    const daysShort = ["V", "H", "K", "Sze", "Cs", "P", "Szo"];
    const dayNamesFull = ["Vasárnap", "Hétfő", "Kedd", "Szerda", "Csütörtök", "Péntek", "Szombat"];

    function getCityWeather(i) {
        const d = Array.isArray(rawData) ? rawData[i] : rawData;
        if (!d || !d.current) return null;
        
        return {
            temp: d.current.temperature_2m,
            code: d.current.weather_code,
            hum: d.current.relative_humidity_2m,
            wind: d.current.wind_speed_10m,
            pres: d.current.pressure_msl,
            daily: d.daily
        };
    }

    function getIcon(code, wind = 0, isDaily = false) {
        const p = "../img/weather_img/";
        
        if (wind > 25) return p + "szeles.jpg";

        if (!isDaily) {
            const h = new Date().getHours();
            if (h >= 18 || h < 6) {
                if (code <= 1) return p + "ejszakatiszta.jpg";
                if (code === 2 || code === 3 || (code >= 45 && code <= 48)) return p + "felhosejszaka.jpg";
            }
        }

        if (code === 0) return p + "nap.jpg";
        if (code === 1 || code === 2) return p + "naposfelhos.jpg";
        if (code === 3 || (code >= 45 && code <= 48)) return p + "felhos.jpg";
        
        if ((code >= 51 && code <= 55) || (code >= 61 && code <= 65)) return p + "esos.jpg";
        if (code === 56 || code === 57 || code === 66 || code === 67) return p + "havaseso.jpg";
        if ((code >= 71 && code <= 77) || code === 85 || code === 86) return p + "havas.jpg";
        if ((code >= 80 && code <= 82) || (code >= 95 && code <= 99)) return p + "zapor.jpg";

        return p + "naposfelhos.jpg";
    }

    window.openWeatherList = () => {
        document.getElementById('weather-modal').style.display = 'block';
        history.pushState({view: 'weather_modal'}, '');
        const list = document.getElementById('m-list-content');
        const saved = localStorage.getItem('mSel') || 0;
        list.innerHTML = '';
        
        const today = new Date();

        cityNames.forEach((name, i) => {
            const w = getCityWeather(i);
            if (!w) return;
            
            // 6 napos előrejelzés generálása
            let dailyHtml = '';
            for (let d = 1; d <= 6; d++) {
                if(w.daily.time[d]) {
                    const targetDate = new Date(today);
                    targetDate.setDate(today.getDate() + d);
                    const dayName = daysShort[targetDate.getDay()];
                    
                    const minT = Math.round(w.daily.temperature_2m_min[d]);
                    const maxT = Math.round(w.daily.temperature_2m_max[d]);
                    const wCode = w.daily.weather_code[d];
                    
                    dailyHtml += `
                        <div class="daily-item">
                            <span class="daily-day">${dayName}</span>
                            <img src="${getIcon(wCode, 0, true)}" class="daily-icon">
                            <span class="daily-temps"><span class="temp-min">${minT}</span> / <span class="temp-max">${maxT}</span></span>
                        </div>
                    `;
                }
            }

            const card = document.createElement('div');
            card.className = `city-card ${i == saved ? 'active' : ''}`;
            
            // Csak a felső, nagyméretű rész kapja meg a kattintást!
            card.innerHTML = `
                <div class="clickable-header" style="display:flex; justify-content:space-between; align-items:center; cursor:pointer; padding: 5px;" onclick="window.updateMini(${i}); window.closeWeatherList();">
                    <b style="font-size:20px; ${i == saved ? 'color:#38bdf8' : 'color:#fff'}">${name}</b>
                    <div style="display:flex; align-items:center; gap:12px;">
                        <img src="${getIcon(w.code, w.wind)}" style="height:44px; border-radius:8px; background:white; padding:2px;">
                        <span style="font-size:28px; font-weight:900; color:#fff">${Math.round(w.temp)}°</span>
                    </div>
                </div>
                
                <div class="card-details">
                    <div><i class="fa fa-wind"></i> <b>${Math.round(w.wind)} km/h</b></div>
                    <div><i class="fa fa-tint"></i> <b>${w.hum}%</b></div>
                    <div><i class="fa fa-tachometer"></i> <b>${Math.round(w.pres)}</b></div>
                    <div style="color:#38bdf8;"><i class="fa fa-arrows-v"></i> <b>${Math.round(w.daily.temperature_2m_min[0])}° / ${Math.round(w.daily.temperature_2m_max[0])}°</b></div>
                </div>
                <div class="daily-forecast-container">
                    ${dailyHtml}
                </div>`;
            list.appendChild(card);
        });
    };

    window.closeWeatherList = () => {
        document.getElementById('weather-modal').style.display = 'none';
        if (history.state && history.state.view === 'weather_modal') history.back();
    };

    window.addEventListener('popstate', () => {
        document.getElementById('weather-modal').style.display = 'none';
    });

    function updateDateTime() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const dayNameFull = dayNamesFull[now.getDay()];
        const timeStr = now.toLocaleTimeString('hu-HU', { hour: '2-digit', minute: '2-digit' });

        // Kis widget datum frissítése
        const sDate = document.getElementById('s-date');
        if(sDate) sDate.innerText = `${year}.${month}.${day}. ${timeStr}`;

        // Nagy modal frissítése - külön a dátum/idő és külön a nap neve
        const mDateTime = document.getElementById('m-date-time');
        if(mDateTime) mDateTime.innerText = `${year}.${month}.${day}. ${timeStr}`;
        
        const mDay = document.getElementById('m-day');
        if(mDay) mDay.innerText = dayNameFull;
    }

    updateDateTime();
    setInterval(updateDateTime, 60000);

    window.updateMini = function(idx) {
        const w = getCityWeather(idx);
        if (!w) return;
        document.getElementById('mw-name').innerText = cityNames[idx];
        document.getElementById('mw-temp').innerText = Math.round(w.temp) + '°';
        document.getElementById('mw-img').src = getIcon(w.code, w.wind);
        localStorage.setItem('mSel', idx);
    }

    if (rawData) {
        window.updateMini(parseInt(localStorage.getItem('mSel') || 0));
    } else {
        document.getElementById('mw-name').innerText = "Hiba az adatokkal";
    }
})();
</script>