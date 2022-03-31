Nuestro sistema de puntuación de credibilidad, altamente avanzado y propio, está teniendo algunos problemas de rendimiento. Para ayudar a depurarlo, queremos medir el tiempo que tarda en calcularse una puntuación. La forma más sencilla de implementarlo sería casi por completo dentro de `SightingScorer`. Podríamos establecer una hora de inicio en la parte superior, y luego usarla aquí abajo para calcular una duración. Y luego podríamos pasar ese `$duration` a la clase `BigFootSightingScore`. Mantén pulsado Comando o Ctrl y haz clic para abrirla: está en el directorio `src/Model/`. Aquí dentro, podríamos crear una nueva propiedad llamada `$duration`... con un getter para poder utilizar ese valor.

## Vamos: ¡Sustituir una clase!

Pero... déjame deshacer eso. ¡Hagamos las cosas más interesantes! Para mantener nuestra aplicación lo más delgada posible en producción, sólo quiero ejecutar este nuevo código de sincronización cuando estemos en el entorno `dev` de Symfony. Y sí, podríamos inyectar algún valor de`$shouldCalculateDuration` en `SightingScorer` basado en el entorno y utilizarlo para determinar si debemos hacer ese trabajo.

Pero, siguiendo el espíritu de Liskov, en lugar de cambiar `SightingScorer`, quiero crear una subclase que haga la sincronización y sustituir esa clase en nuestro sistema como el servicio `SightingScorer`.

¡Va a ser muy divertido! Y es un patrón que encontrarás dentro del propio Symfony, como con el `TraceableEventDispatcher`: una clase que se sustituye por el despachador de eventos real sólo mientras se desarrolla. Añade información de depuración. Bueno, técnicamente, esa clase utiliza la decoración en lugar de ser una subclase. Ese es un patrón de diseño diferente, y normalmente mejor, cuando quieres sustituir una clase existente. Pero, para entender realmente a Liskov, utilizaremos una subclase.

## Creación de la subclase

Empecemos por crear esa nueva subclase. En el directorio `Service/`... para que esté al lado de nuestro `SightingScorer` normal, añade una nueva clase llamada`DebuggableSightingScorer`. Haz que extienda la normal `SightingScorer`.

[[[ code('504c810a6a') ]]]

Dado que nuestra subclase no realiza ningún cambio en la clase madre, Liskov estará definitivamente contento con ella. Lo que quiero decir es que deberíamos poder sustituir esta clase en nuestra aplicación en lugar de la original, sin problemas.

## Sustitución de la clase real

Pero, ¿dónde se utiliza realmente el servicio normal de `SightingScorer`? Abre`src/Controller/BigFootSightingController.php`. Esta acción `upload()` es la que se ejecuta cuando, desde la página de inicio, hacemos clic para enviar un avistamiento. Sí, aquí abajo, puedes ver que éste es el método `upload()`.

[[[ code('f9b94716fb') ]]]

Uno de los argumentos que se está autoconectando a este método es el `SightingScorer`... que se utiliza aquí abajo en el envío para calcular la puntuación.

Ahora quiero cambiar este servicio para que utilice nuestra nueva clase: Quiero sustituirla. ¿Cómo? Abre `config/services.yaml`. Antes he mencionado que íbamos a intercambiar nuestro `DebuggableSightingScorer` sólo en el entorno `dev`. Pero para simplificar las cosas, en realidad voy a hacerlo en todos los entornos. Si quisieras que esto sólo afectara a tu entorno `dev`, podrías hacer los mismos cambios que vamos a hacer en un archivo `services_dev.yaml`.

De todos modos, para empezar a utilizar de repente nuestra nueva clase en todos los lugares en los que se utiliza el`SightingScorer`, añade `class:` y luego`App\Service\DebuggableSightingScorer`.

[[[ code('5370f75079') ]]]

Lo sé, esto parece un poco raro. Esta primera línea sigue siendo el id del servicio. Pero ahora, en lugar de utilizarlo como clase, Symfony utilizará `DebuggableSightingScorer`. El resultado final es que cada vez que alguien autocable `SightingScorer` - como hacemos en nuestro controlador - Symfony instanciará una instancia de nuestro`DebuggableSightingScorer`... y pasará el argumento normal `$scoringFactors`. Sí, ¡acabamos de sustituir nuestra subclase en el sistema!

Para probarlo, busca tu terminal y ejecuta

```terminal
php bin/console debug:container Sighting
```

Quiero mirar el servicio `SightingScorer`, así que le daré al 5. Y... ¡perfecto! El id del servicio es `App\Service\SightingScorer`, pero la clase es`App\Service\DebuggableSightingScorer`.

Otra forma de mostrar esto sería entrar en nuestro `BigFootSightingController`y temporalmente en `dd($sightingScorer)`.

De vuelta a tu navegador, actualiza y... ¡ahí está! `DebuggableSightingScorer`

Vamos a quitar eso... y a refrescar de nuevo. La página funciona y... aunque no lo pruebe, si la enviamos, nuestro `DebuggableSightingScorer` calcularía correctamente la puntuación de credibilidad.

En otras palabras, ninguna sorpresa: si creas una subclase y no cambias nada en ella, puedes sustituir esa clase por su clase madre. Sigue el principio de Liskov.

## Cambios de métodos que NO están permitidos

Empecemos a añadir nuestro mecanismo de sincronización. En la clase, ve a Código -> Generar - o Comando + N en un Mac - selecciona "Anular métodos" y anula el método `score()`. Si anulas un método y mantienes las mismas sugerencias de tipo de argumento y tipo de retorno, esta clase sigue siendo sustituible: Puedo actualizar y PHP sigue contento.

[[[ code('df17ba1e8d') ]]]

Pero si cambiamos las pistas de tipo de argumento o el tipo de retorno por algo totalmente diferente, entonces incluso PHP nos dirá que lo dejemos. Por ejemplo, cambiemos completamente el tipo de retorno a `int`

[[[ code('dd14f9d90b') ]]]

¡PhpStorm se vuelve loco! Y si refrescamos, ¡PHP también está loco!

> `DebuggableSightingScorer::score()` debe ser compatible con el padre
> `score()`, que devuelve `BigFootSightingScore`.

Nuestra firma es incompatible y, amablemente, PHP no nos permite violar el principio de Liskov de esta manera. Ve y deshaz ese cambio.

Entonces, ¿significa esto que nunca podemos cambiar el tipo de retorno o las sugerencias de tipo de los argumentos en una subclase? En realidad... ¡no! Recuerda las reglas de antes: puedes cambiar un tipo de retorno si lo haces más estrecho, es decir, más específico. Y también puedes cambiar una sugerencia de tipo de argumento... siempre que la hagas aceptar un tipo más amplio o menos específico.

Veamos esto en acción terminando nuestra función de tiempo a continuación.
