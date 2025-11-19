<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Patel Studio — Member Login</title>
<style>
:root{
    --accent:#00eaff;
    --bg1:#021024;
    --bg2:#032142;
    --bg3:#043B7A
}
body{
    margin:0;
    height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    font-family:Arial;
    background:linear-gradient(135deg,var(--bg1),var(--bg2),var(--bg3));
    color:#fff
}
.login-box{
    width:380px;
    padding:28px;
    background:rgba(255,255,255,0.06);
    border-radius:12px;
    backdrop-filter:blur(6px);
    box-shadow:0 8px 30px rgba(0,0,0,0.6);
    text-align:center
}
.login-box h2{
    margin:0 0 12px;
    text-shadow:0 0 10px rgba(0,255,255,0.2)
}
input{
    width:100%;
    padding:12px;
    margin:8px 0;
    border-radius:8px;
    border:1px solid rgba(255,255,255,0.12);
    background:rgba(255,255,255,0.03);
    color:#fff
}
input:focus{
    outline:none;
    box-shadow:0 0 8px var(--accent);
    border-color:var(--accent)
}
.btn{
    width:100%;
    padding:12px;
    border-radius:8px;
    border:none;
    background:var(--accent);
    color:#001;
    font-weight:700;
    cursor:pointer
}
.back{
    display:block;
    margin-top:12px;
    color:var(--accent);
    text-decoration:none
}
.small{
    font-size:13px;
    color:#cfeffb
}
</style>
</head>
<body>

<div class="login-box">
  <h2>Member Login</h2>

  <form action="member_login_process.php" method="POST">

    <!-- USERNAME LOGIN INSTEAD OF ID -->
    <input type="text" name="username" placeholder="Enter Username" required>

    <input type="password" name="password" placeholder="Password" required>

    <button class="btn" type="submit">Login</button>
  </form>

  <a class="back" href="index.html">← Back to Home</a>

  <p class="small">
    Don't have an account? 
    <a style="color:var(--accent)" href="member_signup.php">Signup</a>
  </p>
</div>

</body>
</html>
