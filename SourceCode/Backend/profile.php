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
    /*Pagina del profilo dell'utente, contiene il suo piano e i corsi già iniziati */
    /*INIZIO CONTROLLO DELLA SESSIONE*/
    session_start(); //Avvio della sessione

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


    echo "<div id='container'>"; //Inserisce nella pagina il container per tutti i dati

    /*Estrazione dell'Id del piano attuale dell'utente e della data di acquisto in base all'utente
    * L'id verrà usato per ricavare il titolo, la data per dare ulteriori informazioni all'utente*/
    $planQuery = "SELECT MAX(idPlan), acquisitionDate FROM Acquisition WHERE userId = ?;";
    $result = $conn->prepare($planQuery);
    $result->bind_param("s", $userId);
    $result->execute();
    $result->bind_result($plan, $acquisitionDate);
    $result->fetch();
    $result->close();

    $_SESSION["idPlanUser"] = $plan; //Mette il piano dell'utente nell'array $_SESSION per evitare riutilizzo del codice

    if($plan > 1) {
        /*Estrazione del titolo del piano in base all'Id */
        $planTitleQuery = "SELECT title FROM Plans WHERE idPlan = ?;";
        $result = $conn->prepare($planTitleQuery);
        $result->bind_param("s", $plan);
        $result->execute();
        $result->bind_result($planTitle);
        $result->fetch();
        $result->close();

        /*Stampa a schermo dei Piano e data di acquisto*/
        echo "<section id='plan'>
            <h2>Il tuo piano</h2>
            <p> Il tuo piano è " . $planTitle . " ed è stato acquistato il " . $acquisitionDate . "</p>
        </section>";
    } else {
        echo "<section id='plan'>
            <h2>Non hai ancora un piano</h2>
            <p>Non hai ancora acquistato un piano, pertanto non hai accesso ai corsi, clicca qui sotto per acquistare un nuovo piano</p>
            <a class='links' href='plans.php'>Acquista un Piano</a>
            </section>";
    }
    
    /*Estrazione dal Database dei corsi iniziati dall'utente
    * L'id verrà usato per estrarre titolo e descrizione del corso*/
    $courseIdQuery = "SELECT courseId FROM startCourse WHERE userId = ?;";
    $result = $conn->prepare($courseIdQuery);
    $result->bind_param("s", $userId);
    $result->execute();
    $result->store_result();
    $result->bind_result($courseId);
    
    if($result->num_rows > 0) {
        /*Stampa a schermo la section contenente tutti i corsi */
        echo "<section id='courses'>
        <h2>I tuoi corsi</h2>";

        //Dichiarazione dell'array vuoto che andremo a riempire con tutti gli Id dell'utente
        $coursesId = array();
    
        /*Ciclo che utilizzeremo per rimepire l'array di tutti gli id dei corsi dell'utente da usare successivamente*/
        while($result->fetch()) {
            array_push($coursesId, $courseId);
        }
        $result->close();

        /*Preparazione della nuova query che estrae titolo e descrizione del corso*/
        $courseInfoQuery = "SELECT title, description FROM courses WHERE courseId = ?";
        $result = $conn->prepare($courseInfoQuery);

        for($i = 0; $i < count($coursesId); $i++) {
            $result->bind_param("s", $coursesId[$i]);
            $result->execute();
            $result->bind_result($title, $description);
            $result->fetch();

            echo "<h3>$title</h3>
            <p>$description</p>
            <a class='links' href='accessCourse.php?courseId=$coursesId[$i]'>Accedi al corso</a>";
        }

        echo "</section>";
    } else {
        echo "<section id='courses'>
            <h2>Non hai iniziato corsi, clicca qua sotto per iniziarne uno</h2>
            <a class='links' href='courses.php'>Inizia un Corso</a>
        </section>";
    }
    
    echo "</div>";

    $result->close();
?>