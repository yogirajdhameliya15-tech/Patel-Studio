<?php
session_start();
if (!isset($_SESSION['member'])) { header("Location: member_login.php"); exit; }
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Poster Maker — Patel Studio</title>
<link rel="stylesheet" href="tools_style.css">
</head>
<body>
<div class="container">
  <div class="header">
    <h1>Poster Maker</h1>
    <div class="controls">
      <input type="file" id="bgUpload" class="input" accept="image/*">
      <input type="text" id="lineText" class="input" placeholder="Enter text line">
      <input type="color" id="textColor" value="#ffffff" title="Text color">
      <select id="fontSize" class="input">
        <option value="28">28px</option><option value="36" selected>36px</option><option value="48">48px</option><option value="64">64px</option>
      </select>
      <button class="btn" id="addTextBtn">Add Text</button>
      <button class="btn" id="downloadPoster">Download</button>
      <button class="btn" id="savePoster">Save</button>
    </div>
  </div>

  <div class="canvas-wrap">
    <div class="canvas-card">
      <label>Poster (1024×768)</label>
      <canvas id="posterCanvas" width="1024" height="768" style="border:1px solid rgba(255,255,255,0.06);"></canvas>
      <div class="small">Click on canvas to set text position. Dragging not supported — reposition by clicking again.</div>
      <div id="message" class="small"></div>
    </div>
  </div>
</div>

<script>
const canvas = document.getElementById('posterCanvas');
const ctx = canvas.getContext('2d');
const bgUpload = document.getElementById('bgUpload');
const addTextBtn = document.getElementById('addTextBtn');
const downloadPoster = document.getElementById('downloadPoster');
const savePoster = document.getElementById('savePoster');
const lineText = document.getElementById('lineText');
const textColor = document.getElementById('textColor');
const fontSize = document.getElementById('fontSize');
const message = document.getElementById('message');

let backgroundImg = null;
let texts = []; // {text,x,y,size,color}

function draw(){
  // clear
  ctx.fillStyle = '#111';
  ctx.fillRect(0,0,canvas.width,canvas.height);
  if (backgroundImg){
    // fit cover
    const iw = backgroundImg.width, ih = backgroundImg.height;
    const cr = canvas.width / canvas.height;
    const ir = iw / ih;
    let dw, dh, dx, dy;
    if (ir > cr) { // image wider
      dh = canvas.height; dw = ih * (iw/ih) * (canvas.height/ih) * (ir/cr); dw = canvas.width * (iw/ih) * (ir/cr); 
      // easier: drawImage with cover: compute scale
      const scale = Math.max(canvas.width/iw, canvas.height/ih);
      dw = iw * scale; dh = ih * scale;
      dx = (canvas.width - dw)/2; dy = (canvas.height - dh)/2;
    } else {
      const scale = Math.max(canvas.width/iw, canvas.height/ih);
      dw = iw * scale; dh = ih * scale;
      dx = (canvas.width - dw)/2; dy = (canvas.height - dh)/2;
    }
    ctx.drawImage(backgroundImg, dx, dy, dw, dh);
  } else {
    ctx.fillStyle = '#1a263b';
    ctx.fillRect(0,0,canvas.width,canvas.height);
  }

  // draw texts
  for (let t of texts){
    ctx.fillStyle = t.color;
    ctx.font = `${t.size}px Arial`;
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    // stroke for readability
    ctx.lineWidth = Math.max(2, Math.floor(t.size / 18));
    ctx.strokeStyle = 'rgba(0,0,0,0.5)';
    ctx.strokeText(t.text, t.x, t.y);
    ctx.fillText(t.text, t.x, t.y);
  }
}

bgUpload.addEventListener('change', (e)=>{
  const f = e.target.files[0];
  if (!f) return;
  const img = new Image();
  img.onload = ()=>{
    backgroundImg = img;
    draw();
  };
  img.src = URL.createObjectURL(f);
});

let pendingText = null;
addTextBtn.addEventListener('click', ()=>{
  const txt = lineText.value.trim();
  if (!txt){ message.textContent='Enter text to add'; return; }
  const size = parseInt(fontSize.value,10);
  const color = textColor.value;
  pendingText = {text:txt,size: size, color: color};
  message.textContent = 'Click on canvas to place the text';
});

// canvas click to place text
canvas.addEventListener('click', (e)=>{
  if (!pendingText) return;
  const rect = canvas.getBoundingClientRect();
  const x = (e.clientX - rect.left) * (canvas.width/rect.width);
  const y = (e.clientY - rect.top) * (canvas.height/rect.height);
  texts.push({text: pendingText.text, x, y, size: pendingText.size, color: pendingText.color});
  pendingText = null;
  lineText.value = '';
  message.textContent = '';
  draw();
});

// download
downloadPoster.addEventListener('click', ()=>{
  draw();
  const a = document.createElement('a'); a.href = canvas.toDataURL('image/png'); a.download='poster.png'; a.click();
});

// save to server
savePoster.addEventListener('click', ()=>{
  draw();
  const data = canvas.toDataURL('image/png');
  message.textContent = 'Saving...';
  fetch('save_image.php', {
    method:'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: 'data=' + encodeURIComponent(data) + '&tag=poster'
  }).then(r=>r.json()).then(j=>{
    if (j.ok) message.textContent = 'Saved: ' + j.file;
    else message.textContent = 'Save failed: ' + (j.error||'unknown');
  }).catch(()=> message.textContent = 'Save error');
});

// initial draw
draw();
</script>

</body>
</html>
