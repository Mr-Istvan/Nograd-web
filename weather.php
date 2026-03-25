<?php
$varosok = [
    "Salgótarján" => [48.10, 19.80], "Balassagyarmat" => [48.07, 19.29], "Pásztó" => [47.92, 19.66],
    "Szécsény" => [48.08, 19.51], "Bátonyterenye" => [47.98, 19.83], "Rétság" => [47.92, 19.14],
    "Hollókő" => [47.99, 19.58], "Bánk" => [47.92, 19.17], "Nógrádszakál" => [48.19, 19.53],
    "Tar" => [47.95, 19.74], "Somoskő" => [48.17, 19.85], "Rónabánya" => [48.12, 19.89],
    "Ipolytarnóc" => [48.24, 19.62], "Kozárd" => [47.92, 19.63], "Cserhát" => [47.91, 19.46],
    "Börzsöny" => [47.93, 18.92], "Mátra" => [47.87, 19.92]
];

$lats = implode(',', array_column($varosok, 0));
$lons = implode(',', array_column($varosok, 1));
$url = "https://api.open-meteo.com/v1/forecast?latitude=$lats&longitude=$lons&current=temperature_2m,weather_code,relative_humidity_2m,wind_speed_10m&daily=temperature_2m_max,temperature_2m_min,weather_code&timezone=auto";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$res = curl_exec($ch);
curl_close($ch);
$data = json_decode($res, true);
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* --- BAL OLDALI WIDGET --- */
    .side-weather-card {
        width: 100%; max-width: 280px;
        background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(56, 189, 248, 0.4);
        border-radius: 15px; padding: 15px; font-family: sans-serif;
        transition: 0.3s; color: white; position: relative;
    }
    .side-weather-card:hover { border-color: #38bdf8; }
    
    .side-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
    .city-nav-btn { cursor: pointer; color: #38bdf8; padding: 5px; transition: 0.2s; }
    .city-nav-btn:hover { color: #facc15; }
    
    .side-main-info { display: flex; align-items: center; justify-content: space-between; cursor: pointer; }
    .side-img { height: 56px; width: 56px; object-fit: contain; } /* +6px növelés */
    .side-stats { font-size: 11px; color: #cbd5e1; display: flex; gap: 10px; margin-top: 8px; }

    /* --- NAGY MODAL --- */
    #weather-overlay {
        display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.9);
        backdrop-filter: blur(15px); z-index: 999999; font-family: 'Segoe UI', sans-serif;
    }
    .full-container {
        position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
        width: 95%; max-width: 850px; background: #0b1120; border-radius: 30px;
        border: 1px solid #38bdf8; overflow: hidden; display: flex; flex-direction: column; color: white;
    }
    /* MÉRET CSÖKKENTÉSE 900px ÉS 767px KÖZÖTT */
@media (min-width: 767px) and (max-width: 900px) {
    .full-container {
        /* Az eredeti 850px-nek kb. a 70%-a (kb. 600px) */
        width: 70% !important; 
        max-width: 600px !important;
    }
    
    /* A belső tartalmakat is kicsit zsugorítjuk, hogy ne legyen zsúfolt */
    .m-temp-big {
        font-size: 70px !important; /* 100px-ről csökkentve */
    }
    
    .m-info-box h2 {
        font-size: 28px !important; /* 36px-ről csökkentve */
    }

    .m-body {
        padding: 20px 30px !important; /* Kisebb belső margó */
        gap: 15px !important;
    }
    
    .m-visual img {
        width: 140px !important; /* Az ikon is kisebb lesz */
    }
}
    .city-scroll {
        display: flex; overflow-x: auto; gap: 10px; padding: 15px;
        background: rgba(0,0,0,0.4); scrollbar-width: none; cursor: grab;
    }
    .city-scroll::-webkit-scrollbar { display: none; }
    .city-btn {
        padding: 8px 18px; background: #1e293b; border-radius: 20px;
        color: #94a3b8; white-space: nowrap; font-size: 14px; border: 1px solid transparent; transition: 0.2s;
    }
    .city-btn.active { background: #38bdf8; color: #000; font-weight: bold; }

    .m-body { display: flex; padding: 30px; gap: 20px; align-items: center; }
    .m-info-box { flex: 1; background: rgba(255, 255, 255, 0.05); border-radius: 20px; padding: 25px; }
    .m-info-box h2 { color: #facc15; font-size: 36px; margin: 0 0 15px 0; }
    .m-data-row { font-size: 18px; margin-bottom: 12px; display: flex; align-items: center; gap: 12px; color: #ffffff; }

    .m-visual { flex: 1; text-align: center; }
    .m-temp-big { font-size: 100px; font-weight: 900; line-height: 1; color: #ffffff; }
    
    .weekly-strip { display: flex; justify-content: space-around; background: rgba(0,0,0,0.4); padding: 20px; border-top: 1px solid rgba(56, 189, 248, 0.2); }
    .w-day-box { text-align: center; }
    .w-day-box span { color: #38bdf8; font-weight: bold; display: block; margin-bottom: 5px; }
    .w-temp-max { color: #ef4444; font-weight: bold; font-size: 14px; }
    .w-temp-min { color: #7dd3fc; font-weight: bold; font-size: 14px; }

    .close-icon { position: absolute; top: 35px; right: 25px; font-size: 40px; color: #ef4444; cursor: pointer; z-index: 100; }
</style>

<!-- BAL MENÜ WIDGET -->
<div class="side-weather-card">
    <div id="s-date" style="font-size: 10px; color: #cdd412; margin-bottom: 5px;"></div>
    <div class="side-header">
        
        <i class="fa-solid fa-chevron-left city-nav-btn" onclick="prevCity()"></i>
        <div style="font-size: 13px; color: #38bdf8; font-weight: 700;" id="s-name">--</div>
        <i class="fa-solid fa-chevron-right city-nav-btn" onclick="nextCity()"></i>
    </div>
    <div class="side-main-info" onclick="showWeather(window.currentIdx || 0)">
        <span style="font-size: 32px; font-weight: 800;" id="s-temp">--°</span>
        <img src="" id="s-img" class="side-img">
    </div>
    <div class="side-stats">
        <span><i class="fa-solid fa-droplet"></i> <span id="s-hum">--</span>%</span>
        <span><i class="fa-solid fa-wind"></i> <span id="s-wind">--</span> km/h</span>
    </div>
</div>

<!-- NAGY MODAL -->
<div id="weather-overlay">
    <div class="full-container">
        <div class="close-icon" onclick="hideWeather()">&times;</div>
        <div class="city-scroll" id="city-list"></div>
        <div class="m-body">
            <div class="m-info-box">
                <h2 id="m-city-name">Város</h2>
                <div class="m-data-row"><i class="fa-solid fa-droplet" style="color:#38bdf8"></i> Páratartalom: <b id="m-hum">--</b>%</div>
                <div class="m-data-row"><i class="fa-solid fa-wind" style="color:#38bdf8"></i> Szélsebesség: <b id="m-wind">--</b> km/h</div>
                <div style="margin-top: 20px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 15px;">
                    <span style="color:#ef4444; font-weight:bold; font-size: 20px;">MAX: <span id="m-max">--</span>°</span>
                    <span style="margin-left:20px; color:#7dd3fc; font-weight:bold; font-size: 20px;">MIN: <span id="m-min">--</span>°</span>
                </div>
                <div id="m-date" style="margin-top: 15px; font-size: 16px; color: #b1c612; font-weight: 500;"></div>
            </div>
            <div class="m-visual">
                <img src="" id="m-big-img" style="width: 200px; filter: drop-shadow(0 0 30px #38bdf866);">
                <div class="m-temp-big" id="m-big-temp">--°</div>
            </div>
        </div>
        <div class="weekly-strip" id="weekly-strip"></div>
    </div>
</div>
<script>
(function() {
    const weatherData = <?= json_encode($data) ?>;
    const cityNames = <?= json_encode(array_keys($varosok)) ?>;
    const dayNames = ["Vas", "Hét", "Ked", "Sze", "Csü", "Pén", "Szo"];

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

    window.nextCity = () => {
        let idx = (window.currentIdx + 1) % cityNames.length;
        updateSideWidget(idx);
        if(document.getElementById('weather-overlay').style.display === 'block') updateModal(idx);
    };

    window.prevCity = () => {
        let idx = (window.currentIdx - 1 + cityNames.length) % cityNames.length;
        updateSideWidget(idx);
        if(document.getElementById('weather-overlay').style.display === 'block') updateModal(idx);
    };

    // --- JAVÍTOTT MEGNYITÁS ---
    window.showWeather = (idx) => {
        document.getElementById('weather-overlay').style.display = 'block';
        updateModal(idx);
        // Beteszünk egy új állapotot az előzményekbe
        history.pushState({view: 'weather_modal'}, '');
    };

    // --- JAVÍTOTT BEZÁRÁS ---
    window.hideWeather = () => {
        document.getElementById('weather-overlay').style.display = 'none';
        // Ha mi zártuk be (X-szel), és még ott van a 'weather_modal' az előzményben, töröljük
        if (history.state && history.state.view === 'weather_modal') {
            history.back();
        }
    };

    // --- VISSZA GOMB FIGYELÉSE ---
    window.addEventListener('popstate', function(event) {
        // Ha a vissza gombot nyomják és elvész a 'weather_modal' állapot, csukjuk be a modalt
        document.getElementById('weather-overlay').style.display = 'none';
    });

    function updateModal(idx) {
        window.currentIdx = idx;
        const d = weatherData[idx];
        const today = new Date();

        const list = document.getElementById('city-list');
        list.innerHTML = '';
        cityNames.forEach((name, i) => {
            const btn = document.createElement('div');
            btn.className = `city-btn ${i === idx ? 'active' : ''}`;
            btn.innerText = name;
            btn.onclick = () => { updateModal(i); updateSideWidget(i); };
            list.appendChild(btn);
        });

        document.getElementById('m-city-name').innerText = cityNames[idx];
        document.getElementById('m-hum').innerText = d.current.relative_humidity_2m;
        document.getElementById('m-wind').innerText = Math.round(d.current.wind_speed_10m);
        document.getElementById('m-max').innerText = Math.round(d.daily.temperature_2m_max[0]);
        document.getElementById('m-min').innerText = Math.round(d.daily.temperature_2m_min[0]);
        document.getElementById('m-big-temp').innerText = Math.round(d.current.temperature_2m) + '°';
        document.getElementById('m-big-img').src = getIcon(d.current.weather_code, d.current.wind_speed_10m);

        const strip = document.getElementById('weekly-strip');
        strip.innerHTML = '';
        for(let i=1; i<=6; i++) {
            let nextD = new Date(); nextD.setDate(today.getDate() + i);
            strip.innerHTML += `
                <div class="w-day-box">
                    <span>${dayNames[nextD.getDay()]}</span>
                    <img src="${getIcon(d.daily.weather_code[i])}" style="height:35px; margin:5px auto; display:block;">
                    <div class="w-temp-max">${Math.round(d.daily.temperature_2m_max[i])}°</div>
                    <div class="w-temp-min">${Math.round(d.daily.temperature_2m_min[i])}°</div>
                </div>`;
        }
        localStorage.setItem('lastCity', idx);
    } 
    
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

    function updateSideWidget(idx) {
        window.currentIdx = idx;
        const d = weatherData[idx];
        document.getElementById('s-name').innerText = cityNames[idx];
        document.getElementById('s-temp').innerText = Math.round(d.current.temperature_2m) + '°';
        document.getElementById('s-img').src = getIcon(d.current.weather_code, d.current.wind_speed_10m);
        document.getElementById('s-hum').innerText = d.current.relative_humidity_2m;
        document.getElementById('s-wind').innerText = Math.round(d.current.wind_speed_10m);
        localStorage.setItem('lastCity', idx);
    }

    const lastIdx = parseInt(localStorage.getItem('lastCity') || 0);
    updateSideWidget(lastIdx);

    const slider = document.getElementById('city-list');
    let isDown = false, startX, scrollLeft;
    slider.onmousedown = (e) => { isDown = true; startX = e.pageX - slider.offsetLeft; scrollLeft = slider.scrollLeft; };
    slider.onmouseleave = () => isDown = false;
    slider.onmouseup = () => isDown = false;
    slider.onmousemove = (e) => { if(!isDown) return; e.preventDefault(); const x = e.pageX - slider.offsetLeft; slider.scrollLeft = scrollLeft - (x - startX) * 2; };
})();
</script>