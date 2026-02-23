<?php
require_once __DIR__ . "/../config/db.php";
require_auth();

$cart = $_SESSION["cart"] ?? [];
if (count($cart) === 0) {
  header("Location: /tienda_marcos/shop/cart.php");
  exit;
}

$subtotal = 0;
foreach ($cart as $i) $subtotal += $i["unit_price_cents"] * $i["qty"];
$tax = (int) round($subtotal * 0.12);
$total = $subtotal + $tax;

$conn->begin_transaction();

try {
  // crear orden
  $stmt = $conn->prepare("INSERT INTO orders (user_id, subtotal_cents, tax_cents, total_cents) VALUES (?,?,?,?)");
  $uid = (int)$_SESSION["user"]["id"];
  $stmt->bind_param("iiii", $uid, $subtotal, $tax, $total);
  $stmt->execute();
  $order_id = $conn->insert_id;

  // items + descontar stock
  $stmtItem = $conn->prepare("INSERT INTO order_items (order_id, product_id, name, unit_price_cents, qty, line_total_cents) VALUES (?,?,?,?,?,?)");
  $stmtStock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id=? AND stock >= ?");

  foreach ($cart as $i) {
    $pid = (int)$i["product_id"];
    $name = $i["name"];
    $unit = (int)$i["unit_price_cents"];
    $qty = (int)$i["qty"];
    $line = $unit * $qty;

    $stmtStock->bind_param("iii", $qty, $pid, $qty);
    $stmtStock->execute();
    if ($stmtStock->affected_rows !== 1) throw new Exception("Stock insuficiente.");

    $stmtItem->bind_param("iisiii", $order_id, $pid, $name, $unit, $qty, $line);
    $stmtItem->execute();
  }

  $conn->commit();
  $_SESSION["cart"] = [];

  header("Location: /tienda_marcos/shop/invoice.php?id=" . $order_id);
  exit;

} catch (Throwable $e) {
  $conn->rollback();
  include __DIR__ . "/../partials/header.php";
  echo "<div class='alert'>Error al procesar: " . htmlspecialchars($e->getMessage()) . "</div>";
  echo "<a class='btn' href='/tienda_marcos/shop/cart.php'>Volver</a>";
  include __DIR__ . "/../partials/footer.php";
}