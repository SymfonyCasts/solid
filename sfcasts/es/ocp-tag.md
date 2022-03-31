Cuando fuimos a la página de "envío", nos encontramos con un error gigantesco. Lo más relevante es la parte central:

> No se puede autoconectar el servicio `SightingScorer`, el argumento `$scoringFactors` del método
> `__construct` es un array de tipo insinuado. Debes configurar su valor explícitamente.

¡Eso tiene sentido! No le hemos dicho a Symfony qué debe pasar al nuevo argumento de`SightingScorer`.

## Cableado manual del argumento

¿Qué queremos pasar ahí? Un array de todos nuestros servicios de "factor de puntuación". La forma más sencilla de hacerlo es configurarlo manualmente en `config/services.yaml`. Abajo, queremos configurar el servicio `App\Service\SightingScorer`... y queremos controlar su `arguments:`, concretamente este argumento `$scoringFactors`. Copia eso, pégalo, y esto será un array: Utilizaré la sintaxis de varias líneas. Cada entrada del array será uno de los servicios del factor de puntuación. Así que`@App\Scoring\TitleFactor`, copia eso, pega... arregla la sangría... luego pasa`DescriptionFactor` y `CoordinatesFactor`.

[[[ code('634ea29bb0') ]]]

Así pasarás un array con estos tres objetos de servicio dentro.

Inténtalo de nuevo. Actualiza y... el error ha desaparecido... y ahora nos ha llevado a la página de inicio de sesión. Copia el correo electrónico de arriba, introduce la contraseña, pulsa "iniciar sesión" y... ¡bien! La página se carga. Vamos a intentarlo. Rellena los detalles de tu interacción más reciente con el Pie grande. Ah, pero antes de enviar esto, voy a añadir algunas palabras clave a la descripción que sé que busca nuestro factor de puntuación.

Envíalo y... ¡funciona! Ah, tío, ¿¡una puntuación de credibilidad de sólo 10!? Realmente pensé que era un Pie grande.

## Activar la autoconfiguración

Antes de hablar más de OCP, a nivel técnico, de Symfony, hay otra forma de inyectar estos servicios. Se llama "iterador etiquetado"... y es una idea muy interesante. También se utiliza habitualmente en el propio núcleo de Symfony.

Abre `src/Kernel.php`. Lo sé, casi nunca abrimos este archivo. Dentro, ve a Código -> Generar, o Comando + N en un Mac, y selecciona Anular métodos. Anula uno llamado `build()`... déjame encontrarlo. Ahí está.

Se trata de un gancho en el que podemos realizar un procesamiento adicional en el contenedor mientras se construye. El método padre está vacío... pero dejaré la llamada al padre. Añade`$container->registerForAutoconfiguration()`, pasa este`ScoringFactorInterface::class`, y luego `->addTag('scoring.factor')`.

[[[ code('6e72fd510e') ]]]

Gracias a esto, cualquier servicio autoconfigurable, que son todos nuestros servicios, que implemente `ScoringFactorInterface`, se etiquetará automáticamente con`scoring.factor`. Ese `scoring.factor` es un nombre que me acabo de inventar.

Esta línea, por sí sola, no hará ningún cambio real. Pero ahora, de vuelta en `services.yaml`podemos simplificar: establece el argumento `$scoringFactors` con una sintaxis YAML especial:`!tagged_iterator scoring.factor`.

[[[ code('f8d7aa84df') ]]]

Esto dice: por favor, inyecta todos los servicios que estén etiquetados con `scoring.factor`. Así, la autoconfiguración añade la etiqueta a nuestros servicios de factor de puntuación... y esto se encarga de pasarlos. Bastante bien, ¿verdad?

El único problema es que tenemos que cambiar el tipo de sugerencia en `SightingScorer` para que sea un `iterable`. Esto no nos pasará un array... pero nos pasará algo sobre lo que podemos `foreach`. Como ventaja, es un iterable "perezoso": los servicios del factor de puntuación no se instanciarán hasta que ejecutemos el `foreach`. Ah, y cambia también el tipo de la propiedad a `iterable`.

[[[ code('d4a781b3ab') ]]]

A continuación: ahora que entendemos el tipo de cambio que OCP quiere que hagamos en nuestro código, hablemos de por qué debería importarnos -o no- OCP y de cuándo deberíamos seguirlo o no.
