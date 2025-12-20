@echo off
:: This batch script provides a menu to run common commands for the Data Cycle project.
:: Ensure that the file is saved in Windows (CRLF) format e.g. Netbeans bottom right corner

title Data Cycle Command Menu
cd /d "%~dp0"

:menu
cls
echo =======================================
echo         Data Cycle SYSTEM MENU
echo =======================================
echo [1] Run PHP Psalm
echo [2] Run PHP Psalm on a Specific File
echo [2a] Clear Psalm's cache (in the event of stubborn errors)
echo [3] Check Composer Outdated
echo [3a] Composer why-not {repository eg. yiisoft/yii-demo} {patch/minor version e.g. 1.1.1}
echo [4] Run Composer Update
echo [5] Run Composer Require Checker
echo [5a] Run Rector See Potential Changes
echo [5b] Run Rector Make Changes
echo [6] Run PHPUnit Tests (All)
echo [6a] Run PHPUnit Tests with Coverage
echo [6b] Run PHPUnit SQLite Tests
echo [6c] Run PHPUnit MySQL Tests
echo [6d] Run PHPUnit PostgreSQL Tests
echo [6e] Run PHPUnit MSSQL Tests
echo [7] Exit
echo [8] Exit to Current Directory
echo =======================================
set /p choice="Enter your choice [1-8]: "

if "%choice%"=="1" goto psalm
if "%choice%"=="2" goto psalm_file
if "%choice%"=="2a" goto psalm_clear_cache
if "%choice%"=="3" goto outdated
if "%choice%"=="3a" goto composerwhynot
if "%choice%"=="4" goto composer_update
if "%choice%"=="5" goto require_checker
if "%choice%"=="5a" goto rector_see_changes
if "%choice%"=="5b" goto rector_make_changes
if "%choice%"=="6" goto phpunit_all
if "%choice%"=="6a" goto phpunit_coverage
if "%choice%"=="6b" goto phpunit_sqlite
if "%choice%"=="6c" goto phpunit_mysql
if "%choice%"=="6d" goto phpunit_pgsql
if "%choice%"=="6e" goto phpunit_mssql
if "%choice%"=="7" goto exit
if "%choice%"=="8" goto exit_to_directory
echo Invalid choice. Please try again.
pause
goto menu

:composer_update
echo Running Composer Update...
composer update
pause
goto menu

:psalm
echo Running PHP Psalm...
php vendor/bin/psalm
pause
goto menu

:psalm_file
echo Running PHP Psalm on a specific file...
set /p file="Enter the path to the file (relative to the project root): "
if "%file%"=="" (
    echo No file specified. Returning to the menu.
    pause
    goto menu
)
php vendor/bin/psalm "%file%"
pause
goto menu

:psalm_clear_cache
echo Running PHP Psalm...
php vendor/bin/psalm --clear-cache
pause
goto menu

:outdated
echo Checking Composer Outdated...
composer outdated
pause
goto menu

:composerwhynot
@echo off
set /p repo="Enter the package name (e.g. vendor/package): "
set /p version="Enter the version (e.g. 1.0.0): "
composer why-not %repo% %version%
pause
goto menu

:require_checker
echo Running Composer Require Checker...
php vendor/bin/composer-require-checker
pause
goto menu

:rector_see_changes
echo See changes that Rector Proposes
php vendor/bin/rector process --dry-run
pause
goto menu

:rector_make_changes
echo Make changes that Rector Proposed 
php vendor/bin/rector
pause
goto menu

:phpunit_all
echo Running PHPUnit Tests (All Suites)...
php vendor/bin/phpunit --testdox
pause
goto menu

:phpunit_coverage
echo Running PHPUnit Tests with Coverage...
php vendor/bin/phpunit --testdox --coverage-html reports/coverage
pause
goto menu

:phpunit_sqlite
echo Running PHPUnit SQLite Tests...
php vendor/bin/phpunit --testdox --testsuite=Sqlite
pause
goto menu

:phpunit_mysql
echo Running PHPUnit MySQL Tests...
php vendor/bin/phpunit --testdox --testsuite=Mysql
pause
goto menu

:phpunit_pgsql
echo Running PHPUnit PostgreSQL Tests...
php vendor/bin/phpunit --testdox --testsuite=Pgsql
pause
goto menu

:phpunit_mssql
echo Running PHPUnit MSSQL Tests...
php vendor/bin/phpunit --testdox --testsuite=Mssql
pause
goto menu

:exit_to_directory
echo Returning to the current directory. Goodbye!
cmd

:exit
echo Exiting. Goodbye!
pause
exit