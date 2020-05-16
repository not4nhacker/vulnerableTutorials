<?php
    /*INIZIO CONTROLLO DELLA SESSIONE*/
    session_start(); //Avvio della sessione

    /*Controllo del fatto che il login sia stato effettuato
    * Si controlla che il cookie di sessione e le variabili di sessione siano settate*/ 
    if(!isset($_COOKIE["session"]) || !isset($_SESSION["userId"]) || !isset($_SESSION["username"])) {
        $index = fopen("resources/unknownUserIndex.html", "r");
       
        while (!feof($index)) {
            $line = fgets($index);
            echo $line;
        }
        fclose($index);
    
    } else { //Mettiamo il controllo del cookie dentro all'if per controllare la sessione perchè se un utente non è registrato e non ha una sessione, non può avere un cookie
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

        $index = fopen("resources/loggedIndex.html", "r");
        while (!feof($index)) {
            $line = fgets($index);
            echo $line;
        }
        fclose($index);
    }
?>