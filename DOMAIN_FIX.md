# Domain-Aware URL Generation Fix

This solution adds domain-aware URL generation to the EasyAPI package without modifying the core logic.

## Problem

When using `route('articles.show', ['slug' => $article->slug])` in API controllers, the generated URL incorrectly uses the API subdomain (`https://api.qwertyah.ddev.site/articles/test-api-1`) instead of the web domain (`https://qwertyah.ddev.site/articles/test-api-1`).

## Solution

### 1. Custom URL Generator

Created `DomainAwareUrlGenerator` that extends Laravel's `UrlGenerator` to:

-   Detect route type based on name prefix (`api.` for API routes)
-   Automatically use correct domain:
    -   API routes (with `api.` prefix) → use `api.domain.com`
    -   Web routes (without prefix) → use `domain.com` (removes `api.` subdomain if present)

### 2. Service Provider Integration

Modified `EasyApiServiceProvider::register()` to replace Laravel's default URL generator with our domain-aware version.

### 3. Helper Functions

Added convenience functions:

-   `api_route($name, $parameters, $absolute)` - Forces API domain
-   `web_route($name, $parameters, $absolute)` - Forces web domain

## Usage

### Automatic (Recommended)

```php
// In API controller - automatically uses web domain for web routes
route('articles.show', ['slug' => $article->slug])
// Result: https://qwertyah.ddev.site/articles/test-api-1

// For API routes - automatically uses API domain
route('api.articles.index')
// Result: https://api.qwertyah.ddev.site/v1/articles
```

### Explicit (If needed)

```php
// Force web domain
web_route('articles.show', ['slug' => $article->slug])

// Force API domain
api_route('articles.index')
```

## Benefits

1. **No core logic changes** - Package functionality remains untouched
2. **Automatic detection** - Works with existing `route()` calls
3. **Backward compatible** - All existing code continues to work
4. **Flexible** - Helper functions available for explicit control

## Testing

Test the fix by:

1. Calling `route('articles.show', ['slug' => 'test'])` from an API controller
2. Verifying the URL uses the web domain (without `api.` subdomain)
3. Confirming API routes still use the API subdomain correctly
