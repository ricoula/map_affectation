<?php include("header.php") ?>
<!DOCTYPE html>
<html>
  <head>
    <title></title>
    <link rel="stylesheet" type="text/css" href="CSS/style.css">

    <style>
      body {
          font-family: "Lato", sans-serif;
      }

      .sidenav {
          height: 100%;
          width: 0;
          position: fixed;
          z-index: 1;
          top: 0;
          left: 0;
          background-color: #111;
          overflow-x: hidden;
          transition: 0.5s;
          padding-top: 60px;
      }

      .sidenav a {
          padding: 8px 8px 8px 32px;
          text-decoration: none;
          font-size: 25px;
          color: #818181;
          display: block;
          transition: 0.3s;
      }

      .sidenav a:hover {
          color: #f1f1f1;
      }

      .sidenav .closebtn {
          position: absolute;
          top: 0;
          right: 25px;
          font-size: 36px;
          margin-left: 50px;
      }

      @media screen and (max-height: 450px) {
        .sidenav {padding-top: 15px;}
        .sidenav a {font-size: 18px;}
      }


      #closeSlidebar{
        cursor:pointer
      }

      #menuSideBar{
          position: absolute;
          display: flex;
          flex-direction: column;
      }

      #navbar{
          position: absolute;
          z-index: 1000;
      }
    </style>

  </head>
  <body>
  <div id="mySidenav" class="sidenav">
    <a id="closeSlidebar" class="closebtn">&times;</a>
    <a href="#" id="test">About</a>
    <a href="#">Services</a>
    <a href="#">Clients</a>
    <a href="#">Contact</a>
  </div>
  <nav id="navbar">
      <ul>
        <li><span class="glyphicon glyphicon-search open" style="font-size:30px;cursor:pointer"></span></li>
        <li><span class="glyphicon glyphicon-search open" style="font-size:30px;cursor:pointer"></span></li>
        <li><span class="glyphicon glyphicon-search open" style="font-size:30px;cursor:pointer"></span></li>
        <li><span class="glyphicon glyphicon-search open" style="font-size:30px;cursor:pointer"></span></li>
      </ul>
  </nav>

  <!--<h2>Animated Sidenav Example</h2>
  <p>Click on the element below to open the side navigation menu.</p>
  <span style="font-size:30px;cursor:pointer" class="open">&#9776; open</span>-->

    <div id="map"></div>

  <?php include("footer.php") ?>
  </body>
</html>
