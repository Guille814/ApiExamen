# CREACIÓN DE UNA API

## ¿QUÉ ES UN API?

Un API (Interfaz de Programación de Aplicaciones) es un conjunto de reglas
y protocolos que permiten que diferentes aplicaciones se comuniquen entre sí.

Lo que vamos a crear es un conjunto de endpoints o URLs que permitirán a los
clientes (como Postman u otras aplicaciones) realizar solicitudes HTTP para
acceder y manipular los datos.

## API REST:
Si una API es REST, quiere decir que se va a basar o respetar 6 principios
de implementación .

REST es una recomendación, no es un estándar ni un protocolo.

### Principios de implementacion:

#### 1. Arquitectura Cliente-Cervidor:
Separa la interfaz de usuario y la lógica del
usuario (cliente) de la lógica del servidor.


#### 2. Sin Estado
Cada solicitud del cliente al servidor debe
contener toda la información necesaria para
entender y procesar la solicitud.

#### 3. Cacheable
#### 4. Sistema de capas
#### 5. Codigo bajo demanda (opcional)
#### 6. Interfaz uniforme

- Hay que saber cómo solicitar la ejecución del API:
  - Cada recurso
    (información o servicio) se identifica mediante un
    URI (Uniform Resource Identifier).
  - Los recursos pueden ser
    representados y manipulados en diferentes
    formatos, como JSON o XML.
  - Cada mensaje
    incluye suficiente información para describir cómo
    procesar la solicitud.
  -  El servidor debe de facilitar
     información que nos diga cómo navegar por
     la API, no solo facilitar datos.

## Rest Full
Una api es Rest full cuando además de utilizar las 6 restricciones, utiliza el
protocolo http para su implementación .
http es un protocolo que atiende a solicitudes según el verbo o palabra
especial por el que solicitan; los que usaremos son :

- **GET:** Para solicitar un recurso o lista de recursos.
- **POST:** Para enviar un recurso al servidor que queremos crear.
- **DELETE:** Pare eliminar un recurso.
- **PUT:** Para sustituir un recurso por otro.
- **PATCH:** Para modificar algún valor de un recurso.

## Respuestas HTTP
- **1xx:** Información.
- **2xx:** Éxito.
- **3xx:** Redireccionamiento.
- **4xx:** Error en el cliente.
- **5xx:** Error en el servidor.

## JSON:API Specification (Estructura)
Es un intento de estandarizar la respuesta json, la comunicación entre cliente y
servidor
- data
- errors
- meta
- incluyed
- links
- jsonapi


# Realizacion de la API:


Nos movemos a nuestra carpeta de laravel.

```bash
cd laravel
```

Creamos un nuevo proyecto en Laravel con el siguiente comando

```bash
laravel new nombre_proyecto
```

Dejaremos todas las opciones por defecto, en la base de datos elegiremos MySQL.

<hr>

### Creacion del modelo, la factoria y la migración.

En una terminal dentro de la carpeta de nuestro proyecto:

```bash
php artisan make:model Alumno --api -fm  
```

> Alumno es un nombre de ejemplo de un modelo, ese nombre puede ser sustituido por el que se quiera. 
> Teniendo en cuenta que se necesitara utilizar el plural de ese nombre del modelo (en ingles).
> Por ejemplo, en este caso su plural seria "Alumnos", pero si la palabra fuese "Corralon", su plural seria "Corralons".

La bandera "--api" en el comando indica a Laravel que este modelo se utilizará en el contexto de una API. Esto es útil si estás construyendo una API RESTful y quieres que 
Laravel genere automáticamente algunas configuraciones específicas para este propósito.

La condición "-fm" indica que genere automaticamente tanto el archivo de factoria y migracion de ese modelo.

<hr>

### Archivo .env
Tendremos que modificar el archivo [.env](.env) segun nuestras preferencias.

```php
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:2LsZA1WpEGaIoFovyisJ8NXwi+oFyqCmn9nRmLk/Sxg=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=23306
DB_DATABASE=instituto
DB_USERNAME=alumno
DB_PASSWORD=alumno
DB_PASSWORD_ROOT=root
DB_PORT_PHPMYADMIN=8080

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

LANG_FAKE ="es_ES"
```

### Creación del archivo docker.compose

Crearemos el archivo [docker-compose.yaml](docker-compose.yaml) en el primer nivel de nuestra carpeta del proyecto.

```php
#Nombre de la version
version: "3.8"
services:
  mysql:
    # image: mysql <- Esta es otra opcion si no hacemos el build
    image: mysql

    # Para no perder los datos cuando destryamos el contenedor, se guardara en ese derectorio
    volumes:
      - ./datos:/var/lib/mysql
    ports:
      - ${DB_PORT}:3306
    environment:
      - MYSQL_USER=${DB_USERNAME}
      - MYSQL_PASSWORD=${DB_PASSWORD}
      - MYSQL_DATABASE=${DB_DATABASE}
      - MYSQL_ROOT_PASSWORD=${DB_PASSWORD_ROOT}

  phpmyadmin:
    image: phpmyadmin
    container_name: phpmyadmin  #Si no te pone por defecto el nombre_directorio-nombre_servicio
    ports:
      - ${DB_PORT_PHPMYADMIN}:80
    depends_on:
      - mysql
    environment:
      PMA_ARBITRARY: 1 #Para permitir acceder a phpmyadmin desde otra maquina
      PMA_HOST: mysql
```

<hr>

### Levantar Docker

Deberemos de levantar el docker con la base de datos. Para ello primero borraremos los que tengamos creados
para que no haya conflicto con el que creemos ahora.

Primero los paramos.
```bash
docker stop $(docker ps -a -q)  
```

Y lugo los borramos.
```bash
docker rm $(docker ps -a -q)
```

Una vez hecho esto ya se puede levantar con el comando:

```bash
docker compose up -d
```

<hr>

### Artisan Serve

Iniciamos el servidor de desarrollo local con el comando:

```bash
php artisan serve
```
<hr>

### Población de la base de datos

Dento del archivo [database/factories/AlumnoFactory.php](database/factories/AlumnoFactory.php).

Escribiremos el siguiente metodo para poder poblar nuestra base de datos.

#### AlumnoFactory.php

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Alumno>
 */
class AlumnoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "nombre"=>fake()->name(),
            "direcction"=>fake()->address(),
            "email"=>fake()->email()
        ];
    }
}
```

Aqui indicamos que la función ```definition()``` será llamada desde otro lugar del código para obtener la definición de datos que deseamos generar.

Dentro del método, se devuelve un array asociativo que contiene las claves "nombre", "dirección", "email". Estos valores seran generados mediante el uso de la función fake().

Ahora debemos indicar en el fichero [database/seeders/DatabaseSeeder.php](database/seeders/DatabaseSeeder.php) cuantas filas de datos queremos agregar a nuestra tabla en nuestra base de datos.

#### DatabaseSeeder.php

```php
<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Alumno;
use Database\Factories\AlumnoFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Alumno::factory(10)->create();
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
```
Indicamos desde el metodo ```run()``` en numero de datos.
En este caso seran 10 tal y como se puede ver en ```Alumno::factory(10)->create()```.

Si queremos modificar el idioma de población de la base de datos se debera hacer en el fichero [config/app.php](config/app.php).

#### app.php
```php
'faker_locale' => 'es_ES',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */
```
En este caso se queria poner en español, por lo tanto, en la funcion _"faker_locale"_ indicamos _"es_ES"_ para ponerlo en español.

Para crear la tabla debemos modificar el fichero [database/migrations/xxxx_xx_xx_xxxxxx_create_alumnos_table.php](database/migrations/2024_02_20_092518_create_alumnos_table.php).

#### xxxx_xx_xx_xxxxxx_create_alumnos_table.php
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('alumnos', function (Blueprint $table) {
            $table->id();
            $table->string("nombre");
            $table->string("direcction");
            $table->string("email");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumnos');
    }
};

```
A parte de las columnas que hemos dicho antes, tambien se añadira una columna de "id", y dos columnas mas, "created_at:" y "updated_at:".

1. **created_at:** Es una columna que guarda la fecha y hora en la que una fila en la tabla fue creada por primera vez.
2. **updated_at:** Es una columna que guarda la fecha y hora en la que una fila en la tabla fue actualizada por última vez.

Una vez hecho esto, ejectaremos el comando para poblar la base de datos.

```bash
php artisan migrate --seed
```

Para comprobar que la base de datos se ha poblado accedemos a la ruta localhost de nuestra base de datos.

La ruta es la siguiente [localhost:8080](localhost:8080).

<hr>

### Creación del Request y el Resource

1. **Resource:** se refiere a un tipo de controlador que proporciona métodos predefinidos para manejar operaciones CRUD en un recurso específico.


2. **Request:** se refiere a la información enviada desde el cliente al servidor web, y Laravel proporciona una clase dedicada para manejar esta información y validarla cuando sea necesario.

Pondremos los siguientes comandos para crearlos:ç

```bash
php artisan make:request AlumnoFormRequest
```
Este comando crearía un nuevo objeto de solicitud en Laravel, específicamente diseñado para validar los datos de entrada antes de que se procesen en el controlador.

```bash
php artisan make:resource AlumnoResource

```
Este archivo contendrá la clase del recurso, donde puedes definir cómo deseas transformar tus modelos en respuestas JSON estructuradas y normalizadas.

```bash
php artisan make:resource AlumnoCollection
```
Este comando sirve para crear un recurso de colección que son clases que transforman los modelos de tu aplicación en una representación JSON estructurada y normalizada.

Vamos a [app/Http/Resources/AlumnoCollection.php](app/Http/Resources/AlumnoCollection.php).

#### AlumnoCollection.php

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AlumnoCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }

    public function with(Request $request)
    {
        return [
            "jsonapi"=>[
                "version"=>"1.0"
            ]
        ];
    }
}
```
> El metodo toArray te devuelve la colecccion en un array.

>El metodo with agrega datos adicionales con formato JSON.

Vamos a [app/Http/Resources/AlumnoResoruces.php](app/Http/Resources/AlumnoResource.php)
#### AlumnoResoruces.php
```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlumnoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=>$this->id,
            "type"=>"Alumnos",
            "attributes"=>[
                "nombre"=>$this->nombre,
                "direction"=>$this->direcction,
                "email"=>$this->email,
            ],
            "link"=>[
                "self"=> url('api/alumnos/' . $this->id)
            ]
        ];
    }
    public function with(Request $request)
    {
        return [
            "jsonapi"=>[
                "version"=>"1.0"
            ]
        ];
    }
}

```

En resumen, este código define un recurso que formatea la salida del modelo Alumno en formato JSON, incluyendo campos específicos y metadatos adicionales. 

La diferencia entre el Resource y el Collection es que el Resource formatea la salida JSON de un solo modelo, mientras que Collection formatea la salida JSON de múltiples modelos. 

Pondremos la ruta del controlador de nuestro modelo en [routes/api.php](routes/api.php).

#### api.php
```php
Route::apiResource("alumnos",\App\Http\Controllers\AlumnoController::class);
```
Ahora iremos al controllador de nuestro modelo en [app/Http/Controllers/AlumnoController.php](app/Http/Controllers/AlumnoController.php)

#### AlumnoController.php
```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\AlumnoFormRequest;
use App\Http\Requests\UpdateAlumnoFormRequest;
use App\Http\Resources\AlumnoCollection;
use App\Http\Resources\AlumnoResource;
use App\Models\Alumno;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AlumnoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $alumnos = Alumno::all();
        //return response()->json($alumnos);
        return new AlumnoCollection($alumnos);
    }

    /**
     * Store a newly created resource in storage.
     */

public function store(AlumnoFormRequest $request)
    {
        $datos = $request->input("data.attributes");
        $alumno = new Alumno($datos);
        $alumno->save();
        return new AlumnoResource($alumno);
    }
```
En este fragmento del Controller se manejan las solicitudes para listar todos los alumnos y almacenar un nuevo alumno en la base de datos. 

Se utilizan clases de "AlumnoCollection" y "AlumnoResource" para formatear la salida de datos antes de devolverla como respuesta. 

Además, utiliza una clase de "AlumnoFormRequest" para validar los datos de entrada antes de almacenar un nuevo alumno.

Vamos a comprobar que al hacer la peticion a la API desde el navegador aparecen los datos con los que poblamos la base de datos en formato JSON.

Pare ello deberemos poner en nuestro navegador [localhost:8000/api/alumnos](localhost:8000/api/alumnos).

<hr>

Cuando no podamos conectarnos a la base de datos gestionaremos los errores desde [app/Exceptions/Handler.php](app/Exceptions/Handler.php)

#### Handdler.php
```php
public function render($request, Throwable $exception): \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
    {
// Errores de base de datos)
        if($exception instanceof QueryException){
            return $this->invalidJson($request, $exception);
        }
        if ($exception instanceof QueryException) {
            return response()->json([
                'errors' => [
                    [
                        'status' => '500',
                        'title' => 'Database Error',
                        'detail' => 'Error procesando la respuesta. Inténtelo más tarde.'
                    ]
                ]
            ], 500);
        }
// Delegar a la implementación predeterminada para otras excepciones no manejadas
        return parent::render($request, $exception);
    }
```

Es una forma de personalizar la respuesta de la aplicación a diferentes tipos de errores que pueden ocurrir durante su ejecución.

Para comprobar este error que hemos personalizado, bajaremos el docker con el siguiente comando:

```bash
docker compose down
```
Refrescaremos la pagina y nos daremos cuenta que nos sale el mensaje de la excepcion en formatio JSON.

<hr>

### Creación del middleware

Pondremos en nuestra consola:

```bash 
php artisan make:middleware HandleMiddleware
```

Y abriremos el archivo creado en [app/Http/Middleware/HeaderMiddleware.php](app/Http/Middleware/HeaderMiddleware.php).

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HeaderMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
{
    if ($request->header('accept') != 'application/vnd.api+json') {
        return response()->json([
            'error' => [
                "status"=>"406",
                "title"=>"Not Acceptable",
                "details"=>"Content File not specified"
            ]
        ],406);
    }
    return $next($request);
    }
}

```
Este encabezado se utiliza por el cliente para indicar al servidor qué tipo de contenido es capaz de procesar o aceptar.

En este caso, el middleware espera que el encabezado Accept tenga el valor "application/vnd.api+json", que es un tipo _MIME_ utilizado comúnmente para indicar el formato JSON de una API.

En la ruta [app/Http/Kernel.php](app/Http/Kernel.php) y dentro de ```'api' => []``` añadiremos la linea ```\App\Http\Middleware\HeaderMiddleware::class```.

Haciendo esto aplicariamos ese middleware a todas las rutas que están dentro de ese grupo específico, en este caso, las rutas de la API.

### Creacion de Usuarios

Vamos al controlador de nuestro modelo en la ruta [app/Http/Controllers/AlumnoController.php](app/Http/Controllers/AlumnoController.php)

#### AlumnoController.php
```php
public function store(AlumnoFormRequest $request)
    {
        $datos = $request->input("data.attributes");
        $alumno = new Alumno($datos);
        $alumno->save();
        return new AlumnoResource($alumno);
    }
```

Este metodo maneja la lógica para almacenar un nuevo alumno en la base de datos utilizando los datos proporcionados en la solicitud HTTP. 

Luego, devuelve una respuesta que encapsula el nuevo alumno creado en un formato específico utilizando la clase AlumnoResource.

Vamos al archivo Request en la ruta [app/Http/Requests/AlumnoFormRequest.php](app/Http/Requests/AlumnoFormRequest.php)

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AlumnoFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "data.attributes.nombre"=>"required|min:5",
            "data.attributes.direcction"=>"required",
            "data.attributes.email"=>"required|email|unique:alumnos,email"
        ];
    }
}
```

Esta clase define reglas de validación específicas que se aplicarán a los datos de entrada recibidos en una solicitud HTTP para crear un alumno.

Vamos a modificar nuestro modelo para poder introducir masivamente con datos la estructura que queremos.

Nos movemos al archivo [app/Models/Alumno.php](app/Models/Alumno.php)

#### Alumno.php

```php
    protected $fillable=["nombre","direccion","email"];
```

Abrimos el programa POSTMAN para poder hacer una solicitud, pondremos la ruta de nuestra API (localhost:8000/api/alumnos).

Y añadimos un header _Accept_, dentro pondremos _application/vnd.api+json_, y dentro de body pondremos los atributos que queremos añadir a nuestra base de datos en formato JSON.

```json
{
    "data":
    {
        "type": "Alumnos",
        "attributes":
        {
            "nombre": "Pedro",
            "direccion": "Calle falsa 33",
            "email": "g@g.com"
        }
    }
}
```
Si ha creado bien el usuario en la salida tendrá que salir el elemento en formato JSON y el código 201 correspondiuente a que se ha creado bien.

```json
{
    "data": {
        "id": "33",
        "type": "Alumnos",
        "attributes": {
            "nombre": "Pedro",
            "direccion": "Calle falsa 33",
            "email": "g@g.com"
        },
        "links": {
            "self": "http://localhost:8000/api/alumnos/33"
        }
    },
    "jsonapi": {
        "version": "1.0"
    }
}
```

### Eliminacion de Usuarios

Tendremos que dirigirnos al controlador de nuestro modelo y modificarlo

```php
public function destroy(int $id)
    {
        $alumno = Alumno::find($id);
        if (!$alumno) {
            return response()->json([
                'errors' => [
                    [
                        'status' => '404',
                        'title' => 'Resource Not Found',
                        'detail' => 'The requested resource does not exist or could not be found.'
                    ]
                ]
            ], 404);
        }

        $alumno->delete();
        return response()->noContent();
}}
```
Este método maneja las solicitudes de eliminación de un alumno específico. Primero busca el alumno por su ID, luego verifica si existe y lo elimina de la base de datos si se encuentra. Finalmente, devuelve una respuesta adecuada según el resultado de la operación de eliminación.

Si queremos comprobar que funciona la eliminación de usuarios, vamos a POSTMAN y hacemos una solicitud DELETE sobre un usuario por su id, por ejemplo ```localhost:8000/api/alumnos/12```.

Nos tendrá que dar un código 204, y comprobamos en nuestra base de datos que esa fila de usuario se ha borrado.

Al intentar borrar un usuario no existente nos dará el siguiente error:

```json
    {
    "errors": [
        {
            "status": "404",
            "title": "Resource Not Found",
            "detail": "The requested resource does not exist or could not be found."
        }
    ]
}
```

### Actualizar un usuario

Volvemos a dirigirnos a nuestro controlador para modificarlo.

```php
public function update(Request $request, int $id)
    {
        $alumno = Alumno::find($id);
        if (!$alumno) {
            return response()->json([
                'errors' => [
                    [
                        'status' => '404',
                        'title' => 'Resource Not Found',
                        'detail' => 'The requested resource does not exist or could not be found.'
                    ]
                ]
            ], 404);
        }

        $verbo = $request->method();
        //En función del verbo creo unas reglas de
        // validación u otras
        if ($verbo == "PUT") { //Valido por PUT
            $rules = [
                "data.attributes.nombre" => ["required", "min:5"],
                "data.attributes.direccion" => "required",
                "data.attributes.email" => ["required", "email", Rule::unique("alumnos", "email")->ignore($alumno)]
            ];

        } else { //Valido por PATCH
            if ($request->has("data.attributes.nombre"))
                $rules["data.attributes.nombre"]= ["required", "min:5"];
            if ($request->has("data.attributes.direccion"))
                $rules["data.attributes.direccion"]= ["required"];
            if ($request->has("data.attributes.email"))
                $rules["data.attributes.email"]= ["required", "email", Rule::unique("alumnos", "email")->ignore($alumno)];
        }

        $datos_validados = $request->validate($rules);
        //dump($datos_validados);
        foreach ($datos_validados['data']['attributes'] as $campo=>$valor)
            $datos[$campo]=$valor;


        $alumno->update($datos);
        return new AlumnoResource($alumno);
    }
```

Este controlador maneja las solicitudes de actualización de un alumno específico en la base de datos, asegurándose de validar los datos de entrada y aplicar las reglas de validación necesarias antes de realizar la actualización.

Dependiendo del verbo utilizado en la solicitud (PUT o PATCH), se definen diferentes reglas de validación para los campos del alumno. Las reglas de validación se definen en un array asociativo que contiene las reglas para cada campo.

Si queremos comprobar que funciona la actualización de usuarios, vamos a POSTMAN y hacemos una solicitud PATCH y otra PUT sobre un usuario por su id, por ejemplo ```localhost:8000/api/alumnos/12```.

Insertamos un JSON con datos.

```json
{
  "data": {
    "attributes": {
      "nombre": "pepe botika",
      "direccion":"calle esperanza s/n",
      "email": "e@d.com"
    }
  }
}
```
Si se acrualizó bien nos dara el codigo 200 y nos mostrara el JSON actualizado.

```json
    {
    "data": {
        "id": "12",
        "type": "Alumnos",
        "attributes": {
            "nombre": "pepe botika",
            "direccion": "calle esperanza s/n",
            "email": "e@d.com"
        },
        "links": {
            "self": "http://localhost:8000/api/alumnos/12"
        }
    },
    "jsonapi": {
        "version": "1.0"
    }
}

```
### Error de validación

En el handler:

```php
protected function invalidJson($request, ValidationException $exception):JsonResponse
    {
        return response()->json([
            'errors' => collect($exception->errors())->map(function ($message, $field) use
            ($exception) {
                return [
                    'status' => '422',
                    'title' => 'Validation Error',
                    'details' => $message[0],
                    'source' => [
                        'pointer' => '/data/attributes/' . $field
                    ]
                ];
            })->values()
        ], $exception->status);
    }
```
