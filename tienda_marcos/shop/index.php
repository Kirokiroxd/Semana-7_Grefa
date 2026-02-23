<?php
require_once __DIR__ . "/../config/db.php";
$products = $conn->query("SELECT * FROM products ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);

if (!isset($_SESSION["cart"])) $_SESSION["cart"] = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  require_auth();

  $product_id = (int)($_POST["product_id"] ?? 0);
  $qty = (int)($_POST["qty"] ?? 1);
  if ($qty < 1) $qty = 1;

  // buscar producto
  $stmt = $conn->prepare("SELECT id, name, price_cents, stock FROM products WHERE id=?");
  $stmt->bind_param("i", $product_id);
  $stmt->execute();
  $p = $stmt->get_result()->fetch_assoc();

  if ($p) {
    // añadir a carrito (sumar qty si ya existe)
    $found = false;
    foreach ($_SESSION["cart"] as &$item) {
      if ($item["product_id"] === (int)$p["id"]) {
        $item["qty"] += $qty;
        $found = true;
        break;
      }
    }
    if (!$found) {
      $_SESSION["cart"][] = [
        "product_id" => (int)$p["id"],
        "name" => $p["name"],
        "unit_price_cents" => (int)$p["price_cents"],
        "qty" => $qty
      ];
    }
  }

  header("Location: /tienda_marcos/shop/cart.php");
  exit;
}

include __DIR__ . "/../partials/header.php";
?>
<h1>Productos</h1>

<div class="grid">
  <?php foreach ($products as $p): ?>
    <div class="card">
      <div style="font-weight:700"><?php echo htmlspecialchars($p["name"]); ?></div>
      <div class="muted">$<?php echo money((int)$p["price_cents"]); ?></div>
      <div class="muted">Stock: <?php echo (int)$p["stock"]; ?></div>

      <form method="post" style="margin-top:10px">
        <input type="hidden" name="product_id" value="<?php echo (int)$p["id"]; ?>" />
        <input type="number" name="qty" value="1" min="1" style="width:70px;padding:8px" />
        <button class="btn" type="submit">Agregar</button>
      </form>

      <?php if (empty($_SESSION["user"])): ?>
        <div class="muted" style="margin-top:8px">Para comprar: inicia sesión.</div>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
</div>

<?php include __DIR__ . "/../partials/footer.php"; ?>