<?php
// expects $username from session before include
?>
<header class="topbar">

  <!-- LEFT: LOGO -->
  <div class="top-left">
      <img src="assets/logo.png" class="top-logo" alt="Patel Studio">
      <span class="top-title">Patel Studio</span>
  </div>

  <!-- RIGHT: PROFILE -->
  <div class="top-right">

      <div class="profile-box" id="profileToggle">
          <img src="assets/profile_icon.png" class="top-profile" alt="profile">
      </div>

      <div class="dropdown" id="profileMenu">
          <span class="dropdown-user">👤 <?php echo htmlspecialchars($username); ?></span>
          <a href="member_profile.php">Profile</a>
          <a href="member_change_password.php">Change Password</a>
          <a href="logout.php">Logout</a>
      </div>

  </div>

</header>

<!-- DROPDOWN SCRIPT -->
<script>
document.addEventListener('click', function(e){
    let toggle = document.getElementById('profileToggle');
    let menu = document.getElementById('profileMenu');

    if(toggle.contains(e.target)){
        menu.classList.toggle("show");
    } else if(!menu.contains(e.target)){
        menu.classList.remove("show");
    }
});
</script>
