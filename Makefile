# Makefile – SCPI Portfolio Simulator

# Variables
BACKEND_DIR=backend
FRONTEND_DIR=frontend
ENV_FILE=$(BACKEND_DIR)/.env

.PHONY: help setup backend frontend db migrate fixtures serve test serve-dev clean

help:
	@echo "Commandes Make disponibles :"
	@echo "  make setup         → Installe tout (backend, frontend, BDD)"
	@echo "  make backend       → Installe les dépendances backend (Composer)"
	@echo "  make frontend      → Installe les dépendances frontend (npm)"
	@echo "  make db            → Crée la base de données PostgreSQL (dev)"
	@echo "  make migrate       → Exécute les migrations Doctrine"
	@echo "  make fixtures      → Charge les fixtures de démonstration"
	@echo "  make serve         → Démarre le serveur Symfony"
	@echo "  make dev           → Démarre le serveur frontend (Vite)"
	@echo "  make serve-dev     → Démarre backend + frontend (nécessite un terminal séparé)"
	@echo "  make test          → Prépare et lance les tests backend"

# Setup complet
setup: backend frontend db migrate fixtures

# Backend (Symfony)
backend:
	cd $(BACKEND_DIR) && composer install

# Frontend (Vue.js)
frontend:
	cd $(FRONTEND_DIR) && npm install

# Base de données (PostgreSQL)
db:
	createdb scpi_portfolio || echo "DB déjà créée"

# Migrations
migrate:
	cd $(BACKEND_DIR) && php bin/console doctrine:migrations:migrate --no-interaction

# Fixtures
fixtures:
	cd $(BACKEND_DIR) && php bin/console doctrine:fixtures:load --no-interaction

# Lancer le serveur Symfony
serve:
	cd $(BACKEND_DIR) && symfony serve

# Lancer le frontend (Vite)
dev:
	cd $(FRONTEND_DIR) && npm run dev

# Lancer les deux (nécessite deux terminaux ou tmux)
serve-dev:
	@echo "Ouvre deux terminaux :"
	@echo "Terminal 1 → make serve"
	@echo "Terminal 2 → make dev"

# Tests backend
test:
	cd $(BACKEND_DIR) && \
	createdb scpi_portfolio_test || echo "Test DB déjà créée" && \
	php bin/console doctrine:migrations:migrate --env=test --no-interaction && \
	php bin/console doctrine:fixtures:load --env=test --no-interaction && \
	php bin/phpunit

