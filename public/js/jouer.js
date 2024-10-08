let nomsRandom=["Ines","Victoire","Hassan","Emiliedu78","Véroooo"]; /* Avoir plus de noms, et vérifier si le nom entré n'est pas déjà pris*/

document.addEventListener('DOMContentLoaded',function(){

    const boutongenerer = document.querySelector('.generate');
    const input= document.getElementById('name');
    const validation=document.querySelector('.validation');
    const text_validate=document.getElementById('validate_text');


    boutongenerer.addEventListener('click', function(){
        const rand=Math.floor(Math.random()*nomsRandom.length);
        input.value=nomsRandom[rand];
    })

    validation.addEventListener('click',function(){
        const username=input.value.trim();

        if (username){
            text_validate.textContent='Bienvenue '+ username +', super nom !'
        } else {
            text_validate.textContent="Eh, choisis un nom d'abord !";
        }
    })
})

let numeros = [];
let etoiles = [];

const bouton_numero = document.querySelectorAll('.numero');
const num_choisi = document.getElementById('numeros_choisi');

const bouton_etoile = document.querySelectorAll('.etoile');
const etoile_choisi = document.getElementById('etoiles_choisi');

const valider_grille = document.getElementById('grille_validation');
const valider_grille_text = document.getElementById('grille_validation_text');

// Gérer la sélection des numéros
bouton_numero.forEach(bouton => {
    bouton.addEventListener('click', function() {
        const n = parseInt(this.getAttribute('data-value'));
        
        if (numeros.includes(n)) {
            alert("Ce numéro est déjà choisi !");
        } else {
            if (numeros.length < 5) {
                numeros.push(n);
                num_choisi.textContent = numeros.join(", ");
            } else {
                alert("Tu as déjà choisi 5 numéros !");
            }
        }
    });
});

// Gérer la sélection des étoiles
bouton_etoile.forEach(bouton => {
    bouton.addEventListener('click', function() {
        const n = parseInt(this.getAttribute('data-value'));

        if (etoiles.includes(n)) {
            alert("Cette étoile est déjà choisie !");
        } else {
            if (etoiles.length < 2) {
                etoiles.push(n);
                etoile_choisi.textContent = etoiles.join(", ");
            } else {
                alert("Tu as déjà choisi 2 étoiles !");
            }
        }
    });
});

// Valider la grille
valider_grille.addEventListener('click', function() {
    if (numeros.length === 5 && etoiles.length === 2) {
        valider_grille_text.textContent = "Super, votre composition est validée !";

        // Mettre à jour les champs cachés
        document.getElementById('numeros_input').value = JSON.stringify(numeros);
        document.getElementById('etoiles_input').value = JSON.stringify(etoiles);

        // Soumettre le formulaire
        document.querySelector('form').submit();
    } else {
        valider_grille_text.textContent = "Zut ... Il manque quelque chose ? Revoyez votre composition !";
    }
});
