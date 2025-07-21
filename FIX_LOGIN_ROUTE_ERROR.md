# Fix: "Route login not defined" Error

## 🐛 Problem Description

When using `AUTH=oauth` or `AUTH=saml`, accessing protected admin routes (like `/admin/tokens`) resulted in:

```
Route [login] not defined
```

## 🔍 Root Cause Analysis

1. **Conditional Route Definition**: Login routes were only defined when `AUTH=internal`
2. **Default Auth Middleware**: Admin routes used Laravel's default `auth` middleware
3. **Hardcoded Redirect**: Default middleware always redirects to `route('login')`
4. **Missing Route**: When `AUTH=oauth`, no `login` route existed

## ✅ Solution Implemented

### 1. **Created Dynamic Authentication Middleware**

**File**: `app/Http/Middleware/AuthenticateWithAuthType.php`

- Handles authentication redirects based on AUTH type
- Redirects to appropriate login method:
  - `AUTH=internal` → `/login` 
  - `AUTH=oauth` → `/oauth/redirect`
  - `AUTH=saml` → `/saml/login`
- Supports both web and JSON responses

### 2. **Registered New Middleware**

**File**: `bootstrap/app.php`

```php
'auth.dynamic' => LaravelApp\Http\Middleware\AuthenticateWithAuthType::class,
```

### 3. **Updated Admin Routes**

**File**: `routes/web.php`

```php
// Changed from 'auth' to 'auth.dynamic'
Route::group(['prefix' => 'admin', 'middleware' => 'auth.dynamic'], function () {
    // Admin routes...
});
```

### 4. **Added Compatibility Login Routes**

**File**: `routes/web.php`

- `AUTH=oauth`: Creates `/login` route that redirects to `/oauth/redirect`
- `AUTH=saml`: Creates `/login` route that redirects to `/saml/login`
- Ensures `route('login')` always exists for compatibility

## 🧪 Testing Results

### ✅ **Before Fix (Broken)**
```bash
curl http://localhost/admin
# Error: Route [login] not defined
```

### ✅ **After Fix (Working)**
```bash
curl http://localhost/admin
# Redirects to: http://localhost/oauth/redirect
```

### ✅ **All Auth Types Supported**
- `AUTH=none` → Direct access to admin (no auth required)
- `AUTH=internal` → Redirects to `/login` form
- `AUTH=oauth` → Redirects to OAuth provider via `/oauth/redirect`
- `AUTH=saml` → Redirects to SAML SSO via `/saml/login`

## 📋 Files Modified

1. **Created**: `app/Http/Middleware/AuthenticateWithAuthType.php`
2. **Modified**: `bootstrap/app.php` (middleware registration)
3. **Modified**: `routes/web.php` (middleware usage + compatibility routes)

## 🔧 Key Features

### **Smart Redirects**
- Web requests: Redirect to appropriate login method
- JSON requests: Return 401 with authentication URL

### **Backward Compatibility** 
- All existing functionality preserved
- `route('login')` works for all auth types
- Seamless integration with existing code

### **Error Prevention**
- No more "route not defined" errors
- Graceful handling of unauthenticated requests
- Proper error messages for misconfigured auth

## 🚀 Benefits

1. **✅ Fixed Route Errors**: No more "Route [login] not defined" 
2. **✅ Consistent Behavior**: All auth types work seamlessly
3. **✅ Better UX**: Users get redirected to correct login method
4. **✅ API Compatible**: JSON requests get proper 401 responses
5. **✅ Future Proof**: Easy to add new auth types

## 🔄 Migration Notes

**No breaking changes** - all existing functionality preserved:
- Existing login flows continue to work
- API authentication unchanged
- Admin interface fully functional
- Token management operational

The fix is transparent to end users and maintains all existing functionality while resolving the route definition error.
