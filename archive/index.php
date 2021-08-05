<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
  header('Location: ../../index.html');
  exit;
}
?>

<!doctype html>
<html>

<head>
  <meta charset="UTF-8">
  <title>LedCameras</title>
  <style>
    * {
      padding: 0;
      margin: 0;
    }

    body {
      color: #333;
      font: 14px Sans-Serif;
      padding: 4rem 50px 50px 50px;
      background: #eee;
      display: grid;
    }

    h1 {
      text-align: center;
      padding: 20px 0 20px 0;
      width: 100%;
    }

    h2 {
      font-size: 20px;
      text-align: center;
      padding: 20px 0 12px 0;
    }

    div p {
      font: 16px Sans-Serif;
      font-weight: 600;
      margin: 0.6rem auto 1rem auto;
      white-space: nowrap;
    }

    nav {
      position: fixed;
      top: 0;
      background: #212121;
      margin: auto;
      width: 100%;
      height: 3.53rem;
      margin: 0 50px 0 -50px;
      overflow: auto;
      display: inline-flex;
      justify-content: space-between;
      z-index: 999;
    }

    nav div {
      white-space: nowrap;
      display: inline-flex;
      margin: 0 20px;
      align-items: center;
      color: white;
    }

    nav div a {
      display: inline-flex;
      align-items: center;
      padding: 0 10px;
      font-family: 'Open Sans', sans-serif;
      color: white;
      text-decoration: none;
    }

    nav div p {
      margin: 0;
      font-weight: 400;
    }

    nav div a p {
      font-weight: 400;
      align-items: center;
      margin: 0;
    }

    nav div svg {
      margin: 0 5px;
      height: 30px;
      width: 30px;
    }

    #container {
      box-shadow: 0 5px 10px -5px rgba(0, 0, 0, 0.5);
      position: relative;
      background: white;
      white-space: nowrap;
    }

    #sn_logo {
      height: auto;
      width: auto;
      box-shadow: none;
      max-height: 40px;
      position: fixed;
      right: calc((100% - 234px) / 2);
      top: 8px;
    }

    .image-gallery {
      width: 100%;
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(450px, 1fr));
      justify-content: center;
      padding: 4px;
    }

    .box {
      justify-content: center;
      flex-basis: 25%;
      padding: 10px;
      margin: 2px;
      display: grid;
    }

    .img-gallery {
      width: 100%;
      height: auto;
      object-fit: cover;
      transform: scale(1);
      transition: all 0.3s ease-in-out;
    }

    .img-gallery:hover {
      transform: scale(1.05);
    }

    .lightbox {
      display: none;
      position: fixed;
      z-index: 999;
      width: 100%;
      height: 100%;
      text-align: center;
      top: 0;
      left: 0;
      background: rgba(0, 0, 0, 0.9);
    }

    .lightbox img {
      max-width: 75%;
      max-height: 75%;
      width: auto;
      height: auto;
      margin: 5%;
      border-radius: 0.33%;
    }

    .lightbox img:hover {
      transform: none;
      transition: none;
    }

    .lightbox:target {
      outline: none;
      display: block;
    }

    .footer {
      margin: 3rem auto 0 auto;
      width: 50%;
      text-align: center;
    }

    @media (orientation: portrait) {
      body {
        padding-top: 7.5rem;
      }

      p {
        font: 24px Sans-Serif;
        font-weight: 600;
      }

      nav {
        padding: 1.4rem;
        width: Calc(100% - 45px);
      }

      nav div {
        margin: 0px;
      }

      nav div svg {
        margin: 0 5px;
        height: 50px;
        width: 50px;
      }

      nav div p {
        font-size: 24px;
        padding: 0px 5px;
        margin: 0px;
      }

      nav div a {
        padding: 0px;
        margin-right: 1rem;
      }

      nav div a p {
        display: none;
      }

      #arch {
        margin-right: 3rem;
      }

      #p_nav {
        height: 4rem;
      }

      #sn_logo {
        top: 1rem;
        height: 70px;
        max-height: 70px;
        width: auto;
        right: calc((100% - 409px) / 2);
      }
    }
  </style>
</head>

<body>
  <nav>
    <div>
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#FFFFFF">
        <path d="M0 0h24v24H0V0z" fill="none" />
        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zM7.07 18.28c.43-.9 3.05-1.78 4.93-1.78s4.51.88 4.93 1.78C15.57 19.36 13.86 20 12 20s-3.57-.64-4.93-1.72zm11.29-1.45c-1.43-1.74-4.9-2.33-6.36-2.33s-4.93.59-6.36 2.33C4.62 15.49 4 13.82 4 12c0-4.41 3.59-8 8-8s8 3.59 8 8c0 1.82-.62 3.49-1.64 4.83zM12 6c-1.94 0-3.5 1.56-3.5 3.5S10.06 13 12 13s3.5-1.56 3.5-3.5S13.94 6 12 6zm0 5c-.83 0-1.5-.67-1.5-1.5S11.17 8 12 8s1.5.67 1.5 1.5S12.83 11 12 11z" />
      </svg>
      <p><?= $_SESSION['name'] ?></p>
    </div>
    <div>
      <img id="sn_logo" src="../../sn_logo.png" alt="sn_logo">
    </div>
    <div>
      <a href="../../archive_page/.index.php">
        <p>Powrót</p>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#FFFFFF">
          <path d="M0 0h24v24H0V0z" fill="none" />
          <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z" />
        </svg>
      </a>
      <a id="home" href="../../index_logged.php">
        <p>Home</p>
        <svg id="home" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#FFFFFF">
          <path d="M0 0h24v24H0V0z" fill="none" />
          <path d="M12 5.69l5 4.5V18h-2v-6H9v6H7v-7.81l5-4.5M12 3L2 12h3v8h6v-6h2v6h6v-8h3L12 3z" />
        </svg>
      </a>
      <a href="../../logout.php">
        <p>Wyloguj</p>
        <svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" viewBox="0 0 24 24" fill="#FFFFFF">
          <g>
            <path d="M0,0h24v24H0V0z" fill="none" />
          </g>
          <g>
            <path d="M17,8l-1.41,1.41L17.17,11H9v2h8.17l-1.58,1.58L17,16l4-4L17,8z M5,5h7V3H5C3.9,3,3,3.9,3,5v14c0,1.1,0.9,2,2,2h7v-2H5V5z" />
          </g>
        </svg>
      </a>
    </div>
  </nav>
  <div id="container">
    <h1 id="header">Archiwum zrzutów z komputera </h1>
  </div>
  <script>
    var i = location.pathname.split('/')
    var name = i[i.length - 2]
    var actual = document.getElementById('header').innerHTML
    document.getElementById('header').innerHTML = (actual + name)
    document.title = (document.title + " - " + name + " archive")
  </script>
  <div class="image-gallery">
    <?php
    function better_scandir($dir, $sorting_order)
    {
      $files = array();
      foreach (scandir($dir, $sorting_order) as $file) {
        if ($file[0] === '.') {
          continue;
        }
        $files[$file] = filemtime($dir . '/' . $file);
      }

      if ($sorting_order == SCANDIR_SORT_ASCENDING) {
        asort($files, SORT_NUMERIC);
      } else {
        arsort($files, SORT_NUMERIC);
      }

      $ret = array_keys($files);
      return ($ret) ? $ret : false;
    }

    $files = better_scandir('.', SCANDIR_SORT_ASCENDING);
    $files = array_reverse($files);
    if (count($files) > 1) {
      foreach ($files as $file) {
        if ($file !== "." && $file !== ".." && $file !== "index.php") {
          $fn = str_replace(".jpg", "", $file);
          $fnWithoutFloor = str_replace("_", " ", $fn);
          $fnWithoutFloor = substr_replace($fnWithoutFloor, ":", -3, 1);
          $fnWithoutFloor = substr_replace($fnWithoutFloor, ".", -11, 1);
          $fnWithoutFloor = substr_replace($fnWithoutFloor, ".", -14, 1);
          echo "<div class='box'>
  <a href='#$fn'>
      <img src='$file' class='img-gallery' alt='$file' />
  </a>
  <a href='#_' class='lightbox' id='$fn'>
      <img src='$file' class='img-gallery' alt='$file' />
  </a>
  <p>$fnWithoutFloor</p>
</div>";
        }
      }
    } else {
      echo "<h2>Brak archiwalnych zdjeć z kamery</h2>";
    }
    ?>
  </div>
  <div class="footer">
    <p>Copyright &copy; 2021 Screen Network S.A.</p>
    <p>Made by <a href="http://exiges.pl">Exiges</a></p>
  </div>
</body>

</html>