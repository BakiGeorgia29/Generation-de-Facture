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
        $nom=$_POST['nom'];
        $adresse=$_POST['adresse'];
        $numcompte = $_POST['numcompte'];
        $nomcompte = $_POST['nomcompte'];
        $nombanque = $_POST['nombanque'];
        $date = $_POST['date'];
    }

//Insertion des donnees dans la table infosclients
    $sql = "INSERT INTO InfosClients (NomClient,Adresse,NumCompte,NomCompte,NomBanque) VALUES (:nom,:adresse,:numcompte,:nomcompte,:nombanque)";
    
    $stmt = $connexion->prepare($sql);


    $stmt->bindParam(':nom',$nom);
    $stmt->bindParam(':adresse',$adresse);
    $stmt->bindParam(':numcompte',$numcompte);
    $stmt->bindParam(':nomcompte',$nomcompte);
    $stmt->bindParam(':nombanque',$nombanque);

    //Execution de la requete
    $stmt->execute();
 
    //Preparer le modele de facture
    $template= file_get_contents('C:\xampp\htdocs\bnr\pagefinal.html');

    //Remplacer les espaces reserves par les donnees du formulaire 
    $template=str_replace('{nomclient}' , $nom , $template);
    $template=str_replace('{adresse}' , $adresse , $template);
    $template=str_replace('{date}' , $date , $template);
    //remplacer les informations du payement dans le modele de facture
    $template = str_replace('{numcompte}' , $numcompte , $template);
    $template = str_replace('{nomcompte}',$nomcompte,$template);
    $template = str_replace('{nombanque}',$nombanque,$template);

//Etablissement du tableau des produits    

    $total= 0;//Total des achats effectues

    //Recuperation des informations sur les produits pris
    $nomproduit = $_POST['produit'];
    $quantite = $_POST['quantite'];

    //Affiche les noms et prix des produits dans la base de donnees Produits
    $st = $connexion->prepare ("SELECT NomProduit, Prix FROM produits");  
    $st->execute();//Execution de la requete
    $npr = $st->fetchAll(PDO::FETCH_ASSOC);
    $nomp = array_column($npr,'NomProduit');//Extrait uniquement les noms de produits
    $prix = array_column($npr,'Prix');//Extrait uniquement les prixs des produits
    $htmlfacture = ' ';//Affiche une colonne de prix
    $index = 1;//Index permettant de compter le nombre de produit selectionnes
    $totalproduit = 0;//Montant du [rix du produit par la quantite prise
    for($i=0;$i<count($nomproduit);$i++){
        $nproduit = $nomproduit[$i];//Recuperation de chaque produits selectionnes par le client 
        $j = 0;//Index avec lequel on va parcourir la liste de produits selectionnes par le client
        while($j<count($nomp)){
            if($nomp[$j] == $nproduit){//Ici on verifie que le produit selectionne est dans la base de donnees
                $pr = $prix[$i];//PrixUnitaire d'un produit
                $quant = $quantite[$i];//Quantite d'un produit pris
                $totalproduit = $pr*$quant;//Calcul du prix total d'un produit pris
                
                //Affichage d'un produit dans la facture
                $htmlfacture .= "
                    <tr>
                        <td>$index</td>
                        <td>$nproduit</td>
                        <td> $ $pr</td>
                        <td>$quant</td>
                         <td>$totalproduit</td>
                    </tr>
                ";
                $index++;
            }
            $total += $totalproduit;//Calcul du prix total
            $j++;//Incrementation de l'index j
        }
        
    }
    $tax = $total *0.1;//Calcul de la taxe
    $totaltax = $total+$tax;//Calcul prix avec taxe

//Remplacement des differentes variables dans le modele de facture
    $template = str_replace('{tableauproduit}' ,$htmlfacture, $template );
    $template = str_replace('{total}' ,$total, $template );
    $template = str_replace('{totaltax}' ,$totaltax, $template );
    $template = str_replace('{tax}' ,$tax, $template );


    json_encode($npr);

    // $prixprun = 0;
    // for($p=0;$p<count($nomproduit);$p++){
    //     switch($nomproduit[$p]){
    //         case('banane'): $prixprun=100;
    //         break;
    //         case('tomate'):$prixprun = 50;
    //         break;
    //         case('orange'):$prixprun = 150;
    //         break;
    //     };
    // }

    // $template = str_replace('{prix}',$prixprun,$template);


//Generation du document
    $document = $template;

    echo $document;

    $connexion=null;
?>
