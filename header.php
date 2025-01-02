<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="./css/header.css">
    <title>Park & GO</title>
  </head>
  <body>
    <nav class="navbar">
      <div class="logo"><a href="home.php">Park & GO</a></div>
      
      <!-- Updated search form -->
      <div id="search-box">
        <form id="search-form" action="home.php" method="POST">
        <input type="text" id="search-bar" name="q" placeholder="Search..." required />
        <button type="submit" id="search-btn">
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>
        </form>
      </div>
      
      <ul class="nav-links">
        <li><a href="./home.php">Home</a></li>
        <li><a href="./my_reservations.php">My reservations</a></li>
        <li><a href = "./regulations.php">Rules & Regulations</a></li>
        <li><a href="./registerbook.php">Add a space</a></li>
        <li><a href="./requests.php">Requests</a></li>
        <li><a href="./complains.php">make complain</a></li>
        <li><a href="./profile.php">My profile</a></li>
        
        
      </ul>
    </nav>

    <script
      src="https://kit.fontawesome.com/4599b1e468.js"
      crossorigin="anonymous"
    ></script>
  </body>

</html>

