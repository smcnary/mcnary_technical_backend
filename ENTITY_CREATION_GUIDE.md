# 🏗️ Entity Creation Guide for Symfony Backend

This guide explains how to create database entities in your Symfony application that connects to PostgreSQL.

## 📋 What Are Entities?

Entities are PHP classes that represent database tables. They use Doctrine ORM annotations to define:
- Table structure
- Field types and constraints
- Relationships between tables
- API endpoints (via API Platform)

## 🚀 Quick Start: Creating a New Entity

### 1. Create the Entity File

Create a new PHP file in `src/Entity/` with your entity name (e.g., `Product.php`):

```php
<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'products')]
#[ApiResource]
class Product
{
    // Entity properties and methods go here
}
```

### 2. Basic Entity Structure

Every entity should have:

```php
class Product
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private string $id;

    #[ORM\Column(name: 'tenant_id', type: 'uuid', nullable: true)]
    private ?string $tenantId = null;

    #[ORM\Column(name: 'created_at', type: 'datetimetz_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetimetz_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    // Getters and setters for all properties
}
```

## 🔧 Essential Annotations

### Entity Declaration
```php
#[ORM\Entity]                                    // Marks class as entity
#[ORM\Table(name: 'table_name')]                // Custom table name
#[ORM\UniqueConstraint(columns: ['field1', 'field2'])]  // Unique constraints
```

### Field Types
```php
#[ORM\Column(type: 'string', length: 255)]      // String with max length
#[ORM\Column(type: 'text', nullable: true)]     // Text field, can be null
#[ORM\Column(type: 'integer')]                  // Integer field
#[ORM\Column(type: 'boolean', options: ['default' => false])]  // Boolean with default
#[ORM\Column(type: 'datetimetz_immutable')]     // DateTime field
#[ORM\Column(type: 'json')]                     // JSON field
#[ORM\Column(type: 'uuid')]                     // UUID field
```

### Field Options
```php
#[ORM\Column(
    type: 'string',
    length: 100,
    nullable: true,
    unique: false,
    options: ['default' => 'active']
)]
```

### Validation Constraints
```php
#[Assert\NotBlank]                              // Field cannot be empty
#[Assert\Length(min: 2, max: 255)]             // String length limits
#[Assert\Email]                                 // Must be valid email
#[Assert\Regex(pattern: '/^[a-z0-9-]+$/')]     // Custom regex validation
#[Assert\Positive]                              // Must be positive number
#[Assert\Range(min: 0, max: 100)]              // Number range validation
```

## 🔗 Relationships

### One-to-Many (One Category has many Posts)
```php
// In Category entity
#[ORM\OneToMany(mappedBy: 'category', targetEntity: Post::class)]
private Collection $posts;

public function __construct()
{
    $this->posts = new ArrayCollection();
}

public function getPosts(): Collection
{
    return $this->posts;
}

public function addPost(Post $post): self
{
    if (!$this->posts->contains($post)) {
        $this->posts->add($post);
        $post->setCategory($this);
    }
    return $this;
}
```

### Many-to-One (Many Posts belong to one Category)
```php
// In Post entity
#[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'posts')]
#[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id')]
private ?Category $category = null;

public function getCategory(): ?Category
{
    return $this->category;
}

public function setCategory(?Category $category): self
{
    $this->category = $category;
    return $this;
}
```

### Many-to-Many
```php
// In Post entity
#[ORM\ManyToMany(targetEntity: Tag::class)]
#[ORM\JoinTable(name: 'post_tags')]
private Collection $tags;

public function __construct()
{
    $this->tags = new ArrayCollection();
}
```

## 🛡️ API Platform Integration

### Basic API Resource
```php
#[ApiResource]
class Product
```

### Custom Operations with Security
```php
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_USER')"),
        new GetCollection(security: "is_granted('ROLE_USER')"),
        new Post(security: "is_granted('ROLE_ADMIN')"),
        new Put(security: "is_granted('ROLE_ADMIN')"),
        new Delete(security: "is_granted('ROLE_ADMIN')")
    ]
)]
```

### API Resource Groups
```php
#[ApiResource(
    normalizationContext: ['groups' => ['product:read']],
    denormalizationContext: ['groups' => ['product:write']]
)]
```

## 📊 Database Schema Generation

After creating entities, generate the database schema:

```bash
# Create a migration
bin/console make:migration

# Run migrations
bin/console doctrine:migrations:migrate

# Or update schema directly (development only)
bin/console doctrine:schema:update --force
```

## 🎯 Best Practices

### 1. Naming Conventions
- **Class names**: PascalCase (e.g., `ProductCategory`)
- **Table names**: snake_case (e.g., `product_categories`)
- **Field names**: camelCase (e.g., `productName`)
- **Column names**: snake_case (e.g., `product_name`)

### 2. Required Fields
- Always include `id`, `createdAt`, `updatedAt`
- Include `tenantId` for multi-tenancy
- Use UUIDs for primary keys (security + scalability)

### 3. Validation
- Validate input data with Assert constraints
- Use appropriate field types and lengths
- Add database-level constraints where needed

### 4. Security
- Implement proper access control in API operations
- Use role-based security for different operations
- Validate and sanitize all input data

### 5. Performance
- Add database indexes for frequently queried fields
- Use appropriate field types (e.g., `text` vs `varchar`)
- Consider lazy loading for large collections

## 🔍 Example: Complete Product Entity

```php
<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'products')]
#[ORM\Index(columns: ['tenant_id', 'status'], name: 'idx_products_tenant_status')]
#[ORM\UniqueConstraint(columns: ['tenant_id', 'sku'])]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_USER')"),
        new GetCollection(security: "is_granted('ROLE_USER')"),
        new Post(security: "is_granted('ROLE_ADMIN')"),
        new Put(security: "is_granted('ROLE_ADMIN')"),
        new Delete(security: "is_granted('ROLE_ADMIN')")
    ]
)]
class Product
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private string $id;

    #[ORM\Column(name: 'tenant_id', type: 'uuid', nullable: true)]
    private ?string $tenantId = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 100)]
    private string $sku;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\Positive]
    private float $price;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    #[Assert\PositiveOrZero]
    private int $stockQuantity = 0;

    #[ORM\Column(type: 'string', length: 50, options: ['default' => 'active'])]
    private string $status = 'active';

    #[ORM\Column(name: 'created_at', type: 'datetimetz_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetimetz_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    // Getters and setters for all properties...
}
```

## 🚀 Next Steps

1. **Create your entities** following this guide
2. **Generate migrations** to update the database schema
3. **Test your API endpoints** using the built-in API Platform interface
4. **Add relationships** between entities as needed
5. **Implement custom logic** in entity methods or services

## 📚 Additional Resources

- [Doctrine ORM Documentation](https://www.doctrine-project.org/projects/doctrine-orm/en/latest/)
- [API Platform Documentation](https://api-platform.com/docs/)
- [Symfony Validation Constraints](https://symfony.com/doc/current/validation.html)
- [Symfony Security Documentation](https://symfony.com/doc/current/security.html)

---

**Happy coding! 🎉**
