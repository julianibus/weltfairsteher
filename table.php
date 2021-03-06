<?php
include "include/header.php";

include "include/chart.php";

?>
<script src="libs/vue.js"></script>
<script src="js/table.js"></script>
  <section  class="sectionbg" style="background-color: #F2F2DA;">
<br>
<br>
<br>
<div  style="
             z-index: 5;
             margin-left: 0%;
             float: left;
             width: 100%;
             margin-right: 0%;

             margin-top: -75px;

             margin-bottom: 10px;
             ">


    <img src="table-banner.png" tag="kompass" width="100%" alt="kompass"
         height="auto">

</div>
<?php

// classes and getCurrentPoints is from chart.php
usort($classes, function($a, $b) {
    return getCurrentPoints($b) - getCurrentPoints($a);
});
?>

<form method="POST">
    <select id="class-select" name="klasse" size="1" style="color: black; margin-left: 1%;">
        <option value="default">Wähle eine Klasse</option>
        <?php
        $classStmt = $db->prepare("SELECT id, name FROM class");
        $classStmt->execute();
        foreach($classStmt->fetchAll(PDO::FETCH_OBJ) as $row) {
        ?>
            <option value="<?= e($row->id) ?>"><?= e($row->name) ?></option>
        <?php } ?>
    </select>
</form>
<br>
<div class="container" style="width: 98%;" id="vue-root">
    <div id="fortschritt-message" class="row" v-if="!cclass">
        <h3 style="color: black;">
            Alle Klassen haben gemeinsam <b style="font-size: 24pt;"><?= e($numSolvedChallenges) ?></b> Challenges absolviert. <br>
            Wähle eine Klasse, um mehr zu erfahren.
        </h3>
    </div>

    <div id="fortschritt-content" class="row" v-if="cclass">

        <div class="col-xs-12 col-sm-8 col-md-8" >

            <b style="font-size: 18px;">Bestandene Challenges je Kategorie</b><br><br>
            <!--Je Kategorie: Symbol -> Teil-transparenter Balken, dessen Breite die Anzahl der möglichen Challenges repräsentiert und darin ein
                 nicht transparenter Balken (Kategorie-Farbe), dessen Breite die Anzahl der bestandenen Challenges der Klasse repräsentiert. Ganz rechts im
                 teil-transparenten Balken steht bspw 5/9 für "5 von 9 Challenges dieser Kategorie bestanden". Beide Balken besitzen die gleiche Höhe von 30px.
                 Die Breite beträgt die Anzahl der Challenges der Kategorie mal 8%, bei Ernährung also 4 mal 8% = 32%. Diese Werte (Breite der Balken und Zahlen)
                 müssen automatisch erstellt werden, sodass auf Änderungen selbständig reagiert wird (v.a. bei den Eigenkreationen und der Kreativität von Vorteil)
               -->
<br>
            <div v-for="c in categories">
                <div :title="c.title">
                    <img :src="'symbols/symbol-' + c.name + '2.png'"
                         style="margin-right: 5%; margin-top: -24px; float: left;" width="15%;" height="auto;" />
                    <div :class="c.name + '-fortschritt-bg'" style="position: relative; height: 40px; margin-left: 18%;"
                         :style="{width: calcBgWidth(c.num)*0.7 + '%'}">
                    <span style="float: right; margin-right: 5px; font-size: 28px; color: white;">{{cclass.solved[c.name]}}/{{c.num}}</span>
                    <div :class="c.name + '-fortschritt-fg'"
                         style="height: 40px;"
                         :style="{width: (cclass.solved[c.name] / c.num * 100) + '%'}"></div>
                    </div>
                </div>
                <br><br><br><br>
            </div>


        </div>

        <div class="col-xs-12 col-sm-4 col-md-4" >
            <b><h2 id="class-name" style="color: #595959; float: left; margin-top: -6px;">{{cclass.name}}</h2></b>
            <br><br><br><br>

            <span title="Anzahl der Eigenkreationen"><b style="font-size: 18px;">Eigenkreationen</b></span><br><br>

            <div class="fortschritt-circle" style="margin-top: 50px; margin-right: 5%; font-size: 21px;">
                {{cclass.numCreativity}}/<?= e(MAX_SELFMADE_PER_CLASS)?>
            </div>
            <div title="Eigenkreationen">
                <img alt="Kreativität" :src="'symbols/creativity-' + cclass.numCreativity + '.png'" tag="creativity" style="float: left;"/>
            </div>

            <div style="clear: left;"></div><br><br><br><br>

            <b style="font-size: 18px; margin-top: 5px;"> Punkte bis zur nächsten Etappe </b><br><br><br>
            <span class="fortschritt-circle">
                {{nextMilestone}}
            </span>
            <br><br><br><br><br><br>

            <span>
                <b style="font-size: 18px; margin-top: 5px;">  Gesamtpunktzahl </b><br><br><br>
                <span class="fortschritt-circle">
                    {{currentPoints}}
                </span>
            </span>
        </div>
    </div>
</div>
<br><br><br>
<br><br><br>
<br><br><br>
<br><br><br>
<br><br><br>
<br><br><br>

<!--

<span class="indexlink" style="color: white; background-color: #05661D; margin-left: auto; margin-right: auto; display: block; text-align: center;" > <a onclick="return toggleMe('chart')"
href="javascript:void(0)" style="color: white;"><span data-title="Zeitlichen Verlauf anzeigen">
Zeitlichen Verlauf anzeigen
</span></a></span>

<div id="chart" class="abstaende" style="width: 68%; margin-top: 25px; position: relative; display: none;"></canvas>
<script type="text/javascript">
 $('document').ready(function() {
     var chart = new LineChart("chart");
 });
</script></div>


Liniendiagramm einfügen, das die gewählte Klasse highlighted: abszisse: 1. Tag bis heute  -  ordinate: punkte (0 bis max) -->



</section>
<?php include "include/footer.php" ?>
