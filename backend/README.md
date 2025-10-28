# Laravel Multi-Tenancy Backend

Sistema backend multi-tenancy desenvolvido em Laravel utilizando o pacote `stancl/tenancy` com autenticação segura via Laravel Sanctum.

## Características

- **Multi-tenancy**: Cada tenant possui seu próprio banco de dados SQLite
- **Autenticação Segura**: Sistema de tokens via Laravel Sanctum (não utiliza localStorage)
- **API Aberta**: Endpoint de login fora do contexto de tenant
- **Tenant Admin**: Tenant especial "admin" com acesso a todos os dados
- **Middleware Personalizado**: Captura tenant via cabeçalho HTTP
- **Separação de Bancos**: Landlord para tenants, bancos individuais para cada tenant

## Estrutura do Sistema

### Bancos de Dados

- **Landlord Database**: `database/landlord.sqlite` - Armazena informações dos tenants
- **Tenant Databases**: `database/tenant{tenant_id}.sqlite` - Banco individual para cada tenant

### Autenticação

O sistema utiliza Laravel Sanctum para autenticação baseada em tokens:

1. **Login**: `POST /api/login`
   - Recebe: `tenant`, `email`, `password`
   - Retorna: token de acesso seguro
   
2. **Logout**: `POST /api/logout` (autenticado)
3. **User Info**: `GET /api/user` (autenticado)

### Middleware de Tenant

O `TenantMiddleware` captura o tenant do cabeçalho `X-Tenant-ID` e:
- Inicializa o contexto do tenant apropriado
- Permite acesso especial para tenant "admin"
- Valida existência do tenant

### Tenant Admin

O tenant "admin" possui privilégios especiais:
- Acesso a dados de todos os tenants
- Não é limitado ao contexto de um tenant específico
- Pode listar todos os tenants do sistema

## Instalação e Configuração

### 1. Instalar Dependências

```bash
composer install
```

### 2. Configurar Ambiente

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Executar Migrations do Landlord

```bash
php artisan migrate
```

### 4. Criar Tenants de Teste

```bash
php artisan setup:tenants
```

### 5. Executar Migrations dos Tenants (Manual)

```bash
# Para cada tenant criado
php artisan tenants:migrate --tenants=empresa1
php artisan tenants:migrate --tenants=empresa2
php artisan tenants:migrate --tenants=empresa3
```

### 6. Criar Usuários de Teste (Manual)

Para cada tenant, acesse o contexto e crie usuários:

```php
// Exemplo via tinker
php artisan tinker

use App\Models\Tenant;
use App\Models\User;
use Stancl\Tenancy\Facades\Tenancy;

$tenant = Tenant::find('empresa1');
Tenancy::initialize($tenant);

User::create([
    'name' => 'Admin Empresa 1',
    'email' => 'admin@empresa1.com',
    'password' => bcrypt('password123')
]);
```

## Uso da API

### Headers Obrigatórios

Para rotas protegidas (exceto login):

```
Authorization: Bearer {token}
X-Tenant-ID: {tenant_id}
Content-Type: application/json
Accept: application/json
```

### Endpoints Disponíveis

#### Autenticação (Rotas Abertas)

```bash
# Login
POST /api/login
{
    "tenant": "empresa1",
    "email": "admin@empresa1.com", 
    "password": "password123"
}

# Teste da API
GET /api/test
```

#### Rotas Protegidas (Requerem Autenticação + Tenant Header)

```bash
# Informações do usuário
GET /api/user
Headers: Authorization: Bearer {token}

# Logout
POST /api/logout
Headers: Authorization: Bearer {token}

# Usuários do tenant atual
GET /api/users
Headers: Authorization: Bearer {token}, X-Tenant-ID: empresa1

# Informações do tenant atual
GET /api/current-tenant
Headers: Authorization: Bearer {token}, X-Tenant-ID: empresa1

# Listar todos os tenants (apenas admin)
GET /api/tenants
Headers: Authorization: Bearer {token}, X-Tenant-ID: admin

# Teste do contexto do tenant
GET /api/test-tenant
Headers: Authorization: Bearer {token}, X-Tenant-ID: empresa1
```

### Exemplos de Uso

#### 1. Login Normal

```bash
curl -X POST http://localhost:8000/api/login \\
  -H "Content-Type: application/json" \\
  -d '{
    "tenant": "empresa1",
    "email": "admin@empresa1.com",
    "password": "password123"
  }'
```

#### 2. Acessar Dados como Tenant Normal

```bash
curl -X GET http://localhost:8000/api/users \\
  -H "Authorization: Bearer {token}" \\
  -H "X-Tenant-ID: empresa1"
```

#### 3. Acessar Dados como Admin (Todos os Tenants)

```bash
curl -X GET http://localhost:8000/api/users \\
  -H "Authorization: Bearer {token}" \\
  -H "X-Tenant-ID: admin"
```

## Estrutura de Arquivos

```
app/
├── Console/Commands/
│   └── SetupTenantsCommand.php      # Comando para criar tenants
├── Http/
│   ├── Controllers/Api/
│   │   ├── AuthController.php       # Autenticação
│   │   └── TenantDataController.php # Dados dos tenants
│   └── Middleware/
│       └── TenantMiddleware.php     # Middleware de tenant
└── Models/
    ├── Tenant.php                   # Modelo de tenant personalizado
    └── User.php                     # Modelo de usuário

config/
├── tenancy.php                      # Configuração do stancl/tenancy
└── database.php                     # Configurações de banco

routes/
├── api.php                          # Rotas abertas (login)
└── tenant.php                       # Rotas protegidas por tenant

database/
├── migrations/                      # Migrations do landlord
└── migrations/tenant/               # Migrations dos tenants
```

## Segurança

### Tokens de Acesso

- Utiliza Laravel Sanctum para tokens seguros
- Tokens não são armazenados no localStorage do frontend
- Tokens podem ser revogados individualmente
- Suporte a múltiplos tokens por usuário

### Isolamento de Dados

- Cada tenant possui banco de dados isolado
- Middleware valida tenant antes de cada requisição
- Tenant "admin" tem acesso controlado especial
- Contexto de tenant é inicializado automaticamente

## Desenvolvimento Frontend (Vue.js)

### Configuração Recomendada

```javascript
// axios interceptor example
axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.headers.common['Content-Type'] = 'application/json';

// Adicionar tenant header em cada requisição
axios.interceptors.request.use(config => {
    const token = getTokenFromSecureStorage(); // Não usar localStorage
    const tenant = getCurrentTenant();
    
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    
    if (tenant) {
        config.headers['X-Tenant-ID'] = tenant;
    }
    
    return config;
});
```

### Armazenamento Seguro de Tokens

- **NÃO** usar localStorage
- Usar httpOnly cookies quando possível
- Ou armazenar em memória com refresh automático
- Implementar logout automático em caso de token inválido

## Comandos Úteis

```bash
# Listar tenants
php artisan tenants:list

# Executar comando para todos os tenants
php artisan tenants:run "php artisan cache:clear"

# Executar migrations para tenant específico
php artisan tenants:migrate --tenants=empresa1

# Rollback migrations de tenant
php artisan tenants:rollback --tenants=empresa1

# Criar novo tenant manualmente
php artisan tinker
>>> App\\Models\\Tenant::create(['id' => 'novo-tenant', 'data' => ['name' => 'Novo Tenant']])
```

## Troubleshooting

### Erro "Tenant não encontrado"
- Verificar se o tenant existe no banco landlord
- Confirmar se o X-Tenant-ID está sendo enviado corretamente

### Erro "Database manager not registered"
- Verificar configuração do driver SQLite no config/database.php
- Confirmar se as migrations do landlord foram executadas

### Token inválido
- Verificar se o token não expirou
- Confirmar se o header Authorization está correto
- Verificar se o usuário ainda existe no tenant

## Licença

Este projeto foi desenvolvido como sistema multi-tenancy personalizado utilizando Laravel e stancl/tenancy.
