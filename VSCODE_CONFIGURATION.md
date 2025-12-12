# VS Code Configuration for Open Source Development

This repository includes a comprehensive VS Code workspace configuration designed specifically for open source PHP development. The configuration provides a consistent, professional development environment for all contributors.

## 🚀 Quick Start

1. **Open the workspace** in VS Code
2. **Install recommended extensions** when prompted
3. **Start coding** with full tooling support

## 📦 Recommended Extensions

The workspace automatically suggests these free extensions:

- **bmewburn.vscode-intelephense-client** - PHP IntelliSense and language support
- **ms-vscode.vscode-json** - JSON language support and validation
- **redhat.vscode-xml** - XML language support for configuration files
- **felixfbecker.php-debug** - PHP debugging capabilities
- **sanmai.phpunit-watcher** - Automatic test running and watching
- **getpsalm.psalm-vscode-plugin** - Static analysis integration

## ⚙️ Development Tools Integration

### Testing
- **One-click test execution** via `Ctrl+Shift+P` → "Tasks: Run Task" → "Run PHPUnit Tests"
- **Debug test runs** using F5 launch configurations
- **Database-specific test suites** (SQLite, MySQL, PostgreSQL, MSSQL)
- **Combined test & analysis** workflow for comprehensive validation

### Code Quality
- **Psalm static analysis** integrated with problem panel
- **Automatic code formatting** with Intelephense
- **Import organization** on save
- **Trailing whitespace cleanup** automatically applied

### File Management
- **Smart exclusions** - Vendor, cache, and report directories hidden from search
- **Optimized performance** - File watchers exclude heavy directories
- **Consistent formatting** - 4-space indentation for PHP, JSON, and XML

## 🛠️ Available Tasks

Access via `Ctrl+Shift+P` → "Tasks: Run Task":

| Task | Purpose |
|------|---------|
| **Run PHPUnit Tests** | Execute full test suite with detailed output |
| **Run Psalm Analysis** | Static analysis for type safety and code quality |
| **Install Dependencies** | Run `composer install` |
| **Update Dependencies** | Run `composer update` |
| **Run Tests & Psalm** | Combined workflow for complete validation |

## 🔍 Debug Configurations

Launch via F5 or Debug panel:

- **Run PHPUnit Tests** - Debug full test suite
- **Run PHPUnit SQLite Tests** - Debug SQLite-specific tests
- **Run PHPUnit MySQL Tests** - Debug MySQL-specific tests
- **Run Psalm Analysis** - Debug static analysis

## 📁 Project Structure Awareness

The configuration automatically handles:

- **Cache directories** (`/reports`, `/runtime/cache`, `/private/cache`)
- **Vendor dependencies** (`/vendor`)
- **Test artifacts** (`/.phpunit.cache`)
- **Configuration files** (`phpunit.xml.dist`, `psalm.xml`, `composer.json`)

## 🌍 Cross-Platform Compatibility

- **Platform-agnostic paths** using workspace variables
- **No OS-specific settings** - works on Windows, macOS, and Linux
- **Consistent behavior** across different development environments
- **Relative paths** ensure portability

## 🎯 Benefits for Contributors

### New Contributors
- **Zero configuration** - workspace provides everything needed
- **Consistent environment** - same tools and settings for everyone
- **Quality guidance** - built-in linting and analysis
- **Easy testing** - one-click test execution

### Experienced Developers
- **Professional tooling** - enterprise-grade PHP development setup
- **Efficiency** - integrated debugging, testing, and analysis
- **Flexibility** - can override settings in user configuration
- **Standards compliance** - follows PHP community best practices

## 📋 Requirements

- **VS Code** 1.60+ (for workspace trust features)
- **PHP** 8.1+ (matching project requirements)
- **Composer** (for dependency management)

## 🔧 Customization

### Personal Overrides
Add personal preferences to your VS Code user settings - they will override workspace settings without affecting the repository.

### Team Standards
The workspace configuration enforces:
- 4-space indentation for PHP
- UTF-8 encoding
- LF line endings
- Trailing whitespace removal
- Final newline insertion

## 🚀 Getting Started

```bash
# Clone the repository
git clone https://github.com/rossaddison/data-cycle.git
cd data-cycle

# Install dependencies
composer install

# Open in VS Code
code .

# Install recommended extensions when prompted
# Press F5 to run tests or Ctrl+Shift+P for tasks
```

## 📈 Project Context

This configuration supports the **data-cycle** library, a critical dependency for the [rossaddison/invoice](https://github.com/rossaddison/invoice) project with **2,700+ fortnightly downloads**. The setup ensures reliable development practices for:

- **Cycle ORM integration** with Yii Data package
- **Multi-database support** (SQLite, MySQL, PostgreSQL, MSSQL)
- **Psalm level 1 compliance** for maximum type safety
- **117 comprehensive tests** with 257 assertions

The professional development environment helps maintain the high quality standards expected by the thousands of users depending on this library.