let nomsRandom = ["Ines", "Victoire", "Hassan", "Emiliedu78", "Véroooo"];

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
            if (numeros.includes(n)) {
                alert("Ce numéro est déjà choisi !");
                return;
            }
            if (numeros.length < 5) {
                numeros.push(n);
                num_choisi.textContent = numeros.join(", ");
                this.classList.add('selected');
            } else {
                alert("Tu as déjà choisi 5 numéros !");
            }
        });
    });

    // Gérer la sélection manuelle des étoiles
    bouton_etoile.forEach(bouton => {
        bouton.addEventListener('click', function () {
            const n = parseInt(this.getAttribute('data-value'));
            if (etoiles.includes(n)) {
                alert("Cette étoile est déjà choisie !");
                return;
            }
            if (etoiles.length < 2) {
                etoiles.push(n);
                etoile_choisi.textContent = etoiles.join(", ");
                this.classList.add('selected');
            } else {
                alert("Tu as déjà choisi 2 étoiles !");
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
        updateHiddenFields();  // Assurez-vous que les champs sont à jour

        const participate = document.getElementById('participate');

        // Vérification finale avant soumission
        if (participate.checked) {
            // Si l'utilisateur veut participer, vérifiez la sélection
            if (numeros.length !== 5 || etoiles.length !== 2) {
                e.preventDefault();
                alert('Veuillez sélectionner 5 numéros et 2 étoiles avant de lancer la partie.');
            }
        }
    });

    // Afficher/Masquer les champs de participation
    const participate = document.getElementById('participate');
    participate.addEventListener('change', function () {
        const participationFields = document.getElementById('participation_fields');
        participationFields.style.display = this.checked ? 'block' : 'none';
    });

    // Génération aléatoire des numéros et étoiles
    const boutonGenererGrille = document.getElementById('generer-grille');

    boutonGenererGrille.addEventListener('click', function () {
        resetSelections(); // Réinitialiser les sélections actuelles

        // Générer 5 numéros aléatoires uniques entre 1 et 49
        numeros = generateRandomNumbers(5, 49);  // Mise à jour du tableau 'numeros'
        // Générer 2 étoiles aléatoires uniques entre 1 et 9
        etoiles = generateRandomNumbers(2, 9);   // Mise à jour du tableau 'etoiles'

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
});
