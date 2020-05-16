<?php
    /*Questa pagina permette all'utente di effettuare il login all'applicativo*/

    session_start();
    $servername = "localhost";
    $dataBaseName = "TutorialEnrietti";
    $conn = new mysqli($servername, "root", "", $dataBaseName);

    if ($conn->connect_error) {
        die("Problemi nella connessione al DataBase <br> " . $conn->connect_error);
    }

    /*Prende dal form username e password */
    $user = $_POST["username"];
    $pass = $_POST["password"];

    /**Query che estrae dal database l'hash della password in base all'username digitato dall'utente */
    $loginQuery = "SELECT userId, password FROM users WHERE username = ?;"; //Query contenente il parametro "?" necessaria per effettuare una query con le Prepared Statements
    $result = $conn->prepare($loginQuery); //Preparazione della query
    $result->bind_param("s", $user); //Associazione del parametro "?" all'username
    $result->execute(); //Esecuzione della query
    $result->bind_result($userId, $dbPass); //
    $result->fetch();
    $result->close();

    /**Generazione della sessione dell'utente se le credenziali inserite sono esatte*/
    $cookie = password_hash($user . $dbPass, PASSWORD_DEFAULT); //Genera l'Hash da usare per il cookie di sessione concatenando user e password
    if(password_verify($pass, $dbPass)) { //password_verify confronta una stringa in chiaro con un Hash e restituisce true se l'hash della stringa corrisponde alla stringa in chiaro
        setcookie("session", $cookie, time()+86400);
        $_SESSION["userId"] = $userId;
        $_SESSION["username"] = $user;
        header("location: profile.php");
    } else {
        echo "Login non effettuato";
    }
?>
