<?php
require_once __DIR__ . "/../config/db.php";
$error = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = trim($_POST["email"] ?? "");
  $password = $_POST["password"] ?? "";

  $stmt = $conn->prepare("SELECT id, email, password_hash FROM users WHERE email=?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $user = $stmt->get_result()->fetch_assoc();

  if (!$user || !password_verify($password, $user["password_hash"])) {
    $error = "Credenciales incorrectas.";
  } else {
    $_SESSION["user"] = ["id" => (int)$user["id"], "email" => $user["email"]];
    if (!isset($_SESSION["cart"])) $_SESSION["cart"] = [];
    header("Location: /tienda_marcos/shop/index.php");
    exit;
  }
}

include __DIR__ . "/../partials/header.php";
?>
<h1>Login</h1>
<?php if ($error): ?><div class="alert"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

<form method="post" class="card">
  <label>Email</label><br/>
  <input name="email" type="email" required style="width:100%;padding:10px;margin:6px 0 12px" />
  <label>Contraseña</label><br/>
  <input name="password" type="password" required style="width:100%;padding:10px;margin:6px 0 12px" />
  <button class="btn" type="submit">Entrar</button>
</form>
<?php include __DIR__ . "/../partials/footer.php"; ?>