.DEFAULT_GOAL := menu

CLI_ARGS := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))
$(eval $(CLI_ARGS):;@:)

PRIMARY_GOAL := $(firstword $(MAKECMDGOALS))

#
# Menu / Help Targets
#

ifeq ($(PRIMARY_GOAL),menu)
menu: ## Show the Data Cycle SYSTEM MENU (Make targets)
	@echo "================================================================================"
	@echo "                 Data Cycle SYSTEM MENU (Make targets)"
	@echo "================================================================================"
	@echo "make install           - Composer install"
	@echo "make p                 - Run PHP Psalm"
	@echo "make pf FILE=src/Foo.php     - Run PHP Psalm on specific file"
	@echo "make pd DIR=src/           - Run PHP Psalm on directory"
	@echo "make pc                - Clear Psalm's cache"
	@echo "make pi                - Psalm: Show Config/Plugins"
	@echo "make co                - Composer outdated"
	@echo "make cwn REPO=vendor/package VERSION=1.0.0  - Composer why-not"
	@echo "make ccl               - Composer clear-cache & update --lock"
	@echo "make cv                - Composer validate"
	@echo "make cda               - Composer dump-autoload"
	@echo "make cu                - Composer update"
	@echo "make crc               - Composer Require Checker"
	@echo "make cs                - PHP-CS-Fixer Dry Run"
	@echo "make cm                - PHP-CS-Fixer Fix"
	@echo "make rdr               - Rector Dry Run"
	@echo "make rmc               - Rector Make Changes"
	@echo "make pu                - Run PHPUnit Tests"
	@echo "make puc               - Run PHPUnit Tests with Coverage"
	@echo "make ric               - Roave Infection Covered"
	@echo "make riu               - Roave Infection Uncovered"
	@echo "make i                 - Infection Mutation Test"
	@echo "make ep                - Check PostgreSQL PHP extensions"
	@echo "make em                - Check MySQL PHP extensions"
	@echo "make es                - Check SQLite PHP extensions"
	@echo "make ex                - Check MSSQL PHP extensions"
	@echo "make ea                - Check ALL DB PHP extensions"
	@echo "make rp                - Run PHPUnit PostgreSQL test suite"
	@echo "make rm                - Run PHPUnit MySQL test suite"
	@echo "make rs                - Run PHPUnit SQLite test suite"
	@echo "make re                - Run PHPUnit MSSQL test suite"
	@echo "make info              - System Info/Diagnostics"
	@echo ""
	@echo "make help              - Show summary of commands"
	@echo ""
endif

ifeq ($(PRIMARY_GOAL),help)
help: ## This help.
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)
endif

#
# Installation
#

ifeq ($(PRIMARY_GOAL),install)
install: ## Composer install
	composer install
endif

#
# Psalm
#

ifeq ($(PRIMARY_GOAL),p)
p: ## Run PHP Psalm
	php vendor/bin/psalm
endif

ifeq ($(PRIMARY_GOAL),pf)
pf: ## Run PHP Psalm on a specific file
ifndef FILE
	$(error Please provide FILE, e.g. 'make pf FILE=src/Reader/EntityReader.php')
endif
	php vendor/bin/psalm "$(FILE)"
endif

ifeq ($(PRIMARY_GOAL),pd)
pd: ## Run PHP Psalm on a directory
ifndef DIR
	$(error Please provide DIR, e.g. 'make pd DIR=src/Reader/')
endif
	php vendor/bin/psalm "$(DIR)"
endif

ifeq ($(PRIMARY_GOAL),pc)
pc: ## Clear Psalm's cache
	php vendor/bin/psalm --clear-cache
endif

ifeq ($(PRIMARY_GOAL),pi)
pi: ## Psalm: Show Config/Plugins
	php vendor/bin/psalm --show-info || echo Psalm version does not support --show-info
endif

#
# Composer
#

ifeq ($(PRIMARY_GOAL),co)
co: ## Composer outdated
	composer outdated
endif

ifeq ($(PRIMARY_GOAL),cwn)
cwn: ## Composer why-not
ifndef REPO
	$(error Please provide REPO, e.g. 'make cwn REPO=vendor/package VERSION=1.0.0')
endif
ifndef VERSION
	$(error Please provide VERSION, e.g. 'make cwn REPO=vendor/package VERSION=1.0.0')
endif
	composer why-not $(REPO) $(VERSION)
endif

ifeq ($(PRIMARY_GOAL),ccl)
ccl: ## Composer clear-cache & update --lock
	composer clear-cache
	composer update --lock
endif

ifeq ($(PRIMARY_GOAL),cv)
cv: ## Composer validate
	composer validate
endif

ifeq ($(PRIMARY_GOAL),cda)
cda: ## Composer dump-autoload
	composer dump-autoload -o
endif

ifeq ($(PRIMARY_GOAL),cu)
cu: ## Composer update
	composer update
endif

#
# Composer Tools
#

ifeq ($(PRIMARY_GOAL),crc)
crc: ## Composer Require Checker
	php -d memory_limit=512M vendor/bin/composer-require-checker
endif

#
# Code Quality Tools
#

ifeq ($(PRIMARY_GOAL),cs)
cs: ## PHP-CS-Fixer Dry Run
	php vendor/bin/php-cs-fixer fix --dry-run --diff --verbose
endif

ifeq ($(PRIMARY_GOAL),cm)
cm: ## PHP-CS-Fixer Fix
	php vendor/bin/php-cs-fixer fix
endif

ifeq ($(PRIMARY_GOAL),rdr)
rdr: ## Rector Dry Run
	php vendor/bin/rector process --dry-run --output-format=console
endif

ifeq ($(PRIMARY_GOAL),rmc)
rmc: ## Rector Make Changes
	php vendor/bin/rector
endif

#
# Testing
#

ifeq ($(PRIMARY_GOAL),pu)
pu: ## Run PHPUnit Tests
	php vendor/bin/phpunit --testdox
endif

ifeq ($(PRIMARY_GOAL),puc)
puc: ## Run PHPUnit Tests with Coverage
	php vendor/bin/phpunit --testdox --coverage-html reports/coverage
endif

#
# Mutation Testing
#

ifeq ($(PRIMARY_GOAL),ric)
ric: ## Roave Infection Covered
	php vendor/bin/roave-infection-static-analysis-plugin --only-covered
endif

ifeq ($(PRIMARY_GOAL),riu)
riu: ## Roave Infection Uncovered
	php vendor/bin/roave-infection-static-analysis-plugin
endif

ifeq ($(PRIMARY_GOAL),i)
i: ## Infection Mutation Test
	php vendor/bin/infection
endif

#
# Database Extension Checks
#

ifeq ($(PRIMARY_GOAL),ep)
ep: ## Check PostgreSQL PHP extensions
	@echo "Checking for pdo_pgsql and pgsql extensions..."
	@php -m | grep -E "^pdo_pgsql$$|^pgsql$$" || (echo 'Missing PostgreSQL extensions!'; exit 1)
endif

ifeq ($(PRIMARY_GOAL),em)
em: ## Check MySQL PHP extensions
	@echo "Checking for pdo_mysql and mysql extensions..."
	@php -m | grep -E "^pdo_mysql$$|^mysql$$" || (echo 'Missing MySQL extensions!'; exit 1)
endif

ifeq ($(PRIMARY_GOAL),es)
es: ## Check SQLite PHP extensions
	@echo "Checking for pdo_sqlite and sqlite3 extensions..."
	@php -m | grep -E "^pdo_sqlite$$|^sqlite3$$" || (echo 'Missing SQLite extensions!'; exit 1)
endif

ifeq ($(PRIMARY_GOAL),ex)
ex: ## Check MSSQL PHP extensions
	@echo "Checking for pdo_sqlsrv and sqlsrv extensions..."
	@php -m | grep -E "^pdo_sqlsrv$$|^sqlsrv$$" || (echo 'Missing MSSQL extensions!'; exit 1)
endif

ifeq ($(PRIMARY_GOAL),ea)
ea: ## Check ALL DB PHP extensions
	$(MAKE) ep
	$(MAKE) em
	$(MAKE) es
	$(MAKE) ex
endif

#
# Database Test Suites
#

ifeq ($(PRIMARY_GOAL),rp)
rp: ## Run PHPUnit PostgreSQL test suite
	php vendor/bin/phpunit --testdox --testsuite Pgsql
endif

ifeq ($(PRIMARY_GOAL),rm)
rm: ## Run PHPUnit MySQL test suite  
	php vendor/bin/phpunit --testdox --testsuite Mysql
endif

ifeq ($(PRIMARY_GOAL),rs)
rs: ## Run PHPUnit SQLite test suite
	php vendor/bin/phpunit --testdox --testsuite Sqlite
endif

ifeq ($(PRIMARY_GOAL),re)
re: ## Run PHPUnit MSSQL test suite
	php vendor/bin/phpunit --testdox --testsuite Mssql
endif

#
# Diagnostics
#

ifeq ($(PRIMARY_GOAL),info)
info: ## System Info / Diagnostics
	@echo ".......... DATA CYCLE DIAGNOSTICS .........."
	php -v
	composer --version
	@echo "------------ Composer Platform Check ------------"
	composer check-platform-reqs
	@echo "------------ Project Dependencies ------------"
	composer show --tree
	@echo "------------ Database Extensions Check ------------"
	@echo "PostgreSQL extensions:"
	@php -m | grep -E "^pdo_pgsql$$|^pgsql$$" || echo "Not available"
	@echo "MySQL extensions:"
	@php -m | grep -E "^pdo_mysql$$|^mysql$$" || echo "Not available"
	@echo "SQLite extensions:"
	@php -m | grep -E "^pdo_sqlite$$|^sqlite3$$" || echo "Not available"
	@echo "MSSQL extensions:"
	@php -m | grep -E "^pdo_sqlsrv$$|^sqlsrv$$" || echo "Not available"
	@echo "------------ Test Suite Status ------------"
	php vendor/bin/phpunit --list-suites
endif

.PHONY: menu help install p pf pd pc pi co cwn ccl cv cda cu crc cs cm rdr rmc pu puc ric riu i ep em es ex ea rp rm rs re info