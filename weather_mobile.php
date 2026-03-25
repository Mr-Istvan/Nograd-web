<?php
// 1. TELEPÜLÉSEK ÉS KOORDINÁTÁK
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

// API hívás - Fontos a formátum!
$m_url = "https://api.open-meteo.com/v1/forecast?latitude=$lats&longitude=$lons&current=temperature_2m,weather_code,relative_humidity_2m,wind_speed_10m,pressure_msl&daily=temperature_2m_max,temperature_2m_min&timezone=auto";

$m_ch = curl_init(); 
curl_setopt($m_ch, CURLOPT_URL, $m_url); 
curl_setopt($m_ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($m_ch, CURLOPT_SSL_VERIFYPEER, false); // Biztonság kedvéért, ha a szerver nem tudná ellenőrizni az SSL-t
$m_res = curl_exec($m_ch); 
curl_close($m_ch); 
$m_data = json_decode($m_res, true);
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
            border-top: 1px solid rgba(255,255,255,0.1); margin-top: 10px; padding-top: 10px;
        }
        .card-details i { color: #facc15; width: 18px; }
    }
</style>

<div class="weather-mobile-mini" onclick="window.openWeatherList()">
    <div style="font-size:10px; color:#facc15; font-weight:800; text-transform:uppercase;" id="mw-name">Betöltés...</div>
    <div id="s-date" style="font-size: 12px; color: #e6c50b; margin-bottom: 10px;"></div>
    <div style="display:flex; align-items:center; gap:8px;">
        <img src="" id="mw-img" style="height:35px;">
        <span style="font-size:28px; font-weight:800;" id="mw-temp">--°</span>
    </div>
    
</div>

<div id="weather-modal">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h2 style="color:#facc15; margin:0; font-size: 22px;">Helyszínek</h2>
        <div onclick="window.closeWeatherList()" style="font-size:40px; color:#ef4444; line-height:1; cursor:pointer;">&times;</div>
    </div>
    <div id="m-list-content"></div>
</div>

<script>
(function() {
    const rawData = <?= json_encode($m_data) ?>;
    const cityNames = <?= json_encode(array_keys($varosok)) ?>;

    // JAVÍTOTT ADATKINYERÉS: Az Open-Meteo tömböt ad vissza, ha több koordinátát kérünk
    function getCityWeather(i) {
        // Ha csak egy város lenne, nem lenne tömb, de 18 városnál tömb van!
        const d = Array.isArray(rawData) ? rawData[i] : rawData;
        if (!d || !d.current) return null;
        
        return {
            temp: d.current.temperature_2m,
            code: d.current.weather_code,
            hum: d.current.relative_humidity_2m,
            wind: d.current.wind_speed_10m,
            pres: d.current.pressure_msl,
            min: d.daily.temperature_2m_min[0],
            max: d.daily.temperature_2m_max[0]
        };
    }

    function getIcon(code, wind = 0) {
        const h = new Date().getHours();
        const p = "../img/weather_img/";
        if (wind > 25) return p + "szeles.jpg";
        if (h >= 18 || h < 6) return (code <= 1) ? p + "ejszakatiszta.jpg" : p + "felhosejszaka.jpg";
        if (code === 0) return p + "nap.jpg";
        if (code <= 2) return p + "naposfelhos.jpg";
        if (code === 3 || (code >= 45 && code <= 48)) return p + "felhos.jpg";
        if (code >= 51 && code <= 65) return p + "esos.jpg";
        if (code >= 71 && code <= 77) return p + "havas.jpg";
        if (code === 66 || code === 67 || (code >= 83 && code <= 86)) return p + "havaseso.jpg";
        return p + "zapor.jpg";
    }

    window.openWeatherList = () => {
        document.getElementById('weather-modal').style.display = 'block';
        history.pushState({view: 'weather_modal'}, '');
        const list = document.getElementById('m-list-content');
        const saved = localStorage.getItem('mSel') || 0;
        list.innerHTML = '';
        
        cityNames.forEach((name, i) => {
            const w = getCityWeather(i);
            if (!w) return;
            const card = document.createElement('div');
            card.className = `city-card ${i == saved ? 'active' : ''}`;
            card.onclick = () => { updateMini(i); window.closeWeatherList(); };
            card.innerHTML = `
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <b style="font-size:17px; ${i == saved ? 'color:#38bdf8' : ''}">${name}</b>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <img src="${getIcon(w.code, w.wind)}" style="height:38px;">
                        <span style="font-size:22px; font-weight:800;">${Math.round(w.temp)}°</span>
                    </div>
                </div>
                <div class="card-details">
                    <div><i class="fa fa-wind"></i> <b>${Math.round(w.wind)} km/h</b></div>
                    <div><i class="fa fa-tint"></i> <b>${w.hum}%</b></div>
                    <div><i class="fa fa-tachometer"></i> <b>${Math.round(w.pres)}</b></div>
                    <div style="color:#38bdf8;"><i class="fa fa-arrows-v"></i> <b>${Math.round(w.min)}° / ${Math.round(w.max)}°</b></div>
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
    const days = ["Vasárnap", "Hétfő", "Kedd", "Szerda", "Csütörtök", "Péntek", "Szombat"];
    const months = ["január", "február", "március", "április", "május", "június", "július", "augusztus", "szeptember", "október", "november", "december"];
    
    const dayName = days[now.getDay()];
    const monthName = months[now.getMonth()];
    const dateStr = `${now.getFullYear()}. ${monthName} ${now.getDate()}. ${dayName}`;
    const timeStr = now.toLocaleTimeString('hu-HU', { hour: '2-digit', minute: '2-digit' });

    // Kis widget frissítése
    const sDate = document.getElementById('s-date');
    if(sDate) sDate.innerText = `${dateStr} | ${timeStr}`;

    // Nagy modal frissítése
    const mDate = document.getElementById('m-date');
    if(mDate) mDate.innerText = `${dateStr} | ${timeStr}`;
}

    // Azonnali futtatás és percenkénti frissítés
    updateDateTime();
    setInterval(updateDateTime, 60000);

    function updateMini(idx) {
        const w = getCityWeather(idx);
        if (!w) return;
        document.getElementById('mw-name').innerText = cityNames[idx];
        document.getElementById('mw-temp').innerText = Math.round(w.temp) + '°';
        document.getElementById('mw-img').src = getIcon(w.code, w.wind);
        localStorage.setItem('mSel', idx);
    }

    // Indítás
    if (rawData) {
        updateMini(parseInt(localStorage.getItem('mSel') || 0));
    } else {
        document.getElementById('mw-name').innerText = "Hiba az adatokkal";
    }
})();
</script>