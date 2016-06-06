<?php include "include/header.php";
?>

<div  style="
margin-left: 1%;
float: left;
width: 100%;
margin-left: 0%;
margin-top: -15px;
margin-right: 0%;
margin-bottom: 5px;
">


   <img src="challenge-banner.png" tag="world" width="100%" alt="world"
 height="auto">

        </div>

        <div  style="background-color:#1BAB3F; width: 98%; margin-top: 15px;
        margin-left: 1%; height: auto; font-size: 15px; color: white; padding: 10px;">
        <b style="font-size: 18px; float: left;">

        Allgemeine Hinweise</b> 	&#x2003;&#x2003;

          <a href="javascript:void(0)" onclick="return toggleArrow(this, '#challengeinfo')" style="background-color: white; margin-top: 10px;
           border: 2px solid white; border-radius: 30px;"

          ><i class="fa fa-arrow-down"></i></a><br><br>
<span id="challengeinfo" style="display:none; margin-left: 2%; margin-right: 2%; text-align: justify; font-size: 11px; color: black;">Auf dieser Seite findet ihr alle Challenges,
  die ihr bei WeltFAIRsteher absolvieren könnt.
   In welcher Reihenfolge ihr dabei versucht, Aufgaben zu lösen, ist im Grunde egal. Ebenso könnt ihr selbst entscheiden, wie viele
   Challenges ihr in die Tat umsetzt.  Die sechs vorgegebenen Kategorien, aus denen ihr wählen könnt, sollen "Nachhaltigkeit" übrigens nicht umfassend abbilden.
Euch fehlt also eine Challenge? Wenn ihr selbst eine sinnvolle Idee habt, dann entwickelt daraus doch eine Eigenkreation
und lasst sie von eurer Lehrkraft im Lehrkraft-Bereich vorschlagen. Das geht auch unabhängig von den sechs Kategorien.<br><br>
Wenn ihr mehr Informationen zu einer Challenge wollt, so klickt auf den Titel und lest am besten auch die dazugehörige PDF-Datei,
die ihr jeweils herunterladen könnt. Wenn ihr eine Klasse in der Navigationsleiste auswählt, so seht ihr anhand der grünen Titel, welche
Challenges diese Klasse bereits absolviert hat. Wie viel Punkte ihr für eine
bestandene Challenge bekommt, könnt ihr an der Zahl neben dem Titel ablesen. <br><br>Übrigens: Bei einigen Challenges gibt es eine Zusatzoption, also
eine kleine Aufgabe, die auf der Challenge aufbaut und mit Extrapunkten belohnt wird. Mögliche Extrapunkte sind mit einem "+" gekennzeichnet, also etwa so: <span style="background-color: #0F9C2E; color: white;">+4</span>.
Eingeloggte Lehrkräfte können auf dieser Seite außerdem jede Challenge als <i>abgeschlossen</i> markieren. Aber Vorsicht: Wenn die Challenge mitsamt Zusatzaufgabe abgeschlossen wurde,
so muss die Lehrkraft die Challenge im Lehrkraft-Bereich als <i>abgeschlossen</i> eintragen. Nachträglich können keine Extrapunkte mehr für eine Challenge geltend gemacht werden, die bereits
eingetragen wurde.
Die folgenden Symbole zeigen euch außerdem, wo, beziehungweise mit wem ihr eine Challenge absolviert. <br>
<br>
<div class="container" style="width: 100%;">
  <div class="row">
    <div class="col-xs-4 col-sm-4 col-md-4">
<img src="symbols/school.png" alt="SCHULE" height="45px" width="45px">
<br><i><b>in der Schule, <br> aber ohne Lehrkraft</i></b><br></div>
  <div class="col-xs-4 col-sm-4 col-md-4">
<img src="symbols/teacher.png" alt="LEHRKRAFT" height="45px" width="45px"> <br><i><b>mit einer <br>Lehrkraft</b></i> <br></div>
  <div class="col-xs-4 col-sm-4 col-md-4">
<img src="symbols/home.png" alt="HAUS" height="45px" width="45px"><br> <i><b>zuhause</b></i> <br></div>
</div>
</div>
        </span></div>
<br>
<span style="width: auto; height: auto; margin-left: 35%; margin-right: auto; margin-bottom: 0px; background-color: yellow; color: black;">
  Die Challenges sind noch in Bearbeitung.</span>
<?php
function printChallenge($row) {
    global $db;
    # finc classes for challenge
    $classStmt = $db->prepare("SELECT cl.id FROM challenge as c
JOIN solved_challenge as sc ON c.id=sc.challenge
JOIN class as cl ON cl.id = sc.class
WHERE c.id = :id");

    $classStmt->execute(['id' => $row->id]);
    $classes = "";
    foreach($classStmt->fetchAll(PDO::FETCH_OBJ) as $classRow) {
        $classes = $classes . " class-" . e($classRow->id);
    }
?>

<div class=" challenge-location" >
    <img src="symbols/<?= e($row->location) ?>.png" alt="<?= e($row->location)?>" height="35px" width="35px">
</div>
    <div class="<?= e($row->category) ?> challenge-points" >
        <b style="font-family: Titillium Web;"><?= e($row->points)?></b>
    </div>


    <b><u><a class="<?= $classes ?> challenge-title"
             onclick="return toggleMe('challenge-<?=e($row->id)?>')"
             href="javascript:void(0)"
             style="font-family: Titillium Web;"><?=e($row->name)?></a></u></b>
             <div style="font-family: Titillium Web; font-size: 11px; margin-left: 94%; margin-top: 3px; float: left; position: relative; background-color: #0F9C2E;">
               <?php if($row->extrapoints) {echo "+" . e($row->extrapoints);}?></div>

               <br>
    <div style="display:none;" class="dbox" id="challenge-<?=e($row->id)?>">
      <br>
        <?= e($row->description) ?>
        <br>
        <?php if($row->author) { ?>
            <div style="color: black; font-family: Titillium Web;">Von:<b><?=e($row->author)?></b></div>
        <?php
        }
        // pdfs
        if(file_exists(getPDFPath($row->id, PUPIL_PDF))) {?>
            <div>
                <a href="#" onclick="downloadPDF(<?= e($row->id)?>, '<?=e(PUPIL_PDF)?>')" style="color: black; font-family: Titillium Web;"><b>Download:</b> Challenge-Beschreibung [PDF]</a>
            </div>
        <?php
        }
        if(isLoggedIn() && file_exists(getPDFPath($row->id, TEACHER_PDF))) {?>
            <div>
                <a href="#" onclick="downloadPDF(<?= e($row->id)?>, '<?=e(TEACHER_PDF)?>')" style="color: black; font-family: Titillium Web;"><b>Download:</b> Hinweise für Lehrkräfte [PDF]</a>
            </div>
        <?php } ?>
    </div>
    <?php if(isLoggedIn()) {?>
        <div class="solve-link <?= $classes ?>" >
            <a href="#" onclick="if(classNames[selectedClass] && confirm('Challenge \'<?=e($row->name)?>\' für Klasse \'' + classNames[selectedClass] + '\' abschließen (keine Extrapunkte)?'))callApi('solveChallenge', {'class': selectedClass, 'challenge': <?= e($row->id)?>})" style="color: black; font-family: Titillium Web;">Challenge abschließen!</a>
        </div>


    <?php } ?>
    <br><br>
<?php } ?>

<br>
<br>
<div class="container" style="width: 100%; margin-right: 1%;">
    <?php
$challengeStmt = $db->prepare("SELECT c.id, c.name, c.description, c.points, c.category, c.location, c.extrapoints, cl.name AS author
FROM challenge as c
LEFT JOIN class as cl ON cl.id = c.author
WHERE category=:category");

    $i = 0;
    define("NUM_COLS", 2);
    foreach($categories as $c) {
        if($i % NUM_COLS == 0 ) { ?>
        <div class="row">
    <?php } ?>
    <div class="col-xs-12 col-md-6 col-lg-6">
        <div class="challenge-header <?= e($c->name) ?>">
            <?= e($c->title) ?>
        </div>
        <div class="challenge-box">
            <?php
            $challengeStmt->execute(['category' => $c->name]);
            foreach($challengeStmt->fetchAll(PDO::FETCH_OBJ) as $row) {
                printChallenge($row);
            }
            ?>
        </div>
    </div>
    <?php
    if($i % NUM_COLS == NUM_COLS-1 || $i == count($categories)-1) { ?>
    </div>
    <?php
    }
    $i++;
    } ?>
</div>

<div class="selfmade-whole">
    Eigenkreationen
</div>
<div class="selfmade-box">
  <div class="container" style="width: 100%; margin-right: 1%;">

    <?php
    $challengeStmt->execute(['category' => "selfmade"]);

    $i = 0;
    $cols = $challengeStmt->fetchAll(PDO::FETCH_OBJ);
    foreach($cols as $col) {
        if($i % NUM_COLS == 0 ) {
    ?>
        <div class="row">
    <?php } ?>
    <div class="col-xs-12 col-md-6 col-lg-6">
        <div class="challenge-box">
            <?php
            printChallenge($col);
            ?>
        </div>
    </div>
    <?php
    if($i % NUM_COLS == NUM_COLS-1 || $i == count($cols)-1) { ?>
        </div>
    <?php
    }
    $i++;
    }
    ?>



</div>
</div>

</div>

<?php include "include/footer.php" ?>
