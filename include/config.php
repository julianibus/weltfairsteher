<?php
if(!defined('CONFIG_PHP')) {
    define('CONFIG_PHP', true);

    session_start();
    include __DIR__."/secrets.php";

    // database connection
    $db = new PDO(DB_CONNECTION_NAME, DB_CONNECTION_USER, DB_CONNECTION_PW,
                  array(PDO::ATTR_EMULATE_PREPARES => false,
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));


    // categories
    class Category {
        public $name, $title;
        function __construct($name, $title) {
            $this->name = $name;
            $this->title = $title;
        }
    }
    $categories = [
        new Category("food", 'ERNÄHRUNG'),
        new Category("water", "WASSER & RESSOURCEN"),
        new Category("culture", "SOZIALE VERANTWORTUNG"),
        new Category("climate-change", "KLIMAWANDEL"),
        new Category("production", "PRODUKTION & KONSUM"),
        new Category("energy", "ENERGIE & MOBILITÄT"),
    ];
    $leckerwissenTypes = [
        ['name' => 'article', "desc" => "Artikel"],
        ['name' => 'video', "desc" => "Videos"],
        ['name' => 'other', "desc" => "Weiteres"]
    ];
    $locationTypes = [
        ['name' => "home", "desc" => "Zuhause"],
        ["name" => "school", "desc" => "Schule ohne Lehrkraft"],
        ["name" => "teacher", "desc" => "Schule mit Lehrkraft"]
    ];

    define("MAX_SELFMADE_PER_CLASS", 5);

    // from http://stackoverflow.com/questions/1243418/php-how-to-resolve-a-relative-url
    function rel2abs($rel, $base)
    {
        /* return if already absolute URL */
        if (parse_url($rel, PHP_URL_SCHEME) != '' || substr($rel, 0, 2) == '//')
            return $rel;

        /* queries and anchors */
        if ($rel[0]=='#' || $rel[0]=='?') return $base.$rel;

        /* parse base URL and convert to local variables:
           $scheme, $host, $path */
        extract(parse_url($base));

        /* remove non-directory element from path */
        $path = preg_replace('#/[^/]*$#', '', $path);

        /* destroy path if relative url points to root */
        if ($rel[0] == '/') $path = '';

        /* dirty absolute URL */
        $abs = "$host$path/$rel";

        /* replace '//' or '/./' or '/foo/../' with '/' */
        $re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
        for($n=1; $n>0; $abs=preg_replace($re, '/', $abs, -1, $n)) {}

        /* absolute URL is ready! */
        return $scheme.'://'.$abs;
    }

    # escape
    function e($str) {
        return htmlspecialchars($str, ENT_QUOTES, "UTF-8");
    }

    function fetchAll($query, $params = []) {
        global $db;
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    function fetch($query, $params = []) {
        global $db;
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    function dbExists($query, $params = []) {
        global $db;
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch() !== false;
    }

    function dbExecute($query, $params = []) {
        global $db;
        $stmt = $db->prepare($query);
        $stmt->execute($params);
    }

    function isAdmin() {
        global $_SESSION;
        return isLoggedIn() && $_SESSION["role"] == 2;
    }
    function isTeacher() {
        global $_SESSION;
        return isLoggedIn() && $_SESSION["role"] == 1;
    }
    function isLoggedIn() {
        global $_SESSION;
        return isset($_SESSION["role"]);
    }

    define("TEACHER_PDF", "TEACHER");
    define("PUPIL_PDF", "PUPIL");
    define("MAX_PDF_SIZE", 50000000);

    function getPDFPath($challenge, $type) {
        assert($type === TEACHER_PDF || $type === PUPIL_PDF);
        assert(is_numeric($challenge));
        return __DIR__."/../uploads/" . $type . "_" . $challenge . ".pdf";
    }

    function getPicturePath($challenge) {
        assert(is_numeric($challenge));
        # remember to also change challenge.php, when changing this
        return __DIR__. "/../challenge-bilder/challenge-" . $challenge . ".png";
    }



    function own_mail($to, $title, $content) {
        // für umlaute
        $headers   = [
            "MIME-Version: 1.0",
            "Content-type: text/plain; charset=utf-8",
            "From: kontakt@weltfairsteher.de",
            "Reply-To: kontakt@weltfairsteher.de"
        ];
        mail($to, $title, $content, implode("\r\n", $headers));
    }

    function calculatePoints($classes) {
        global $db;
        $startTime = mktime(0, 0, 0, 10, 1, 2016);
        $secondsPerDay = 60 * 60 * 24;

        $wasArray = is_array($classes);
        if(!$wasArray) {
            $classes = [$classes];
        }

        $challengeStmt = $db->prepare("
SELECT c.points, c.extrapoints, sc.extra, sc.at, 0 AS creativity FROM solved_challenge AS sc
JOIN challenge AS c ON c.id = sc.challenge
WHERE sc.class = :id
UNION
SELECT 0 AS points, 0 AS extrapoints, false AS extra, author_time AS at, 1 AS creativity FROM challenge
WHERE author = :id2
ORDER BY at
");
        $ret = [];
        foreach($classes as $class) {
            // php is strange, can't use param multiple times
            $challengeStmt->execute(['id' => $class, 'id2' => $class]);

            $history = array();
            $points = 0;
            $creativity = 1;
            $numCreativity = 0;
            $curday = $startTime;
            foreach(array_merge($challengeStmt->fetchAll(PDO::FETCH_ASSOC),
                                [["points" => 0,
                                  "extra" => false,
                                  "creativity" => 0,
                                  "at" => date("Y-m-d H:i:s", time() + $secondsPerDay)]])
                as $ch) {
                while(strtotime($ch["at"]) >= $curday) {
                    array_push($history, round($points * $creativity, 1));
                    $curday += $secondsPerDay;
                }
                $points += $ch["points"];
                if($ch["extra"] && $ch["extrapoints"]) {
                    $points += $ch["extrapoints"];
                }
                $creativity += $ch["creativity"] * 0.1;
                $numCreativity += $ch["creativity"];
            }
            $ret[$class] = ["points" => $history,
                            "creativity" => $creativity,
                            "numCreativity" => $numCreativity,
                            "id" => $class];
        }

        if(!$wasArray) {
            return array_pop($ret);
        }
        return $ret;
    }
}

?>
