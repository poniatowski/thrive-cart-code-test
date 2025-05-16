# Acme Widget Co - Shopping Basket Solution

[![PHP Version](https://img.shields.io/badge/php-%5E8.2-blue.svg)](https://php.net/)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

Thrive Cart - Architech Labs Code Test (proof of concept for new sales system).

A modern PHP application with containerized development environment, featuring:

- Docker-based infrastructure for consistent development
- Quality assurance tools (PHPStan, PHPCS) baked into workflows
- Testing suite with PHPUnit and coverage reporting
- Makefile automation for streamlined development tasks

## Features

A high-performance shopping basket microservice designed for e-commerce platforms, featuring:

Core Features 🚀
- 🛒 Cart Management – Add, remove, or update items with ease
- 💰 Flexible Pricing – Handle discounts, promotions, and taxes
- ⚡ Blazing Fast – Optimized for quick cart operations
- 📦 Simple Integration – Clean REST API for any platform
- 📊 Analytics Ready – Track cart abandonment and popular items

Why Choose ACME Basket? 🎯
- Lightweight – No complex dependencies
- Scalable – Containerized with Docker for cloud-native deploys
- Consistent – Containerized for reliable dev/prod parity
- Tested – Rigorous checks via make all (PHPStan, PHPCS, PHPUnit)

## Requirements

- Git
- Docker
- Docker Compose

## Installation

1. Clone the repository:
```bash
   git clone https://github.com/poniatowski/thrive-cart-code-test.git
   cd thrive-cart-code-test
```

2. Set up the project:
```bash
make setup
```

3. Run basket:
```bash
make debug
```

4. Run all test:
```bash
make test
```

## All commands are run through make:

### 🛠️ Development Commands
| Command      | Description                               |
|--------------|-------------------------------------------|
| `make help`  | Show all available make commands          |
| `make build` | Rebuild Docker containers                 |
| `make setup` | Full project setup (build + install deps) |
| `make up`    | Start containers in detached mode         |
| `make down`  | Stop containers                           |
| `make bash`  | Enter the app container shell             |


### ✅ Testing & Quality Assurance
| Command              | Description                       |
|----------------------|-----------------------------------|
| `make test`          | Run PHPUnit tests                 |
| `make test-coverage` | Run tests with coverage reporting |
| `make stan`          | Run PHPStan static analysis       |
| `make cs`            | Run PHP Code Sniffer              |
| `make fix`           | Run PHP Code Beautifier and Fixer |
| `make all`           | Run all checks (test, stan, cs)   |

