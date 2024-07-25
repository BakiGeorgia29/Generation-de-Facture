<?php

//Connexion a la base de donnees
    $dbhost="localhost";
    $dbUser="root";
    $dbmotpasse="";
    $dbName="BNRCompany";

    try{
        $connexion= new PDO("mysql:host=$dbhost;dbname=$dbName;",$dbUser,$dbmotpasse);
        $connexion->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    }catch(PDOException $e){
        echo "Erreur de connexion a la base de donnees: ".$e->getMessage();
        exit;
    }
//Recuperation des donnees du client
if(isset($_POST['valider'])){
    $nom=$_POST['noment'];
    $numtel = $_POST['num'];
    $adresse=$_POST['adress'];
    $site = $_POST['site'];
    $devise = $_POST['devise'];
//Recuperation de l'image du formulaire
    if($_FILES['image']['error'] === UPLOAD_ERR_OK){
        $fileName = $_FILES['image']['name'];
        $tmpName = $_FILES['image']['tmp_name'];
        $fileSize=$_FILES['image']['size'];
        $fileType = $_FILES['image']['type'];

        $targetDir = "C:/xampp/htdocs/bnr/";
        $targetFile = $targetDir .basename($fileName);

        if(move_uploaded_file($tmpName ,  $targetFile)){
            echo "yes";
        }else{
            echo "no";
        }

    }else{
        echo "error";
    }

    $terms = $_POST['terms'];
}
 //Preparer le modele de facture
 $template= file_get_contents('C:\xampp\htdocs\bnr\page.html');

 //Remplacer les espaces reserves par les donnees du formulaire 
 $template=str_replace('{Brand Name}' , $nom , $template);
 $template=str_replace('{slogan}' , $devise , $template);
 $template=str_replace('{terms}' , $terms, $template);
 $template=str_replace('{phone}' , $numtel , $template);
 $template=str_replace('{adress}' , $adresse, $template);
 $template=str_replace('{site}' , $site , $template);
 $template=str_replace('{logo}' , $fileName, $template);

 //Affichage du document
 $document = $template;
 echo $document;
//Creation d'un nouveau document avec les informations de l'entreprise
 file_put_contents('pagefinal.html', $document);
?>