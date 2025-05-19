# Invent-AI | Gestione dell'inventario intelligente

Software di gestione dell'inventario pensato per le piccole e grandi aziende.

## Indice

- [Requisiti](#requisiti)
- [Installazione](#installazione)
- [Configurazione](#configurazione)
- [Uso](#uso)
- [API Documentation con Swagger](#api-documentation-con-swagger)
- [Esempi di Annotations per Swagger](#esempi-di-annotations-per-swagger)
- [Testing](#testing)
- [License](#license)

## Requisiti

Totalmente Dockerizzato

## Installazione

1. Clona il repository:
   ```bash
   git clone https://github.com/DanioFiore/invent-ai.git
   cd invent-ai
   ```

2. Lancia il run.sh:
   ```bash
   ./run.sh
   ```

3. Installa le dipendenze JavaScript e PHP:
   ```bash
   composer install
   npm install && npm run dev
   ```

4. Copia il file di ambiente e configuralo:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. Configura il tuo database nel file `.env`

6. Esegui le migrazioni:
   ```bash
   php artisan migrate
   ```

## Configurazione

### Configurazione del database

Modifica il file `.env` per impostare le credenziali del database:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=invent-ai
DB_USERNAME=root
DB_PASSWORD=
```

### Configurazione di L5-Swagger

Installa il pacchetto L5-Swagger per la documentazione API:

```bash
composer require darkaonline/l5-swagger
```

Pubblica i file di configurazione:

```bash
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
```

Modifica il file `config/l5-swagger.php` per personalizzare la configurazione.

## Uso

Per avviare il server di sviluppo:

```bash
php artisan serve
```

L'applicazione sarà accessibile all'indirizzo: http://localhost:8000

## API Documentation con Swagger

La documentazione API è generata automaticamente utilizzando L5-Swagger. Per generare la documentazione:

```bash
php artisan l5-swagger:generate
```

La documentazione sarà disponibile all'indirizzo: http://localhost:8000/api/documentation

Se riscontri l'errore `Required @OA\PathItem() not found`, assicurati di aver aggiunto correttamente le annotazioni OpenAPI nei tuoi controller.

## Esempi di Annotations per Swagger

Di seguito sono riportati esempi di come scrivere le annotazioni OpenAPI per generare la documentazione Swagger.

### Annotazione di base del Controller

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

### Annotazione per un endpoint GET

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

### Annotazione per un endpoint POST

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

### Annotazione per un endpoint PUT/PATCH

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

### Annotazione per un endpoint DELETE

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

### Definizione di uno Schema

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

### Annotazione per QueryParams

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

Per eseguire i test:

```bash
php artisan test
```

## License

Questo progetto è rilasciato sotto licenza MIT. Vedi il file [LICENSE](LICENSE) per i dettagli. 