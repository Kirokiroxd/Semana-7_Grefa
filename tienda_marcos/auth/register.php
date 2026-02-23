<?php
require_once __DIR__ . "/../config/db.php";

$error = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = trim($_POST["email"] ?? "");
  $password = $_POST["password"] ?? "";

  if ($email === "" || $password === "") {
    $error = "Faltan datos.";
  } else {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $exists = $stmt->get_result()->fetch_assoc();

    if ($exists) {
      $error = "Ese email ya está registrado.";
    } else {
      $hash = password_hash($password, PASSWORD_BCRYPT);
      $stmt = $conn->prepare("INSERT INTO users (email, password_hash) VALUES (?, ?)");
      $stmt->bind_param("ss", $email, $hash);
      $stmt->execute();
      header("Location: /tienda_marcos/auth/login.php");
      exit;
    }
  }
}

include __DIR__ . "/../partials/header.php";
?>
<h1>Registro</h1>
<?php if ($error): ?><div class="alert"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

<form method="post" class="card">
  <label>Email</label><br/>
  <input name="email" type="email" required style="width:100%;padding:10px;margin:6px 0 12px" />
  <label>Contraseña</label><br/>
  <input name="password" type="password" required style="width:100%;padding:10px;margin:6px 0 12px" />
  <button class="btn" type="submit">Crear cuenta</button>
</form>
<?php include __DIR__ . "/../partials/footer.php"; ?>