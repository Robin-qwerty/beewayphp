<?php if (isset($_SESSION['userrole']) && isset($_SESSION['userid']) && $_SESSION['userrole'] == 'admin') { // check if user is logedin ?>
  <div class="beewaylijst">
      <?php if ($_SESSION['userrole'] == "superuser") { ?>
        <div class="beewaylijsttitel"><h1>Welkom op het super user dashboard</h1></div>
        <h2>beheer hier dingen (:</h2>

        <div class="beewaylijstopties">
          <button onclick="window.location.href='index.php?page=userlijst';" id="beewaylijstopties5">Users</button>
          <b>|</b>
          <button onclick="window.location.href='index.php?page=scholenlijst';" id="beewaylijstopties5"><u>Scholen</u></button>
          <b>|</b>
          <button onclick="window.location.href='index.php?page=logslijst';" id="beewaylijstopties5">Site Logs</button>
      <?php } else if ($_SESSION['userrole'] == "admin") {?>
        <div class="beewaylijsttitel"><h1>Welkom op het admin dashboard</h1></div>
        <h2>beheer hier dingen (:</h2>

        <div class="beewaylijstopties">
          <button onclick="window.location.href='index.php?page=beewaylijst';" id="beewaylijstopties1">Beeway's</button>
          <b>|</b>
          <button onclick="window.location.href='index.php?page=klassenlijst';" id="beewaylijstopties4">Groepen/Klassen</button>
          <b>|</b>
          <button onclick="window.location.href='index.php?page=vakkenlijst';" id="beewaylijstopties2"><u>Vakken</u></button>
          <b>|</b>
          <button onclick="window.location.href='index.php?page=hoofdthemalijst';" id="beewaylijstopties3">Hoofdthema's</button>
          <b>|</b>
          <button onclick="window.location.href='index.php?page=userlijst';" id="beewaylijstopties5">Users</button>
      <?php } else { ?>
        <div class="beewaylijsttitel"><h1>Welkom op het docenten dashboard</h1></div>
        <h2>beheer hier dingen (:</h2>
        <div class="beewaylijstopties">
          <button onclick="window.location.href='index.php?page=beewaylijst';" id="beewaylijstopties1">Beeway's</button>
      <?php } ?>
    </div>
    <hr>
    <br>
      <?php
        if (isset($_GET['offset'])) {
          $offset = $_GET['offset'] * 25;
        } else {
          $sql = 'SELECT * FROM disciplines
                  WHERE archive=1
                  LIMIT 25';
          $sth = $conn->prepare($sql);
          $sth->execute();
        }
        if ($sth->rowCount() > 0) {
          echo '<table class="beewaylijsttable">
            <tr>
              <th><h3>vak</h3></th>
            </tr>';
          while ($disciplines = $sth->fetch(PDO::FETCH_OBJ)) {
            echo'
              <tr>
                <td><b>'.$disciplines->disciplinename.'</b></td>
              </tr>
            ';
          }
          echo '</table>

          <div class="tablebuttons">';
            if (isset($_GET['offset'])) {
              $terug = $_GET['offset'] - 1;
              $volgende = $_GET['offset'] + 1;
              if ($_GET['offset'] == '0') {
                // echo '
                //   <a href="index.php?page=scholenlijst&offset='.$volgende.'" class="addbutton">volgende</a>
                // ';
              } else {
                // echo '
                //   <a href="index.php?page=scholenlijst&offset='.$terug.'" class="addbutton">terug</a>
                //   <a href="index.php?page=scholenlijst&offset='.$volgende.'" class="addbutton">volgende</a>
                // ';
              }
            } else {
              // echo '
              //   <a href="index.php?page=scholenlijst&offset=1" class="addbutton">volgende</a>
              // ';
            }
          echo '</div>';
        } else {
          // the query did not return any rows
          echo '<h2><strong>the query did not return any rows</strong></h2>';
          if (isset($_GET['offset']) && $_GET['offset'] >= '1') {
            $terug = $_GET['offset'] - 1;

            echo '<div class="tablebuttons"><a href="index.php?page=scholenlijst&offset='.$terug.'" class="addbutton">terug</a></div>';
          } else if (isset($_GET['offset'])) {
            echo '<div class="tablebuttons"><a href="index.php?page=scholenlijst" class="addbutton">terug</a></div>';
          }
          $_SESSION['error'] = "the query did not return any rows. Pech!";
        }
      ?>
    <hr>
  </div>
  
<?php
  } else {
    $_SESSION['error'] = "er ging iets mis. Pech!";
    header("location: index.php?page=dashboard");
    exit;
  }

  require_once 'include/info.inc.php';
  require_once 'include/error.inc.php';
?>
