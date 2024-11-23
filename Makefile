# Variables
DOCKER_COMPOSE = docker-compose
SERVICE_LARAVEL = app
SERVICE_NODE = node

ifneq (,$(wildcard .env))
    include .env
    export
endif

# Commande de setup
.PHONY: setup
setup: build up npm-install npm-dev composer-update ## Configure l'environnement de développement
	@echo "L'environnement de développement est prêt ! Si c'est une première installation, utilisez 'make first-setup' pour inclure les migrations et un utilisateur par défaut."

.PHONY: first-setup
first-setup: build up npm-install npm-dev composer-update migrate seed ## Configure l'environnement de développement pour une première utilisation
	@echo "Environnement prêt avec migrations et utilisateur créé."

# Commandes Docker Compose
.PHONY: build
build: ## Build les conteneurs Docker avec --no-cache et --force-rm
	$(DOCKER_COMPOSE) build --no-cache --force-rm

.PHONY: up
up: ## Démarre tous les conteneurs en arrière-plan, puis lance le serveur Vite
	$(DOCKER_COMPOSE) up -d
	$(MAKE) npm-dev

.PHONY: start
start: ## Démarre les conteneurs déjà créés
	$(DOCKER_COMPOSE) start

.PHONY: stop
stop: ## Arrête les conteneurs sans les supprimer
	$(DOCKER_COMPOSE) stop

.PHONY: down
down: ## Supprime tous les conteneurs, réseaux et volumes associés
	$(DOCKER_COMPOSE) down

.PHONY: status
status: ## Vérifie l'état des conteneurs
	$(DOCKER_COMPOSE) ps

# Commandes Laravel
.PHONY: migrate
migrate: ## Exécute les migrations de la base de données
	$(DOCKER_COMPOSE) exec $(SERVICE_LARAVEL) sh -c 'until mysqladmin ping -h"db" --silent; do echo "Waiting for database..."; sleep 1; done; php artisan migrate'

.PHONY: seed
seed: ## Exécute les seeders de la base de données
	$(DOCKER_COMPOSE) exec $(SERVICE_LARAVEL) php artisan db:seed

.PHONY: composer-update
composer-update: ## Met à jour les dépendances PHP
	$(DOCKER_COMPOSE) exec $(SERVICE_LARAVEL) composer update

# Commandes pour le front-end
.PHONY: npm-install
npm-install: ## Installe les dépendances npm
	$(DOCKER_COMPOSE) exec $(SERVICE_NODE) npm install

.PHONY: npm-dev
npm-dev: ## Lance le serveur de développement Vite
	$(DOCKER_COMPOSE) exec -d $(SERVICE_NODE) npm run dev

.PHONY: npm-build
npm-build: ## Compile les assets pour la production
	$(DOCKER_COMPOSE) exec $(SERVICE_NODE) npm run build

# Commande de nettoyage
.PHONY: clean
clean: ## Supprime les volumes et conteneurs orphelins
	$(DOCKER_COMPOSE) down --volumes --remove-orphans

# Commande d'aide détaillée
.PHONY: help
help: ## Affiche cette aide
	@echo "Commandes disponibles :"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' Makefile | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2}' | sort
