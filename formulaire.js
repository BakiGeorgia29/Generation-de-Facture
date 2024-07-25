//Fonction permettant d'afficher le prix d'un produit

function afficherPrix(){
    var select = document.getElementById('produit');//Selectionne le produit
    var prix = document.getElementById('prix');//Selectionne la partie "prix dans le document html"
    var  nomprod = select.options[select.selectedIndex].value;//Selectionne le prix du produit 
    var option = {method:'POST',};
    var pr = 0;
    fetch('formulaire.php',option)
        .then(reponse => {
            if(!reponse.ok){
                throw new Error('Erreur reseau');
            }
            return reponse.json();
        })
        .then(data=>{
            data.forEach(element => {
                // if(element.NomProduit == nomprod){
                    console.log(element.Prix);
                // }
            });
        })
    prix.textContent = "Prix : " +pr+ " FrancsCFA";//Affiche le prix du produit
}

//Fonction qui permet a l'utilisateur d'augmenter un produit
let comptProduit = 1;
function ajouterProduit(){
    comptProduit++;
    let divproduits = document.getElementById('Produits');//Selectionner la div ayant l'id Produits
    let nouveauproduit = document.createElement('div');//Creer une nouvelle div
    nouveauproduit.classList.add('Produit');//Inserer la div dans la div Porduits

    //Text HTML delanouvelle Div
    nouveauproduit.innerHTML = `
        <label for="produit">Nom du Produit</label>
        <select name="produit[]" id="produit_${comptProduit}" onchange="afficherPrix()">
            <option value="Tomate">tomate</option>
            <option value="Orange">orange</option>
            <option value="Banane">banane</option>
        </select>
        <label for="prix" id="prix_${comptProduit}">Prix: </label>
        <label for="quantite">Quantite</label>
        <input type="number"  name="quantite[]" placeholder="Quantite" id="quantite_${comptProduit}" required>
        <button type="button"  onclick="ajouterProduit()">AJOUTER UN PRODUIT</button>
    `;
    divproduits.appendChild(nouveauproduit);
}

