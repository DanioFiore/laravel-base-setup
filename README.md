# Invent-AI | Manage your inventory in a smarte way

Software to manage your inventory with AI features.

## Summary

- [Requirements](#requirements)
- [Installation](#Installation)
- [Setup](#Setup)
- [How to use](#how-to-use)
- [API Documentation with Swagger](#api-documentation-with-swagger)
- [Annotations examples for Swagger](#annotations-examples-for-swagger)
- [Testing](#testing)
- [License](#license)

## Requirements

The software is completely dockerized, no needs of complex requirements, setup etc..

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/DanioFiore/invent-ai.git
   cd invent-ai
   ```

2. Run the run.sh, it will install everything you need:
   ```bash
   ./run.sh
   ```

3. Install dependencies:
   ```bash
   composer install
   npm install
   ```

4. Copy the .env file and configure it:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. Run migrations:
   ```bash
   php artisan migrate
   ```

## Setup

### L5-Swagger

We use Swagger for the documentation. Publish the files:

```bash
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
```

Edit the file `config/l5-swagger.php` to customize the configuration.

## How to use

By running the run.sh, you already start the server and you will be in the container bash

You can visit the app at: http://localhost:8080

## API Documentation with Swagger

The Swagger documentation is automatically generated thanks to the .env configuration. To generate the docs manually:

```bash
php artisan l5-swagger:generate
```

You can see the documentation at: http://localhost:8080/api/documentation

If you have error `Required @OA\PathItem() not found`, be sure to have writed correctly the Swagger annotations in your controller

## Annotations examples for Swagger

### Controller base annotation

```php
/**
 * @OA\Info(
 *     title="API Gestione Inventario",
 *     version="1.0.0",
 *     description="API per il sistema di gestione dell'inventario",
 *     @OA\Contact(
 *         email="admin@example.com",
 *         name="Support Team"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 */
class InventoryController extends Controller
{
    // metodi del controller
}
```

### Annotation for GET endpoint

```php
/**
 * @OA\Get(
 *     path="/api/products",
 *     summary="Ottieni tutti i prodotti",
 *     description="Restituisce un elenco di tutti i prodotti nell'inventario",
 *     operationId="getProductsList",
 *     tags={"Products"},
 *     @OA\Response(
 *         response=200,
 *         description="Operazione riuscita",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Product")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non autorizzato"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Accesso negato"
 *     )
 * )
 */
public function index()
{
    // implementazione
}
```

### Annotation for POST endpoint

```php
/**
 * @OA\Post(
 *     path="/api/products",
 *     summary="Crea un nuovo prodotto",
 *     description="Crea e restituisce un nuovo prodotto",
 *     operationId="storeProduct",
 *     tags={"Products"},
 *     @OA\RequestBody(
 *         required=true,
 *         description="Dati del prodotto",
 *         @OA\JsonContent(
 *             required={"name","quantity","price"},
 *             @OA\Property(property="name", type="string", example="iPhone 13"),
 *             @OA\Property(property="description", type="string", example="Smartphone Apple"),
 *             @OA\Property(property="quantity", type="integer", example=10),
 *             @OA\Property(property="price", type="number", format="float", example=999.99)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Prodotto creato con successo",
 *         @OA\JsonContent(ref="#/components/schemas/Product")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Errore di validazione"
 *     )
 * )
 */
public function store(Request $request)
{
    // implementazione
}
```

### Annotation for PUT/PATCH endpoint

```php
/**
 * @OA\Put(
 *     path="/api/products/{id}",
 *     summary="Aggiorna un prodotto esistente",
 *     description="Aggiorna e restituisce un prodotto esistente",
 *     operationId="updateProduct",
 *     tags={"Products"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID del prodotto da aggiornare",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         description="Dati del prodotto aggiornati",
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="iPhone 13 Pro"),
 *             @OA\Property(property="description", type="string", example="Smartphone Apple Pro"),
 *             @OA\Property(property="quantity", type="integer", example=5),
 *             @OA\Property(property="price", type="number", format="float", example=1299.99)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Prodotto aggiornato con successo",
 *         @OA\JsonContent(ref="#/components/schemas/Product")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Prodotto non trovato"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Errore di validazione"
 *     )
 * )
 */
public function update(Request $request, $id)
{
    // implementazione
}
```

### Annotation for DELETE endpoint

```php
/**
 * @OA\Delete(
 *     path="/api/products/{id}",
 *     summary="Elimina un prodotto",
 *     description="Elimina un prodotto dal database",
 *     operationId="deleteProduct",
 *     tags={"Products"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID del prodotto da eliminare",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Prodotto eliminato con successo"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Prodotto non trovato"
 *     )
 * )
 */
public function destroy($id)
{
    // implementazione
}
```

### Definition of a Schema

```php
/**
 * @OA\Schema(
 *     schema="Product",
 *     required={"name", "quantity", "price"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="ID del prodotto",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Nome del prodotto",
 *         example="Laptop Dell XPS 15"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Descrizione del prodotto",
 *         example="Laptop professionale con schermo 15 pollici"
 *     ),
 *     @OA\Property(
 *         property="quantity",
 *         type="integer",
 *         description="Quantità disponibile",
 *         example=20
 *     ),
 *     @OA\Property(
 *         property="price",
 *         type="number",
 *         format="float",
 *         description="Prezzo unitario",
 *         example=1499.99
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Data di creazione"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Data di ultimo aggiornamento"
 *     )
 * )
 */
```

### QueryParams annotations

```php
/**
 * @OA\Get(
 *     path="/api/products/search",
 *     summary="Cerca prodotti",
 *     description="Cerca prodotti per nome o categoria",
 *     operationId="searchProducts",
 *     tags={"Products"},
 *     @OA\Parameter(
 *         name="query",
 *         in="query",
 *         description="Termine di ricerca",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="category",
 *         in="query",
 *         description="Filtra per categoria",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="min_price",
 *         in="query",
 *         description="Prezzo minimo",
 *         required=false,
 *         @OA\Schema(type="number", format="float")
 *     ),
 *     @OA\Parameter(
 *         name="max_price",
 *         in="query",
 *         description="Prezzo massimo",
 *         required=false,
 *         @OA\Schema(type="number", format="float")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Elenco dei prodotti filtrati",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Product")
 *         )
 *     )
 * )
 */
public function search(Request $request)
{
    // implementazione
}
```

## Testing

To execute tests:

```bash
php artisan test
```

## License

This project is released under MIT license. See file [LICENSE](LICENSE) for details. 