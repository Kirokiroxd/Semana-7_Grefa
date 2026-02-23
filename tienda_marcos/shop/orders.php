<?php
require_once __DIR__ . "/../config/db.php";
require_auth();

$uid = (int)$_SESSION["user"]["id"];
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY id DESC");
$stmt->bind_param("i", $uid);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

include __DIR__ . "/../partials/header.php";
?>
<h1>Mis compras</h1>

<?php if (!$orders): ?>
  <div class="card">Aún no tienes compras.</div>
<?php else: ?>
  <table class="table">
    <thead><tr><th>ID</th><th>Fecha</th><th>Total</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($orders as $o): ?>
      <tr>
        <td>#<?php echo (int)$o["id"]; ?></td>
        <td><?php echo htmlspecialchars($o["created_at"]); ?></td>
        <td>$<?php echo money((int)$o["total_cents"]); ?></td>
        <td><a class="btn sec" href="/tienda_marcos/shop/invoice.php?id=<?php echo (int)$o["id"]; ?>">Ver factura</a></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

<?php include __DIR__ . "/../partials/footer.php"; ?>