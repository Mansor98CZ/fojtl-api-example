# Setup
- `docker-compose up -d`
- `docker-compose exec app bash`
- `composer install`
- `bin/console migrations:migrate`

# Running URL
- http://localhost:8081/api/v1/product/ [GET, POST, PUT, DELETE]

## Request examples

### POST
```json
{
    "name": "Product 1",
    "price": 100.50
}
```
### PUT
```json
{
    "id": 1,
    "name": "Product 1",
    "price": 100.50
}
```

### DELETE
```json
{
    "id": 1
}
```
