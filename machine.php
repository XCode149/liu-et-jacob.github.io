<?php
// Génération de la page HTML en PHP
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Machine à Dessiner</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: #f0f0f0;
        }
        canvas {
            border: 2px solid #333;
            background-color: #fff;
            cursor: crosshair;
            touch-action: none;
        }
        .controls {
            margin-top: 10px;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        button, select, input[type="color"] {
            margin: 5px;
            padding: 5px 10px;
            font-size: 16px;
            cursor: pointer;
        }
        .bubble {
            position: absolute;
            border: 2px solid black;
            background-color: white;
            border-radius: 10px;
            padding: 10px;
            resize: both;
            overflow: hidden;
            font-family: Arial, sans-serif;
        }
    </style>
</head>
<body>
    <h1>Machine à Dessiner</h1>
    <canvas id="drawCanvas" width="360" height="640"></canvas>
    <div class="controls">
        <select id="toolSelector">
            <option value="pen">Plume</option>
            <option value="pencil">Crayon</option>
            <option value="bubble">Ajouter une bulle</option>
        </select>
        <input type="color" id="colorPicker" value="#000000">
        <button id="clearButton">Effacer</button>
        <button id="saveButton">Télécharger</button>
    </div>

    <script>
        const canvas = document.getElementById('drawCanvas');
        const ctx = canvas.getContext('2d');
        const toolSelector = document.getElementById('toolSelector');
        const colorPicker = document.getElementById('colorPicker');
        const clearButton = document.getElementById('clearButton');
        const saveButton = document.getElementById('saveButton');

        let isDrawing = false;
        let lastX = 0;
        let lastY = 0;
        let currentTool = 'pen';

        function startDrawing(e) {
            if (currentTool === 'bubble') return;
            isDrawing = true;
            [lastX, lastY] = [e.offsetX, e.offsetY];
        }

        function draw(e) {
            if (!isDrawing || currentTool === 'bubble') return;
            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(e.offsetX, e.offsetY);
            ctx.strokeStyle = colorPicker.value;
            ctx.lineWidth = currentTool === 'pen' ? 3 : 1;
            ctx.stroke();
            ctx.closePath();
            [lastX, lastY] = [e.offsetX, e.offsetY];
        }

        function stopDrawing() {
            isDrawing = false;
            ctx.beginPath();
        }

        function clearCanvas() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }

        function saveCanvas() {
            const link = document.createElement('a');
            link.download = 'dessin.png';
            link.href = canvas.toDataURL();
            link.click();
        }

        function addBubble() {
            const bubble = document.createElement('div');
            bubble.contentEditable = true;
            bubble.className = 'bubble';
            bubble.style.left = '50px';
            bubble.style.top = '50px';
            bubble.style.width = '150px';
            bubble.style.height = '100px';
            bubble.style.position = 'absolute';
            document.body.appendChild(bubble);

            bubble.addEventListener('mousedown', (e) => {
                e.preventDefault();
                const offsetX = e.offsetX;
                const offsetY = e.offsetY;

                function moveBubble(ev) {
                    bubble.style.left = `${ev.pageX - offsetX}px`;
                    bubble.style.top = `${ev.pageY - offsetY}px`;
                }

                function stopMoveBubble() {
                    document.removeEventListener('mousemove', moveBubble);
                    document.removeEventListener('mouseup', stopMoveBubble);
                }

                document.addEventListener('mousemove', moveBubble);
                document.addEventListener('mouseup', stopMoveBubble);
            });
        }

        toolSelector.addEventListener('change', () => {
            currentTool = toolSelector.value;
        });

        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('mouseout', stopDrawing);

        clearButton.addEventListener('click', clearCanvas);
        saveButton.addEventListener('click', saveCanvas);

        document.addEventListener('click', (e) => {
            if (currentTool === 'bubble' && e.target === canvas) {
                addBubble();
            }
        });
    </script>
</body>
</html>
