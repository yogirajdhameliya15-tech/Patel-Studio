<?php
session_start();
if (!isset($_SESSION['member'])) { header("Location: member_login.php"); exit; }
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>QR Generator — Patel Studio</title>
<link rel="stylesheet" href="tools_style.css">
<style>
   

</style>
</head>
<body>
<div class="container">
  <div class="header">
    <h1>QR Generator</h1>
    <div class="controls">
      <input id="qrText" class="input" placeholder="Text or URL for QR" style="width:360px">
      <select id="qrSize" class="input">
        <option value="200">200×200</option>
        <option value="300" selected>300×300</option>
        <option value="400">400×400</option>
      </select>
      <button class="btn" id="previewBtn">Preview</button>
      <button class="btn" id="downloadBtn">Download PNG</button>
      <button class="btn" id="saveBtn">Save to Server</button>
    </div>
  </div>

  <div class="canvas-wrap">
    <div class="canvas-card">
      <label>Preview</label>
      <div id="previewArea">
        <img id="qrImage" class="previewImage" src="assets/qr.png" alt="QR preview">
      </div>
      <div id="message" class="small"></div>
    </div>
  </div>
</div>

<script>
const previewBtn = document.getElementById('previewBtn');
const downloadBtn = document.getElementById('downloadBtn');
const saveBtn = document.getElementById('saveBtn');
const qrText = document.getElementById('qrText');
const qrSize = document.getElementById('qrSize');
const qrImage = document.getElementById('qrImage');
const message = document.getElementById('message');

function generateQRUrl(text, size){
  // use Google Chart API (simple and reliable)
  const url = 'https://chart.googleapis.com/chart?cht=qr&chs=' + size + 'x' + size + '&chl=' + encodeURIComponent(text) + '&choe=UTF-8';
  return url;
}

previewBtn.addEventListener('click', ()=>{
  const text = qrText.value.trim();
  if(!text){ message.textContent='Enter text or URL'; return; }
  const size = qrSize.value;
  qrImage.src = generateQRUrl(text,size);
  message.textContent = '';
});

// download: draw image to canvas and save
downloadBtn.addEventListener('click', async ()=>{
  if (!qrImage.src) { message.textContent = 'Generate a preview first'; return; }
  const img = new Image();
  img.crossOrigin = 'anonymous';
  img.src = qrImage.src;
  img.onload = ()=>{
    const canvas = document.createElement('canvas');
    canvas.width = img.width; canvas.height = img.height;
    const ctx = canvas.getContext('2d');
    ctx.fillStyle = '#ffffff'; ctx.fillRect(0,0,canvas.width, canvas.height);
    ctx.drawImage(img,0,0);
    const a = document.createElement('a');
    a.download = 'qr.png';
    a.href = canvas.toDataURL('image/png');
    a.click();
  };
  img.onerror = ()=> message.textContent = 'Failed to load QR for download (CORS?)';
});

// save to server
saveBtn.addEventListener('click', ()=>{
  if (!qrImage.src) { message.textContent = 'Generate a preview first'; return; }
  const img = new Image();
  img.crossOrigin = 'anonymous';
  img.src = qrImage.src;
  img.onload = ()=>{
    const canvas = document.createElement('canvas');
    canvas.width = img.width; canvas.height = img.height;
    const ctx = canvas.getContext('2d');
    ctx.fillStyle = '#ffffff'; ctx.fillRect(0,0,canvas.width, canvas.height);
    ctx.drawImage(img,0,0);
    const data = canvas.toDataURL('image/png');
    message.textContent = 'Saving...';
    fetch('save_image.php', {
      method:'POST',
      headers: {'Content-Type':'application/x-www-form-urlencoded'},
      body: 'data=' + encodeURIComponent(data) + '&tag=qr'
    }).then(r=>r.json()).then(j=>{
      if (j.ok){ message.textContent = 'Saved: ' + j.file; }
      else message.textContent = 'Save failed: ' + (j.error||'unknown');
    }).catch(e=> message.textContent = 'Save error');
  };
  img.onerror = ()=> message.textContent = 'Failed to load QR for save (CORS?)';
});

let qrStyle = "square";

let qrCode = new QRCodeStyling({
    width: 260,
    height: 260,
    type: "png",
    data: "https://patelstudio.in",
    dotsOptions: { color: "#000", type: "square" },
    backgroundOptions: { color: "#ffffff" }
});

qrCode.append(document.getElementById("qrPreview"));

document.getElementById("qrText").addEventListener("keyup", function () {
    qrCode.update({ data: this.value });
});

// Style Change
document.querySelectorAll(".styleBtn").forEach(btn => {
    btn.addEventListener("click", function(){
        document.querySelectorAll(".styleBtn").forEach(b=>b.classList.remove("active"));
        this.classList.add("active");

        let type = this.getAttribute("data-style");
        let dotType = "square";

        if(type === "dots") dotType = "dots";
        if(type === "rounded") dotType = "rounded";
        if(type === "classy") dotType = "classy-rounded";

        qrCode.update({
            dotsOptions: { type: dotType }
        });
    });
});

// Download Button
document.getElementById("downloadBtn").addEventListener("click", () => {
    qrCode.download({ name: "patelstudio_qr", extension: "png" });
});

</script>
</body>
</html>
