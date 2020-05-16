DROP DATABASE IF EXISTS TutorialEnrietti;

CREATE DATABASE TutorialEnrietti;

USE TutorialEnrietti;

SET CHARACTER SET utf8;

CREATE TABLE users(
    userId INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(35) NOT NULL,
    surname VARCHAR(35) NOT NULL,
    email VARCHAR(40) NOT NULL,
    username VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    cardNumber VARCHAR(16) NOT NULL,
    expirationDate date NOT NULL,
    cvv VARCHAR(3) NOT NULL
);

CREATE TABLE courses(
    courseId INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(50) NOT NULL,
    description VARCHAR(500) NOT NULL,
    content TEXT NOT NULL,
    coursePlan INT NOT NULL
);

CREATE TABLE plans(
    idPlan INT PRIMARY KEY AUTO_INCREMENT, 
    title VARCHAR(20) NOT NULL,
    price INT NOT NULL
);

CREATE TABLE startCourse(
    idStarting INT PRIMARY KEY AUTO_INCREMENT,
    userId INT NOT NULL,
    courseId INT NOT NULL,
    startingDate DATE NOT NULL,
    FOREIGN KEY (userId) REFERENCES users(userId),
    FOREIGN KEY (courseId) REFERENCES courses(courseId)
);

CREATE TABLE acquisition(
    idAcquisition INT PRIMARY KEY AUTO_INCREMENT,
    userId INT NOT NULL,
    idPlan INT NOT NULL,
    acquisitionDate DATE NOT NULL,
    FOREIGN KEY (userId) REFERENCES users(userId),
    FOREIGN KEY (idPlan) REFERENCES plans(idPlan)
);

INSERT INTO users(name, surname, email, username, password, cardNumber, expirationDate, cvv) VALUES
("Root", "Superuser", "root@root.toot", "root", "$2y$10$Rhp957HROZ4wykk8QiYsFeL2OUBIcwfMNr7xpWoQPYBCQpQ4Zwd4q", "0000000000000000", "2020-05-14", "000"), /*Password in chiaro: iMsuperuser123*/
("User", "Userone", "user@user.user", "justauser", "$2y$10$/ekYkHvSZ3jV8MvJCZekRetgMXHmQIPAf.I9tM40RVzU4crayYk8C", "1234567890123456", "2020-05-04", "123"); /*Password in chiaro: justauser*/

INSERT INTO courses(title, description, content, coursePlan) VALUES
    ("HTML da 0", "Un corso introduttivo al linguaggio di formattazione HTML", "HTML è un linguaggio di formattazione con cui è possibile creare la struttura di un sito. È formato dai tag, ovvero istruzioni racchiuse dentro a parentesi angolari.", 2),
    ("C da 0", "Un corso introduttivo al linguaggio C", "Il linguaggio C è un linguaggio di programmazione compilato a basso livello. La sua sintassi ha ispirato successivamente diversi altri linguaggi, definiti C-Like.", 2),
    ("Assemblaggio PC da 0", "Un corso introduttivo all'assemblaggio dei Computer", "Un Pc è formato da diversi componenti: Scheda Madre, Processore, RAM, SSD o Hard Disk, Scheda Video, Scheda di rete, ecc.. Questi pezzi vengono assemblati all'interno di un Case, che si occupa di accenderli e di raffreddarli tramite il circuito di dissipazione. Necessitano inoltre di un alimentatore.", 2),
    ("Migliora i tuoi siti con CSS", "Una guida dalle stalle alle stelle alla creazione siti con CSS", "Il CSS è un linguaggio usato per definire la formattazione di documenti HTML. Con il CSS è possibile posizionare gli elemetni in una pagina, cambiarne dimensioni e colori e realizzare animazioni", 3),
    ("La OOP e il linguaggio Java", "Un tutorial dettagliato della Programmazione ad Oggetti con il linguaggio Java", "La Programmazione ad Oggetti è un paradigma di programmazione con cui si va ad astrarre la realtà di cui si effettua l'analisi, attribuendo ad ogni elemento una classe e dei metodi. Il linguaggio Java raccoglie tutti i suoi metodi all'interno di classi, ed ogni volta che si va a sviluppare codice è necessario creare una classe per ogni parte del codice.", 3),
    ("Rendi dinamici i tuoi siti con JavaScript", "Un corso introduttivo che ti permetterà di rendere interattivi i tuoi siti con il linguaggio JavaScript", "JavaScript è un linguaggio di programmazione con cui è possibile assegnare delle azioni agli elementi della pagina. Queste azioni verranno effettuate allo scatenarsi di un evento, ovvero di una determinata azione dell'utente (Che, per esempio, clicca su un elemento della pagina).", 3),
    ("Architettura Client-Server from Zero to Hero", "Un corso completo per imparare l'architettura Client-Server", "L'architettura Client-Server è un'architettura che prevede la presenza di uno o più client e un server all'interno di una rete che si scambiano dati e informazioni. Su un Server Web è per esempio presente un sito internet, che rende disponibili all'utente le sue pagine.", 4),
    ("DataBase e PHP", "Un corso avanzato che ti permetterà di salvare i tuoi dati grazie all'avanzata tecnologia dei Database", "I DataBase sono delle raccolte organizzate di dati. Per accedere a questi dati, inserirne di nuovi, modificarne o eliminarne tramite un'applicazione Web è necessario utilizzare un linguaggio di Back-End come il PHP, il Java, il C#, il Golang o tanti altri.", 4),
    ("Web Security per piccoli DEV insicuri", "Un corso complesso che ti farà muovere i primi passi nel mondo della Sicurezza Informatica", "La sicurezza nei propri siti è importantissima: permette di evitare che un utente malintenzionato acceda ai dati sensibili degli utenti normali di un'applicazione o che provochi malfunzionamenti all'interno della stessa.", 4),
    ("Root Only", "Questo corso è visibile solamente da utente Root", "Questo corso non contiene nulla, è solamente un corso a cui solo l'utente root può accedere", 5);

/*1 = Base, 2 = Silver, 3 = Gold, 4 = Root Only*/
INSERT INTO plans(title, price) VALUES
("None", 0),
("Base", 10),
("Silver", 25),
("Gold", 50),
("Root Only", 1000000000);

INSERT INTO acquisition (userId, idPlan, acquisitionDate) VALUES
(1, 1, "2020-5-5"),
(1, 2, "2020-5-5"),
(1, 3, "2020-5-5"),
(1, 4, "2020-5-5"),
(1, 5, "2020-5-5"),
(2, 1, "2020-5-6");

INSERT INTO startCourse (userId, courseId, startingDate) VALUES 
(1, 1, "2020-5-5"),
(1, 2, "2020-5-5"),
(1, 3, "2020-5-5"),
(1, 4, "2020-5-5"),
(1, 5, "2020-5-5"),
(1, 6, "2020-5-5"),
(1, 7, "2020-5-5"),
(1, 8, "2020-5-5"),
(1, 9, "2020-5-5"),
(1, 10, "2020-5-5")