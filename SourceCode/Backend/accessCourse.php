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

    $courseId = $_GET["courseId"];

    /**Estrae dal database il titolo e la desrizione del corso */
    $courseInfoQuery = "SELECT title, description FROM courses WHERE courseId = ?";
    $result = $conn->prepare($courseInfoQuery);

    $result->bind_param("s", $courseId);
    $result->execute();
    $result->bind_result($title, $description);
    $result->fetch();

    echo "<div id='container'>
        <section id='information'>
        <h2>$title</h2>
            <p>$description</p>
        </section>
    </div>";

    echo "</section>";
?>