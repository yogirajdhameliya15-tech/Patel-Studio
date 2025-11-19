<!doctype html> 
<html> 
  <head> 
    <meta charset="utf-8"> 
    <title>Member Signup — Patel Studio</title> 
    <style> body{font-family:Arial;background:linear-gradient(135deg,#021024,#032142);color:#fff;display:flex;align-items:center;justify-content:center;height:100vh;margin:0} .box{background:rgba(255,255,255,0.04);padding:26px;border-radius:12px;width:420px} input{width:100%;padding:10px;margin:8px 0;border-radius:8px;border:1px solid rgba(255,255,255,0.12);background:transparent;color:#fff} button{padding:12px 16px;border-radius:8px;border:none;background:#00eaff;color:#001;font-weight:700;cursor:pointer;width:100%} 

    </style> 
    </head> 
    <body> 
      <div class="box"> 
        <h2>Create Account</h2> 
        <form action="signup_process.php" method="POST"> 
          <input name="username" placeholder="Username" required> 
          <input name="password" type="password" placeholder="Password" required> 
          <input name="fullname" placeholder="Full name"> 
          <input name="email" placeholder="Email"> 
          <button type="submit">Sign up</button> 
        </form> 
        <p style="margin-top:10px">
          <a style="color:#00eaff" href="member_login.php">Already have account? Login</a>
        </p> 
      </div> 
    </body> 
    </html>