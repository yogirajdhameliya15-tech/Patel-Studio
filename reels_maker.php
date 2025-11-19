<?php
session_start();
if (!isset($_SESSION['member'])) { header("Location: member_login.php"); exit; }
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Reel Maker — Patel Studio</title>
<link rel="stylesheet" href="tools_style.css">
</head>
<body>
<div class="container">
  <div class="header">
    <h1>Reel Maker (1080×1920)</h1>
    <div class="controls">
      <input type="file" id="bgUpload" accept="image/*" class="input">
      <input type="text" id="headline" class="input" placeholder="Headline">
      <input type="color" id="textCol" value="#ffffff">
      <select id="sizeSelect" class="input"><option value="48">48px</option><option value="72" selected>72px</option><option value="96">96px</option></select>
      <button class="btn" id="addHeadline">Add Headline</button>
      <button class="btn" id="downloadReel">Download</button>
      <button class="btn" id="saveReel">Save</button>
    </div>
  </div>

  <div class="canvas-wrap">
    <div class="canvas-card">
      <label>Preview (1080×1920)</label>
      <canvas id="reelCanvas" width="1080" height="1920" style="border:1px solid rgba(255,255,255,0.06)"></canvas>
      <div class="small">Tap to position text</div>
      <div id="msg" class="small"></div>
    </div>
  </div>
</div>

<script>
const canvas = document.getElementById('reelCanvas');
const ctx = canvas.getContext('2d');
const bgUpload = document.getElementById('bgUpload');
const addHeadline = document.getElementById('addHeadline');
const headline = document.getElementById('headline');
const textCol = document.getElementById('textCol');
const sizeSelect = document.getElementById('sizeSelect');
const downloadReel = document.getElementById('downloadReel');
const saveReel = document.getElementById('saveReel');
const msg = document.getElementById('msg');

let bg = null, items = [];

function draw(){
  if(bg){
    const scale = Math.max(canvas.width/bg.width, canvas.height/bg.height);
    const dw = bg.width*scale, dh = bg.height*scale;
    const dx = (canvas.width-dw)/2, dy = (canvas.height-dh)/2;
    ctx.drawImage(bg,dx,dy,dw,dh);
  } else { ctx.fillStyle='#111'; ctx.fillRect(0,0,canvas.width,canvas.height); }

  for (let it of items){
    ctx.fillStyle = it.color; ctx.font = `${it.size}px Arial`;
    ctx.textAlign='center'; ctx.textBaseline='middle';
    ctx.lineWidth = Math.max(2, Math.floor(it.size/18));
    ctx.strokeStyle = 'rgba(0,0,0,0.6)';
    ctx.strokeText(it.text, it.x, it.y);
    ctx.fillText(it.text, it.x, it.y);
  }
}

bgUpload.addEventListener('change', (e)=>{
  const f = e.target.files[0]; if(!f) return;
  const im = new Image(); im.onload = ()=>{ bg = im; draw(); }; im.src = URL.createObjectURL(f);
});

addHeadline.addEventListener('click', ()=>{
  const t = headline.value.trim(); if(!t){ msg.textContent='Enter headline'; return; }
  msg.textContent='Tap canvas to position headline';
  function onClick(e){
    const rect = canvas.getBoundingClientRect();
    const x = (e.clientX - rect.left) * (canvas.width/rect.width);
    const y = (e.clientY - rect.top) * (canvas.height/rect.height);
    items.push({text:t,x,y,size:parseInt(sizeSelect.value,10),color:textCol.value});
    draw(); msg.textContent='Headline added';
    canvas.removeEventListener('click', onClick);
  }
  canvas.addEventListener('click', onClick);
});

downloadReel.addEventListener('click', ()=>{ draw(); const a=document.createElement('a'); a.href=canvas.toDataURL('image/png'); a.download='reel.png'; a.click(); });
saveReel.addEventListener('click', ()=>{ draw(); const data = canvas.toDataURL('image/png'); msg.textContent='Saving...'; fetch('save_image.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'data='+encodeURIComponent(data)+'&tag=reel'}).then(r=>r.json()).then(j=>{ if(j.ok) msg.textContent='Saved: '+j.file; else msg.textContent='Save failed'; }); });
draw();
</script>
</body>
</html>
