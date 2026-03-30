<?php
// =========================================================================
// 1. PHP ADATKEZELŐ ÉS CACHE BLOKK
// =========================================================================
// Visszaállítva az eredeti fájlnévre! (Kérlek töröld a szerverről a régit, hogy azonnal frissüljön!)
$cacheFile = 'data/weather_24.json';
$refreshInterval = 3600;

// A Nógrád vármegyei és környékbeli települések koordinátái
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

// Ha nincs még cache fájl, vagy lejárt a 3600 másodperc, újra lekérjük az adatokat
if (!file_exists($cacheFile) || (time() - filemtime($cacheFile) > $refreshInterval)) {
    // Koordináták összefűzése URL barát formátumba
    $lats = implode(',', array_column($varosok, 0));
    $lons = implode(',', array_column($varosok, 1));
    
    // API URL összeállítása (+ HOZZÁADVA: hourly=temperature_2m,weather_code a 2h/4h/6h widgethez)
    $url = "https://api.open-meteo.com/v1/forecast?latitude=$lats&longitude=$lons&current=temperature_2m,weather_code,relative_humidity_2m,wind_speed_10m,pressure_msl&hourly=temperature_2m,weather_code&daily=temperature_2m_max,temperature_2m_min,weather_code&timezone=auto";

    // cURL inicializálása a gyors és biztonságos adatlekéréshez
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Helyi (localhost) teszteléshez ajánlott
    $res = curl_exec($ch);
    curl_close($ch);

    // Ha sikeres a lekérés, mentjük a fájlba
    if ($res) {
        if (!is_dir('data')) mkdir('data', 0777, true);
        file_put_contents($cacheFile, $res);
    }
}

// Fájl beolvasása és JSON dekódolása PHP tömbbé
$data = json_decode(file_get_contents($cacheFile), true);
?>

<!-- =========================================================================
     2. KÜLSŐ ERŐFORRÁSOK (IKONOK)
     ========================================================================= -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- =========================================================================
     3. STÍLUSLAPOK (CSS) - KIZÁRÓLAG ASZTALI NÉZETRE
     ========================================================================= -->
<style>
    /* --- KÖZÖS ALAPBEÁLLÍTÁSOK --- */
    * { box-sizing: border-box; }

    /* --- ASZTALI OLDALSÁV WIDGET (SIDE CARD) --- */
    .side-weather-card {
        width: 100%; 
        max-width: 280px;
        background: rgba(15, 23, 42, 0.8); 
        border: 1px solid rgba(56, 189, 248, 0.4);
        border-radius: 15px; 
        padding: 15px; 
        font-family: 'Open Sans', sans-serif;
        transition: 0.3s; 
        color: white; 
        position: relative;
    }
    .side-weather-card:hover { 
        border-color: #38bdf8; 
        box-shadow: 0 0 15px rgba(56, 189, 248, 0.2);
    }

    /* ÚJ: Felső sor a Dátumnak és a Hétfő feliratnak */
    .s-top-row { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 5px; }
    .s-day-badge { color: #facc15; font-weight: 900; font-size: 14px; text-transform: capitalize; background: rgba(0,0,0,0.3); padding: 2px 8px; border-radius: 5px; }

    .side-header { 
        display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; 
    }
    .city-nav-btn { cursor: pointer; color: #38bdf8; padding: 5px; transition: 0.2s; }
    .city-nav-btn:hover { color: #facc15; transform: scale(1.1); }
    .side-main-info { display: flex; align-items: center; justify-content: space-between; cursor: pointer; }
    .side-img { height: 56px; width: 56px; object-fit: contain; }
    .side-stats { font-size: 11px; color: #cbd5e1; display: flex; gap: 10px; margin-top: 8px; }

    /* ÚJ: Órás előrejelzés sáv a widget alján */
    .s-hourly-strip {
        display: flex; justify-content: space-between; margin-top: 15px;
        border-top: 1px solid rgba(255,255,255,0.2); padding-top: 12px;
    }
    .s-hour-col { display: flex; flex-direction: column; align-items: center; width: 20%; }
    .s-hour-label { color: #facc15; font-weight: bold; font-size: 12px; margin-bottom: 5px; }
    .s-hour-box { background: rgba(255,255,255,0.1); border-radius: 5px; padding: 2px; width: 100%; text-align: center; }
    .s-hour-box img { width: 30px; height: 30px; object-fit: contain; }
    .s-hour-temp { margin-top: 5px; font-weight: bold; font-size: 12px; color: #ffffff; }

    /* --- NAGY KÖZPONTI MODAL (OVERLAY) --- */
    #weather-overlay {
        display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.9);
        backdrop-filter: blur(15px); z-index: 999999; font-family: 'Segoe UI', sans-serif;
    }
    .full-container {
        position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
        width: 95%; max-width: 850px; background: #0b1120; border-radius: 30px;
        border: 1px solid #38bdf8; overflow: hidden; display: flex; flex-direction: column; color: white;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    }

    /* Modal Bezáró Gomb */
    .close-icon { 
        position: absolute; top: 45px; right: 25px; font-size: 40px; 
        color: #ef4444; cursor: pointer; z-index: 100; transition: 0.2s;
    }
    .close-icon:hover { color: #facc15; transform: scale(1.1); }

    /* Felső Város Választó Sáv */
    .city-scroll {
        display: flex; overflow-x: auto; gap: 10px; padding: 15px;
        background: rgba(0,0,0,0.4); scrollbar-width: none; cursor: grab;
    }
    .city-scroll::-webkit-scrollbar { display: none; }
    .city-btn {
        padding: 8px 18px; background: #1e293b; border-radius: 20px;
        color: #94a3b8; white-space: nowrap; font-size: 14px; border: 1px solid transparent; transition: 0.2s;
    }
    .city-btn.active { background: #38bdf8; color: #000; font-weight: bold; cursor: pointer; }

    /* A Modal Belső Része (Fő adatok) */
    .m-body { display: flex; padding: 30px; gap: 20px; align-items: center; }
    .m-info-box { flex: 1; background: rgba(255, 255, 255, 0.05); border-radius: 20px; padding: 25px; position: relative; }
    .m-info-box h2 { color: #facc15; font-size: 36px; margin: 0 0 15px 0; text-shadow: 2px 2px 4px rgba(0,0,0,0.5); }
    .m-data-row { font-size: 18px; margin-bottom: 12px; display: flex; align-items: center; gap: 12px; color: #ffffff; }
    .m-visual { flex: 1; text-align: center; }
    .m-temp-big { font-size: 100px; font-weight: 900; line-height: 1; color: #ffffff; text-shadow: 0 0 20px rgba(56, 189, 248, 0.3); }
    .m-today-label { position: absolute; bottom: 25px; right: 25px; color: #facc15; font-weight: 900; font-size: 24px; text-transform: capitalize; }

    /* --- WEEKLY STRIP (6 NAPOS ALSÓ SÁV) --- */
    .weekly-strip { 
        display: flex; justify-content: center; gap: 20px;
        background: rgba(0,0,0,0.4); padding: 20px; border-top: 1px solid rgba(56, 189, 248, 0.2); 
    }
    .w-day-col { display: flex; flex-direction: column; align-items: center; flex: 1; max-width: 90px; }
    .w-day-name { color: #facc15; font-weight: 900; font-size: 18px; margin-bottom: 5px; }
    .w-day-box { display: flex; justify-content: center; align-items: center; margin-bottom: 8px; }
    
    /* 70x70px, enyhén kerekítve, keret nélkül */
    .w-day-box img { width: 70px; height: 70px; object-fit: cover; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.3); }
    
    .w-temp-row { font-weight: 900; font-size: 16px; display: flex; align-items: center; }
    .w-temp-min { color: #38bdf8; } 
    .w-temp-sep { color: #ffffff; margin: 0 4px; }
    .w-temp-max { color: #ef4444; } 

    /* Kisebb asztali/tablet nézet finomítás */
    @media (min-width: 767px) and (max-width: 900px) {
        .full-container { width: 85% !important; max-width: 650px !important; }
        .m-temp-big { font-size: 70px !important; }
        .m-info-box h2 { font-size: 28px !important; }
        .m-body { padding: 20px !important; gap: 15px !important; }
        .m-visual img { width: 140px !important; }
        .m-today-label { font-size: 18px; }
    }
</style>

<!-- =========================================================================
     4. HTML STRUKTÚRA - ASZTALI OLDALSÁV WIDGET
     ========================================================================= -->
<div class="side-weather-card">
    <!-- ÚJ FELSŐ SOR -->
    <div class="s-top-row">
        <div id="s-date" style="font-size: 10px; color: #cdd412;"></div>
        <div id="s-day" class="s-day-badge">--</div>
    </div>
    
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
        <span><i class="fa-solid fa-gauge"></i> <span id="s-pres">--</span> hPa</span>
    </div>

    <!-- ÚJ: AZ ÓRÁS BONTÁS SÁVJA -->
    <div class="s-hourly-strip" id="s-hourly-strip"></div>
</div>

<!-- =========================================================================
     5. HTML STRUKTÚRA - NAGY MODAL OVERLAY
     ========================================================================= -->
<div id="weather-overlay">
    <div class="full-container">
        <!-- Bezáró Gomb -->
        <div class="close-icon" onclick="hideWeather()">&times;</div>
        
        <!-- Város Kereső Csúszka -->
        <div class="city-scroll" id="city-list"></div>
        
        <!-- Fő információs blokk -->
        <div class="m-body">
            <div class="m-info-box">
                <h2 id="m-city-name">Város</h2>
                <div class="m-data-row"><i class="fa-solid fa-droplet" style="color:#38bdf8"></i> Páratartalom: <b id="m-hum">--</b>%</div>
                <div class="m-data-row"><i class="fa-solid fa-wind" style="color:#38bdf8"></i> Szélsebesség: <b id="m-wind">--</b> km/h</div>
                <div class="m-data-row"><i class="fa-solid fa-gauge" style="color:#38bdf8"></i> Légnyomás: <b id="m-pres">--</b> hPa</div>
                <div style="margin-top: 20px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 15px;">
                    <span style="color:#ef4444; font-weight:bold; font-size: 20px;">MAX: <span id="m-max">--</span>°</span>
                    <span style="margin-left:20px; color:#7dd3fc; font-weight:bold; font-size: 20px;">MIN: <span id="m-min">--</span>°</span>
                </div>
                
                <div id="m-date" style="margin-top: 15px; font-size: 16px; color: #b1c612; font-weight: 500;"></div>
                <div id="m-today-name" class="m-today-label">--</div>
            </div>
            
            <!-- Aktuális hőmérséklet és fő ikon -->
            <div class="m-visual">
                <img src="" id="m-big-img" style="width: 200px; filter: drop-shadow(0 0 30px #38bdf866); border-radius: 15px;">
                <div class="m-temp-big" id="m-big-temp">--°</div>
            </div>
        </div>
        
        <!-- A Heti Előrejelzés Sávja -->
        <div class="weekly-strip" id="weekly-strip"></div>
    </div>
</div>

<!-- =========================================================================
     6. JAVASCRIPT MOTOR ÉS LOGIKA
     ========================================================================= -->
<script>
(function() {
    const weatherData = <?= json_encode($data) ?>;
    const cityNames = <?= json_encode(array_keys($varosok)) ?>;
    const shortDays = ["V", "H", "K", "Sze", "Cs", "P", "Szo"];
    const fullDays = ["Vasárnap", "Hétfő", "Kedd", "Szerda", "Csütörtök", "Péntek", "Szombat"];

    // ==========================================
    // IKON VÁLASZTÓ LOGIKA
    // ==========================================
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

    // ==========================================
    // NAVIGÁCIÓS FÜGGVÉNYEK
    // ==========================================
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

    window.showWeather = (idx) => {
        document.getElementById('weather-overlay').style.display = 'block';
        updateModal(idx);
        history.pushState({view: 'weather_modal'}, '');
    };

    window.hideWeather = () => {
        document.getElementById('weather-overlay').style.display = 'none';
        if (history.state && history.state.view === 'weather_modal') {
            history.back();
        }
    };

    window.addEventListener('popstate', function(event) {
        document.getElementById('weather-overlay').style.display = 'none';
    });

    // ==========================================
    // A NAGY MODAL FRISSÍTÉSE
    // ==========================================
    function updateModal(idx) {
        window.currentIdx = idx;
        const d = Array.isArray(weatherData) ? weatherData[idx] : weatherData;
        if (!d || !d.current) return;
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
        document.getElementById('m-pres').innerText = Math.round(d.current.pressure_msl);
        document.getElementById('m-max').innerText = Math.round(d.daily.temperature_2m_max[0]);
        document.getElementById('m-min').innerText = Math.round(d.daily.temperature_2m_min[0]);
        document.getElementById('m-big-temp').innerText = Math.round(d.current.temperature_2m) + '°';
        document.getElementById('m-big-img').src = getIcon(d.current.weather_code, d.current.wind_speed_10m);
        document.getElementById('m-today-name').innerText = fullDays[today.getDay()];

        const strip = document.getElementById('weekly-strip');
        strip.innerHTML = '';
        
        for(let i = 1; i <= 6; i++) {
            let nextD = new Date(); 
            nextD.setDate(today.getDate() + i);
            
            let dName = shortDays[nextD.getDay()]; 
            let tMin = Math.round(d.daily.temperature_2m_min[i]);
            let tMax = Math.round(d.daily.temperature_2m_max[i]);
            let iconSrc = getIcon(d.daily.weather_code[i], 0, true);

            strip.innerHTML += `
                <div class="w-day-col">
                    <div class="w-day-name">${dName}</div>
                    <div class="w-day-box">
                        <img src="${iconSrc}" alt="idojaras">
                    </div>
                    <div class="w-temp-row">
                        <span class="w-temp-min">${tMin}</span>
                        <span class="w-temp-sep">/</span>
                        <span class="w-temp-max">${tMax}</span>
                    </div>
                </div>
            `;
        }
        localStorage.setItem('lastCity', idx);
    } 
    
    // ==========================================
    // DÁTUM ÉS IDŐ FÜGGVÉNY
    // ==========================================
    function updateDateTime() {
        const now = new Date();
        const days = ["Vasárnap", "Hétfő", "Kedd", "Szerda", "Csütörtök", "Péntek", "Szombat"];
        const months = ["január", "február", "március", "április", "május", "június", "július", "augusztus", "szeptember", "október", "november", "december"];
        
        const dayName = days[now.getDay()];
        const monthName = months[now.getMonth()];
        const dateStr = `${now.getFullYear()}. ${monthName} ${now.getDate()}.`;
        const timeStr = now.toLocaleTimeString('hu-HU', { hour: '2-digit', minute: '2-digit' });

        // Beállítja az új bal oldali mezőket is
        if(document.getElementById('s-date')) document.getElementById('s-date').innerText = `${dateStr} ${timeStr}`;
        if(document.getElementById('s-day')) document.getElementById('s-day').innerText = dayName;
        
        // Modal dátuma
        if(document.getElementById('m-date')) document.getElementById('m-date').innerText = `${dateStr} | ${timeStr}`;
    }

    // ==========================================
    // ASZTALI OLDALSÁV FRISSÍTÉSE
    // ==========================================
    function updateSideWidget(idx) {
        window.currentIdx = idx;
        const d = Array.isArray(weatherData) ? weatherData[idx] : weatherData;
        if (!d || !d.current) return;
        
        document.getElementById('s-name').innerText = cityNames[idx];
        document.getElementById('s-temp').innerText = Math.round(d.current.temperature_2m) + '°';
        document.getElementById('s-img').src = getIcon(d.current.weather_code, d.current.wind_speed_10m);
        document.getElementById('s-hum').innerText = d.current.relative_humidity_2m;
        document.getElementById('s-wind').innerText = Math.round(d.current.wind_speed_10m);
        document.getElementById('s-pres').innerText = Math.round(d.current.pressure_msl);
        
        // --- ÓRÁS ELŐREJELZÉS GENERÁLÁSA ---
        const currentHour = new Date().getHours();
        const hourlyStrip = document.getElementById('s-hourly-strip');
        
        // Csak akkor rajzolja ki, ha van hourly adat (ha a felhasználó törölte a json-t)
        if(hourlyStrip && d.hourly && d.hourly.temperature_2m) {
            hourlyStrip.innerHTML = '';
            
            [2, 4, 6, 8].forEach(offset => {
                let hIdx = currentHour + offset;
                
                if(d.hourly.temperature_2m[hIdx] !== undefined) {
                    let temp = Math.round(d.hourly.temperature_2m[hIdx]);
                    let code = d.hourly.weather_code[hIdx];
                    let iconSrc = getIcon(code, 0, false);

                    hourlyStrip.innerHTML += `
                        <div class="s-hour-col">
                            <div class="s-hour-label">${offset}h</div>
                            <div class="s-hour-box"><img src="${iconSrc}"></div>
                            <div class="s-hour-temp">${temp}°</div>
                        </div>
                    `;
                }
            });
        }
        
        localStorage.setItem('lastCity', idx);
    }

    // ==========================================
    // INICIALIZÁLÁS ÉS ESEMÉNYKEZELŐK
    // ==========================================
    updateDateTime();
    setInterval(updateDateTime, 60000);

    const lastIdx = parseInt(localStorage.getItem('lastCity') || 0);
    updateSideWidget(lastIdx);

    const slider = document.getElementById('city-list');
    let isDown = false, startX, scrollLeft;
    slider.onmousedown = (e) => { isDown = true; startX = e.pageX - slider.offsetLeft; scrollLeft = slider.scrollLeft; };
    slider.onmouseleave = () => isDown = false;
    slider.onmouseup = () => isDown = false;
    slider.onmousemove = (e) => { 
        if(!isDown) return; 
        e.preventDefault(); 
        const x = e.pageX - slider.offsetLeft; 
        slider.scrollLeft = scrollLeft - (x - startX) * 2; 
    };

})();
</script>