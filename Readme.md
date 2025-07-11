### Installation

You can install the package via Composer:

```bash
composer require vkoori/laravel-model-dto
```
> 💡 This package requires Laravel 8+ and PHP 8.0+ 

---

### Configuration

1. Add `\Vkoori\EntityDto\EntityDtoProvider` to Providers list.

2. Create `config/dto.php` to define which DTOs should be generated:

```php
return [
    'User' => [
        'id' => [
            'type' => 'int',
            'fillable' => false,
            'cast' => false,
        ],
        'name' => [
            'type' => 'string',
            'fillable' => true,
            'cast' => false,
        ],
        'email' => [
            'type' => '?string',
            'fillable' => true,
            'cast' => false,
        ],
        'status' => [
            'type' => '\App\Enums\StatusEnum',
            'fillable' => true,
            'cast' => true,
        ],
        'is_active' => [
            'type' => 'bool',
            'fillable' => true,
            'cast' => false,
        ],
        'deleted_at' => [
            'type' => '?\Carbon\Carbon',
            'fillable' => false,
            'cast' => false,
        ],
    ],
    // Add more entities as needed
];
```

---

### Usage

1. Generate a DTO using Artisan:

```bash
php artisan make:dto User --module=Users
```

2. Use the Trait in Your Model to automatically setup $fillable and $casts properties

```php
use Illuminate\Database\Eloquent\Model;
use Vkoori\EntityDto\Traits\AutoFillableAndCasts;

class User extends Model
{
    use AutoFillableAndCasts;
}
```

---

### Support

If you find this package useful, please consider starring it on GitHub or sharing it with others.
