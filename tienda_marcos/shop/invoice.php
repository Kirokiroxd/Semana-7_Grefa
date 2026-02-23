<?php
require_once __DIR__ . "/../config/db.php";
require_auth();

$order_id = (int)($_GET["id"] ?? 0);

$stmt = $conn->prepare("SELECT * FROM orders WHERE id=? AND user_id=?");
$uid = (int)$_SESSION["user"]["id"];
$stmt->bind_param("ii", $order_id, $uid);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
  header("Location: /tienda_marcos/shop/orders.php");
  exit;
}

$stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id=? ORDER BY id ASC");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

include __DIR__ . "/../partials/header.php";
?>
<h1>Factura #<?php echo (int)$order["id"]; ?></h1>

<div class="card" id="invoiceArea"
  data-invoice='<?php echo htmlspecialchars(json_encode([
    "invoiceNo" => (int)$order["id"],
    "date" => $order["created_at"],
    "customer" => $_SESSION["user"]["email"],
    "items" => array_map(fn($i) => [
      "name" => $i["name"],
      "qty" => (int)$i["qty"],
      "unit" => (int)$i["unit_price_cents"],
      "line" => (int)$i["line_total_cents"]
    ], $items),
    "subtotal" => (int)$order["subtotal_cents"],
    "tax" => (int)$order["tax_cents"],
    "total" => (int)$order["total_cents"]
  ]), ENT_QUOTES, "UTF-8"); ?>'>
  <div class="row"><span>Cliente</span><b><?php echo htmlspecialchars($_SESSION["user"]["email"]); ?></b></div>
  <div class="row"><span>Fecha</span><b><?php echo htmlspecialchars($order["created_at"]); ?></b></div>

  <table class="table" style="margin-top:12px">
    <thead><tr><th>Producto</th><th>Qty</th><th>Unit</th><th>Total</th></tr></thead>
    <tbody>
      <?php foreach ($items as $i): ?>
        <tr>
          <td><?php echo htmlspecialchars($i["name"]); ?></td>
          <td><?php echo (int)$i["qty"]; ?></td>
          <td>$<?php echo money((int)$i["unit_price_cents"]); ?></td>
          <td>$<?php echo money((int)$i["line_total_cents"]); ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div style="margin-top:12px">
    <div class="row"><span>Subtotal</span><b>$<?php echo money((int)$order["subtotal_cents"]); ?></b></div>
    <div class="row"><span>IVA 12%</span><b>$<?php echo money((int)$order["tax_cents"]); ?></b></div>
    <div class="row"><span>Total</span><b>$<?php echo money((int)$order["total_cents"]); ?></b></div>
  </div>

  <div style="margin-top:14px;display:flex;gap:10px;flex-wrap:wrap">
    <button class="btn" id="btnPdf">Descargar PDF (JS)</button>
    <a class="btn sec" href="/tienda_marcos/shop/orders.php">Ver compras</a>
  </div>
</div>

<!-- jsPDF desde CDN -->
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
<script src="/tienda_marcos/public/js/invoice.js"></script>

<?php include __DIR__ . "/../partials/footer.php"; ?>