<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Youdemy - Inscription</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-b from-blue-50 to-white min-h-screen flex items-center justify-center">

    <div class="bg-white shadow-md rounded-lg p-8 py-6 w-full max-w-md ">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Créer un Compte</h2>
        <?php
if (isset($_SESSION['form_errors']) && !empty($_SESSION['form_errors'])) {
    echo '<div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">';
    echo '<ul class="list-disc pl-5">';
    foreach ($_SESSION['form_errors'] as $error) {
        echo '<li>' . htmlspecialchars($error) . '</li>';
    }
    echo '</ul>';
    echo '</div>';
    unset($_SESSION['form_errors']); 
}

if (isset($_SESSION['signup_error'])) {
    echo '<div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">';
    echo '<p>' . htmlspecialchars($_SESSION['signup_error']) . '</p>';
    echo '</div>';
    unset($_SESSION['signup_error']);
}
?>
        <form action="../actions/process_signup.php" method="post" id="signupForm">
           <div class="mb-4">
                <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Nom d'utilisateur</label>
                <input type="text" name="username" id="username" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Entrez votre nom d'utilisateur" required>
                 <p id="usernameError" class="text-red-500 text-xs italic hidden"></p>
           </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Adresse E-mail</label>
                <input type="email" name="email" id="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Entrez votre adresse e-mail" required>
                   <p id="emailError" class="text-red-500 text-xs italic hidden"></p>
           </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Mot de Passe</label>
                <input type="password" name="password" id="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Entrez votre mot de passe" required>
                <p id="passwordError" class="text-red-500 text-xs italic hidden"></p>
            </div>
            <div class="mb-6">
                <label for="confirm_password" class="block text-gray-700 text-sm font-bold mb-2">Confirmer Mot de Passe</label>
                <input type="password" name="confirm_password" id="confirm_password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Confirmer votre mot de passe" required>
                   <p id="confirmPasswordError" class="text-red-500 text-xs italic hidden"></p>
            </div>

            <label class="block text-gray-700 text-sm font-bold mb-2">Je suis un(e) :</label>
            <input type="hidden" name="role" id="role" value="3">
            <div class="flex justify-around mb-6">
        <button type="button" id="etudiantBtn" class="role-button bg-blue-500 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" data-role-id="3">
            Étudiant
        </button>
        <button type="button" id="enseignantBtn" class="role-button bg-white text-blue-500 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" data-role-id="2">
            Enseignant
        </button>
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    S'inscrire
                </button>
                  <a href="./login.php" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                   Déjà un compte ?
                </a>
            </div>
        </form>
    </div>

    <script>
        const form = document.getElementById('signupForm');
        const usernameInput = document.getElementById('username');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const usernameError = document.getElementById('usernameError');
        const emailError = document.getElementById('emailError');
        const passwordError = document.getElementById('passwordError');
        const confirmPasswordError = document.getElementById('confirmPasswordError');


        form.addEventListener('submit', function(event) {
            let valid = true;
            const usernameRegex = /^[a-zA-Z0-9_]+$/;
           if(!usernameRegex.test(usernameInput.value)){
            usernameError.textContent = "Le nom d'utilisateur doit contenir uniquement des lettres et des chiffres";
             usernameError.classList.remove('hidden');
                valid = false;
           } else {
               usernameError.classList.add('hidden');
           }

           const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
           if(!emailRegex.test(emailInput.value)){
            emailError.textContent = "Veuillez entrer un email valide.";
             emailError.classList.remove('hidden');
                valid = false;
           } else {
               emailError.classList.add('hidden');
           }

             if(passwordInput.value.length < 6){
             passwordError.textContent = "Le mot de passe doit contenir au moins 6 caractères";
             passwordError.classList.remove('hidden');
               valid = false;
           } else {
               passwordError.classList.add('hidden');
           }

            if (passwordInput.value !== confirmPasswordInput.value) {
             confirmPasswordError.textContent = "Les mots de passe ne correspondent pas.";
                confirmPasswordError.classList.remove('hidden');
                valid = false;
            } else {
                 confirmPasswordError.classList.add('hidden');
            }

            if(!valid){
                event.preventDefault();
            }

        });

        const roleInput = document.getElementById('role');
        const etudiantBtn = document.getElementById('etudiantBtn');
        const enseignantBtn = document.getElementById('enseignantBtn');
        const roleButtons = document.querySelectorAll('.role-button');

        function selectRole(button) {
            roleButtons.forEach(btn => {
                btn.classList.remove('bg-blue-500', 'text-white');
                btn.classList.add('bg-white', 'text-blue-500');
            });
            button.classList.remove('bg-white', 'text-blue-500');
            button.classList.add('bg-blue-500', 'text-white');
            roleInput.value = button.dataset.roleId;
        }

        etudiantBtn.addEventListener('click', function() {
            selectRole(this);
        });

        enseignantBtn.addEventListener('click', function() {
            selectRole(this);
        });

        
        selectRole(etudiantBtn);
    </script>
</body>
</html>