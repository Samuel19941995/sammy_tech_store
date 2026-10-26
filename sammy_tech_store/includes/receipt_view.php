<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Receipt <?= e($order['order_number']) ?> | Sammy Tech</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root{--ink:#12231d;--muted:#65736c;--line:#dfe7df;--paper:#f4f7f2;--lime:#d8e55d;--blue:#0b9bd7}*{box-sizing:border-box}body{margin:0;background:linear-gradient(135deg,#edf5f1,#f8f6ef);color:var(--ink);font-family:Manrope,sans-serif}.receipt-shell{width:min(900px,calc(100% - 32px));margin:48px auto}.receipt{overflow:hidden;background:#fff;border:1px solid var(--line);border-radius:24px;box-shadow:0 24px 70px rgba(18,35,29,.13)}.receipt-head{padding:30px 38px;background:linear-gradient(120deg,#10251d,#1c4132);color:#fff;display:flex;justify-content:space-between;gap:26px;position:relative;overflow:hidden}.receipt-head:after{content:"";position:absolute;width:260px;height:260px;border:30px solid rgba(11,155,215,.24);border-radius:50%;right:-100px;top:-155px}.brand{position:relative;z-index:1;display:flex;align-items:center;gap:13px;color:#fff;text-decoration:none}.brand img{width:58px;height:58px;object-fit:cover;border-radius:50%;border:2px solid #32bae9}.brand b{display:block;font-size:22px;letter-spacing:-.05em}.brand small{display:block;margin-top:3px;color:#c8ddd2;font:500 10px 'DM Mono';letter-spacing:.12em}.receipt-type{position:relative;z-index:1;text-align:right}.receipt-type span{display:inline-block;padding:7px 10px;background:var(--lime);color:#16251e;font:700 10px 'DM Mono';letter-spacing:.1em}.receipt-type h1{margin:9px 0 4px;font-size:29px;letter-spacing:-.06em}.receipt-type p{margin:0;color:#c8ddd2;font-size:11px}.receipt-body{padding:34px 38px 38px}.details{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:28px}.details article{padding:17px 18px;border:1px solid var(--line);border-radius:13px;background:#fbfdfb}.details span{display:block;margin-bottom:8px;color:var(--muted);font:500 10px 'DM Mono';letter-spacing:.12em;text-transform:uppercase}.details b{font-size:15px}.details p{margin:5px 0 0;color:var(--muted);font-size:12px;line-height:1.55}.items{width:100%;border-collapse:collapse;margin-bottom:23px}.items th{padding:11px 10px;border-bottom:2px solid var(--ink);text-align:left;color:var(--muted);font:500 10px 'DM Mono';letter-spacing:.1em;text-transform:uppercase}.items th:not(:first-child),.items td:not(:first-child){text-align:right}.items td{padding:16px 10px;border-bottom:1px solid var(--line);font-size:13px}.items td:first-child{font-weight:700}.items td small{display:block;margin-top:3px;color:var(--muted);font-weight:500}.summary{display:grid;grid-template-columns:1fr 300px;gap:28px;align-items:end}.payment-note{padding:17px;border-radius:13px;background:#edf8fb;border-left:4px solid var(--blue)}.payment-note b{display:block;font-size:13px}.payment-note p{margin:5px 0 0;color:#42626d;font-size:12px;line-height:1.5}.totals{font-size:13px}.totals p{display:flex;justify-content:space-between;margin:0;padding:8px 0;border-bottom:1px solid var(--line);color:var(--muted)}.totals p b{color:var(--ink)}.total{display:flex;justify-content:space-between;gap:15px;margin-top:10px;padding:15px 17px;border-radius:12px;background:var(--lime);font-size:17px;font-weight:800}.total small{display:block;margin-top:3px;color:#435033;font:500 9px 'DM Mono'}.receipt-foot{display:flex;align-items:center;justify-content:space-between;gap:18px;padding:20px 38px;border-top:1px solid var(--line);background:#fbfdfb}.receipt-foot p{margin:0;color:var(--muted);font-size:11px}.print{border:0;border-radius:9px;padding:11px 16px;background:var(--ink);color:#fff;font:700 12px Manrope;cursor:pointer}.print:hover{background:#254437}@media(max-width:640px){.receipt-shell{width:min(100% - 20px,900px);margin:10px auto}.receipt{border-radius:16px}.receipt-head,.receipt-body,.receipt-foot{padding-left:20px;padding-right:20px}.receipt-head,.summary{display:block}.receipt-type{text-align:left;margin-top:22px}.details{grid-template-columns:1fr}.items{font-size:11px}.items th,.items td{padding:12px 5px}.payment-note{margin-bottom:18px}.receipt-foot{display:block}.print{margin-top:14px;width:100%}}@media print{body{background:#fff}.receipt-shell{width:100%;margin:0}.receipt{border:0;border-radius:0;box-shadow:none}.print{display:none}}
</style>
</head>
<body>
<main class="receipt-shell">
<section class="receipt">
<header class="receipt-head">
<a class="brand" href="index.php"><img src="assets/images/sammy-tech-logo.jpeg" alt="Sammy Tech logo"><span><b>Sammy Tech</b><small>CONNECTED FROM JUBA</small></span></a>
<div class="receipt-type"><span>ORDER RECEIPT</span><h1>Thank you</h1><p>Internet and Starlink accessories</p></div>
</header>
<div class="receipt-body">
<section class="details">
<article><span>Order details</span><b><?= e($order['order_number']) ?></b><p><?= e(date('d M Y, H:i', strtotime($order['created_at']))) ?><br><?= e(ucwords(str_replace('_', ' ', $order['delivery_method']))) ?> from Sammy Tech, Juba</p></article>
<article><span>Customer</span><b><?= e($order['customer_name']) ?></b><p><?= e($order['phone']) ?><?= $order['alternative_phone'] ? '<br>'.e($order['alternative_phone']) : '' ?></p></article>
</section>
<table class="items"><thead><tr><th>Item</th><th>Qty</th><th>Unit price</th><th>Amount</th></tr></thead><tbody><?php foreach ($items as $item): ?><tr><td><?= e($item['product_name']) ?><small>Sammy Tech equipment</small></td><td><?= e((string) $item['quantity']) ?></td><td><?= money((float) $item['unit_price']) ?></td><td><b><?= money((float) $item['unit_price'] * $item['quantity']) ?></b></td></tr><?php endforeach; ?></tbody></table>
<section class="summary">
<div class="payment-note"><b>Payment: <?= e(ucwords(str_replace('_', ' ', $order['payment_method']))) ?> - <?= e(ucfirst($order['payment_status'])) ?></b><p><?= $order['payment_status'] === 'paid' ? 'Payment has been confirmed. Thank you for choosing Sammy Tech.' : 'Your order is reserved while payment is being confirmed. Contact us if you need help.' ?></p></div>
<div class="totals"><p><span>Subtotal</span><b><?= money((float) $order['subtotal']) ?></b></p><p><span>Delivery</span><b><?= money((float) $order['delivery_charge']) ?></b></p><div class="total"><span>Total due<small>USD reference price</small></span><span><?= money((float) $order['total_amount']) ?></span></div></div>
</section>
</div>
<footer class="receipt-foot"><p>Need assistance? Call 0921628884 or WhatsApp +211 981 628 884.<br>Sammy Tech, Juba, South Sudan</p><button class="print" onclick="window.print()">Print or save PDF</button></footer>
</section>
</main>
</body>
</html>
