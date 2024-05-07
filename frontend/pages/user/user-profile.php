<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Homepage</title>
  <style>
    /* CSS */
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      display: flex;
      height: 100vh;
    }

    .sidebar {
      background-color: brown;
      color: #fff;
      width: 200px;
      padding: 20px;
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .sidebar-logo {
      font-size: 20px;
      font-weight: bold;
    }

    .sidebar-nav a {
      color: #fff;
      text-decoration: none;
      display: block;
      padding: 10px 0;
      border-bottom: 1px solid #fff; /* Add border-bottom */
    }

    .sidebar-nav a:last-child { /* Remove border-bottom for the last link */
      border-bottom: none;
    }

    .sidebar-nav a:hover {
      background-color: #444;
    }

    .content {
      flex-grow: 1;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 100px;
      padding: 100px;
      background-color: #f1f1f1;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .profile {
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      padding: 40px; /* Increased padding for a larger card */
      animation: floatCard 3s infinite alternate; /* Floating animation */
    }

    @keyframes floatCard {
      0% {
        transform: translateY(10px);
      }
      50% {
        transform: translateY(-50px);
      }
      100% {
        transform: translateY(10px);
      }
    }

    .profile-avatar {
      width: 160px; /* Set a fixed width for a square shape */
      height: 160px; /* Set a fixed height for a square shape */
      border-radius: 8px; /* Keep a rounded border for the square image */
      object-fit: cover;
      margin-bottom: 20px;
    }

    .profile-name {
      font-size: 28px; /* Larger font size */
      font-weight: bold;
      margin-bottom: 15px; /* Increased margin */
    }

    .profile-info {
      font-size: 18px; /* Larger font size */
      color: #333;
      margin-bottom: 15px; /* Increased margin */
    }

    .profile-progress {
      width: 450%;
      margin-top: 30px; /* Increased margin */
    }

    .profile-progress-bar {
      height: 30px; /* Increased progress bar height */
      background-color: #ff69b4; /* Pinkish progress bar */
      border-radius: 10px;
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <div class="sidebar-logo">Homepage</div>
    <nav class="sidebar-nav">
      <a href="index.php">Home</a>
      <a href="weather.php">Weather</a>
      <a href="data-analysis.php">Analysis</a>
      <a href="profile.php">Profile</a>
    </nav>
  </div>
  <main class="content">
    <div class="profile">
      <img src="assets/profile.jpg" alt="Profile Avatar" class="profile-avatar">
      <h2 class="profile-name">John Doe</h2>
      <p class="profile-info">Software Engineer</p>
      <p class="profile-info">City, Country</p>
      <div class="profile-progress">
        <div class="profile-progress-bar" style="width: 75%;"></div>
      </div>
    </div>
  </main>

  <script>
    // JavaScript
    // Fetch user data and update profile
    fetch('/api/user')
      .then(response => response.json())
      .then(userData => {
        updateProfile(userData);
      });

    function updateProfile(userData) {
      const profileAvatar = document.querySelector('.profile-avatar');
      const profileName = document.querySelector('.profile-name');
      const profileInfo = document.querySelectorAll('.profile-info');
      const profileProgressBar = document.querySelector('.profile-progress-bar');

      profileAvatar.src = userData.avatarUrl;
      profileName.textContent = userData.name;
      profileInfo[0].textContent = userData.occupation;
      profileInfo[1].textContent = userData.location;
      profileProgressBar.style.width = `${userData.progress}%`;
    }
  </script>
</body>
</html>