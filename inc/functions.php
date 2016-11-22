<?php

// J'inclus le fichier avec les paramètres
require_once 'config.php';


$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

$offset = ($page -1) * 3;


$triDate = isset($_POST['tri_date']) ? htmlentities($_POST['tri_date']) : '';


// Crée une fonction avec la requête pour affichage des films dans 'Catalog'
function dp_sqlShowCatalog() {
    // Déclare l'accès aux variables endehors de la fonction
    global $pdo;
    global $offset;
    global $triDate;
    // Requête pour affichage des films dans 'Catalog'
    $sql = "
	SELECT mov_id AS ID, mov_title AS title, SUBSTRING_INDEX(mov_synopsis, ' ', 20) AS synopsis, mov_poster AS affiche
    FROM movies
    LIMIT $offset, 3
    ";

    $stmt = $pdo->query($sql);

    if (!$stmt->execute()) {
        print_r($stmt->errorInfo());
    }
    else {
        $mvliste = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return $mvliste;
}
$dp_sqlShowCatalog = dp_sqlShowCatalog();

// Le nombre de pages pour la pagination
function nbPages() {
    global $pdo;
    $sql='
    SELECT COUNT(*)
    FROM movies
    ';
    
    $stmt = $pdo->query($sql);
    
    if (!$stmt->execute()) {
        print_r($stmt->errorInfo());
    }
    else {
        $nbrPages = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return $nbrPages;
}
$nbPages = nbPages();
$resultat = ceil(intval($nbPages['COUNT(*)']) / 3);

// Affichage du catalogue
function showCatalog() {
    global $dp_sqlShowCatalog;
    foreach ( $dp_sqlShowCatalog as $key => $value) {
        
        echo "<tr>";
        echo "<td style='padding-left: 15px; padding-right: 15px; padding-bottom: 15px'>" . "<a href='movie.php?id=" . $value['ID'] . "'><img src=" . $value['affiche'] . " alt='movie-poster' height='200px' width='200px'" . " /></a>" . "</td>";
        echo "<td width='70%'>#" . $value['ID'] ." <a href='movie.php?id=" . $value['ID'] ."'>" . $value['title'] . "</a><br />" . $value['synopsis'] . " [.....]</td>";
        echo "<td style='padding-left: 15px'><a href='movie.php?id=" . $value['ID'] . "'><input type='button' class='btn btn-primary name='detail' value='Détails' /><br /><br /></a>
                <a href='admin/movies.php?id=" . $value['ID'] . "'><input type='button' class='btn btn-primary name='modifier' value='Modifier' /></a>

                </td>";
        echo "</tr>";
    }
}


    /////////////////////////////////////////////////////////////////////
    //fonction pour les categories et le nombre de film par categories//
    ///////////////////////////////////////////////////////////////////
function catPlusCount(){
    global $pdo;
    $sql='
        SELECT cat_name, count(mov_id) AS nb_film
        FROM movies
        LEFT JOIN category ON category.cat_id = movies.category_cat_id
        GROUP BY cat_name
        ORDER BY nb_film DESC
        LIMIT 4
        ';

    //Exécution de ma requete SELECT
    $pdoStatement = $pdo->query($sql);

    //je récupère toutes les données
    $catResultCount = $pdoStatement->fetchAll();
    //print_r($catResultCount);
    return $catResultCount;
}

////////////////////////////////////////////////////////////////////
//fonction pour recupérer posters, titre film des 4 derniers ajouts
function recupPosterTitle(){
    global $pdo;
    $sql='
        SELECT mov_title, mov_poster, mov_adDate, mov_id
        FROM movies
        ORDER BY mov_adDate DESC
        LIMIT 4
        ';

    //Exécution de ma requete SELECT
    $pdoStatement = $pdo->query($sql);

    //je récupère les données
    $moviesData = $pdoStatement->fetchAll();
    //print_r($moviesData);
    return $moviesData;
}