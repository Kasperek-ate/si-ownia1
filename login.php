<?php
// Start sesji
session_start();

// Ustawienia bazy danych (dostosuj do swojego XAMPP)
$servername = "localhost";
$username = "root"; // domyślnie w XAMPP root
$password = ""; // puste hasło w XAMPP
$dbname = "powerzone"; // stwórz taką bazę w phpMyAdmin

$conn = new mysqli($servername, $username, $password, $dbname);

// Sprawdzenie połączenia
if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $pass = $_POST["password"];

    // Sprawdź dane w tabeli users
    $sql = "SELECT * FROM users WHERE email=? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        // Sprawdź hasło (jeśli zapisane jako hash)
        if (password_verify($pass, $row["password"])) {
            $_SESSION["user_id"] = $row["id"];
            $_SESSION["user_email"] = $row["email"];
            header("Location: index.html"); // powrót na stronę główną
            exit();
        } else {
            $message = "Nieprawidłowe hasło!";
        }
    } else {
        $message = "Użytkownik nie istnieje!";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <title>Logowanie - PowerZone</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
  <div class="bg-white p-8 rounded-2xl shadow-md w-full max-w-md">
    <h2 class="text-2xl font-bold mb-6 text-center">Logowanie</h2>

    <?php if ($message): ?>
      <p class="mb-4 text-red-600 font-semibold"><?= $message ?></p>
    <?php endif; ?>

    <form method="POST" action="">
      <input type="email" name="email" placeholder="Email" required
             class="w-full p-3 mb-4 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
      <input type="password" name="password" placeholder="Hasło" required
             class="w-full p-3 mb-4 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
      <button type="submit"
              class="w-full bg-red-600 text-white p-3 rounded-lg font-semibold hover:bg-red-700 transition">
        Zaloguj
      </button>
    </form>
  </div>
</body>
</html>
