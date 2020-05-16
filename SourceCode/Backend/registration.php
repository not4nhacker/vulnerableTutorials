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

    /**Questa pagina permette all'utente di registrarsi */

    $servername = "localhost";
    $dataBaseName = "TutorialEnrietti";
    $conn = new mysqli($servername, "root", "", $dataBaseName);

    if ($conn->connect_error) {
        die("Problemi nella connessione al DataBase <br> " . $conn->connect_error);
    }

    //Prende tutti i dati inseriti nel form
    $name = $_POST["firstname"];
    $surname = $_POST["surname"];
    $email = $_POST["email"];
    $user = $_POST["username"];
    $pass = $_POST["password"];
    $repeatPassword = $_POST["repeatPassword"];
    $cardnumber = $_POST["cardnumber"];
    $expirationDate = $_POST["expirationdate"];
    $cvv = $_POST["cvv"];

    /*Controlla che i dati inseriti nel form siano validi*/
    if(empty($name) || empty($surname) || empty($email) || empty($user) || empty($pass) || empty($repeatPassword) || empty($cardnumber) || empty($expirationDate) || empty($cvv)) {
        die("Assicurati di aver inserito ogni valore");
    } else if($pass != $repeatPassword) {
        die("Le password sono diverse");
    } else if(strpos($email, "@") == false) {
        die("Email non valida");
    }

    $permission = true;
    $checkDoubleUserQuery = "SELECT username FROM users WHERE username = ?;"; //Query per controllare che non ci sia un altro user con lo stesso username
    $result = $conn->prepare($checkDoubleUserQuery); //Preparazione della query
    $result->bind_param("s", $user); //Associazione del parametro "?" all'username
    $result->execute(); //Esecuzione della query
    $result->store_result();

    /**Controlla se la query ha prodotto risultati. Se ne ha prodotti vuol dire che vi è già un utente registrato e quindi stampa un errore per l'utente */
    if($result->num_rows > 0) {
        die("<div id='container'>
            <section id='error'>
                <h2>C'è già un utente registrato</h2>
                <p>C'è già un utente registrato con il tuo stesso username, per favore, torna alla registrazione e cambialo</p>
                <a class='links' href='../registration.html'>Torna alla registrazione</a>
            </section>
        </div>");
    }

    $result->close();
    if($permission == true) {  
        /*Si cifra la password con l'Algoritmo di Hash BCrypt
        * Andremo poi a lanciare la query che inserisca l'utente nella tabella users*/
        $pass = password_hash($pass, PASSWORD_DEFAULT);
        $registrationQuery = "INSERT INTO users(name, surname, email, username, password, cardNumber, expirationDate, cvv) VALUES ('".$name."', '".$surname."', '".$email."', '".$user."', '".$pass."', '".$cardnumber."', '".$expirationDate."', '".$cvv."');";
        if(!$conn->query($registrationQuery)) {
            die("Errore durante la query " . $conn->error);
        }

        /* Andiamo ad estrarre dal Databasel'userId dell'utente appena registrato
        * Che ci servirà per dare all'utente il piano zero*/
        $loginQuery = "SELECT userId FROM users WHERE username = ?;"; //Query contenente il parametro "?" necessaria per effettuare una query con le Prepared Statements
        $result = $conn->prepare($loginQuery); //Preparazione della query
        $result->bind_param("s", $user); //Associazione del parametro "?" all'username
        $result->execute(); //Esecuzione della query
        $result->bind_result($userId);
        $result->fetch();
        $result->close();

        $date = date("Y-m-d");
        /*Query che si occupa di dare il piano zero all'utente in base all'userId*/
        $insertNonePlan = "INSERT INTO acquisition (userId, idPlan, acquisitionDate) VALUES (" . $userId . ", 1, '" . $date . "');";
        if(!$conn->query($insertNonePlan)) {
            die("Errore durante la query " . $conn->error);
        }

        header("location: ../login.html"); //Manda l'utente alla pagina successiva
    }
?>