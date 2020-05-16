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
    /*Questa pagina inserisce il nuovo corso appena iniziato dentro alla tabella e avvisa l'utente se tutto è andato a buon fine*/
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

    $courseId = $_GET["courseId"];
    $date = date("Y-m-d");

    $startcourse = "INSERT INTO startCourse (userId, courseId, startingDate) VALUES ($userId, $courseId, '$date');";
    if(!$conn->query($startcourse)) {
        die("Errore durante la query " . $conn->error);
    } else{
        echo "<div id='container'>
        <section id='greetings'>
        <h2>Corso iniziato con successo</h2>
        <p>Hai iniziato il corso con successo, clicca qui sotto per tornare al tuo profilo</p>
        <a class='links' href='profile.php'>Il tuo profilo</a>
        </div>";
    }
?>