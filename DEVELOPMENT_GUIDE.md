# Development Workflow Guide

## Quick Start for Contributors

### Prerequisites
- PHP 8.1+ (tested on 8.4.15)
- Composer
- SQLite (built into PHP)
- MySQL (optional, for full test suite)

### Initial Setup
```bash
# Clone and install dependencies
git clone <repository-url>
cd data-cycle
composer install

# Verify installation
vendor/bin/phpunit --configuration=phpunit.xml.dist
vendor/bin/psalm --show-info=false
```

## VS Code Integration

### Recommended Extensions
The project includes `.vscode/extensions.json` with essential extensions:
- **bmewburn.vscode-intelephense-client**: PHP IntelliSense
- **getpsalm.psalm-vscode-plugin**: Static analysis integration
- **sanmai.phpunit-watcher**: Test runner integration
- **github.copilot**: AI-powered development assistance

### Built-in Tasks
Access via `Ctrl+Shift+P` → "Tasks: Run Task":

| Task | Command | Purpose |
|------|---------|---------|
| Run PHPUnit Tests | `vendor/bin/phpunit --configuration=phpunit.xml.dist` | Execute full test suite |
| Run Psalm Analysis | `vendor/bin/psalm --show-info=false` | Static analysis |
| Install Dependencies | `composer install` | Fresh dependency install |
| Update Dependencies | `composer update` | Update to latest versions |
| Run Tests & Psalm | Combined task | Full validation pipeline |

### Debug Configurations
Available in VS Code Debug panel (`F5`):
- **Run PHPUnit Tests**: Debug full test suite
- **Run PHPUnit SQLite Tests**: Debug SQLite-specific tests
- **Run PHPUnit MySQL Tests**: Debug MySQL-specific tests  
- **Run Psalm Analysis**: Debug static analysis

## Testing Strategy

### Test Organization
```
tests/
├── Exception/           # Exception behavior tests
├── Feature/            # Integration tests by database
│   ├── Base/          # Shared test logic
│   ├── Sqlite/        # SQLite-specific tests
│   ├── Mysql/         # MySQL-specific tests
│   ├── Pgsql/         # PostgreSQL tests
│   └── Mssql/         # SQL Server tests
├── Unit/              # Unit tests
│   └── Reader/        # EntityReader unit tests
└── Support/           # Test utilities and stubs
```

### Running Specific Test Suites
```bash
# All tests (default: SQLite)
vendor/bin/phpunit

# Specific database
vendor/bin/phpunit --testsuite=Mysql
vendor/bin/phpunit --testsuite=Pgsql
vendor/bin/phpunit --testsuite=Mssql

# Specific test file
vendor/bin/phpunit tests/Unit/Reader/EntityReaderTest.php

# With coverage (if xdebug enabled)
vendor/bin/phpunit --coverage-html reports/coverage
```

### Test Categories

#### Unit Tests
- **EntityReader**: Core functionality testing
- **Filter Handlers**: Individual filter behavior
- **Cache Components**: Caching mechanism validation

#### Integration Tests  
- **Database Operations**: End-to-end database interactions
- **Filter Combinations**: Complex filtering scenarios
- **Performance**: Query optimization validation

#### Feature Tests
- **Cross-Database**: Behavior consistency across databases
- **Error Handling**: Exception scenarios
- **Edge Cases**: Boundary condition testing

## Code Quality Standards

### Psalm Level 1 Compliance
```bash
# Check compliance
vendor/bin/psalm --show-info=false

# Generate baseline (if needed)
vendor/bin/psalm --set-baseline=psalm-baseline.xml
```

**Requirements**:
- Zero static analysis errors
- Proper type annotations on all methods
- Generic templates for collections
- No mixed types unless absolutely necessary

### PHPUnit Best Practices
- **No Risky Tests**: All tests must have assertions
- **No Notices**: Clean test execution
- **Descriptive Names**: Clear test method naming
- **Data Providers**: Use for multiple test scenarios
- **Proper Mocking**: Mock external dependencies only

### Code Style
Following project conventions:
- **PSR-12** code style
- **Strict types** declaration in all files
- **Final classes** where inheritance not intended
- **Readonly properties** where applicable

## Development Workflow

### Feature Development
1. **Branch Creation**: Create feature branch from `master`
2. **Implementation**: Write code with full type annotations
3. **Testing**: Add comprehensive tests for new functionality
4. **Analysis**: Ensure Psalm Level 1 compliance
5. **Validation**: Run full test suite across databases

### Bug Fixes
1. **Reproduction**: Create failing test case
2. **Investigation**: Use debugger and static analysis
3. **Fix**: Implement minimal necessary changes
4. **Regression Testing**: Ensure fix doesn't break existing functionality

### Release Process
1. **Final Validation**: All tests passing, zero Psalm errors
2. **Documentation Update**: Update relevant documentation
3. **Version Tagging**: Follow semantic versioning
4. **Impact Assessment**: Consider downstream dependencies

## Debugging Techniques

### PHPUnit Debugging
```bash
# Verbose output
vendor/bin/phpunit --verbose

# Stop on first failure  
vendor/bin/phpunit --stop-on-failure

# Filter specific tests
vendor/bin/phpunit --filter="testMethodName"

# Debug mode (with xdebug)
php -dxdebug.mode=debug vendor/bin/phpunit
```

### Database Query Debugging
```php
// In tests, inspect generated SQL
$reader = new EntityReader($select);
$sql = $reader->withFilter($filter)->getSql();
var_dump($sql); // Examine actual SQL generated
```

### Static Analysis Debugging
```bash
# Detailed psalm output
vendor/bin/psalm --show-info=true --verbose

# Specific file analysis
vendor/bin/psalm src/Reader/EntityReader.php
```

## Integration with rossaddison/invoice

### Dependency Chain
```
rossaddison/invoice (2,700+ fortnightly downloads)
└── yiisoft/data-cycle (this package)
    ├── cycle/orm
    ├── yiisoft/data
    └── Various database drivers
```

### Critical Integration Points
1. **Entity Reading**: Invoice data querying and filtering
2. **Report Generation**: Complex data aggregation
3. **Search Functionality**: User-facing data filtering
4. **Performance**: Large dataset handling

### Compatibility Requirements
- **PHP Version**: Must support invoice project's PHP requirements
- **Database Support**: SQLite (dev) + MySQL (production) minimum
- **Memory Efficiency**: Handle large invoice datasets
- **Type Safety**: Full static analysis compatibility

This guide ensures smooth development workflow while maintaining the high quality standards required for a package supporting thousands of users.