<?php
  if (isset($_SESSION['userid']) && isset($_SESSION['userrole']) && $_SESSION['userrole'] == 'admin' || $_SESSION['userrole'] == 'docent') { // check if user is logedin
    if (isset($_GET['beewayid']) && $_GET['beewayid'] > 0) {
      // Retrieve the school ID of the logged-in user
      $loggedInUserID = $_SESSION['userid'];

      // Modify this section to fetch the school ID from the users table
      $stmt = $conn->prepare("SELECT schoolid FROM users WHERE userid = :userid");
      $stmt->bindValue(':userid', $loggedInUserID, PDO::PARAM_INT);
      $stmt->execute();
      $loggedInUserSchoolID = $stmt->fetchColumn();

      // Retrieve the school ID of the BeeWay
      $beewayID = $_GET['beewayid'];

      // Modify this section to fetch the school ID from the beeway table
      $stmt = $conn->prepare("SELECT schoolid FROM beeway WHERE beewayid = :beewayid");
      $stmt->bindValue(':beewayid', $beewayID, PDO::PARAM_INT);
      $stmt->execute();
      $beewaySchoolID = $stmt->fetchColumn();

      // Check if the user's school ID does not match the BeeWay's school ID
      if ($loggedInUserSchoolID != $beewaySchoolID) {
        $_SESSION['error'] = "Je hebt geen toegang tot deze beeway of deze beeway bestaad niet. Pech!";
        header("Location: index.php?page=beewaylijst");
        exit;
      }

      $sql = 'SELECT schoolid
             FROM users
             WHERE schoolid<>0
             AND archive<>1
             AND userid=:userid';
      $sth = $conn->prepare($sql);
      $sth->bindValue(':userid', $_SESSION['userid']);
      $sth->execute();

      $result = $sth->fetch(); // Fetch the result from the executed query
      $schoolid = $result['schoolid']; // Access the schoolid value from the result array

      $sql = 'SELECT * FROM beeway
              WHERE beewayid=:beewayid';
      $sth = $conn->prepare($sql);
      $sth->bindParam(':beewayid', $_GET['beewayid']);
      $sth->execute();

      if ($beeway = $sth->fetch(PDO::FETCH_OBJ)) {

        echo'
        <div class="beewayedit">
            <div><h2>'.$beeway->beewayname.'</h2></div>
            <div>
        ';

        $sql1 = 'SELECT firstname, lastname FROM users WHERE userid = :userid1
                UNION ALL
                SELECT firstname, lastname FROM users WHERE userid = :userid2';
        $sth1 = $conn->prepare($sql1);
        $sth1->bindParam(':userid1', $beeway->createdby);
        $sth1->bindParam(':userid2', $beeway->updatedby);
        $sth1->execute();

        $y = 1;

        while ($editedby = $sth1->fetch(PDO::FETCH_OBJ)) {
          if ($y == 1) {
            echo'<p>Aangemaakt door: <b>'.$editedby->firstname.' '.$editedby->lastname.'</b></p>';
          } else {
            echo'<p>Als laast bewerkt door: <b>'.$editedby->firstname.' '.$editedby->lastname.'</b></p>';
          }

          $y++;
        }
      }
      echo '
      </div>
    </div>

    <hr>';
    // Controleren of het BeeWay ID in de URL is opgegeven
    if(isset($_GET['beewayid'])) {
        $beewayId = $_GET['beewayid'];

        // Query uitvoeren om de planninggegevens op te halen
        $planningSql = "SELECT * FROM beewayplanning WHERE beewayid = :beewayid LIMIT 8";
        $planningStmt = $conn->prepare($planningSql);
        $planningStmt->bindParam(':beewayid', $beewayId);
        $planningStmt->execute();
        $planningResult = $planningStmt->fetchAll(PDO::FETCH_ASSOC);

        // Query uitvoeren om de observatiegegevens op te halen
        $observationSql = "SELECT * FROM beewayobservation WHERE beewayid = :beewayid";
        $observationStmt = $conn->prepare($observationSql);
        $observationStmt->bindParam(':beewayid', $beewayId);
        $observationStmt->execute();
        $observationResult = $observationStmt->fetchAll(PDO::FETCH_ASSOC);

        // Controleren of er resultaten zijn voor de planning
        if (!empty($planningResult)) {
          ?>
          <div class="helebeeway">
            <div id="grid-line">
              <div class="cell BEEWAY">
                <h1>[naam]</h1>
                <h2>Iedere dag ’n beetje beter</h2>
                <div id="groepen">
                  <label>Groepen</label>
                  <?php
                    $sql = 'SELECT `groups` FROM groups WHERE groupid = :groupid';
                    $sth = $conn->prepare($sql);
                    $sth->bindValue(':groupid', $beeway->groupid);
                    $sth->execute();

                    if ($group = $sth->fetch(PDO::FETCH_OBJ)) {
                      echo '<div class="editable-text">' . $group->groups . '</div>';
                    } else {
                      echo '<div class="editable-text">Group Not Found</div>';
                    }
                  ?>
                </div>
              </div>

              <div class="cell HOOFDTHEMA">
                <h2 id="orange">HOOFDTHEMA</h2>
                <div>
                  <input type="radio" name="hoofdthemaid" value="1" <?php echo isset($beeway->themeperiodid) && $beeway->themeperiodid == 1 ? 'checked' : ''; ?> disabled>
                  <label for="html">P1: EDI</label>
                  <br>
                  <input type="radio" name="hoofdthemaid" value="2" <?php echo isset($beeway->themeperiodid) && $beeway->themeperiodid == 2 ? 'checked' : ''; ?> disabled>
                  <label for="html">P2: BEGELEIDENDE INOEFENING</label>
                  <br>
                  <input type="radio" name="hoofdthemaid" value="3" <?php echo isset($beeway->themeperiodid) && $beeway->themeperiodid == 3 ? 'checked' : ''; ?> disabled>
                  <label for="html">P3: LEZEN</label>
                  <br>
                  <input type="radio" name="hoofdthemaid" value="4" <?php echo isset($beeway->themeperiodid) && $beeway->themeperiodid == 4 ? 'checked' : ''; ?> disabled>
                  <label for="html">P4: DIFFERENTIATIE EDI</label>
                  <br>
                  <input type="radio" name="hoofdthemaid" value="5" <?php echo isset($beeway->themeperiodid) && $beeway->themeperiodid == 5 ? 'checked' : ''; ?> disabled>
                  <label for="html">P5: DOELENPLANNER</label>
                  <br>
                </div>
              </div>

              <div class="cell CONCREETDOEL">
                <h2 id="orange">CONCREET DOEL</h2>
                <div class="editable-text"><?php echo $beeway->concretegoal; ?></div>
              </div>

              <div class="cell beoordeling">
                <div class="beoordeling-item beoordeling-item1"><iconify-icon icon="fa:smile-o" style='font-size:62px'></iconify-icon></div>
                <div class="beoordeling-item beoordeling-item2">
                  <div class="editable-text"><?php echo $beeway->begood; ?></div>
                </div>
                <div class="beoordeling-item beoordeling-item3"><iconify-icon icon="fa:meh-o" style='font-size:62px'></iconify-icon></div>
                <div class="beoordeling-item beoordeling-item4">
                  <div class="editable-text"><?php echo $beeway->beenough; ?></div>
                </div>
                <div class="beoordeling-item beoordeling-item5"><iconify-icon icon="fa:frown-o" style='font-size:62px'></iconify-icon></div>
                <div class="beoordeling-item beoordeling-item6">
                  <div class="editable-text"><?php echo $beeway->benotgood; ?></div>
                </div>
              </div>


              <div class="cell vakgebied">
                <h2 id="orange">VAKGEBIED</h2>
                <select name="vakgebiedid" id="vakgebied" required disabled style="color: black;">
                  <option value="">-- vakgebied niet gevonde --</option>
                  <?php
                    $sql = 'SELECT disciplinename, disciplineid
                            FROM disciplines
                            WHERE archive=0
                            AND schoolid=:schoolid
                            AND disciplineid=:disciplineid';
                    $sth = $conn->prepare($sql);
                    $sth->bindValue(':schoolid', $schoolid);
                    $sth->bindValue(':disciplineid', $beeway->disciplineid);
                    $sth->execute();

                    while ($disciplines = $sth->fetch(PDO::FETCH_OBJ)) {
                      $selected = isset($beeway->disciplineid) && $beeway->disciplineid == $disciplines->disciplineid ? 'selected="selected"' : '';
                      echo '<option value="'.$disciplines->disciplineid.'" '.$selected.'>'.$disciplines->disciplinename.'</option>';
                    }
                  ?>
                </select>
              </div>
            </div>
          <?php
            // Een formulier genereren met de planninggegevens
            echo "<table>
                    <tr>
                        <th>Planning</th>
                        <th>Planning Wat</th>
                        <th>Planning Wie</th>
                        <th>Planning Deadline</th>
                        <th>Planning Klaar</th>
                    </tr>";

            // Gegevens weergeven in de tabel voor de planning
            $desiredRowCount = 8;
            for ($i = 1; $i <= $desiredRowCount; $i++) {
                $row = isset($planningResult[$i - 1]) ? $planningResult[$i - 1] : array('planningid' => '', 'planning' => '', 'planningwhat' => '', 'planningwho' => '', 'planningdeadline' => '', 'planningdone' => '');
                echo "<tr>
                        <td><div class='editable-text'>" . $row['planning'] . "</div></td>
                        <td><div class='editable-text'>" . $row['planningwhat'] . "</div></td>
                        <td><div class='editable-text'>" . $row['planningwho'] . "</div></td>
                        <td><div class='editable-text'>" . $row['planningdeadline'] . "</div></td>
                        <td><div class='editable-text checkbox-text'>" . ($row['planningdone'] == '1' ? '&#x2713;' : '') . "</div></td>
                    </tr>";
            }
            echo "</table>";
            echo "<br>";
            echo "
            <style>
              .editable-text {
                  display: inline-block;
                  border: 1px solid #ccc;
                  padding: 5px;
                  min-width: 100px;
              }

              .checkbox-text {
                  text-align: center;
              }
            </style>
            ";
        } else {
            $_SESSION['error'] = "Geen planninggegevens gevonden. Pech!";
            echo "<br> Geen planninggegevens gevonden.";
        }

        // Controleren of er resultaten zijn voor de observatie
        if (!empty($observationResult)) {
          // Een formulier genereren met de observatiegegevens
          echo "<table>
                  <tr>
                      <th>Dataclass</th>
                      <th>Leerdoel</th>
                      <th>Evaluatie</th>
                      <th>Werkdoel</th>
                      <th>Actie</th>
                  </tr>";

          // Gegevens weergeven in de tabel voor de observatie
          $desiredRowCount = 8;
          for ($i = 1; $i <= $desiredRowCount; $i++) {
              $row = isset($observationResult[$i - 1]) ? $observationResult[$i - 1] : array('observationid' => '', 'dataclass' => '', 'learninggoal' => '', 'evaluation' => '', 'workgoal' => '', 'action' => '');
              echo "<tr>
                      <td><div class='editable-text'>" . $row['dataclass'] . "</div></td>
                      <td><div class='editable-text'>" . $row['learninggoal'] . "</div></td>
                      <td><div class='editable-text'>" . $row['evaluation'] . "</div></td>
                      <td><div class='editable-text'>" . $row['workgoal'] . "</div></td>
                      <td><div class='editable-text'>" . $row['action'] . "</div></td>
                  </tr>";
          }

          echo "</table>";
          echo "<br>";
        } else {
          $_SESSION['error'] = "Geen observatiegegevens gevonden. Pech!";
          echo "<br> Geen observatiegegevens gevonden.";
        }

        // De knop weergeven voor het bijwerken van zowel planning als observatie
        // echo "<input type='submit' name='updateData' value='Update Planning en Observatie'>";
    }

    // Controleren of het formulier voor het bijwerken van planning en observatie is verzonden
    if(isset($_POST['updateData'])) {
        $planningData = $_POST['planning'];
        $observationData = $_POST['observation'];

        // Voorbereiden van de update query voor planning
        $updatePlanningStmt = $conn->prepare("UPDATE beewayplanning SET planning = :planning, planningwhat = :planningwhat, planningwho = :planningwho, planningdeadline = :planningdeadline, planningdone = :planningdone WHERE planningid = :planningid");

        // Bijwerken van de planninggegevens
        foreach($planningData as $planningId => $data) {
          $planning = isset($data['planning']) ? $data['planning'] : '';
          $planningWhat = isset($data['planningwhat']) ? $data['planningwhat'] : '';
          $planningWho = isset($data['planningwho']) ? $data['planningwho'] : '';
          $planningDeadline = isset($data['planningdeadline']) ? $data['planningdeadline'] : '';
          $planningDone = isset($data['planningdone']) ? '1' : '0';

          $updatePlanningStmt->bindParam(':planningid', $planningId);
          $updatePlanningStmt->bindParam(':planning', $planning);
          $updatePlanningStmt->bindParam(':planningwhat', $planningWhat);
          $updatePlanningStmt->bindParam(':planningwho', $planningWho);
          $updatePlanningStmt->bindParam(':planningdeadline', $planningDeadline);
          $updatePlanningStmt->bindParam(':planningdone', $planningDone);

          $updatePlanningStmt->execute();
        }

        // Voorbereiden van de update query voor observatie
        $updateObservationStmt = $conn->prepare("UPDATE beewayobservation SET dataclass = :dataclass, learninggoal = :learninggoal, evaluation = :evaluation, workgoal = :workgoal, action = :action WHERE observationid = :observationid");

        // Bijwerken van de observatiegegevens
        foreach($observationData as $observationId => $data) {
          $dataClass = isset($data['dataclass']) ? $data['dataclass'] : '';
          $learningGoal = isset($data['learninggoal']) ? $data['learninggoal'] : '';
          $evaluation = isset($data['evaluation']) ? $data['evaluation'] : '';
          $workGoal = isset($data['workgoal']) ? $data['workgoal'] : '';
          $action = isset($data['action']) ? $data['action'] : '';

          $updateObservationStmt->bindParam(':observationid', $observationId);
          $updateObservationStmt->bindParam(':dataclass', $dataClass);
          $updateObservationStmt->bindParam(':learninggoal', $learningGoal);
          $updateObservationStmt->bindParam(':evaluation', $evaluation);
          $updateObservationStmt->bindParam(':workgoal', $workGoal);
          $updateObservationStmt->bindParam(':action', $action);

          $updateObservationStmt->execute();
        }
        echo "Planning en observatiegegevens zijn succesvol bijgewerkt.";
    }

    } else {
      $_SESSION['error'] = 'Failed to get beeway';
      header('Location: index.php?page=beewaylijst');
      exit;
    }
  } else {
    $_SESSION['error'] = "er ging iets mis. Pech!";
    header("Location: index.php?page=dashboard");
    exit;
  }

  require_once 'include/info.inc.php';
  require_once 'include/error.inc.php';
?>
