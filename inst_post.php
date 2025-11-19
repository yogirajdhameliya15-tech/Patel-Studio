<?php
session_start();
if (!isset($_SESSION['member'])) { header("Location: member_login.php"); exit; }
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Instagram Post Maker — Patel Studio</title>
<link rel="stylesheet" href="tools_style.css">
</head>
<body>
<div class="container">
  <div class="header">
    <h1>Instagram Post Maker (1080×1080)</h1>
    <div class="controls">
      <input type="file" id="bgUpload" accept="image/*" class="input">
      <select id="template" class="input">
        <option value="solid">Solid color</option>
        <option value="gradient">Gradient</option>
        <option value="image">Image cover</option>
      </select>
      <input type="color" id="bgColor" value="#0aaeff">
      <input type="text" id="caption" class="input" placeholder="Caption text">
      <button class="btn" id="addCaption">Add Caption</button>
      <button class="btn" id="downloadBtn">Download</button>
      <button class="btn" id="saveBtn">Save</button>
    </div>
  </div>

  <div class="canvas-wrap">
    <div class="canvas-card">
      <label>Preview (1080×1080)</label>
      <canvas id="postCanvas" width="1080" height="1080" style="border:1px solid rgba(255,255,255,0.06)"></canvas>
      <div class="small">Click canvas to position caption</div>
      <div id="msg" class="small"></div>
    </div>
  </div>
</div>

<script>
const canvas = document.getElementById('postCanvas');
const ctx = canvas.getContext('2d');
const bgUpload = document.getElementById('bgUpload');
const template = document.getElementById('template');
const bgColor = document.getElementById('bgColor');
const caption = document.getElementById('caption');
const addCaption = document.getElementById('addCaption');
const downloadBtn = document.getElementById('downloadBtn');
const saveBtn = document.getElementById('saveBtn');
const msg = document.getElementById('msg');

let bgImg = null, captions = [];

function render(){
  // background
  if (template.value === 'solid'){
    ctx.fillStyle = bgColor.value; ctx.fillRect(0,0,canvas.width,canvas.height);
  } else if (template.value === 'gradient'){
    let g = ctx.createLinearGradient(0,0,canvas.width,canvas.height);
    g.addColorStop(0, bgColor.value);
    g.addColorStop(1, '#001');
    ctx.fillStyle = g; ctx.fillRect(0,0,canvas.width,canvas.height);
  } else if (template.value === 'image' && bgImg){
    const scale = Math.max(canvas.width/bgImg.width, canvas.height/bgImg.height);
    const dw = bgImg.width * scale, dh = bgImg.height * scale;
    const dx = (canvas.width - dw)/2, dy = (canvas.height - dh)/2;
    ctx.drawImage(bgImg, dx, dy, dw, dh);
  } else {
    ctx.fillStyle = '#222'; ctx.fillRect(0,0,canvas.width,canvas.height);
  }

  // captions
  for (let c of captions){
    ctx.fillStyle = c.color; ctx.font = `${c.size}px Arial`;
    ctx.textAlign = 'center'; ctx.textBaseline='middle';
    ctx.lineWidth = Math.max(2, Math.floor(c.size/18));
    ctx.strokeStyle = 'rgba(0,0,0,0.5)';
    ctx.strokeText(c.text, c.x, c.y);
    ctx.fillText(c.text, c.x, c.y);
  }
}

bgUpload.addEventListener('change', (e)=>{
  const f = e.target.files[0]; if (!f) return;
  const i = new Image();
  i.onload = ()=>{ bgImg = i; render(); }
  i.src = URL.createObjectURL(f);
});

addCaption.addEventListener('click', ()=>{
  const t = caption.value.trim(); if(!t){ msg.textContent='Enter caption'; return; }
  msg.textContent='Click on canvas to place caption';
  // waiting for click
  function onClick(e){
    const rect = canvas.getBoundingClientRect();
    const x = (e.clientX - rect.left) * (canvas.width/rect.width);
    const y = (e.clientY - rect.top) * (canvas.height/rect.height);
    captions.push({text:t,x,y,size:48,color:'#fff'});
    render(); msg.textContent='Caption added';
    canvas.removeEventListener('click', onClick);
  }
  canvas.addEventListener('click', onClick);
});

downloadBtn.addEventListener('click', ()=>{
  render();
  const a = document.createElement('a'); a.href = canvas.toDataURL('image/png'); a.download='insta_post.png'; a.click();
});

saveBtn.addEventListener('click', ()=>{
  render();
  const data = canvas.toDataURL('image/png');
  msg.textContent = 'Saving...';
  fetch('save_image.php', {
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body: 'data=' + encodeURIComponent(data) + '&tag=insta_post'
  }).then(r=>r.json()).then(j=>{
    if (j.ok) msg.textContent = 'Saved: ' + j.file; else msg.textContent = 'Save failed';
  });
});

// initial
render();
</script>
</body>
</html>
