# Development Guide

This guide is for contributors and developers working on the lastfm-bundle itself.

## 🧪 Testing

### Quick Commands

```bash
# Unit tests (fast, CI-compatible, no external dependencies)
composer test

# Integration tests (requires Last.fm API credentials)
composer test-integration

# All tests together (unit + integration)
composer test-all

# Code coverage (HTML + XML reports)
composer test-coverage
```

### Static Analysis & Code Quality

```bash
# Static analysis (PHPStan Level 8) - Default for Symfony 7.4+
composer analyse

# Static analysis without baseline (Symfony < 7.4)
composer analyse-legacy

# Code style check (Symfony standards)
composer cs

# Auto-fix code style
composer cs-fix
```

## 🔗 Integration Tests

Integration tests are **separated from the CI pipeline** to prevent:

- 🚫 Rate limiting (Last.fm allows 5 requests/second per IP)
- 🚫 Flaky builds due to network issues
- 🚫 Dependency on external API availability
- 🚫 Slow build times (2+ minutes vs. 0.4 seconds)

### Test Strategy

- **Unit Tests**: Fast, reliable, no external dependencies → **CI default**
- **Integration Tests**: Real API calls, rate-limited → **Manual execution**
- **Bundle Focus**: Test Symfony integration, service wiring, configuration

### Test Levels

#### 1. Public API Tests (Always Run)

- File: `tests/Integration/PublicApiIntegrationTest.php`
- No credentials required
- Tests public endpoints through Bundle: artists, albums, tracks, tags
- Safe for forks and pull requests

#### 2. Authentication Levels Test (Conditional)

- File: `tests/Integration/AuthenticatedApiIntegrationTest.php`
- Requires environment variables below
- Tests Bundle authentication configuration:
  - API Key: Read access (artist info, track search)
  - API Key + Secret: Write access (scrobbling, loving tracks)
  - User Authentication: Full user operations

### GitHub Secrets Required

To enable authenticated integration tests in CI/CD, add these secrets to your GitHub repository:

#### Repository Settings → Secrets and variables → Actions

| Secret Name            | Description               | Where to get it                                                      |
|------------------------|---------------------------|----------------------------------------------------------------------|
| `LASTFM_API_KEY`       | Your Last.fm API key      | [Last.fm API Account](https://www.last.fm/api/account/create)        |
| `LASTFM_SECRET`        | Your Last.fm API secret   | [Last.fm API Account](https://www.last.fm/api/account/create)        |
| `LASTFM_SESSION`       | Your Last.fm session key  | [Last.fm Authentication Flow](https://www.last.fm/api/authentication)|

### Local Development

```bash
# Set environment variables
export LASTFM_API_KEY="your-api-key"
export LASTFM_SECRET="your-api-secret"
export LASTFM_SESSION="your-session-key"

# Run public tests only (requires API key)
vendor/bin/phpunit tests/Integration/PublicApiIntegrationTest.php

# Run authentication tests (requires env vars above)
vendor/bin/phpunit tests/Integration/AuthenticatedApiIntegrationTest.php

# Run all integration tests
vendor/bin/phpunit tests/Integration/ --testdox
```

### Safety Notes

- Public tests are safe for any environment
- Authentication tests will be skipped if secrets are missing
- No credentials are logged or exposed in the test output
- Tests use read-only operations only (no data modification)

## 🛠️ Development Workflow

1. Fork the repository
2. Create feature branch (`git checkout -b feature/name`)
3. Make changes with tests
4. Run test suite (`composer test-all`)
5. Check code quality (`composer analyse && composer cs` or `composer analyse-legacy && composer cs` for Symfony < 7.4)
6. Commit changes (`git commit -m 'Add feature'`)
7. Push to branch (`git push origin feature/name`)
8. Open Pull Request

## 📋 Code Standards

- **PHP Version**: ^8.1
- **Code Style**: Symfony Coding Standards (@Symfony + @Symfony:risky)
- **Static Analysis**: PHPStan Level 8
- **Test Coverage**: Comprehensive unit and integration tests
- **Symfony Compatibility**: 6.4+ | 7.x | 8.x
- **Bundle Focus**: Minimal footprint, clean integration

## 🏗️ Bundle Architecture

The Symfony bundle provides:

1. **Service Integration**: Seamless LastfmClient autowiring
2. **Configuration Management**: YAML-based bundle configuration
3. **Authentication Setup**: API key for read operations, API key + secret for write operations
4. **Rate Limiting**: Optional throttling for API calls (5 requests/second per IP)
5. **Symfony Integration**: Compatible with Symfony 6.4+ | 7.x | 8.x

### Bundle-Specific Testing Focus

Integration tests ensure:

- **Bundle Integration**: Bundle correctly configures the Last.fm API client
- **Symfony Integration**: Services are properly wired and injectable  
- **Configuration**: Bundle configuration is correctly applied
- **Error Handling**: Bundle handles API errors gracefully
- **Rate Limiting**: Throttling works as configured

## 🔍 Getting Credentials

1. Go to [Last.fm API Account Creation](https://www.last.fm/api/account/create)
2. Create a new application
3. Note down your API Key and API Secret
4. For user authentication, implement the [authentication flow](https://www.last.fm/api/authentication)
