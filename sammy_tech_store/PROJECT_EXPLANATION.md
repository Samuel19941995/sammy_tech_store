# Sammy Tech Online Store: Project Explanation

## 1. Project Overview

Sammy Tech is an online Internet and Starlink accessories store for customers in Juba, South Sudan. The system is built with PHP, MySQL, HTML, CSS, and JavaScript. It allows customers to browse connectivity products, place multiple items in a cart, submit an order, choose a payment method, request delivery or pickup, track an order, print a receipt, and request installation support.

The staff area allows Sammy Tech to manage products, monitor orders, confirm payments, process installations, and view sales and stock reports.

## 2. Customer Workflow

1. A customer opens the storefront at `index.php`.
2. The system loads active products and categories from the MySQL database.
3. The customer adds one or more products to the session-based shopping cart.
4. In the cart, the customer can update quantities or remove products.
5. At checkout, the customer enters their name, phone number, optional email and alternative phone number.
6. The customer selects pickup or delivery. Delivery requires an area and address or landmark.
7. The customer selects cash, mobile money, or bank transfer and can optionally provide a reference number or payment proof.
8. The system creates an order, order items, customer record, and payment record.
9. The customer receives an order number and can use it with their phone number on the tracking page.
10. After verification, the customer can print an order receipt.

Stock is not reduced at checkout. This protects the store from reducing stock for orders that are never confirmed.

## 3. Administrator Workflow

1. A staff member signs in through `admin/login.php`.
2. The dashboard displays active products, open orders, low-stock products, and completed revenue.
3. Staff review incoming customer orders and update their order status.
4. When staff confirm an order, the system locks the relevant product rows, checks stock availability, deducts stock, and records stock movements.
5. If a confirmed order is cancelled, the system restores the deducted quantity and records a restoration movement.
6. Staff update payment status to pending, paid, failed, or refunded.
7. Staff can add catalog products, handle installation requests, and open the reporting screen.

## 3.1 How the System Works Internally

The system works by passing information through the following steps:

```text
Customer chooses products
        |
        v
Products are stored in the browser session cart
        |
        v
Customer submits checkout information
        |
        v
PHP validates the form and saves customer, order, items, delivery, and payment data in MySQL
        |
        v
Order starts with status "new" and payment status "pending"
        |
        v
Staff confirm the order in the administration dashboard
        |
        v
System locks product stock, checks availability, deducts stock, and records a stock movement
        |
        v
Staff update payment and fulfilment status
        |
        v
Customer tracks the order and prints a verified receipt
```

### Example: A Starlink Order

1. A customer adds a Starlink Standard Kit, Ethernet cable, and installation service to the cart.
2. The cart calculates each line total and the combined subtotal.
3. The customer selects delivery, enters a location such as Munuki, chooses mobile money, and adds a transaction reference.
4. Checkout creates one order record and three related order-item records.
5. The order is saved as `new`; stock remains unchanged until Sammy Tech approves it.
6. A staff member verifies availability and changes the order to `confirmed`.
7. The system deducts each item's quantity in one database transaction. If any item is unavailable, the confirmation is rejected and no stock is reduced.
8. If the order is cancelled after confirmation, the same quantities are restored automatically.
9. The customer enters the order number and their phone number on the tracking page to view order and payment progress.

### Why Stock Is Processed at Confirmation

The project uses a confirmation-based stock policy. A checkout request may be abandoned, submitted with an invalid payment, or cancelled by the customer. Deducting stock immediately would make inventory inaccurate. Confirming first allows staff to validate payment, availability, and delivery details. The database transaction also helps prevent two staff actions from selling the same limited stock at the same time.

## 4. Main Modules

| Module | Purpose |
| --- | --- |
| Storefront | Shows products and lets customers add products to the cart. |
| Cart | Supports multiple items, quantity updates, removals, and totals. |
| Checkout | Stores customer, delivery, order, and payment details. |
| Order Tracking | Lets a customer verify an order using the order number and phone number. |
| Receipt | Creates a printable summary of ordered products, totals, payment, and order status. |
| Administration | Lets staff manage orders, payments, products, installations, and reports. |
| Installation Requests | Lets customers request Starlink or Wi-Fi setup support. |
| Reports | Shows sales, order status, best sellers, low stock, and inventory value. |

## 5. Database Design

The application uses MySQL. The most important tables are:

| Table | Purpose |
| --- | --- |
| `administrators` | Stores staff usernames and password hashes. |
| `categories` | Stores product groups such as Starlink Kits and Accessories. |
| `products` | Stores products, prices, quantities, product status, and reorder levels. |
| `customers` | Stores customer contact information. |
| `orders` | Stores the main order, delivery, payment, and fulfilment information. |
| `order_items` | Stores every product and quantity belonging to an order. |
| `payments` | Stores payment method, amount, reference, proof filename, and confirmation status. |
| `addresses` | Stores structured delivery locations. |
| `installation_requests` | Stores installation service requests and appointment status. |
| `stock_movements` | Records stock deductions and restoration actions for auditing. |
| `login_attempts` | Supports future login-throttling controls. |

`config/database.php` creates missing database tables and columns when the application connects locally for the first time.

## 6. Security Measures

- Passwords use PHP password hashing instead of plain text.
- Forms use CSRF tokens to reduce unauthorized form submissions.
- Customer input is validated on the server, including phone numbers and accepted payment values.
- SQL queries use prepared statements where user data is included.
- Session cookies use HttpOnly and SameSite settings.
- Payment proofs are stored outside the public website folder.
- Payment proof uploads accept only JPG, PNG, WEBP, or PDF files up to 5 MB.
- Order tracking requires both the order number and matching phone number.
- The receipt is available only after a successful tracking verification in the current session.

## 7. Installation Instructions

1. Place the `sammy_tech_store` folder inside the XAMPP or WAMP web root.
2. Start Apache and MySQL.
3. Update database credentials in `config/database.php` if the local MySQL username or password differs.
4. Open `http://localhost/sammy_tech_store/`.
5. The local database and initial sample products are created automatically if they do not already exist.
6. Create the first administrator account from a terminal:

```powershell
cd C:\wamp64\www\sammy_tech_store
php scripts/create_admin.php adminname a-secure-password-of-12-or-more-characters
```

7. Sign in at `http://localhost/sammy_tech_store/admin/login.php`.

## 8. Current Scope

The project is designed as a guest-checkout e-commerce mini-project. Customers do not need accounts to order or track an order. The core scope is product sales, payment capture, delivery or collection, stock control, installation requests, and staff reporting.

Future improvements can include customer accounts, full product editing and image management, CSV report export, order search/filtering, and production deployment using HTTPS and a dedicated MySQL user.

