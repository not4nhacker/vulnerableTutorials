<html>
    <head>
        <link rel="stylesheet" type="text/css" href="../general.css">
        <link rel="stylesheet" type="text/css" href="../resources/containers.css">
    </head>

    <body>
        <header>
            <h1 id="title">Open Knowledge</h1>
            <nav id="navbar">
                <a class="navlink" href="plans.php">Acquista un piano</a>
                <a class="navlink" href="courses.php">Inizia un corso</a>
                <a class="navlink" href="profile.php">Il mio profilo</a>
            </nav>
        </header>
    </body>
</html>



<?php
    /*Questa pagina mostra all'utente tutti i corsi disponibili per il suo piano */
    /*INIZIO CONTROLLO DELLA SESSIONE*/
    session_start();
    
    /*Controllo del fatto che il login sia stato effettuato
    * Si controlla che il cookie di sessione e le variabili di sessione siano settate*/ 
    if(!isset($_COOKIE["session"]) || !isset($_SESSION["userId"]) || !isset($_SESSION["username"])) {
        die("Sembra che tu non abbia effettuato il login");
    }


    //Connessione al Database
    $servername = "localhost";
    $dataBaseName = "TutorialEnrietti";
    $conn = new mysqli($servername, "root", "", $dataBaseName);

    if ($conn->connect_error) {
        die("Problemi nella connessione al DataBase <br> " . $conn->connect_error);
    }


    /*Estrazione della password dal Database
    * La password estratta servirà per controllare che il cookie di sessione sia valido*/
    $userId = $_SESSION["userId"];
    $user = $_SESSION["username"];
    $loginQuery = "SELECT password FROM users WHERE userId = ?;";
    $result = $conn->prepare($loginQuery);
    $result->bind_param("s", $userId);
    $result->execute();
    $result->bind_result($dbPass);
    $result->fetch();
    $result->close();

    /*Controllo di validità del Cookie di sessione
    * Inizialmente andiamo ad unire username e password per poi verificare che il loro hash corrisponda al cookie di sessione*/
    $userInfo = $user . $dbPass;
    if(!password_verify($userInfo, $_COOKIE["session"])) {
        die("Sembra che tu stia provando ad accedere alla sessione di un altro utente");
    }

    /*FINE CONTROLLO DELLA SESSIONE*/

    /**Estra dalla tabella dei corsi il numero di tutti i corsi eccetto quello Root Only */
    $numberCourseQuery = "SELECT COUNT(courseId) FROM COURSES WHERE courseId != 10";
    $result = $conn->prepare($numberCourseQuery);
    $result->execute();
    $result->bind_result($numberCourse);
    $result->fetch();
    $result->close();
    echo $numberCourse;
    echo "<div id='container'>";

    /*Estrazione dal Database dei corsi iniziati dall'utente
    * Gli id verranno salvati in un Array e utilizzati per controllarli con gli id dei corsi estratti successivamente
    * Quando gli id saranno diversi vorrà dire che il corso non sarà iniziato, e potrà quindi essere stampato per l'utente*/
    $courseIdQuery = "SELECT courseId FROM startCourse WHERE userId = ?;";
    $result = $conn->prepare($courseIdQuery);
    $result->bind_param("s", $userId);
    $result->execute();
    $result->store_result();
    $result->bind_result($courseId);
    
    if($result->num_rows < $numberCourse) { //Controlla che l'utente non abbia iniziato tutti i corsi
        //Dichiarazione dell'array vuoto che andremo a riempire con tutti gli Id dell'utente
        $startedCoursesId = array();
    
        /*Ciclo che utilizzeremo per rimepire l'array di tutti gli id dei corsi dell'utente da usare successivamente*/
        while($result->fetch()) {
            array_push($startedCoursesId, $courseId);
        }
        $result->close();


        $plan = $_SESSION["idPlanUser"];

        /*Query che estrae tutti gli id, i titoli e le descrizioni dei corsi disponibili per il piano dell'utente */
        $coursesQuery = "SELECT courseId, title, description FROM courses WHERE coursePlan <= ?";
        $result = $conn->prepare($coursesQuery);
        $result->bind_param("s", $plan);
        $result->execute();
        $result->store_result();
        $result->bind_result($courseId, $title, $description);

        if($result->num_rows() == 0) {
            echo "<section id='courses'>
            <h2>Non hai ancora acquistato un piano</h2>
            <p>Clicca qui sotto per acquistare un nuovo piano</p>
            <a class='links' href='plans.php'>Acquista un piano</a>
            </section>";
        } else {
            echo "<section id='courses'>
                <h2>Corsi disponibili</h2>";

            /*Ciclo che va ad inserire negli array tutti i titoli e le descrizioni*/
            $flag = true; //Questa variabile verrà utilizzata per controllare che sia possibile effettuare la stampa: TRUE = Si effettua la stampa, FALSE = Non si effettua la stampa
            while($result->fetch()) {
        
                for($i = 0; $i < count($startedCoursesId); $i++) {
                    if($startedCoursesId[$i] == $courseId) { //Se questo controllo è positivo l'utente ha già iniziato questo corso
                        $flag = false;
                    }
                }

                if($flag) {
                    echo "<h3>$title</h3>
                    <p>$description</p>
                    <a class='links' href='startCourse.php?courseId=$courseId'>Clicca qui per iniziare</a>";
                }

                $flag = true;
            }

            echo "</section>";
        }
    } else {
        echo "<section id='courses'>
        <h2>Hai iniziato tutti i corsi disponibili nel tuo piano</h2>
        <p>Non hai corsi disponibili per il tuo piano, clicca qui sotto per acquistare un altro piano</p>
        <a class='links' href='plans.php'>Acquista un piano</a>";
    }

    echo "</div>";
    $result->close();
?>