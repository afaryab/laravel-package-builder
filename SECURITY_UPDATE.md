# Security Update: Integration Token Management

## ⚠️ IMPORTANT SECURITY CHANGE

**Integration tokens can now ONLY be created through the admin web interface, not via API endpoints.**

### What Changed

Previously, integration tokens could be created via the API endpoint `POST /api/tokens/integration`. This has been **removed for security reasons**.

### New Token Creation Process

1. **Admin Authentication Required**: You must be logged into the admin web interface
2. **Web Interface Only**: Navigate to `/admin/tokens` to create integration tokens
3. **Enhanced Security**: Tokens are created with proper audit trails and admin oversight

### How to Create Integration Tokens

1. **Login to Admin Panel**:
   - Navigate to your application's admin panel
   - Login using your configured authentication method (internal/oauth/saml)

2. **Access Token Management**:
   - Go to `/admin/tokens` or click "Tokens" in the admin navigation
   - You'll see the token creation form and existing token list

3. **Create New Token**:
   - Fill in the token name (e.g., "Production API Integration")
   - Select required scopes/permissions
   - Optionally set an expiration date
   - Click "Create Integration Token"

4. **Secure Token Storage**:
   - The token will be displayed **once only**
   - Copy and store it securely immediately
   - The token will not be shown again

### Updated API Documentation

The API documentation has been updated to reflect this change:

- ✅ `POST /api/auth/exchange-token` - Still available for external token exchange
- ❌ `POST /api/tokens/integration` - **REMOVED** for security
- ✅ `GET /api/tokens/all` - Still available for listing tokens (admin only)

### Security Benefits

1. **Admin Oversight**: Only administrators can create integration tokens
2. **Audit Trail**: All token creation is logged with admin user information
3. **Reduced Attack Surface**: No API endpoint available for token creation
4. **Better Access Control**: Web interface provides better authentication verification

### Migration Guide

If you were previously creating tokens via API:

#### Before (DEPRECATED - NO LONGER WORKS):
```bash
curl -X POST /api/tokens/integration \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer <admin-token>" \
  -d '{"name": "My Integration", "scopes": ["integration:read", "integration:write"]}'
```

#### After (REQUIRED METHOD):
1. Login to admin web interface at `/admin`
2. Navigate to `/admin/tokens`
3. Use the web form to create tokens
4. Copy the generated token immediately

### API Usage Remains the Same

Once you have an integration token, API usage is unchanged:

```bash
curl -X GET /api/v1/user \
  -H "Authorization: Bearer <your-integration-token>"
```

### Available Scopes

When creating integration tokens, you can select from these scopes:

- `integration:read` - Read access for integrations
- `integration:write` - Write access for integrations  
- `user:read` - Read user information
- `users:read` - Read all users (admin level access)

### Token Management

From the admin interface, you can:

- ✅ Create new integration tokens
- ✅ View all existing tokens
- ✅ See token usage statistics
- ✅ Revoke tokens immediately
- ✅ Set expiration dates
- ✅ Audit token access

### Questions?

This change improves security by ensuring only authenticated administrators can create powerful integration tokens. The token usage and API access patterns remain exactly the same - only the creation process has moved to a more secure admin-only interface.

If you have any questions about this change or need assistance with token management, please refer to the admin documentation or contact your system administrator.
