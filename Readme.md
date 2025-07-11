### Installation

You can install the package via Composer:

```bash
composer require vkoori/laravel-model-dto
```
> 💡 This package requires Laravel 8+ and PHP 8.0+ 

---

### Configuration

Create `config/dto.php` to define which DTOs should be generated:

```php
return [
    'User' => [
        'id' => 'int',
        'name' => 'string',
        'email' => '?string',
        'is_active' => 'bool',
        'deleted_at' => '?\Carbon\Carbon',
    ],
    // Add more entities as needed
];
```

---

### Usage

Generate a DTO using Artisan:

```bash
php artisan make:dto User --module=Users
```

---

### Support

If you find this package useful, please consider starring it on GitHub or sharing it with others.
