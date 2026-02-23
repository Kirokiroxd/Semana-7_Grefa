<?php require_once __DIR__ . "/../config/db.php"; ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Tienda Marcos</title>
  <link rel="stylesheet" href="/tienda_marcos/public/css/styles.css" />
</head>
<body>
<header class="topbar">
  <a class="brand" href="/tienda_marcos/shop/index.php">Tienda Marcos</a>
  <nav>
    <a href="/tienda_marcos/shop/cart.php">Carrito</a>
    <?php if (!empty($_SESSION["user"])): ?>
      <a href="/tienda_marcos/shop/orders.php">Mis compras</a>
      <span class="muted"><?php echo htmlspecialchars($_SESSION["user"]["email"]); ?></span>
      <a href="/tienda_marcos/auth/logout.php">Salir</a>
    <?php else: ?>
      <a href="/tienda_marcos/auth/login.php">Login</a>
      <a href="/tienda_marcos/auth/register.php">Registro</a>
    <?php endif; ?>
  </nav>
</header>
<main class="container">