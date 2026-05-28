# User Module

The User module provides a comprehensive system for user management, authentication, and authorization within the SkeletonApp. It handles user profiles, roles, permissions, addresses, and provides both web and API interfaces for user-related operations.

## Overview

The module includes:
- **User Management**: Creation, updating, and validation of user accounts.
- **Authentication**: Secure login and session management (handled via `User\Auth`).
- **Authorization**: Role-based access control (RBAC) and granular permission management.
- **Address Management**: Support for multiple addresses per user (e.g., billing, shipping).
- **Extensible Plugin System**: View plugins for checking permissions and retrieving user/role data.
- **CLI Support**: Commands for password hashing and permission generation.
- **API & Dashboard Integration**: Dedicated routers and controllers for frontend, dashboard, and API access.

## Requirements

- **PHP**: >= 8.2
- **SkeletonApp Core**: Integration with the base `Provider`, DI container, and Console system.
- **Slim Framework**: Used for routing and HTTP handling.
- **Illuminate Database**: Eloquent ORM for database interactions.
- **Symfony Console**: Powers the module's CLI commands.

## Project Structure

- `ApiController/`: Controllers for API endpoints.
- `Console/`: CLI commands (`Hash`, `GeneratePermission`, `ForgotClear`).
- `Controller/`: Web controllers for frontend and dashboard.
- `Db/`: 
  - `Models/`: Eloquent models (`User`, `Role`, `Permission`, `Address`, etc.).
  - `Schema.php`: Database migration and schema definition.
- `Manager/`: Business logic managers (`UserManager`, `UserModel`, `UserAuthModel`).
- `Plugins/`: View plugins for templating engine integration.
- `Validators/`: Input validation logic (`UserValidator`).
- `Router.php`, `DashboardRouter.php`, `ApiRouter.php`: Route definitions for different contexts.
- `ServiceProvider.php`: Module initialization, service registration, and plugin management.

## Setup & Run Commands

The module is integrated into the SkeletonApp ecosystem.

1.  **Installation**:
    ```bash
    composer require skeleton-app/user
    ```

2.  **Registration**:
    The module's `ServiceProvider` is automatically registered or should be added to the application bootstrap.

3.  **Database Migration**:
    The schema is automatically added to the migration collection. Run the application's migration command to create the necessary tables.

## Usage

### Services
The module registers several services in the DI container:
- `User\Auth`: Authentication service.
- `User\Manager`: User entity and management service.
- `User\Model`: Business logic for user data.
- `Validators:UserValidator`: Validator for user-related inputs.

### View Plugins
The following plugins are available for use in templates (e.g., via Latte):
- `getUser()`: Retrieve current user data.
- `hasPermission($name)`: Check if the user has a specific permission.
- `getRole()`: Get user role information.
- `getPermission()`, `getMenuPermission()`, etc.

## Configuration (Env Vars / Config)

The module uses the application's configuration system. Key settings may include:
- `queue.mail`: Used for queuing user-related emails (e.g., registration, password reset).
- `domain`: Domain setting for email links and redirects.

TODO: Document all specific configuration keys used by this module from `config/config.ini`.

## Scripts

The module provides the following CLI commands:

- **Hash Password**:
  ```bash
  php cli hash:hash <password>
  ```
- **Generate Permissions**:
  ```bash
  php cli permission:generate
  ```

## Tests

TODO: Tests are not yet implemented for this module. When added, run them from the project root:
```bash
./vendor/bin/phpunit modules/User/tests
```

## License

This project is licensed under a proprietary license as specified in `composer.json`.
