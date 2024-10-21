let nomsRandom = ["Alpheus", "Brody", "Caelum", "Dax", "Elysia", "Finn", "Galen", "Harper", "Ilias", 
    "Jaxon", "Kiera", "Lila", "Milo", "Nova", "Orion", "Phaedra", "Quinlan", "Raine", "Soren", "Talia",
    "Ulysses", "Vesper", "Wren", "Xena", "Yara", "Zephyr", "Anika", "Bodhi", "Cassian", "Delia", 
    "Emrys", "Freya", "Gideon", "Harlow", "Isla", "Jett", "Kael", "Lyra", "Maxon", "Nyla", "Odin", "Piper", 
    "Quin", "Rhea", "Sage", "Thorne", "Uma", "Valor", "Wynne", "Xander", "Yvette", "Zuri", "Asher", "Bria",
    "Caden", "Daria", "Elowen", "Felix", "Greer", "Huxley", "Imani", "Juno", "Kieran", "Livia", "Maverick", 
    "Niamh", "Oren", "Pippa", "Riven", "Sable", "Tegan", "Uriel", "Vega", "Willa", "Xanthe", "Yasmine", "Zane", 
    "Aisling", "Beau", "Calla", "Darian", "Elara", "Finley", "Greysen", "Hadley", "Indigo", "Jace", "Kaia", 
    "Leif", "Maelis", "Niko", "Odette", "Pax", "Quinley", "Riven","Selene", "Taliah", "Ulrich", "Vespera", "Zephyra"
];
let grillesJoueurs = [];

document.addEventListener('DOMContentLoaded', function () {
    const boutongenerer = document.querySelector('.generate');
    const input = document.getElementById('name');
    const validation = document.querySelector('.validation');
    const text_validate = document.getElementById('validate_text');

    let numeros = [];
    let etoiles = [];

    const bouton_numero = document.querySelectorAll('.numero');
    const num_choisi = document.getElementById('numeros_choisi');

    const bouton_etoile = document.querySelectorAll('.etoile');
    const etoile_choisi = document.getElementById('etoiles_choisi');

    const valider_grille = document.getElementById('grille_validation');
    const valider_grille_text = document.getElementById('grille_validation_text');

    // Inputs pour le nombre de joueurs
    const nb_joueurs_input = document.getElementById('nb_joueurs');
    const nb_joueurs_random_input = document.getElementById('nb_joueurs_random');
    const validatePlayersButton = document.getElementById('validate_players');  // Bouton pour valider le nombre de joueurs

    // Fonction pour générer des nombres aléatoires uniques
    function generateRandomNumbers(count, max) {
        let numbers = [];
        while (numbers.length < count) {
            let num = Math.floor(Math.random() * max) + 1;
            if (!numbers.includes(num)) {
                numbers.push(num);
            }
        }
        return numbers.sort((a, b) => a - b); // On trie les nombres pour un affichage plus lisible
    }

    // Réinitialiser toutes les sélections actuelles (numéros et étoiles)
    function resetSelections() {
        document.querySelectorAll('.selected').forEach(bouton => bouton.classList.remove('selected'));
        document.getElementById('numeros_choisi').textContent = '';
        document.getElementById('etoiles_choisi').textContent = '';
        numeros = [];
        etoiles = [];
    }

    // Fonction pour mettre à jour les champs cachés
    function updateHiddenFields() {
        document.getElementById('numeros_input').value = JSON.stringify(numeros);
        document.getElementById('etoiles_input').value = JSON.stringify(etoiles);
    }

    // Générer un nom aléatoire
    boutongenerer.addEventListener('click', function () {
        const rand = Math.floor(Math.random() * nomsRandom.length);
        input.value = nomsRandom[rand];
    });

    // Valider le nom d'utilisateur
    validation.addEventListener('click', function () {
        const username = input.value.trim();
        if (username) {
            text_validate.textContent = 'Bienvenue ' + username + ', super nom !';
        } else {
            text_validate.textContent = "Eh, choisis un nom d'abord !";
        }
    });

// Gérer la sélection manuelle des numéros
    bouton_numero.forEach(bouton => {
        bouton.addEventListener('click', function () {
            const n = parseInt(this.getAttribute('data-value'));

            // Si le numéro est déjà choisi, le retirer
            if (numeros.includes(n)) {
                numeros = numeros.filter(num => num !== n); // Retirer le numéro
                num_choisi.textContent = numeros.join(", "); // Mettre à jour l'affichage
                this.classList.remove('selected'); // Retirer la classe sélectionnée
            } else {
                if (numeros.length < 5) {
                    numeros.push(n);
                    num_choisi.textContent = numeros.join(", ");
                    this.classList.add('selected'); // Ajouter la classe sélectionnée
                } else {
                    alert("Tu as déjà choisi 5 numéros !");
                }
            }
        });
    });

    // Gérer la sélection manuelle des étoiles
    bouton_etoile.forEach(bouton => {
        bouton.addEventListener('click', function () {
            const n = parseInt(this.getAttribute('data-value'));

            // Si l'étoile est déjà choisie, la retirer
            if (etoiles.includes(n)) {
                etoiles = etoiles.filter(etoile => etoile !== n); // Retirer l'étoile
                etoile_choisi.textContent = etoiles.join(", "); // Mettre à jour l'affichage
                this.classList.remove('selected'); // Retirer la classe sélectionnée
            } else {
                if (etoiles.length < 2) {
                    etoiles.push(n);
                    etoile_choisi.textContent = etoiles.join(", ");
                    this.classList.add('selected'); // Ajouter la classe sélectionnée
                } else {
                    alert("Tu as déjà choisi 2 étoiles !");
                }
            }
        });
    });

    // Valider la grille
    valider_grille.addEventListener('click', function () {
        if (numeros.length === 5 && etoiles.length === 2) {
            valider_grille_text.textContent = "Super, votre composition est validée !";
            updateHiddenFields();
        } else {
            valider_grille_text.textContent = "Zut ... Il manque quelque chose ? Revoyez votre composition !";
        }
    });

    // Gestion de la soumission du formulaire
    document.getElementById('jouer_form').addEventListener('submit', function (e) {
        const participate = document.getElementById('participate');

        // Vérifiez d'abord que les champs cachés sont à jour
        updateHiddenFields();

        // Vérification finale avant soumission
        if (participate.checked) {
            // Si l'utilisateur veut participer, vérifiez la sélection
            if (numeros.length !== 5 || etoiles.length !== 2) {
                e.preventDefault();
                alert('Veuillez sélectionner 5 numéros et 2 étoiles avant de lancer la partie.');
            }

            for (let i = 0; i < grillesJoueurs.length; i++) {
                const joueur = grillesJoueurs[i];
                if (joueur.grille.numeros.length !== 5 || joueur.grille.etoiles.length !== 2) {
                    e.preventDefault();
                    alert(`Le joueur ${joueur.nom} doit avoir 5 numéros et 2 étoiles.`);
                    break;
                }
            }
            const grillesInput = document.getElementById('grilles_joueurs');
            grillesInput.value = JSON.stringify(grillesJoueurs);
        }
    });

    // Afficher/Masquer les champs de participation
    const participate = document.getElementById('participate');
    participate.addEventListener('change', function () {
        const participationFields = document.getElementById('participation_fields');
        participationFields.style.display = this.checked ? 'block' : 'none';
    });

    const boutonGenererGrille = document.getElementById('generer-grille');

    boutonGenererGrille.addEventListener('click', function () {
        resetSelections(); // Réinitialiser les sélections actuelles

        numeros = generateRandomNumbers(5, 49);  
        etoiles = generateRandomNumbers(2, 9); 

        // Appliquer les sélections aux boutons des numéros
        numeros.forEach(numero => {
            const bouton = document.querySelector(`#numeros button[data-value="${numero}"]`);
            if (bouton) bouton.classList.add('selected');
        });

        // Appliquer les sélections aux boutons des étoiles
        etoiles.forEach(etoile => {
            const bouton = document.querySelector(`#etoiles button[data-value="${etoile}"]`);
            if (bouton) bouton.classList.add('selected');
        });

        // Mettre à jour les affichages
        document.getElementById('numeros_choisi').textContent = numeros.join(', ');
        document.getElementById('etoiles_choisi').textContent = etoiles.join(', ');

        // Mettre à jour les champs cachés
        updateHiddenFields();
    });

    // Validation du nombre de joueurs et joueurs random
    validatePlayersButton.addEventListener('click', function () {
        const nb_joueurs = parseInt(nb_joueurs_input.value);
        const nb_joueurs_random = parseInt(nb_joueurs_random_input.value);

        // Validation des entrées
        if (isNaN(nb_joueurs) || isNaN(nb_joueurs_random)) {
            alert('Veuillez entrer des valeurs valides.');
            return;
        }

        if (nb_joueurs_random > nb_joueurs) {
            alert('Le nombre de joueurs random ne peut pas être supérieur au nombre total de joueurs.');
            return;
        }

        grillesJoueurs = []; // Réinitialiser les grilles

        // Générer des grilles pour les joueurs random
        for (let i = 0; i < nb_joueurs_random; i++) {
            let grille = {
                numeros: generateRandomNumbers(5, 49),
                etoiles: generateRandomNumbers(2, 9)
            };
            grillesJoueurs.push({
                nom: nomsRandom[i % nomsRandom.length] + "_random",
                grille: grille
            });
        }

        // Préparer les joueurs manuels
        const nb_joueurs_manuels = nb_joueurs - nb_joueurs_random;
        for (let i = 0; i < nb_joueurs_manuels; i++) {
            grillesJoueurs.push({
                nom: "Joueur_" + (i + 1),
                grille: {
                    numeros: [],
                    etoiles: []
                }
            });
        }

        // Afficher les grilles manuelles
        afficherGrillesManuelles(nb_joueurs_manuels);
    });
});

// Fonction pour afficher les grilles des joueurs manuels

function afficherGrillesManuelles(nb_joueurs_manuels) {
    const manualInputContainer = document.getElementById('manual_inputs');

    if (!manualInputContainer) {
        console.error("L'élément manual_inputs est introuvable dans le DOM.");
        return;
    }

    manualInputContainer.innerHTML = ''; 

    for (let i = 0; i < nb_joueurs_manuels; i++) {
        const joueur = grillesJoueurs[grillesJoueurs.length - nb_joueurs_manuels + i];

        const joueurDiv = document.createElement('div');
        joueurDiv.classList.add('grille-utilisateur'); 

        let numerosButtons = '';
        for (let n = 1; n <= 49; n++) {
            numerosButtons += `<button class="numero" data-value="${n}" type="button">${n}</button>`;
        }

        let etoilesButtons = '';
        for (let n = 1; n <= 9; n++) {
            etoilesButtons += `<button class="etoile" data-value="${n}" type="button">${n}</button>`;
        }

        joueurDiv.innerHTML = `
            <h4>${joueur.nom}</h4>
            <div>
                <label>Numéros :</label>
                <span id="numeros_choisi_joueur_${i + 1}">${joueur.grille.numeros.length > 0 ? joueur.grille.numeros.join(', ') : 'À entrer'}</span>
                <div id="numeros_joueur_${i + 1}">${numerosButtons}</div>
            </div>
            <div>
                <label>Étoiles :</label>
                <span id="etoiles_choisi_joueur_${i + 1}">${joueur.grille.etoiles.length > 0 ? joueur.grille.etoiles.join(', ') : 'À entrer'}</span>
                <div id="etoiles_joueur_${i + 1}">${etoilesButtons}</div>
            </div>
        `;

        manualInputContainer.appendChild(joueurDiv);

        const numerosButtonsElements = joueurDiv.querySelectorAll('.numero');
        numerosButtonsElements.forEach(bouton => {
            bouton.addEventListener('click', function () {
                const n = parseInt(this.getAttribute('data-value'));
                if (joueur.grille.numeros.includes(n)) {
                    alert("Ce numéro est déjà choisi !");
                    return;
                }
                if (joueur.grille.numeros.length < 5) {
                    joueur.grille.numeros.push(n);
                    document.getElementById(`numeros_choisi_joueur_${i + 1}`).textContent = joueur.grille.numeros.join(", ");
                    this.classList.add('selected');
                } else {
                    alert("Ce joueur a déjà choisi 5 numéros !");
                }
            });
        });

        const etoilesButtonsElements = joueurDiv.querySelectorAll('.etoile');
        etoilesButtonsElements.forEach(bouton => {
            bouton.addEventListener('click', function () {
                const n = parseInt(this.getAttribute('data-value'));
                if (joueur.grille.etoiles.includes(n)) {
                    alert("Cette étoile est déjà choisie !");
                    return;
                }
                if (joueur.grille.etoiles.length < 2) {
                    joueur.grille.etoiles.push(n);
                    document.getElementById(`etoiles_choisi_joueur_${i + 1}`).textContent = joueur.grille.etoiles.join(", ");
                    this.classList.add('selected');
                } else {
                    alert("Ce joueur a déjà choisi 2 étoiles !");
                }
            });
        });
    }
}

    document.getElementById('jouer_form').addEventListener('submit', function () {
        const grillesJoueursData = grillesJoueurs.map(joueur => ({
            nom: joueur.nom,
            numeros: joueur.grille.numeros,
            etoiles: joueur.grille.etoiles
        }));
        
        document.getElementById('grilles_joueurs').value = JSON.stringify(grillesJoueursData);
    });


function ajouterJoueurManuel(nom, numeros, etoiles) {
    const joueursExistants = document.getElementById('grilles_joueurs').value;
    const joueursArray = joueursExistants ? JSON.parse(joueursExistants) : [];

    const nouveauJoueur = {
        'username': nom,
        'numeros': numeros,
        'etoiles': etoiles
    };

    joueursArray.push(nouveauJoueur);
    document.getElementById('grilles_joueurs').value = JSON.stringify(joueursArray);
}
