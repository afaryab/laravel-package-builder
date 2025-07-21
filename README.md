# Laravel Package Builder with Enterprise API

A Laravel application with Docker setup featuring multiple authentication methods, enterprise-grade API architecture, and external Identity Provider integration.

## 🏗️ Architecture

This setup uses specialized containers and enterpri## 📁 Project Structure

```
laravel-package-builder/
├── app/                           # Application code (LaravelApp namespace)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/              # API controllers
│   │   │   │   ├── ApiController.php
│   │   │   │   └── TokenController.php
│   │   │   └── Auth/             # Authentication controllers
│   │   └── Middleware/
│   │       └── TokenAuthMiddleware.php
│   ├── Models/
│   │   ├── User.php
│   │   └── ApplicationToken.php   # Enterprise token model
│   └── Services/
│       └── ExternalTokenValidationService.php
├── database/
│   └── migrations/
│       └── *_create_application_tokens_table.php
├── routes/
│   ├── api.php                    # Comprehensive API routes
│   └── web.php                    # Web authentication routes
├── docker-compose.yml             # Multi-service orchestration
└── README.md                      # This file
```

## 🌍 Production Deployment

### Security Checklist
- [ ] Change default database passwords
- [ ] Configure SSL/TLS certificates  
- [ ] Set strong `APP_KEY` and Authentik secrets
- [ ] Configure proper CORS settings
- [ ] Set up proper token expiration policies
- [ ] Configure IdP certificates and secrets
- [ ] Enable rate limiting on API endpoints
- [ ] Set up monitoring and logging

### Environment Variables for Production
```env
# Application
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database (use strong passwords)
DB_PASSWORD=your_strong_password

# Authentik (generate secure keys)
AUTHENTIK_SECRET_KEY=your_authentik_secret
AUTHENTIK_POSTGRESQL_PASSWORD=your_authentik_db_password

# Token Security
TOKEN_LIFETIME=3600              # Token expiration in seconds
MAX_TOKENS_PER_USER=10          # Limit tokens per user

# IdP Configuration
SAML2_SP_x509=your_production_cert
OAUTH_CLIENT_SECRET=your_oauth_secret
```patterns:

### Container Architecture
- **`app`** - SSH access for artisan commands
- **`mysql`** - Database server
- **`redis`** - Cache and session storage
- **`authentik-server`** - Identity Provider for enterprise SSO
- **`authentik-worker`** - Background processing for Authentik
- **`authentik-postgresql`** - Database for Authentik
- **`authentik-redis`** - Cache for Authentik

### API Architecture
- **Public API** - No authentication required
- **Session-based API** - Web authentication for admin users
- **Token-based API** - Enterprise API with scope-based authorization
- **External IdP Integration** - Token exchange for SAML, OAuth, Authentik

## 🚀 Quick Start

1. **Clone and setup:**
   ```bash
   git clone <repository-url>
   cd laravel-package-builder
   ./setup.sh
   ```

2. **Start the environment:**
   ```bash
   docker-compose up -d
   ```

3. **Access your application:**
   - **Web**: http://localhost
   - **API Documentation**: http://localhost/api/docs (login required)
   - **Authentik SSO**: http://localhost:9000
   - **SSH for artisan**: `ssh root@localhost -p 2222` (password: `laravel`)

## 🔗 API Endpoints

### Public Endpoints (No Authentication)
```bash
GET  /api/health              # Health check
GET  /api/info                # Application information  
GET  /api/stats               # Application statistics
POST /api/auth/exchange-token # Exchange external token for app token
```

### Session-Based Endpoints (Web Login Required)
```bash
GET  /api/user                    # Get authenticated user
GET  /api/docs                    # API documentation
POST /api/tokens/integration      # Create integration token (admin only)
GET  /api/tokens/all             # List all tokens (admin only)
```

### Token-Based API (v1) - Bearer Authentication
```bash
# Headers: Authorization: Bearer {your_token}
GET    /api/v1/user            # Get current user info
GET    /api/v1/tokens          # List user tokens
GET    /api/v1/tokens/info     # Get current token info
DELETE /api/v1/tokens/{id}     # Revoke token
GET    /api/v1/users           # Get all users (requires users:read scope)
```

## 🎯 Token Management

### Integration Tokens (System-to-System)
```bash
# Create integration token (admin login required)
curl -X POST http://localhost/api/tokens/integration \
  -H "Content-Type: application/json" \
  -d '{"name": "My Integration", "scopes": ["integration:read", "integration:write"]}'
```

### User Tokens (External IdP)
```bash
# Exchange external token for app token
curl -X POST http://localhost/api/auth/exchange-token \
  -H "Content-Type: application/json" \
  -d '{"external_token": "your_external_token", "provider": "authentik"}'
```

### Token Usage
```bash
# Use token in API calls
curl -H "Authorization: Bearer app_your_token_here" \
  http://localhost/api/v1/tokens/info
```

## 🔐 Authentication Methods

### Internal Authentication (Default)
```env
AUTH=internal
```
- Laravel's built-in authentication with automatic user provisioning
- **First Access**: Shows registration form when no users exist
- **Default Admin**: `admin@example.com` / `password` (after first signup)
- **Features**: Session-based web access + API token creation

### Basic Authentication
```env
AUTH=basic
AUTH_USER=admin
AUTH_PASSWORD=secret123
```
- HTTP Basic authentication via nginx
- Simple username/password protection

### SAML 2.0 Authentication
```env
AUTH=saml
AUTH_USER_PROVISIONING=true
SAML2_SP_x509=your_certificate
SAML2_SP_PRIVATEKEY=your_private_key
SAML2_IDP_ENTITYID=your_idp_entity_id
SAML2_IDP_SSO_URL=your_idp_sso_url
SAML2_IDP_x509=your_idp_certificate
```
- Enterprise SAML SSO integration
- **Token Exchange**: External SAML tokens exchanged for app tokens
- **User Provisioning**: Automatic user creation from SAML attributes

### OAuth Authentication
```env
AUTH=oauth
AUTH_USER_PROVISIONING=true
OAUTH_CLIENT_ID=your_client_id
OAUTH_CLIENT_SECRET=your_client_secret
OAUTH_REDIRECT_URI=http://localhost/oauth/callback
```
- OAuth 2.0 with popular providers (Google, GitHub, etc.)
- **Token Exchange**: OAuth tokens exchanged for app tokens
- **User Provisioning**: Automatic user creation from OAuth profile

### Authentik Integration
```env
# Authentik is automatically configured in docker-compose.yml
AUTHENTIK_SECRET_KEY=your_secret_key
AUTHENTIK_POSTGRESQL_PASSWORD=your_db_password
```
- **Enterprise SSO**: Full-featured identity provider
- **Web Interface**: http://localhost:9000
- **Token Validation**: Built-in API token validation
- **User Management**: Complete user lifecycle management

## 🛡️ API Security & Scopes

### Available Scopes
- **`integration:read`** - Read access for system integrations
- **`integration:write`** - Write access for system integrations  
- **`user:read`** - Read user information
- **`user:profile`** - Access user profile data
- **`users:read`** - Read all users (admin scope)
- **`*`** - Full access (admin scope)

### Token Types
1. **Integration Tokens** - For system-to-system API calls
2. **User Tokens** - For external users authenticated via IdP
3. **Session Tokens** - For web-authenticated admin users

### Security Features
- ✅ **Token Hashing** - Tokens stored as hashes in database
- ✅ **Scope Validation** - Fine-grained permission checking
- ✅ **Token Expiration** - Configurable token lifetimes
- ✅ **Audit Trail** - Last used timestamps and metadata
- ✅ **Provider Tracking** - External IdP source tracking

## 🐳 Container Management

```bash
# Start all services (including Authentik)
docker-compose up -d

# Stop all services
docker-compose down

# View logs
docker-compose logs -f

# View specific service logs
docker logs authentik-server
docker logs app

# Access containers
docker exec -it app bash
docker exec -it mysql mysql -u laravel -p

# Run artisan commands via SSH or direct exec
ssh root@localhost -p 2222
# OR
docker exec app php artisan migrate
```

## 🔧 Development & Administration

### Database Migrations
```bash
# Create and run migrations
docker exec app php artisan make:migration create_example_table
docker exec app php artisan migrate

# Check migration status
docker exec app php artisan migrate:status
```

### Token Management (Admin)
```bash
# Create integration token via Tinker
docker exec app php artisan tinker
>>> $token = \LaravelApp\Models\ApplicationToken::generateToken('API Token', 'integration', null, ['*']);
>>> echo $token['token'];
```

### User Management
```bash
# Create admin user
docker exec app php artisan tinker
>>> \LaravelApp\Models\User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => bcrypt('password')]);

# List all tokens
>>> \LaravelApp\Models\ApplicationToken::with('user')->get();
```

### API Testing
```bash
# Test public endpoints
curl http://localhost/api/health
curl http://localhost/api/info

# Test with token
curl -H "Authorization: Bearer your_token" http://localhost/api/v1/tokens/info

# Test token exchange
curl -X POST http://localhost/api/auth/exchange-token \
  -H "Content-Type: application/json" \
  -d '{"external_token": "external_token", "provider": "authentik"}'
```

## 📁 Custom Namespace

The application uses `LaravelApp` as the base namespace instead of the default `App`. This is configured in:
- `composer.json` autoload section
- All PHP classes use `LaravelApp\` prefix

## 🔧 Development

### Adding new routes
Edit `routes/web.php` and add your routes within the appropriate middleware groups.

### Database migrations
```bash
# Via SSH
ssh root@localhost -p 2222
artisan make:migration create_example_table
artisan migrate

# Or via docker-compose
docker-compose exec php php artisan make:migration create_example_table
docker-compose exec php php artisan migrate
```

### Queue jobs
```bash
# Create a job
docker-compose exec php php artisan make:job ProcessEmailJob

# The queue worker runs automatically in the queue container
# Monitor with:
docker-compose logs -f queue
```

### Scheduled tasks
Add scheduled tasks in `app/Console/Kernel.php`. The schedule container runs `php artisan schedule:run` every minute.

## 🗂️ Directory Structure

```
laravel-package-builder/
├── docker/                 # Docker configurations
│   ├── app/               # SSH + artisan container
│   ├── web/               # Nginx container
│   ├── php/               # PHP-FPM container
│   ├── queue/             # Queue worker container
│   └── schedule/          # Cron scheduler container
├── app/                   # Application code (LaravelApp namespace)
├── config/                # Configuration files
├── database/              # Migrations and seeders
├── resources/views/       # Blade templates
├── routes/                # Route definitions
├── docker-compose.yml     # Container orchestration
└── setup.sh              # Setup script
```

## 🔒 Security Notes

- SSH access uses password authentication (change in production)
- Basic auth passwords should be strong
- SAML and OAuth require proper SSL certificates in production
- Database credentials should be changed from defaults

## 📝 Environment Configuration

### Core Application Settings
```env
# Application
APP_NAME="Laravel Package Builder"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Authentication Method
AUTH=internal                       # none|basic|internal|saml|oauth

# Database
DB_CONNECTION=mysql
DB_HOST=mysql
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=password

# Cache & Sessions  
CACHE_DRIVER=redis
SESSION_DRIVER=redis
REDIS_HOST=redis

# Authentik SSO
AUTHENTIK_SECRET_KEY=your_secret_key
AUTHENTIK_POSTGRESQL_PASSWORD=authentik_password
```

### IdP Integration Settings
```env
# SAML Configuration
SAML2_SP_x509=your_certificate
SAML2_SP_PRIVATEKEY=your_private_key
SAML2_IDP_ENTITYID=your_idp_entity_id
SAML2_IDP_SSO_URL=your_idp_sso_url
SAML2_IDP_x509=your_idp_certificate

# OAuth Configuration  
OAUTH_CLIENT_ID=your_client_id
OAUTH_CLIENT_SECRET=your_client_secret
OAUTH_REDIRECT_URI=http://localhost/oauth/callback

# User Provisioning
AUTH_USER_PROVISIONING=true        # Auto-create users from external IdP
```

## 🎯 Integration Examples

### Frontend JavaScript Integration
```javascript
// Get app token from external IdP token
const response = await fetch('/api/auth/exchange-token', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        external_token: userToken,
        provider: 'authentik'
    })
});

const { data } = await response.json();
const appToken = data.token;

// Use app token for API calls
const userInfo = await fetch('/api/v1/user', {
    headers: { 'Authorization': `Bearer ${appToken}` }
});
```

### System Integration (Server-to-Server)
```bash
# 1. Create integration token (admin required)
curl -X POST http://localhost/api/tokens/integration \
  -H "Content-Type: application/json" \
  -d '{"name": "Analytics Service", "scopes": ["users:read", "integration:read"]}'

# 2. Use token for automated API calls
curl -H "Authorization: Bearer app_your_integration_token" \
  http://localhost/api/v1/users
```

### Python Integration Example
```python
import requests

# Exchange external token
response = requests.post('http://localhost/api/auth/exchange-token', {
    'external_token': external_user_token,
    'provider': 'saml'
})
app_token = response.json()['data']['token']

# Make authenticated API calls
headers = {'Authorization': f'Bearer {app_token}'}
user_data = requests.get('http://localhost/api/v1/user', headers=headers)
```

## 🆘 Troubleshooting

### Common Issues

#### Authentication Problems
```bash
# Reset admin user password
docker exec app php artisan tinker
>>> $user = \LaravelApp\Models\User::where('email', 'admin@example.com')->first();
>>> $user->password = bcrypt('newpassword');
>>> $user->save();
```

#### Token Issues
```bash
# Check token validity
docker exec app php artisan tinker
>>> \LaravelApp\Models\ApplicationToken::findToken('your_token_here');

# Revoke all expired tokens
>>> \LaravelApp\Models\ApplicationToken::where('expires_at', '<', now())->delete();
```

#### Container Issues
```bash
# Restart all services
docker-compose down && docker-compose up -d

# Check container health
docker ps
docker logs authentik-server

# Clear application caches
docker exec app php artisan cache:clear
docker exec app php artisan config:clear
```

#### Database Issues
```bash
# Run migrations
docker exec app php artisan migrate

# Reset database (development only)
docker exec app php artisan migrate:fresh --seed

# Check database connection
docker exec app php artisan tinker
>>> DB::select('SELECT 1');
```

#### API Issues
```bash
# Test API endpoints
curl -v http://localhost/api/health
curl -v -H "Authorization: Bearer token" http://localhost/api/v1/tokens/info

# Check middleware registration
docker exec app php artisan route:list | grep api
```

### Performance Optimization
```bash
# Optimize for production
docker exec app php artisan config:cache
docker exec app php artisan route:cache
docker exec app php artisan view:cache

# Monitor token usage
docker exec app php artisan tinker
>>> \LaravelApp\Models\ApplicationToken::whereNotNull('last_used_at')->orderBy('last_used_at', 'desc')->get();
```

## 🔍 Monitoring & Logging

### API Monitoring
- **Health Check**: `GET /api/health` - Monitor application status
- **Token Usage**: Track `last_used_at` in application_tokens table
- **Error Rates**: Monitor 401/403 responses for authentication issues

### Security Monitoring
- **Failed Authentication**: Monitor Laravel logs for authentication failures
- **Token Creation**: Audit token creation and revocation events
- **Scope Violations**: Monitor 403 responses for unauthorized scope access

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/amazing-feature`
3. Commit changes: `git commit -m 'Add amazing feature'`
4. Push to branch: `git push origin feature/amazing-feature`
5. Open a Pull Request

### Development Standards
- Follow PSR-12 coding standards
- Write tests for new API endpoints
- Document new authentication methods
- Update this README for new features

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
