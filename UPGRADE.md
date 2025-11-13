# Upgrade Guide

## Upgrading to v2.0.0 - Complete Rewrite

### 🚨 **Breaking Changes - Complete Rewrite**

Version 2.0.0 is a complete architectural rewrite. This is essentially a new bundle, not an upgrade.

---

## Upgrading from v1.x to v2.0.0

### 🚀 **Complete Architectural Rewrite**

This version represents a ground-up rewrite based on modern Symfony bundle patterns and the new `calliostro/lastfm-client` v2.0.0 library.

---

## ⚠️ **Breaking Changes**

### Complete API Change

- **All method signatures changed** - Direct Last.fm API method mapping
- **Configuration structure changed** - New YAML configuration format
- **Service names changed** - Modern Symfony service naming
- **Parameter style changed** - Named parameters instead of arrays

### Requirements

- **PHP 8.1+ required** (modern PHP features and types)
- **Symfony 6.4+ | 7.x | 8.x** (current and future versions)
- **New dependency**: `calliostro/lastfm-client` v2.0.0+

---

## 🚀 **New in v2.0.0**

### Modern Last.fm API Integration

- **Direct API method mapping** - `$client->getArtistInfo(artist: 'Ed Sheeran')`
- **Complete API coverage** - All Last.fm endpoints (Album, Artist, Auth, Chart, Geo, Library, Tag, Track, User)
- **Named parameters** - Modern PHP 8.1+ parameter style
- **Type safety** - Full type declarations and PHPStan Level 8

### Advanced Features

- **Rate limiting integration** - Optional Symfony RateLimiter component support
- **Scrobbling support** - Full music scrobbling and user operations
- **Authentication levels** - Read-only (API key), authenticated operations (API key + secret), and user operations (API key + secret + session key)
- **Ultra-lightweight** - Minimal overhead, maximum performance

---

## � **Migration Steps**

### This is NOT an upgrade - it's a migration to a new bundle

Since this is a complete rewrite, you need to:

1. **Remove the old bundle** completely
2. **Install the new v2.0.0 bundle**
3. **Update all configuration**
4. **Rewrite all API calls**

### Old vs New Configuration

#### v1.x Configuration (OLD)

```yaml
# This configuration format is no longer supported
old_lastfm:
    client_id: 'your-key'
    # old configuration structure
```

#### v2.0.0 Configuration (NEW)

```yaml
calliostro_lastfm:
    api_key: '%env(LASTFM_API_KEY)%'
    api_secret: '%env(LASTFM_SECRET)%'
    session_key: '%env(LASTFM_SESSION)%'  # optional: for authenticated operations
    user_agent: 'MyApp/1.0 +https://myapp.com'
    rate_limiter: lastfm_api  # optional
```

### Old vs New API Usage

#### v1.x API Usage (OLD)

```php
# Old method calls (no longer supported)
$client->getArtistInfo(['artist' => 'Ed Sheeran']);
```

#### v2.0.0 API Usage (NEW)

```php
# New direct method calls with named parameters
$client->getArtistInfo(artist: 'Ed Sheeran');
$client->scrobbleTrack(artist: 'Artist', track: 'Track', timestamp: time());
$client->loveTrack(artist: 'Artist', track: 'Track');
```

---

## � **Complete Migration Process**

### Step 1: Remove Old Bundle

```bash
# Remove the old bundle completely
composer remove calliostro/lastfm-bundle

# Or if you had a different old bundle
composer remove your-old-lastfm-bundle
```

### Step 2: Install New Bundle

```bash
# Install the new v2.0.0 bundle
composer require calliostro/lastfm-bundle:^2.0
```

### Step 3: Update Configuration

Create new configuration file:

```yaml
# config/packages/calliostro_lastfm.yaml
calliostro_lastfm:
    api_key: '%env(LASTFM_API_KEY)%'
    api_secret: '%env(LASTFM_SECRET)%'
    user_agent: 'MyApp/1.0 +https://myapp.com'
```

### Step 4: Update Environment Variables

```bash
# .env.local
LASTFM_API_KEY=your_actual_api_key
LASTFM_SECRET=your_actual_secret
LASTFM_SESSION=your_session_key  # optional: for authenticated operations
```

### Step 5: Rewrite All API Calls

Update all your service and controller code to use the new API methods.

---

## 🆘 **Troubleshooting**

### Common Migration Issues

#### Old Method Calls Don't Work

**Error**: Method not found or wrong parameters

**Solution**: All method calls have changed. See the new API documentation in [README.md](README.md)

#### Configuration Not Loading

**Error**: Bundle not configured properly

**Solution**: Make sure you're using the new `calliostro_lastfm:` configuration key, not the old format.

#### Missing Environment Variables

**Error**: API calls failing with authentication errors

**Solution**: Set the new environment variables `LASTFM_API_KEY` and `LASTFM_SECRET`

#### Service Not Found

**Error**: Cannot autowire `LastfmClient`

**Solution**: Make sure you've installed v2.0.0 and cleared cache:

```bash
composer require calliostro/lastfm-bundle:^2.0
php bin/console cache:clear
```

---

## 📞 **Need Help?**

- Check the complete [README.md](README.md) for v2.0.0 usage examples
- Review [DEVELOPMENT.md](DEVELOPMENT.md) for testing and development guide
- Open an issue on [GitHub](https://github.com/calliostro/lastfm-bundle/issues)
- **Remember**: This is a completely new bundle, not an upgrade

---

## 🎯 **Summary**

v2.0.0 is a **complete rewrite** with:

✅ **Modern Last.fm API integration**  
✅ **Direct method calls** (`getArtistInfo(artist: 'name')`)  
✅ **Complete API coverage** (all Last.fm endpoints)  
✅ **Type safety** (PHP 8.1+ & PHPStan Level 8)  
✅ **Symfony 6.4+ | 7.x | 8.x support**  
✅ **100% test coverage**  
✅ **Production ready**  

**Migration required** - but worth it for the modern, type-safe API! 🚀
