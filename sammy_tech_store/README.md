# Sammy Tech Store

Standalone online store for internet equipment and Starlink accessories in Juba, South Sudan.

## Setup

1. Update `config/database.php` if your local MySQL credentials differ.
2. Open `http://localhost/sammy_tech_store/`; the local application creates and loads its database on its first visit.
3. Create the first staff user from PowerShell: `cd C:\wamp64\www\sammy_tech_store; php scripts/create_admin.php sammy-admin YourSecurePassword123`.

To change a staff password: `php scripts/reset_admin_password.php sammy-admin YourNewSecurePassword123`.

Staff access is at `/admin/login.php`. The project intentionally ships with no default administrator password. Use a password of at least 12 characters and do not use the example password above. If MySQL cannot create databases automatically, import `config/schema_v2.sql` manually through phpMyAdmin.

## Currency

All product and order values are stored in USD. The store also shows an estimated SSP equivalent using `SSP_PER_USD` in `config/app.php`. Update that one value when your local exchange rate changes, or set `SHOW_SSP_EQUIVALENT` to `false` to display USD only.

At checkout, customers can choose whether they will pay in USD or in the displayed SSP equivalent. The selected payment currency, payment amount, and SSP rate are saved with the payment record for staff review.

## Payment policy

- USD: cash or bank transfer to account `2010111329047`.
- SSP: cash only, calculated at the configured `SSP_PER_USD` rate.
- Customer payment support: `0921628884`, `0917603094`, or WhatsApp `+211 981 628 884`.

## Included features

- Customer storefront, cart, checkout, receipt, and order tracking.
- Juba pickup/delivery details, cash, mobile-money, and bank-transfer payment options.
- Staff dashboard, product/category management, product photo uploads, stock movements, installation requests, reporting, and CSV order export.
- Staff order review and CSV exports include the chosen payment currency, submitted payment amount, quoted SSP rate, reference, and payment proof when supplied.
- Payment-proof storage outside the public web folder, protected staff access, CSRF tokens, password hashing, and sign-in attempt throttling.
