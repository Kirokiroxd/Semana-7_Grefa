<?php
require_once __DIR__ . "/../config/db.php";
require_auth();

if (!isset($_SESSION["cart"])) $_SESSION["cart"] = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $action = $_POST["action"] ?? "";
  $pid = (int)($_POST["product_id"] ?? 0);

  if ($action === "remove") {
    $_SESSION["cart"] = array_values(array_filter($_SESSION["cart"], fn($i) => $i["product_id"] !== $pid));
  }

  if ($action === "update") {
    $qty = (int)($_POST["qty"] ?? 1);
    if ($qty < 1) $qty = 1;
    foreach ($_SESSION["cart"] as &$item) {
      if ($item["product_id"] === $pid) {
        $item["qty"] = $qty;
        break;
      }
    }
  }

  header("Location: /tienda_marcos/shop/cart.php");
  exit;
}

$subtotal = 0;
foreach ($_SESSION["cart"] as $i) {
  $subtotal += $i["unit_price_cents"] * $i["qty"];
}
$tax = (int) round($subtotal * 0.12);
$total = $subtotal + $tax;

include __DIR__ . "/../partials/header.php";
?>
<h1>Carrito</h1>

<?php if (count($_SESSION["cart"]) === 0): ?>
  <div class="card">Tu carrito está vacío.</div>
<?php else: ?>
  <table class="table">
    <thead><tr><th>Producto</th><th>Precio</th><th>Cantidad</th><th>Total</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($_SESSION["cart"] as $i): ?>
        <tr>
          <td><?php echo htmlspecialchars($i["name"]); ?></td>
          <td>$<?php echo money($i["unit_price_cents"]); ?></td>
          <td>
            <form method="post" style="display:flex;gap:8px;align-items:center">
              <input type="hidden" name="action" value="update" />
              <input type="hidden" name="product_id" value="<?php echo (int)$i["product_id"]; ?>" />
              <input type="number" name="qty" min="1" value="<?php echo (int)$i["qty"]; ?>" style="width:80px;padding:6px" />
              <button class="btn sec" type="submit">Actualizar</button>
            </form>
          </td>
          <td>$<?php echo money($i["unit_price_cents"] * $i["qty"]); ?></td>
          <td>
            <form method="post">
              <input type="hidden" name="action" value="remove" />
              <input type="hidden" name="product_id" value="<?php echo (int)$i["product_id"]; ?>" />
              <button class="btn" type="submit">Quitar</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="card" style="margin-top:14px">
    <div class="row"><span>Subtotal</span><b>$<?php echo money($subtotal); ?></b></div>
    <div class="row"><span>IVA (12%)</span><b>$<?php echo money($tax); ?></b></div>
    <div class="row"><span>Total</span><b>$<?php echo money($total); ?></b></div>
    <div style="margin-top:12px">
      <a class="btn" href="/tienda_marcos/shop/checkout.php">Pagar</a>
    </div>
  </div>
<?php endif; ?>

<?php include __DIR__ . "/../partials/footer.php"; ?>