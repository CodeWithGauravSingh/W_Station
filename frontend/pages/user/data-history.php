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
      width: 100px;
      padding: 20px;
      display: flex;
      align-items: center;
      flex-direction: column;
      gap: 20px;
      border-color: black;
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
      gap: 20px;
      padding: 20px;
    }

    .card {
      background-color: #f1f1f1;
      border-radius: 4px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      padding: 20px;
    }

    .card-title {
      font-size: 18px;
      font-weight: bold;
      margin-bottom: 10px;
    }

    .card-content {
      font-size: 14px;
      color: #666;
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <nav class="sidebar-nav">
    <a href="index.php">Home</a>
     <a href="weather.php">Weather</a>
      <a href="data-analysis.php">Analysis</a>
      <a href="user-profile.php">Profile</a>
    </nav>
  </div>
  <main class="content">
    <!-- Cards will be dynamically added here -->
  </main>

  <script>
    // JavaScript
    // Fetch data for the cards
    fetch('/api/cards')
      .then(response => response.json())
      .then(data => {
        // Render the cards
        renderCards(data);
      });

    function renderCards(data) {
      const cardsContainer = document.querySelector('.content');

      data.forEach(card => {
        const cardElement = createCardElement(card);
        cardsContainer.appendChild(cardElement);
      });
    }

    function createCardElement(card) {
      const cardElement = document.createElement('div');
      cardElement.classList.add('card');

      const cardTitle = document.createElement('h3');
      cardTitle.classList.add('card-title');
      cardTitle.textContent = card.title;

      const cardContent = document.createElement('p');
      cardContent.classList.add('card-content');
      cardContent.textContent = card.content;

      cardElement.appendChild(cardTitle);
      cardElement.appendChild(cardContent);

      return cardElement;
    }
  </script>
</body>
</html>