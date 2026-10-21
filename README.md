# AgroProducts — Farm-to-Table Marketplace

A PHP/MySQL academic marketplace connecting customers with farmers. The release package keeps the existing two-role account flow and uses a premium forest-green, warm-ivory and muted-gold design system.

## Features

- Separate customer and farmer registration/login, profiles, role-aware navigation and logout
- Farmer dashboard, product uploads and product management
- Redesigned marketplace catalogue with live search, category filters, price/name sorting, stock signals, wishlist, responsive product cards and toast feedback
- Farmer accounts can browse the public market without being redirected to customer login; cart actions are only available to customers
- Customer catalogue, local-storage cart and order confirmation
- Checkout re-reads prices, farmer IDs and stock from the database on the server; browser-supplied prices are not trusted
- Transactional order creation and stock decrement for products with a finite stock value
- Agro Intelligence Studio: catalogue-aware, explainable product matching; inventory and listing-quality audit; waste-aware shopping planner; and a conversational assistant with a local fallback. The local recommender is a heuristic, not a trained ML model. Optional generative responses require your own API key in an ignored `config.local.php` file.
- Premium responsive visual system across the landing page, marketplace, farmer business workspace and AI Studio
- Clean schema installer that creates the three expected databases without importing real personal/order records

## Requirements

- XAMPP (Apache + MySQL/MariaDB)
- PHP with `mysqli`, `PDO` and `pdo_mysql` enabled
- A modern browser

## Local setup

1. Start **Apache** and **MySQL** in XAMPP.
2. Copy this `AgroProducts` folder into `C:\xampp\htdocs\AgroProducts`.
3. Open `http://localhost/phpmyadmin`.
4. Select **Import**, choose `database_schema.sql`, and run it on a fresh local MySQL instance. It creates the expected table structure for `shop_db`, `farmerreg_db`, and `customerreg_db`. It intentionally does not import personal records or sample orders. Do not import over existing tables; back up first and use an empty instance for the clean demo schema.
5. The PHP pages assume the standard local XAMPP credentials (`localhost`/`127.0.0.1`, user `root`, blank password). If your local MySQL password differs, the connection settings in the PHP files must be aligned before running; do not commit credentials to GitHub.
6. Open `http://localhost/AgroProducts/index.php`.
7. Register a fresh customer and a fresh farmer account. Use the farmer account to add a product; then sign in as the customer, add that product to the cart and place an order.

### Existing database warning

`database_schema.sql` is intended for a fresh local database instance. It is not a migration script and may report duplicate-table errors if tables already exist. Back up existing databases first; never drop or overwrite data you need. Do not import production or personal records into a public repository.

## Checkout and payments

Checkout creates a `Pending` order in the schema supplied with the project. This academic demo **does not process or collect payments**. Legacy endpoints that expected nonexistent payment columns or accepted raw card numbers have been disabled. Never enter real card details. A real payment flow requires a compliant hosted payment provider and must not store raw card numbers.

## GitHub safety

- Do not commit `config.local.php`, `.env`, database dumps containing personal records, logs, screenshots, or real uploads.
- The schema installer is structure-only and safe to share; verify it before public release.
- Use a dedicated database user with a strong password outside a local-only demo.
- Treat this as an academic project until authorization, CSRF protection, upload validation, rate limiting, and deployment security have been independently reviewed.

## Verification status

The source package has been checked for PHP syntax and JavaScript syntax. The archive has not been tested against a live MySQL instance in this build environment. This build environment does not include the MySQL PHP extension or a running MariaDB instance, so a live database-backed end-to-end test could not be executed here. Run the local smoke test in step 7 after importing the schema. Do not describe database checkout as independently verified until that test passes on your XAMPP setup.
