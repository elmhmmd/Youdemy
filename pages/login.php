<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Youdemy - Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-b from-blue-50 to-white min-h-screen flex items-center justify-center">

    <div class="bg-white shadow-md rounded-lg p-8 py-6 w-full max-w-md ">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Se Connecter à Youdemy</h2>
        <?php
    if (isset($_SESSION['login_error'])) {
    echo '<div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">';
    echo '<p>' . htmlspecialchars($_SESSION['login_error']) . '</p>';
    echo '</div>';
    unset($_SESSION['login_error']);
    }

    if (isset($_SESSION['signup_success']) && $_SESSION['signup_success']) {
        echo '<div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">';
        echo '<p>Votre inscription a réussi ! Veuillez vous connecter.</p>';
        echo '</div>';
        unset($_SESSION['signup_success']);
    }
?>
        <form action="../actions/process_login.php" method="post" class="space-y-4" id="loginForm">
    <div>
        <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Adresse E-mail</label>
        <input type="email" name="email" id="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Entrez votre adresse e-mail" required>
        <p id="emailError" class="text-red-500 text-xs italic hidden"></p>
    </div>
    <div>
        <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Mot de Passe</label>
        <input type="password" name="password" id="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Entrez votre mot de passe" required>
        <p id="passwordError" class="text-red-500 text-xs italic hidden"></p>
    </div>
    <div class="flex items-center justify-between">
        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            Se Connecter
        </button>
        <a href="./signup.php" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
            Créer un compte
        </a>
    </div>
</form>
    </div>
    <script>
        const form = document.getElementById('loginForm');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const emailError = document.getElementById('emailError');
        const passwordError = document.getElementById('passwordError');

        form.addEventListener('submit', function(event) {
            let valid = true;

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailInput.value)) {
                emailError.textContent = "Veuillez entrer un email valide.";
                emailError.classList.remove('hidden');
                valid = false;
            } else {
                emailError.classList.add('hidden');
            }

            if (!passwordInput.value) {
                passwordError.textContent = "Veuillez entrer votre mot de passe.";
                passwordError.classList.remove('hidden');
                valid = false;
            } else {
                passwordError.classList.add('hidden');
            }

            if (!valid) {
                event.preventDefault();
            }
        });
        </script>

</body>
</html>