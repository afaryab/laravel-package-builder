# OAuth Setup with Authentik

This Laravel application supports OAuth authentication using Authentik as the identity provider. Follow these steps to configure the OAuth integration.

## Prerequisites

1. Docker and Docker Compose must be installed
2. All services must be running via `docker compose up -d`

## Authentik Configuration

### 1. Initial Setup

1. Access Authentik at `http://localhost:9000`
2. Complete the initial setup wizard to create your admin user
3. Login to the Authentik admin interface

### 2. Create OAuth Application

1. Navigate to **Applications** → **Applications**
2. Click **Create** to create a new application
3. Fill in the following details:
   - **Name**: `Laravel Package Builder`
   - **Slug**: `laravel-package-builder`
   - **Provider**: Create a new OAuth2/OpenID provider (see next step)

### 3. Create OAuth2/OpenID Provider

1. Click **Create Provider** when creating the application
2. Select **OAuth2/OpenID Provider**
3. Configure the provider with these settings:
   - **Name**: `Laravel Package Builder Provider`
   - **Client Type**: `Confidential`
   - **Client ID**: `laravel-app` (this matches the .env configuration)
   - **Client Secret**: `your-secret-key` (this matches the .env configuration)
   - **Redirect URIs**: `http://localhost/oauth/callback`
   - **Signing Algorithm**: `RS256`
   - **Scopes**: `openid profile email`

### 4. Configure Scopes and Claims

1. In the provider settings, ensure these scopes are enabled:
   - `openid`: Required for OpenID Connect
   - `profile`: For user profile information
   - `email`: For user email address

## Laravel Configuration

The Laravel application is already configured with these environment variables in `.env`:

```env
AUTH=oauth
OAUTH_CLIENT_ID=laravel-app
OAUTH_CLIENT_SECRET=your-secret-key
OAUTH_REDIRECT_URI=http://localhost/oauth/callback
OAUTH_BASE_URL=http://localhost:9000
OAUTH_AUTH_URL=http://localhost:9000/application/o/authorize/
OAUTH_TOKEN_URL=http://localhost:9000/application/o/token/
OAUTH_USER_URL=http://localhost:9000/application/o/userinfo/
```

## Testing OAuth Flow

1. Ensure `AUTH=oauth` is set in your `.env` file
2. Restart the application: `docker compose restart app`
3. Visit `http://localhost`
4. Click the **OAuth Login** button
5. You should be redirected to Authentik for authentication
6. After successful login, you'll be redirected back to the Laravel application

## User Provisioning

When a user logs in via OAuth for the first time:

1. The application checks if a user with the OAuth email exists
2. If not found, a new user is created automatically
3. The user is logged into the Laravel application
4. Future logins will use the existing user account

## Troubleshooting

### OAuth Errors

1. **Invalid Client**: Check that the Client ID and Client Secret match between Authentik and Laravel
2. **Invalid Redirect URI**: Ensure the redirect URI in Authentik exactly matches the Laravel configuration
3. **Token Exchange Failed**: Check that the OAuth Token URL is correct and accessible

### User Creation Issues

1. Check Laravel logs: `docker compose logs app`
2. Ensure the User model exists and is properly configured
3. Verify database connectivity

### Network Issues

1. Ensure all Docker containers are running: `docker compose ps`
2. Check that Authentik is accessible at `http://localhost:9000`
3. Verify Laravel application is accessible at `http://localhost`

## Security Considerations

1. **Client Secret**: In production, use a strong, randomly generated client secret
2. **HTTPS**: Always use HTTPS in production environments
3. **Redirect URIs**: Only whitelist trusted redirect URIs
4. **Token Validation**: The application validates tokens with Authentik on each request

## API Access with OAuth

When `AUTH=oauth`, the API endpoints require valid OAuth tokens:

1. Obtain an access token through the OAuth flow
2. Include the token in API requests: `Authorization: Bearer <access_token>`
3. The application validates tokens against Authentik's userinfo endpoint

## Advanced Configuration

### Custom User Attributes

To map additional Authentik user attributes to Laravel users:

1. Modify the `handleProviderCallback` method in `OAuthController`
2. Add custom field mappings in the user creation logic
3. Update the User model to include additional fields

### Token Refresh

The current implementation uses access tokens only. To implement refresh tokens:

1. Enable refresh tokens in the Authentik provider settings
2. Store refresh tokens securely in the Laravel application
3. Implement token refresh logic in the `ExternalTokenValidationService`

## Switching Between Authentication Types

To switch between authentication types, update the `AUTH` environment variable:

- `AUTH=none`: No authentication required
- `AUTH=internal`: Laravel's built-in authentication
- `AUTH=oauth`: OAuth with Authentik
- `AUTH=saml`: SAML authentication (if configured)

After changing the authentication type, restart the application to apply changes.
