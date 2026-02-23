(function () {
  const btn = document.getElementById("btnPdf");
  const area = document.getElementById("invoiceArea");
  if (!btn || !area) return;

  function money(cents) {
    return (cents / 100).toFixed(2);
  }

  btn.addEventListener("click", () => {
    const data = JSON.parse(area.dataset.invoice);
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    let y = 14;

    doc.setFont("helvetica", "bold");
    doc.setFontSize(16);
    doc.text("TIENDA RANDOM - FACTURA", 14, y);

    y += 10;
    doc.setFontSize(11);
    doc.setFont("helvetica", "normal");
    doc.text(`Factura #: ${data.invoiceNo}`, 14, y); y += 6;
    doc.text(`Fecha: ${data.date}`, 14, y); y += 6;
    doc.text(`Cliente: ${data.customer}`, 14, y); y += 10;

    doc.setFont("helvetica", "bold");
    doc.text("Detalle", 14, y); y += 6;

    // Encabezados tabla simple
    doc.setFont("helvetica", "bold");
    doc.text("Producto", 14, y);
    doc.text("Qty", 120, y);
    doc.text("Unit", 140, y);
    doc.text("Total", 170, y, { align: "right" });
    y += 4;

    doc.setLineWidth(0.2);
    doc.line(14, y, 196, y);
    y += 6;

    doc.setFont("helvetica", "normal");

    data.items.forEach((it) => {
      // salto de página si se llena
      if (y > 270) {
        doc.addPage();
        y = 14;
      }

      const name = it.name.length > 50 ? it.name.slice(0, 50) + "..." : it.name;

      doc.text(name, 14, y);
      doc.text(String(it.qty), 120, y);
      doc.text("$" + money(it.unit), 140, y);
      doc.text("$" + money(it.line), 170, y, { align: "right" });
      y += 7;
    });

    y += 4;
    doc.line(120, y, 196, y);
    y += 8;

    doc.setFont("helvetica", "bold");
    doc.text("Subtotal:", 140, y);
    doc.text("$" + money(data.subtotal), 170, y, { align: "right" });
    y += 7;

    doc.text("IVA 12%:", 140, y);
    doc.text("$" + money(data.tax), 170, y, { align: "right" });
    y += 7;

    doc.setFontSize(12);
    doc.text("TOTAL:", 140, y);
    doc.text("$" + money(data.total), 170, y, { align: "right" });

    doc.save(`factura_${data.invoiceNo}.pdf`);
  });
})();