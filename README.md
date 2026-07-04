# 🐜 SiratPay — Open Source Self-Hosted Payment Automation Platform

SiratPay is an open-source payment automation system (AGPL-3.0) — a self-hosted, plugin-based platform that unifies payment gateways, wallets, APIs, and SMS-based verification into one system.

It helps developers and businesses **accept, verify, and automate payments from any method — API or non-API — in a single workflow.**

Supported payment networks include Mobile Financial Services (MFS), payment gateways, and banking systems. The system is fully expandable — developers can build and register new payment channels, gateways, and banking connectors without modifying core logic.

## ⚡ Features

- Plugin-based architecture  
- Multi-gateway support (Stripe, PayPal, bKash, Nagad, etc.)  
- SMS verification engine  
- Webhook automation  
- Custom gateway plugins  
- REST API + SDK support  
- Fully self-hosted  

## � Wasmer deployment

This project is prepared for Wasmer Edge deployment with:
- [app.yaml](app.yaml) for the app definition
- [start.sh](start.sh) as the startup entrypoint
- environment-based database config in [pp-config.php](pp-config.php)

To deploy:
1. Push this repository to GitHub or GitLab.
2. Run `wasmer deploy` from the project root.
3. Set these environment variables in Wasmer for the database: `DB_HOST`, `DB_PORT`, `DB_USERNAME`, `DB_PASSWORD`, `DB_NAME`, `DB_PREFIX`.

## �🛡️ License

AGPL-3.0 — You can use, modify, and self-host SiratPay.  
If you distribute modified versions, you must keep them open-source under the same license.
