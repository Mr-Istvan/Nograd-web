<?php
require_once __DIR__ . '/init.php';
?>
<!DOCTYPE html>
<html lang="hu">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>TETRIS MOBILE ULTRA v3.9 (Nógrád Blog Edition & Avatars)</title>
<style>
    /* A CSS MEGEGYEZIK A HTML VERZIÓVAL */
    body { margin: 0; padding: 0; background: radial-gradient(circle at top, #1e2036 0%, #0a0a10 80%); display: flex; justify-content: center; align-items: center; color: white; font-family: 'Segoe UI', Roboto, sans-serif; overflow: hidden; height: 100vh; height: 100dvh; width: 100vw; }
    
    #container { display: flex; flex-direction: column; width: 100%; height: 100%; height: 100dvh; max-width: 600px; border: 2px solid #46499a; box-shadow: 0 0 30px rgba(70, 73, 154, 0.3); box-sizing: border-box; background: #050508; position: relative; padding-bottom: 5px; }
    
    #gameScreen { display: flex; flex-direction: column; height: 100%; width: 100%; }

    /* FEJLÉC ÉS STATISZTIKÁK STÍLUSA */
    .header { display: flex; justify-content: space-between; align-items: center; padding: 8px 10px; background: linear-gradient(to bottom, rgba(70, 73, 154, 0.2), transparent); border-bottom: 1px solid #2a2c5c; flex-shrink: 0; }
    
    .side-box { display: flex; flex-direction: column; align-items: center; }
    .side-canvas { border: 2px solid #2a2c5c; background: #0a0a10; border-radius: 6px; margin-top: 4px; cursor: pointer; box-shadow: inset 0 0 15px rgba(0,0,0,0.8); }
    #holdCanvas { border-color: rgba(0, 210, 255, 0.5); } 
    #nextCanvas { border-color: rgba(255, 215, 0, 0.4); }
    
    .stats-group { text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; background: rgba(0,0,0,0.6); padding: 5px 15px; border-radius: 8px; border: 1px solid #2a2c5c; box-shadow: 0 4px 10px rgba(0,0,0,0.5);}
    .stat-label { font-size: 10px; color: #aaa; text-transform: uppercase; font-weight: bold; letter-spacing: 1px;}
    .stat-value { font-size: 18px; color: gold; font-weight: 900; text-shadow: 0 0 10px rgba(255,215,0,0.4); }
    
    .controls { display: flex; gap: 6px; padding: 8px; justify-content: center; align-items: center; flex-shrink: 0; flex-wrap: wrap; background: #0a0a10;}
    button.control-btn { padding: 8px 12px; font-weight: bold; border-radius: 4px; cursor: pointer; border: none; color: black; text-transform: uppercase; font-size: 11px; transition: 0.2s; box-shadow: 0 3px 0 rgba(0,0,0,0.4); }
    button.control-btn:active { transform: translateY(2px); box-shadow: none; }
    
    #nogradBtn { background: #46499a; color: white; border: 1px solid #5a5db8;}
    #startBtn { background: #00d2ff; }
    #pauseBtn { background: #d291bc; }
    #exitBtn { background: #ff4b4b; color: white;}
    
    .info-btn { width: 34px; height: 34px; border-radius: 50%; border: 2px solid rgba(255,255,255,0.5); background: #001f3f; color: white; font-size: 18px; font-weight: bold; display: flex; justify-content: center; align-items: center; cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.5); user-select: none; transition: 0.2s;}
    .info-btn:active { transform: scale(0.9); }

    /* NEON JÁTÉKTÉR RÁCCSAL */
    #gameArea { flex-grow: 1; display: flex; justify-content: center; align-items: center; overflow: hidden; position: relative; padding: 5px; }
    canvas#game { border: 2px solid #2a2c5c; background: #000; box-shadow: 0 0 20px rgba(70, 73, 154, 0.2), inset 0 0 30px rgba(0,0,0,0.8); display: block; border-radius: 4px;}
    
    /* ANIMÁLT COMBO ÉS PERFECT CLEAR FELIRAT */
    #combo { position: absolute; top: 35%; left: 50%; transform: translate(-50%, -50%); font-size: 24px; color: gold; font-weight: 900; text-shadow: 0 0 15px red, 0 0 5px black; pointer-events: none; text-align: center; width: 100%; line-height: 1.3; z-index: 10; opacity: 0; }
    @keyframes popIn { 
        0% { transform: translate(-50%, -20%) scale(0.5); opacity: 0; } 
        50% { transform: translate(-50%, -50%) scale(1.1); opacity: 1; } 
        100% { transform: translate(-50%, -50%) scale(1); opacity: 1; } 
    }
    
    /* GAME OVER OVERLAY */
    #gameOverOverlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); z-index: 100; display: none; flex-direction: column; justify-content: center; align-items: center; gap: 15px; cursor: pointer; backdrop-filter: blur(3px); }
    
    /* Információs Panel */
    #infoOverlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 101; display: none; flex-direction: column; justify-content: center; align-items: center; padding: 20px; box-sizing: border-box; color: white; overflow-y: auto; cursor: pointer; backdrop-filter: blur(5px);}
    .info-content { background: #0d0d14; border: 2px solid #46499a; padding: 25px; border-radius: 12px; width: 100%; max-width: 450px; font-size: 13px; line-height: 1.5; text-align: left; box-shadow: 0 10px 30px rgba(70,73,154,0.3);}
    .info-content h2 { color: gold; text-align: center; margin-top: 0; text-transform: uppercase; letter-spacing: 2px; border-bottom: 1px solid #2a2c5c; padding-bottom: 10px;}
    .info-content ul { padding-left: 20px; margin: 8px 0 15px 0;}
    .close-hint { color: #888; text-align: center; font-size: 12px; margin-top: 20px; animation: blink 2s infinite;}

    .block-popup { 
        width: 200px; height: 80px; display: flex; justify-content: center; align-items: center; text-align: center;
        border-top: 4px solid rgba(255,255,255,0.3); border-left: 4px solid rgba(255,255,255,0.3);
        border-bottom: 4px solid rgba(0,0,0,0.6); border-right: 4px solid rgba(0,0,0,0.6);
        box-shadow: 0 15px 25px rgba(0,0,0,0.9); box-sizing: border-box; border-radius: 8px;
    }
    
    #popupGameOver { background: linear-gradient(135deg, #cc0000, #660000); color: white; font-size: 22px; font-weight: 900; text-shadow: 2px 2px 5px #000; }
    #popupNewRecord { background: linear-gradient(135deg, #ffd700, #b8860b); color: black; font-size: 18px; font-weight: 900; animation: pulse 1s infinite; display: none; border-top: 4px solid rgba(255,255,255,0.8); border-left: 4px solid rgba(255,255,255,0.8);}
    
    .continue-hint { color: #ccc; font-size: 13px; text-align: center; margin-top: 15px; font-weight: bold;}
    .continue-btn { margin-top: 8px; background: #222; color: white; padding: 8px 25px; font-weight: bold; border: 1px solid #555; border-radius: 4px; text-transform: uppercase; animation: blink 1.5s infinite; cursor: pointer; box-shadow: 0 4px 10px rgba(0,0,0,0.5);}
    @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }

    #mobileControls { display: flex; justify-content: center; align-items: center; padding: 15px 10px; width: 100%; box-sizing: border-box; flex-shrink: 0; background: #0a0a10;}
    #pushBtn { width: 80px; height: 80px; border-radius: 50%; background: radial-gradient(circle, #ff4b4b 0%, #aa0000 100%); color: white; font-weight: 900; font-size: 18px; letter-spacing: 1px; border: 4px solid #440000; box-shadow: 0 6px 20px rgba(255,0,0,0.5), inset 0 -4px 10px rgba(0,0,0,0.5); cursor: pointer; outline: none; margin: 0 auto; display: block; position: relative; transition: 0.1s; }
    #pushBtn:active { transform: scale(0.92) translateY(4px); box-shadow: 0 2px 10px rgba(255,0,0,0.5), inset 0 -2px 5px rgba(0,0,0,0.5); }

    /* LEADERBOARD DIZÁJN PROFILKÉPEKKEL */
    #leaderboardView { display: none; flex-direction: column; height: 100%; width: 100%; padding: 20px; box-sizing: border-box; text-align: center; background: #0d0d14; }
    .lb-header { flex-shrink: 0; margin-bottom: 15px; background: rgba(0,0,0,0.5); padding: 10px; border-radius: 8px; border: 1px solid #2a2c5c;}
    .lb-controls { display: flex; gap: 8px; justify-content: center; flex-shrink: 0; margin-bottom: 15px; }
    
    .lb-controls input { padding: 10px; font-size: 15px; border-radius: 6px; border: 1px solid #444; width: 55%; max-width: 180px; text-align: center; font-weight: bold; background: #222; color: #fff;}
    .lb-controls input:read-only { background-color: #1a1a24; color: #aaa; border: 1px solid #2a2c5c; outline: none; }
    
    #leaderboardList { background: rgba(20,20,30,0.9); color: white; border-radius: 8px; width: 100%; flex-grow: 1; overflow-y: auto; font-family: 'Segoe UI', Tahoma, sans-serif; border: 2px solid #46499a; margin-bottom: 15px; box-shadow: 0 0 20px rgba(70,73,154,0.2);}
    table { width: 100%; border-collapse: collapse; font-size: 13px; }
    th, td { padding: 8px 5px; text-align: left; border-bottom: 1px solid #2a2c5c; vertical-align: middle;}
    th { background: #1a1a24; position: sticky; top: 0; z-index: 2; font-size: 12px; color: #00d2ff; text-transform: uppercase; letter-spacing: 1px;}
    
    .avatar-img { width: 26px; height: 26px; border-radius: 50%; border: 1px solid gold; vertical-align: middle; margin-right: 8px; object-fit: cover; }
    .avatar-guest { display: inline-block; width: 26px; height: 26px; border-radius: 50%; background: #444; color: white; line-height: 26px; text-align: center; vertical-align: middle; border: 1px solid #888; margin-right: 8px; font-size: 12px; }

    #backBtn { background: #333; color: white; width: 100%; padding: 12px; flex-shrink: 0; font-size: 14px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; transition: 0.2s;}
    #backBtn:active { background: #555; }
    #nogradBackBtn { background: #46499a; color: white; width: 100%; padding: 12px; flex-shrink: 0; font-size: 14px; border: none; border-radius: 6px; margin-top: 8px; font-weight: bold; cursor: pointer; border: 1px solid #5a5db8; transition: 0.2s;}
    #nogradBackBtn:active { background: #5a5db8; }
    
    #newRecordLabel { display: none; color: #00ffcc; font-size: 26px; font-weight: 900; text-shadow: 0 0 20px #00ffcc; margin: 10px 0; animation: pulse 1s infinite; }
    @keyframes pulse { 0% { transform: scale(1); } 50% { transform: scale(1.05); } 100% { transform: scale(1); } }
</style>
</head>
<body>

<div id="container">
    <div id="gameScreen">
        <div class="header">
            <div class="side-box">
                <div class="stat-label" style="color: #00d2ff;">Hold (Katt)</div>
                <canvas id="holdCanvas" class="side-canvas" onclick="holdPiece()"></canvas>
            </div>
            
            <div class="stats-group">
                <div><span class="stat-label">Pont:</span> <span id="score" class="stat-value">0</span></div>
                <div style="display:flex; gap: 15px; margin-top: 5px; border-top: 1px solid #2a2c5c; padding-top: 5px;">
                    <div><span class="stat-label">Sor:</span> <br><span id="lines" class="stat-value" style="color:white; font-size: 14px;">0</span></div>
                    <div><span class="stat-label">Szint:</span> <br><span id="level" class="stat-value" style="color:#ff4b4b; font-size: 14px;">0</span></div>
                    <div><span class="stat-label">Idő:</span> <br><span id="timer" class="stat-value" style="color:#aaa; font-size: 14px;">00:00</span></div>
                </div>
            </div>

            <div class="side-box">
                <div class="stat-label" style="color: gold;">Köv</div>
                <canvas id="nextCanvas" class="side-canvas"></canvas>
            </div>
        </div>

        <div class="controls">
            <button onclick="window.location.href='Blog.php'" id="nogradBtn" class="control-btn">Nógrád</button>
            <button onclick="startGame()" id="startBtn" class="control-btn">Start</button>
            <button onclick="pauseGame()" id="pauseBtn" class="control-btn">Pause</button>
            <button onclick="exitGameBtn()" id="exitBtn" class="control-btn">Exit</button>
            <div class="info-btn" onclick="showInfo()">i</div>
        </div>

        <div id="gameArea">
            <canvas id="game"></canvas>
            <div id="combo"></div>
            
            <div id="gameOverOverlay" onclick="proceedToLeaderboard()">
                <div id="popupNewRecord" class="block-popup">
                    👑 NEW RECORD! 👑
                </div>
                <div id="popupGameOver" class="block-popup">
                    GAME OVER!
                </div>
                <div class="continue-hint">
                    Tap / Space / Enter<br>
                    <button class="continue-btn">Tovább</button>
                </div>
            </div>

            <div id="infoOverlay" onclick="hideInfo()">
                <div class="info-content">
                    <h2>🔥 ÚTMUTATÓ & EXTRÁK 🔥</h2>
                    <strong style="color: #00d2ff;">📱 Irányítás:</strong><br>
                    <ul>
                        <li><strong>↔ Húzás:</strong> Mozgatás balra/jobbra</li>
                        <li><strong>⬇ Húzás le:</strong> Gyorsítás (Soft Drop)</li>
                        <li><strong>👆 Pöttyintés:</strong> Forgatás</li>
                        <li><strong>🔴 PUSH:</strong> Instant leesés (Hard Drop)</li>
                        <li><strong>🔃 Hold box:</strong> Kocka tartalékolása/cseréje</li>
                    </ul>
                    <strong style="color: gold;">🎁 Pontrendszer (Nincs limit!):</strong><br>
                    <ul>
                        <li><span style="color:white;">1-2-3 Sor:</span> Alap pontok (+1, +2, +4)</li>
                        <li><span style="color:#ff4b4b; font-weight:bold;">TETRIS (4 sor):</span> +6 pont</li>
                        <li><span style="color:#ffcc00; font-weight:bold;">COMBO:</span> Sorozatos törlésért egyre növekvő bónusz (akár +10 pont/lépés).</li>
                        <li><span style="color:lime; font-weight:bold;">PERFECT CLEAR:</span> +25 pont, ha teljesen kiürül a pálya!</li>
                    </ul>
                    <strong style="color: #d291bc;">🏆 Nógrád Blog Bajnokság:</strong><br>
                    <ul>
                        <li>Ha <strong>be vagy jelentkezve</strong> a blogon, a saját profilképeddel és neveddel kerülsz a toplistára!</li>
                        <li>Ha nem, egy egyedi <strong>Guest#xxxxxx</strong> vendég nevet kapsz.</li>
                        <li>Heti szinten kiemeljük a <strong>TOP 5</strong> legjobb játékost! Dönts meg minden rekordot!</li>
                    </ul>
                    <div class="close-hint">Érintsd meg a bezáráshoz...</div>
                </div>
            </div>
        </div>
        
        <div id="mobileControls">
            <button id="pushBtn" onclick="hardDrop()">PUSH</button>
        </div>
    </div>

    <div id="leaderboardView">
        <div class="lb-header">
            <h2 style="color: gold; margin: 0; text-shadow: 0 0 10px rgba(255,215,0,0.5);">🏆 REKORDOK (TOP 100) 🏆</h2>
            <div id="newRecordLabel">👑 NEW RECORD! 👑</div>
            <div style="font-size: 15px; color: #ddd; margin-top: 8px; background: rgba(0,0,0,0.5); padding: 5px; border-radius: 5px;">
                Pont: <span id="finalScore" style="color:gold; font-weight:900; font-size:20px;">0</span> | 
                Rang: <span id="finalRank" style="color:#00d2ff; font-weight:bold;">-</span>
            </div>
        </div>
        
        <div class="lb-controls">
            <input id="name" placeholder="Neved..." maxlength="20">
            <button id="saveBtn" onclick="saveScore()" style="background: gold; border:none; padding: 0 15px; border-radius: 6px; font-weight: bold; cursor: pointer; color: black; box-shadow: 0 3px 0 #b8860b;">Mentés</button>
        </div>
        
        <div id="leaderboardList"></div>
        
        <button onclick="backToGame()" id="backBtn">VISSZA A FŐMENÜBE</button>
        <button onclick="window.location.href='http://nogradcsodak.szakdoga.net/blog.php'" id="nogradBackBtn">NÓGRÁD BLOG OLDALRA</button>
    </div>
</div>

<script>
// --- PHP -> JAVASCRIPT ADATÁTADÁS ÉLESBEN ---
// Lekérdezzük a PHP Session adatait, amiket a szerver beilleszt a kódba.
let blogUser = "<?php echo isset($_SESSION['user_name']) ? addslashes($_SESSION['user_name']) : ''; ?>";
// Ha a felhasználói adatok (pl. avatar) egy $userData tömbben vannak, így érhető el:
let blogUserAvatar = "<?php echo isset($userData['uavatar']) && !empty($userData['uavatar']) ? 'img/avatars/' . addslashes($userData['uavatar']) : ''; ?>";

const canvas = document.getElementById('game');
const ctx = canvas.getContext('2d');
const nextCanvas = document.getElementById('nextCanvas');
const nextCtx = nextCanvas.getContext('2d');
const holdCanvas = document.getElementById('holdCanvas');
const holdCtx = holdCanvas.getContext('2d');

const COLS = 10, ROWS = 20;
let SIZE; 
let board = Array.from({length: ROWS}, () => Array(COLS).fill(0));

let running = false, paused = false;
let score = 0, combo = 0, lastClear = false;
let linesTotal = 0, currentLevel = 0;
let secondsElapsed = 0;
let dropInterval, timerInterval;
let topScore = 0;
let hasSaved = false;
let isGameOverScreen = false; 
let isInfoOpen = false; 

let holdPieceType = null; 
let canHold = true;

const SHAPES = [
 [[1,1,1,1]], // 0: I
 [[1,1],[1,1]], // 1: O
 [[0,1,0],[1,1,1]], // 2: T
 [[1,1,0],[0,1,1]], // 3: Z (Piros)
 [[0,1,1],[1,1,0]], // 4: S (Zöld)
 [[1,0,0],[1,1,1]], // 5: J (Sötétkék)
 [[0,0,1],[1,1,1]]  // 6: L (Narancs)
];

const COLORS = [
    '#00FFFF', // 0: I (Világoskék)
    '#FFFF00', // 1: O (Sárga)
    '#800080', // 2: T (Lila)
    '#FF0000', // 3: Z (Piros)
    '#00FF00', // 4: S (Zöld)
    '#0000FF', // 5: J (Sötétkék)
    '#FFA500'  // 6: L (Narancs)
];

const LEVEL_SPEEDS = [1000, 900, 800, 700, 600, 500, 400, 300, 200, 100, 15]; 
const LEVEL_THRESHOLDS = [0, 15, 30, 45, 60, 75, 90, 105, 120, 135, 150];

let piece, nextPieceType;
let pieceBag = [];

fetch('load.php').then(r=>r.json()).then(data=>{
    if(data && data.length > 0) topScore = data[0].record;
}).catch(e=>{});

function getRank(lvl) {
    if(lvl === 0) return "Tanuló";
    if(lvl <= 2) return "Kezdő";
    if(lvl <= 4) return "Haladó";
    if(lvl <= 6) return "Profi";
    if(lvl <= 8) return "Mester";
    if(lvl === 9) return "Nagymester";
    return "Tetris Isten";
}

function resizeGame() {
    const gameArea = document.getElementById('gameArea');
    let availW = gameArea.clientWidth - 10;
    let availH = gameArea.clientHeight - 10;
    
    let sizeW = Math.floor(availW / COLS);
    let sizeH = Math.floor(availH / ROWS);
    
    SIZE = Math.min(sizeW, sizeH);
    if (SIZE < 15) SIZE = 15;

    canvas.width = COLS * SIZE;
    canvas.height = ROWS * SIZE;

    let nSize = Math.floor(SIZE * 0.7); 
    nextCanvas.width = holdCanvas.width = 4 * nSize;
    nextCanvas.height = holdCanvas.height = 4 * nSize;

    draw(); 
    if(nextPieceType !== undefined) drawNext();
    drawHold();
}

window.addEventListener('resize', resizeGame);

window.addEventListener('load', () => { 
    resizeGame(); 
    backToGame(); 
    
    let nameInput = document.getElementById('name');
    
    if (blogUser && blogUser.trim() !== "") {
        // 1. Ha be van jelentkezve PHP-ből
        nameInput.value = blogUser.trim();
        nameInput.readOnly = true; 
    } else {
        // 2. Ha nincs bejelentkezve, ellenőrizzük a memóriát
        let savedGuestName = localStorage.getItem("tetrisGuestName");
        
        if (savedGuestName) {
            nameInput.value = savedGuestName;
        } else {
            // Generátor (t=max 2, e,r,i,s=max 1, többi szám)
            let result = [];
            let availableLetters = ["e", "r", "i", "s"];
            let tCount = 0;
            let usedLetters = [];

            for (let i = 0; i < 6; i++) {
                if (Math.random() < 0.4) {
                    let pick = Math.random();
                    if (pick < 0.4 && tCount < 2) {
                        result.push("t"); tCount++;
                    } else {
                        let remaining = availableLetters.filter(l => !usedLetters.includes(l));
                        if (remaining.length > 0) {
                            let letter = remaining[Math.floor(Math.random() * remaining.length)];
                            result.push(letter); usedLetters.push(letter);
                        } else {
                            result.push(Math.floor(Math.random() * 10).toString());
                        }
                    }
                } else {
                    result.push(Math.floor(Math.random() * 10).toString());
                }
            }

            if (tCount === 0 && usedLetters.length === 0) {
                result[Math.floor(Math.random() * 6)] = "t";
            }

            let newGuestName = "Guest#" + result.join("");
            localStorage.setItem("tetrisGuestName", newGuestName);
            nameInput.value = newGuestName;
        }
        nameInput.readOnly = true;
    }
});

function updateTimerDisplay() {
    let m = Math.floor(secondsElapsed / 60).toString().padStart(2, '0');
    let s = (secondsElapsed % 60).toString().padStart(2, '0');
    document.getElementById('timer').innerText = `${m}:${s}`;
}

function getPieceData(typeIndex) {
    let shapeCopy = SHAPES[typeIndex].map(row => [...row]);
    return { shape: shapeCopy, type: typeIndex, color: COLORS[typeIndex], x: 3, y: 0 };
}

function generateBag() {
    let bag = [0, 1, 2, 3, 4, 5, 6];
    for (let i = bag.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [bag[i], bag[j]] = [bag[j], bag[i]]; 
    }
    return bag;
}

function newPiece() {
    if (pieceBag.length === 0) pieceBag = generateBag();
    
    if (nextPieceType === undefined) {
        nextPieceType = pieceBag.pop();
        if (pieceBag.length === 0) pieceBag = generateBag();
    }
    
    piece = getPieceData(nextPieceType);
    piece.x = Math.floor(COLS/2) - Math.floor(piece.shape[0].length/2);
    piece.y = 0;
    
    nextPieceType = pieceBag.pop();
    
    drawNext();
    canHold = true; 
}

function holdPiece() {
    if (!running || paused || !canHold || isGameOverScreen || isInfoOpen) return;
    
    if (holdPieceType === null) {
        holdPieceType = piece.type; 
        newPiece(); 
    } else {
        let temp = piece.type;
        piece = getPieceData(holdPieceType); 
        piece.x = Math.floor(COLS/2) - Math.floor(piece.shape[0].length/2);
        piece.y = 0;
        holdPieceType = temp; 
    }
    
    canHold = false; 
    drawHold();
    draw();
}

function drawBlock(context, x, y, size, color, alpha = 1) {
    context.globalAlpha = alpha;
    context.fillStyle = color;
    context.fillRect(x, y, size, size);
    
    context.fillStyle = "rgba(255,255,255,0.3)";
    context.beginPath(); context.moveTo(x, y); context.lineTo(x+size, y); context.lineTo(x+size-4, y+4); context.lineTo(x+4, y+4); context.fill();
    context.beginPath(); context.moveTo(x, y); context.lineTo(x, y+size); context.lineTo(x+4, y+size-4); context.lineTo(x+4, y+4); context.fill();
    
    context.fillStyle = "rgba(0,0,0,0.5)";
    context.beginPath(); context.moveTo(x+size, y); context.lineTo(x+size, y+size); context.lineTo(x+size-4, y+size-4); context.lineTo(x+size-4, y+4); context.fill();
    context.beginPath(); context.moveTo(x, y+size); context.lineTo(x+size, y+size); context.lineTo(x+size-4, y+size-4); context.lineTo(x+4, y+size-4); context.fill();
    
    context.strokeStyle = "rgba(0,0,0,0.6)";
    context.lineWidth = 1;
    context.strokeRect(x, y, size, size);
    
    context.globalAlpha = 1; 
}

function drawNext() {
    nextCtx.clearRect(0, 0, nextCanvas.width, nextCanvas.height);
    if(nextPieceType === undefined) return;
    let pShape = SHAPES[nextPieceType];
    let pColor = COLORS[nextPieceType];
    let nSize = Math.floor(SIZE * 0.7);
    let offsetX = (nextCanvas.width - pShape[0].length * nSize) / 2;
    let offsetY = (nextCanvas.height - pShape.length * nSize) / 2;
    pShape.forEach((row, y) => {
        row.forEach((val, x) => {
            if (val) drawBlock(nextCtx, offsetX + x * nSize, offsetY + y * nSize, nSize, pColor);
        });
    });
}

function drawHold() {
    holdCtx.clearRect(0, 0, holdCanvas.width, holdCanvas.height);
    if (holdPieceType === null) return;
    
    let pShape = SHAPES[holdPieceType];
    let pColor = COLORS[holdPieceType];
    let nSize = Math.floor(SIZE * 0.7);
    let offsetX = (holdCanvas.width - pShape[0].length * nSize) / 2;
    let offsetY = (holdCanvas.height - pShape.length * nSize) / 2;
    
    pShape.forEach((row, y) => {
        row.forEach((val, x) => {
            let alpha = canHold ? 1 : 0.3;
            if (val) drawBlock(holdCtx, offsetX + x * nSize, offsetY + y * nSize, nSize, pColor, alpha);
        });
    });
}

function getGhostY() {
    let ghostY = piece.y;
    while (!collide(0, ghostY + 1 - piece.y)) { ghostY++; }
    return ghostY;
}

function draw() {
    if (!SIZE) return; 
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    ctx.strokeStyle = 'rgba(255, 245, 200, 0.08)';
    ctx.lineWidth = 1;
    for(let x=0; x<=COLS; x++) { ctx.beginPath(); ctx.moveTo(x*SIZE, 0); ctx.lineTo(x*SIZE, ROWS*SIZE); ctx.stroke(); }
    for(let y=0; y<=ROWS; y++) { ctx.beginPath(); ctx.moveTo(0, y*SIZE); ctx.lineTo(COLS*SIZE, y*SIZE); ctx.stroke(); }

    board.forEach((row, y) => {
        row.forEach((val, x) => {
            if (val) drawBlock(ctx, x * SIZE, y * SIZE, SIZE, val);
        });
    });

    if (!running) return;

    if(!paused) {
        let ghostY = getGhostY();
        piece.shape.forEach((row, y) => {
            row.forEach((val, x) => {
                if (val) drawBlock(ctx, (piece.x + x) * SIZE, (ghostY + y) * SIZE, SIZE, piece.color, 0.2);
            });
        });
    }

    piece.shape.forEach((row, y) => {
        row.forEach((val, x) => {
            if (val) drawBlock(ctx, (piece.x + x) * SIZE, (piece.y + y) * SIZE, SIZE, piece.color);
        });
    });
    
    if(paused) {
        ctx.fillStyle = 'rgba(0,0,0,0.7)';
        ctx.fillRect(0,0, canvas.width, canvas.height);
        ctx.fillStyle = 'white';
        ctx.font = '30px Arial';
        ctx.textAlign = 'center';
        ctx.fillText("SZÜNET", canvas.width/2, canvas.height/2);
    }
}

function collide(offsetX = 0, offsetY = 0, checkShape = piece.shape) {
    return checkShape.some((row, y) =>
        row.some((val, x) => {
            let nx = piece.x + x + offsetX;
            let ny = piece.y + y + offsetY;
            return val && (ny >= ROWS || nx < 0 || nx >= COLS || (ny >= 0 && board[ny][nx]));
        })
    );
}

function merge() {
    piece.shape.forEach((row, y) => {
        row.forEach((val, x) => {
            if (val && piece.y + y >= 0) {
                board[piece.y + y][piece.x + x] = piece.color; 
            }
        });
    });
}

function rotate() {
    if (isGameOverScreen || isInfoOpen) return;
    let m = piece.shape;
    let r = m[0].map((_, i) => m.map(row => row[i]).reverse());
    
    let offset = 0;
    if (!collide(0, 0, r)) {
        offset = 0;
    } else if (!collide(-1, 0, r)) {
        offset = -1;
    } else if (!collide(1, 0, r)) {
        offset = 1;  
    } else if (!collide(-2, 0, r)) {
        offset = -2; 
    } else if (!collide(2, 0, r)) {
        offset = 2;  
    } else if (!collide(-3, 0, r)) {
        offset = -3; 
    } else {
        return; 
    }

    piece.x += offset;
    piece.shape = r;
    draw();
}

function move(dir) {
    if (isGameOverScreen || isInfoOpen) return;
    if (!collide(dir, 0)) { piece.x += dir; draw(); }
}

function updateLevelAndSpeed() {
    let newLevel = 0;
    for(let i = LEVEL_THRESHOLDS.length - 1; i >= 0; i--) {
        if(linesTotal >= LEVEL_THRESHOLDS[i]) { newLevel = i; break; }
    }
    
    if(newLevel !== currentLevel) {
        currentLevel = newLevel;
        document.getElementById('level').innerText = currentLevel;
        
        clearInterval(dropInterval);
        dropInterval = setInterval(drop, LEVEL_SPEEDS[currentLevel]);
        
        let el = document.getElementById('combo');
        el.innerHTML = `<span style="color:#00d2ff; text-shadow: 0 0 10px #00d2ff;">SZINT LÉPÉS!</span><br>LVL ${currentLevel}`;
        el.style.opacity = 1;
        el.style.animation = 'none';
        void el.offsetWidth; // Reflow az animáció újraindításához
        
        // JAVÍTÁS: Kivettük a 'forwards' paramétert, hogy ne ragadjon kint
        el.style.animation = 'popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
        
        // 2 másodperc után teljesen eltüntetjük és alaphelyzetbe rakjuk
        setTimeout(() => {
            el.style.opacity = 0;
            el.style.animation = 'none';
        }, 2000);
    }
}

function drop() {
    if (!running || paused || isGameOverScreen || isInfoOpen) return;
    if (!collide(0, 1)) {
        piece.y++;
    } else {
        if(piece.y <= 0) { gameOver(); return; }
        merge();
        clearLines();
        newPiece();
    }
    draw();
}

function hardDrop() {
    if(!running || paused || isGameOverScreen || isInfoOpen) return;
    piece.y = getGhostY();
    merge();
    clearLines();
    newPiece();
    draw();
}

function clearLines() {
    let lines = 0;
    for (let y = ROWS - 1; y >= 0; y--) {
        if (board[y].every(v => v)) {
            board.splice(y, 1);
            board.unshift(Array(COLS).fill(0));
            lines++; y++;
        }
    }

    if (lines > 0) {
        linesTotal += lines;
        document.getElementById('lines').innerText = linesTotal;
        updateLevelAndSpeed(); 
        
        if (lastClear) combo++; else combo = 1;
        lastClear = true;
        
        let gain = 0;
        if(lines === 1) gain = 1; else if(lines === 2) gain = 2; else if(lines === 3) gain = 4;
        else if(lines === 4) gain = 6; else if(lines === 5) gain = 9; else if(lines >= 6) gain = 12;

        let comboBonus = 0;
        if (combo === 2) comboBonus = 2; else if (combo === 3) comboBonus = 4;
        else if (combo === 4) comboBonus = 6; else if (combo === 5) comboBonus = 8;
        else if (combo >= 6) comboBonus = 10;

        score += (gain + comboBonus);
        
        let isPerfect = board.every(row => row.every(cell => cell === 0));
        if(isPerfect) {
            score +=25; 
            showCombo(lines, combo, true);
        } else {
            showCombo(lines, combo, false);
        }
        
    } else { 
        combo = 0; lastClear = false; 
    }
    document.getElementById('score').innerText = score;
}

function showCombo(lines, comboCount, isPerfect = false) {
    let t = `+${lines} SOR<br>`;
    if (lines === 4) t = "TETRIS! 🔥<br>";
    
    // Ha Perfect Clear volt
    if (isPerfect) {
        t = "<span style='color:#00ffcc; text-shadow: 0 0 10px #00ffcc;'>PERFECT CLEAR! ✨</span><br><span style='font-size:16px'>+25 PONT</span><br>" + t;
    }
    
    if (comboCount === 2) t += "COMBO (+2)";
    else if (comboCount === 3) t += "2x COMBO (+4)";
    else if (comboCount === 4) t += "3x COMBO (+6)";
    else if (comboCount === 5) t += "MEGA COMBO (+8)";
    else if (comboCount >= 6) t += "GIGA COMBO (+10)";
    
    let el = document.getElementById('combo');
    el.innerHTML = t;
    el.style.opacity = 1;
    el.style.animation = 'none';
    void el.offsetWidth; // Reflow az animáció újraindításához
    
    // Itt a javítás: Kivettem a 'forwards' szót a végéről!
    el.style.animation = 'popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
    
    // 1.8 másodperc után eltüntetjük és alaphelyzetbe rakjuk
    setTimeout(() => {
        el.style.opacity = 0;
        el.style.animation = 'none';
    }, 1800);
}

function gameOver() {
    running = false; 
    clearInterval(dropInterval);
    clearInterval(timerInterval);
    
    let rank = getRank(currentLevel);
    document.getElementById('finalScore').innerText = score;
    document.getElementById('finalRank').innerText = rank;
    
    let isRecord = (score > topScore && score > 0);
    
    document.getElementById('newRecordLabel').style.display = isRecord ? 'block' : 'none';
    document.getElementById('popupNewRecord').style.display = isRecord ? 'flex' : 'none';
    document.getElementById('gameOverOverlay').style.display = 'flex';
    isGameOverScreen = true;
}

function proceedToLeaderboard() {
    if(!isGameOverScreen) return;
    isGameOverScreen = false;
    document.getElementById('gameOverOverlay').style.display = 'none';
    exitGame(); 
}

function exitGameBtn() {
    if(isGameOverScreen) return; 
    exitGame();
}

function showInfo() {
    if(!paused && running) {
        pauseGame(); 
    }
    document.getElementById('infoOverlay').style.display = 'flex';
    isInfoOpen = true;
}

function hideInfo() {
    document.getElementById('infoOverlay').style.display = 'none';
    isInfoOpen = false;
}

let touchStartX = 0, touchStartY = 0, lastTouchX = 0, lastTouchY = 0, moved = false, isSwipeDown = false;

document.addEventListener('touchmove', function(e) {
    if (e.target.id === 'game' || e.target.closest('#gameArea')) e.preventDefault();
}, { passive: false });

canvas.addEventListener('touchstart', e => {
    if (isInfoOpen) return; 
    e.preventDefault(); 
    touchStartX = lastTouchX = e.touches[0].clientX;
    touchStartY = lastTouchY = e.touches[0].clientY;
    moved = false; isSwipeDown = false;
}, {passive: false});

canvas.addEventListener('touchmove', e => {
    if (isInfoOpen) return;
    e.preventDefault();
    if (!running || paused || isGameOverScreen || isInfoOpen) return;
    
    let currentX = e.touches[0].clientX; let currentY = e.touches[0].clientY;
    let dxTotal = currentX - touchStartX; let dyTotal = currentY - touchStartY;
    lastTouchX = currentX; lastTouchY = currentY;

    const moveThreshold = SIZE * 0.8; 
    const softDropThreshold = Math.floor(SIZE * 0.5); 

    if (Math.abs(dyTotal) > Math.abs(dxTotal) && dyTotal > 0) {
        if (dyTotal > softDropThreshold) {
            drop(); 
            touchStartY = currentY; 
            moved = true; 
            isSwipeDown = true;
        }
    } 
    else if (Math.abs(dxTotal) > moveThreshold && !isSwipeDown) {
        if (dxTotal > 0) move(1); else move(-1);
        touchStartX = currentX; touchStartY = currentY; moved = true;
    }
}, {passive: false});

canvas.addEventListener('touchend', e => {
    if (isInfoOpen) return;
    e.preventDefault();
    if (!moved && running && !paused && !isGameOverScreen && !isInfoOpen) rotate();
}, {passive: false});

function startGame() {
    clearInterval(dropInterval);
    clearInterval(timerInterval);
    
    isGameOverScreen = false;
    isInfoOpen = false;
    document.getElementById('gameOverOverlay').style.display = 'none';
    document.getElementById('infoOverlay').style.display = 'none';
    document.getElementById('combo').style.opacity = 0;
    
    board = Array.from({length: ROWS}, () => Array(COLS).fill(0));
    score = 0; combo = 0; secondsElapsed = 0; linesTotal = 0; currentLevel = 0;
    
    pieceBag = [];
    nextPieceType = undefined;
    holdPieceType = null; canHold = true; hasSaved = false;
    
    document.getElementById('score').innerText = score;
    document.getElementById('lines').innerText = linesTotal;
    document.getElementById('level').innerText = currentLevel;
    
    document.getElementById('saveBtn').style.display = 'inline-block'; 
    
    updateTimerDisplay();
    
    running = true; paused = false;
    document.getElementById('startBtn').style.display = 'none';
    document.getElementById('pauseBtn').innerText = "Pause";
    
    newPiece();
    drawHold();
    draw();
    
    dropInterval = setInterval(drop, LEVEL_SPEEDS[currentLevel]);
    timerInterval = setInterval(() => {
        if(!paused && running) { secondsElapsed++; updateTimerDisplay(); }
    }, 1000);
}

function pauseGame() { 
    if(!running || isGameOverScreen) return;
    paused = !paused; 
    document.getElementById('pauseBtn').innerText = paused ? "Resume" : "Pause";
    draw(); 
}

function exitGame() {
    running = false;
    clearInterval(dropInterval);
    clearInterval(timerInterval);
    
    document.getElementById('startBtn').style.display = 'inline-block';
    document.getElementById('startBtn').innerText = "Új Játék";
    document.getElementById('gameScreen').style.display = 'none';
    document.getElementById('leaderboardView').style.display = 'flex';
    
    if(hasSaved) {
        document.getElementById('saveBtn').style.display = 'none';
    } else {
        document.getElementById('saveBtn').style.display = 'inline-block';
    }
    
    loadLeaderboard();
}

function backToGame() {
    document.getElementById('leaderboardView').style.display = 'none';
    document.getElementById('gameScreen').style.display = 'flex';
    resizeGame();
}

function saveScore() {
    if(hasSaved) return;
    let name = document.getElementById('name').value || 'Anoním';
    let timeStr = document.getElementById('timer').innerText;
    let rankStr = getRank(currentLevel);
    
    hasSaved = true; 
    document.getElementById('saveBtn').style.display = 'none'; 
    
    fetch('saved.php', {
        method: 'POST', 
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            nickname: name, 
            record: score, 
            time: timeStr, 
            rank: rankStr, 
            avatar: blogUserAvatar,
            date: new Date().toLocaleDateString('hu-HU')
        })
    })
    .then(() => {
        alert("Sikeresen mentve!");
        loadLeaderboard(); 
    });
}

function loadLeaderboard() {
    document.getElementById('leaderboardList').innerHTML = "<p style='color:white; padding:10px; text-align:center;'>Betöltés...</p>";
    fetch('load.php').then(r => r.json()).then(data => {
        if(!data || data.length === 0) {
             document.getElementById('leaderboardList').innerHTML = "<p style='color:white; padding:10px; text-align:center;'>Nincsenek még rekordok.</p>";
             return;
        }
        topScore = data[0].record;
        
        let html = '<table><tr><th style="text-align:center;">#</th><th>Játékos</th><th style="text-align:center;">Pont</th><th style="text-align:center;">Idő</th><th>Rang</th></tr>';
        
        data.slice(0, 100).forEach((p, i) => {
            let time = p.time || '-';
            let rank = p.rank || '-';
            
            if (i === 0) { rank = "👑 KIRÁLY 👑"; }
            if (i === 1) { rank = "🥈 2. Hely"; }
            if (i === 2) { rank = "🥉 3. Hely"; }
            
            let positionText = (i + 1) + ".";
            
            let avatarHtml = "";
            if (p.avatar && p.avatar.trim() !== "") {
                avatarHtml = `<img src="${p.avatar}" class="avatar-img" alt="avatar">`;
            } else {
                avatarHtml = `<span class="avatar-guest">👤</span>`;
            }
            
            html += `<tr>
                <td style="font-weight:bold; text-align:center; color:${i===0?'#ff8800':(i===1?'#aaa':'#cd7f32')};">${positionText}</td>
                <td style="font-weight:bold; display:flex; align-items:center;">${avatarHtml} ${p.nickname}</td>
                <td style="font-weight:bold; color: gold; text-align:center; font-size:15px;">${p.record}</td>
                <td style="text-align:center; color:#ccc;">${time}</td>
                <td style="color:#00d2ff; font-weight:bold;">${rank}</td>
            </tr>`;
        });
        html += '</table>';
        document.getElementById('leaderboardList').innerHTML = html;
    }).catch(() => {
        document.getElementById('leaderboardList').innerHTML = "<p style='color:red; padding:10px; text-align:center;'>Hiba a betöltéskor.</p>";
    });
}

document.addEventListener('keydown', e => {
    if (isGameOverScreen) {
        if (e.key === ' ' || e.key === 'Enter') {
            e.preventDefault();
            proceedToLeaderboard();
        }
        return;
    }
    
    if (isInfoOpen && (e.key === ' ' || e.key === 'Enter' || e.key === 'Escape')) {
        e.preventDefault();
        hideInfo();
        return;
    }
    
    if (!running || paused || isInfoOpen) return;
    if (e.key === 'ArrowLeft') move(-1);
    if (e.key === 'ArrowRight') move(1);
    if (e.key === 'ArrowUp') rotate();
    if (e.key === 'ArrowDown') drop();
    if (e.key === ' ') hardDrop();
    if (e.key === 'Control') holdPiece(); 
});
</script>
</body>
</html>