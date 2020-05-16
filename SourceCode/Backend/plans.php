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
    /*Questa pagina si occupa di presentare all'utente tutti i piani disponibili*/
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

    echo "<div id='container'>"; //Apre il div che contiene i dati

    $plan = $_SESSION["idPlanUser"];

    /*Estrae tutti i piani disponibili eccetto quello Root Only
    * Estrae Titolo e prezzo per dare informazioni all'utente
    * Estrae l'Id per poterlo passare come parametro di una richiesta GET alla pagina per l'upgrade dei */
    $possiblePlans = "SELECT idPlan ,title, price FROM plans WHERE idPlan != 5 AND idPlan > ?;";
    $result = $conn->prepare($possiblePlans);
    $result->bind_param("i", $plan);
    $result->execute();
    $result->store_result();
    $result->bind_result($idPlan, $planTitle, $price);

    if($result->num_rows() > 0) { //Controlla che ci siano piani possibili

        echo "<section id='plans'>
        <h2>Piani Disponibili</h2>"; //Apre la sezione per i piani
        /*Stampa tutti i risultati possibili*/
        while($result->fetch()) {
            echo "<p> Puoi acquistare il piano $planTitle al prezzo di €$price </p>
            <a class='links' href='upgradePlan.php?idPlan=$idPlan'>Acquista</a>";
        }

        echo "</section>";
    } else {
        echo "<section id='plans'>
        <h2>Hai già acquistato il piano massimo</h2>
        <p>Hai già acquistato il piano massimo, clicca qui sotto per tornare al tuo profilo</p>
        <a class='links' href='profile.php'>Il tuo profilo</a>
        </section>";
    }
    
    echo "</div>";
    $result->close();
?>