# Upgrade Guide

## Upgrading from 0.3.0 to 0.4.0

### Modernization & Compatibility Release

This release modernizes the Last.fm Client Bundle to support current PHP and Symfony versions while dropping support for legacy versions.

---

## ⚠️ **Breaking Changes**

### PHP Version Requirements

- **PHP 8.1+ required** (previously 7.3+)
- Dropped support for PHP 7.3, 7.4, and 8.0
- Added support for PHP 8.1, 8.2, 8.3, 8.4, and upcoming 8.5

### Symfony Version Requirements

- **Symfony 6.4+ required** (previously 5.x+)
- Dropped support for Symfony 5.x
- Added support for Symfony 6.4 LTS, 7.x, and upcoming 8.x

---

## 🚀 **New Features**

### Enhanced PHP Support

- **Full PHP 8.1 – 8.5 support** — Including preparation for PHP 8.5
- Modern PHP features are used in codebase and examples

### Modern Symfony Support

- **Symfony 6.4 LTS support** — Full compatibility with the current LTS version
- **Symfony 7.x support** — Ready for all Symfony 7 releases including 7.4 LTS
- **Symfony 8.x support** — Future-proof with upcoming Symfony 8 releases

---

## 🔧 **Improvements**

### Development & Testing

- **Modernized CI/CD pipeline** — Comprehensive test matrix covering all supported PHP and Symfony versions
- **Updated PHPUnit configuration** — Using modern PHPUnit 9.6 features and schema
- **Improved test reliability** — Test suite runs with randomized execution order
- **Added composer scripts** — `composer test` command for easy testing

### Code Quality

- **Enhanced type safety** — Added return types and improved type hints
- **Modern PHP patterns** — Constructor property promotion in test code
- **Better deprecation handling** — Improved configuration for handling Symfony deprecations

### Documentation

- **Enhanced README** — Improved structure with emojis and clearer instructions
- **Modern code examples** — PHP 8.1+ attributes instead of annotations
- **Better configuration examples** — Including environment variable security tips
- **Comprehensive usage examples** — Realistic controller examples following best practices

### Development Environment

- **Cleaner .gitignore** — Refined patterns relevant to the project
- **Better caching support** — Added modern PHPUnit cache patterns

---

## 🛠 **Technical Updates**

### Dependencies

- Dependencies updated to the latest stable versions are compatible with new requirements
- Symfony components updated to support the 6.4|7.0|8.0 constraint pattern
- PHPUnit Bridge updated for modern Symfony versions

### Configuration

- **Modern PHPUnit schema** — Updated to PHPUnit 9.6 XSD
- **Improved coverage reporting** — Modern `<coverage>` element usage
- **Enhanced CI configuration** — Separate test matrices for different PHP/Symfony combinations

---

## 📚 **Documentation Updates**

### README Improvements

- **Modern PHP examples** — Updated code samples using PHP 8.1+ features
- **Symfony best practices** — Controller examples using attributes and proper DI
- **Security recommendations** — Environment variable handling tips
- **Clear installation guides** — Separate sections for Flex and non-Flex applications

### Code Examples

- **PHP 8 Attributes** — Replaced `@Route` annotations with `#[Route]` attributes
- **Type declarations** — Added proper return types and parameter types
- **Modern patterns** — Constructor property promotion examples
- **Error handling** — Improved exception handling in examples

---

## 🔄 **How to Upgrade**

### Step 1: Check Requirements

Ensure your project meets the new requirements:

```bash
# Check PHP version (must be 8.1+)
php -v

# Check Symfony version (must be 6.4+)
composer show symfony/framework-bundle
```

### Step 2: Update Dependencies

Update your `composer.json` to require the new version:

```json
{
    "require": {
        "calliostro/last-fm-client-bundle": "^0.4"
    }
}
```

### Step 3: Run Composer Update

```bash
composer update calliostro/last-fm-client-bundle
```

### Step 4: Update Your Code (Optional)

If you want to modernize your code to use the new patterns shown in the documentation:

#### Before (old annotations)

```php
/**
 * @Route("/artist/{name}", name="artist_info")
 */
public function getArtist($name, Artist $artistService)
{
    // ...
}
```

#### After (modern attributes)

```php
#[Route('/artist/{name}', name: 'artist_info')]
public function getArtist(string $name, Artist $artistService): JsonResponse
{
    // ...
}
```

### Step 5: Test Your Application

Run your tests to ensure everything works correctly:

```bash
# If using this bundle's test command
composer test

# Or your own test suite
./vendor/bin/phpunit
```

---

## 🆘 **Troubleshooting**

### Common Issues

#### PHP Version Too Old

**Error**: `Your PHP version (7.4.x) does not satisfy requirement ^8.1`

**Solution**: Upgrade PHP to version 8.1 or higher.

#### Symfony Version Too Old

**Error**: Package requirements conflict with Symfony 5.x

**Solution**: Upgrade Symfony to version 6.4 or higher.

#### Deprecation Warnings

If you see deprecation warnings, they are likely from your application code, not the bundle. Consider updating your code to use modern Symfony patterns.

---

## 📞 **Need Help?**

- Check the updated [README.md](README.md) for modern usage examples
- Open an issue on [GitHub](https://github.com/calliostro/last-fm-client-bundle/issues)
- The bundle's API remains the same — only requirements have changed

---

**That's it!** 🎉 Your Last.fm Client Bundle is now modernized and ready for the latest PHP and Symfony versions.
