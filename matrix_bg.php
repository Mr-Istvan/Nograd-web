<?php
$words = ["NÓGRÁD CSODÁK", "LÁTNIVALÓK", "PROGRAMOK", "SZÁLLÁSOK", "GASZTRO", "TÚRÁZÁS", "SALGÓTARJÁN", "HOLLÓKŐ", "BÁNK", "MÁTRA"];
$neon_color = "#00ffff";
$mid_color = "#45489a";
$low_color = "rgba(138, 43, 226, 0.4)";
?>
<canvas id="matrix" style="position:fixed;top:0;left:0;z-index:-1;background:#000;"></canvas>

<script>
    const canvas = document.getElementById('matrix');
    const ctx = canvas.getContext('2d', { alpha: false });

    let width, height, columns, drops;
    let lastTime = 0; // Az utolsó képkocka időpontja
    const fontSize = window.innerWidth < 768 ? 12 : 16;
    const words = <?php echo json_encode($words); ?>;
    
    // Alap sebességi szorzó (ezt állítsd, ha gyorsabb/lassabb esőt akarsz)
    const baseSpeed = 0.15; 

    function init() {
        width = canvas.width = window.innerWidth;
        height = canvas.height = window.innerHeight;
        columns = Math.floor(width / (fontSize + 2));
        drops = [];
        for (let i = 0; i < columns; i++) {
            drops[i] = {
                x: i * (fontSize + 2),
                y: Math.random() * -height,
                word: words[Math.floor(Math.random() * words.length)],
                // Relatív sebesség szorzó minden oszlophoz
                speedMult: 0.5 + Math.random() * 1.5 
            };
        }
    }

    window.addEventListener('resize', init);
    init();

    function draw(currentTime) {
        // 1. Időkülönbség kiszámítása (mint a példádban)
        if (!lastTime) lastTime = currentTime;
        const deltaTime = currentTime - lastTime;
        lastTime = currentTime;

        ctx.fillStyle = "rgba(0, 0, 0, 0.9)"; 
        ctx.fillRect(0, 0, width, height);
        ctx.font = "bold " + fontSize + "px monospace";

        for (let i = 0; i < drops.length; i++) {
            const drop = drops[i];
            const word = drop.word;
            
            for (let j = 0; j < word.length; j++) {
                const charY = drop.y - (j * fontSize);
                if (charY < -fontSize || charY > height) continue;

                const pos = j / word.length;
                ctx.fillStyle = pos < 0.3 ? "<?php echo $neon_color; ?>" : (pos < 0.7 ? "<?php echo $mid_color; ?>" : "<?php echo $low_color; ?>");
                ctx.fillText(word[j], drop.x, charY);
            }

            // 2. Mozgás számítása a deltaTime alapján
            // Így a sebesség független az FPS-től!
            drop.y += baseSpeed * drop.speedMult * deltaTime;

            if (drop.y - (word.length * fontSize) > height) {
                drop.y = -fontSize;
                drop.word = words[Math.floor(Math.random() * words.length)];
            }
        }
        requestAnimationFrame(draw);
    }

    requestAnimationFrame(draw);
</script>