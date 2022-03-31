Calcular el tiempo que tarda en ejecutarse el método padre `score()` será fácil. Pero entonces... ¿qué hacemos con ese número? Este método devuelve una instancia de`BigFootSightingScore`...., así que no podemos cambiarlo de repente para que devuelva un `int` por la duración. ¿Cómo puede este método devolver tanto el `BigFootSightingScore`como la información sobre el tiempo que ha tardado en calcular la puntuación?

## Crear una subclase para el valor de retorno

La respuesta es: ¡crea otra subclase! Una subclase de `BigFootSightingScore` que contenga la información extra. `BigFootSightingScore` vive en el directorio `src/Model/`: ahí está. Justo al lado, añade una nueva clase llamada, qué tal,`DebuggableBigFootSightingScore`. Haz que extienda la normal `BigFootSightingScore`.

[[[ code('d37a1c6545') ]]]

¡Ahora tenemos dos subclases con las que jugar! Esta vez, anula el constructor: hazlo yendo a Código -> Generar - o Comando + N en un Mac. Anula `__construct()`.

Esto llama al constructor padre con la puntuación, ¡lo cual es genial! Añade un nuevo argumento: `float $calculationTime`. Pulsa Alt + Enter y ve a "Inicializar propiedades"... selecciona sólo `$calculationTime`... para crear esa propiedad y establecerla. ¡Para que el `$calculationTime` sea accesible, en la parte inferior, vuelve a Código -> Generar y haz un método "getter" para esto!

[[[ code('b7a45b5867') ]]]

## Espera: ¿Es necesario que __construct siga las reglas de Liskov?

Por cierto, añadir un argumento necesario a un método que estás sobrescribiendo -como estamos haciendo en `__construct` - es normalmente otra forma de violar el principio de Liskov. Pensemos en ello con un ejemplo diferente: `SightingScorer`. Cuando lo utilizamos, normalmente podemos llamar a `score()` y pasarle un único argumento. Si de repente sustituyéramos una clase diferente cuyo método `score()` requiriera dos argumentos... bueno, eso haría que nuestro código explotara. Esa nueva clase no sería sustituible por la antigua.

Sin embargo, el constructor no necesita seguir el principio de Liskov... lo que me costó un minuto entender. ¿Por qué no? Porque si estás instanciando un `DebuggableBigFootSightingScore` -con `new DebuggableBigFootSightingScore` - entonces sabes exactamente qué clase estás instanciando. Y, por tanto, puedes saber exactamente qué argumentos tienes que pasar.

Esto es diferente a que te pasen un objeto `BigFootSightingScore`... donde la verdadera clase puede ser una subclase. En esa situación, necesitas que los métodos que llames a ese objeto se comporten como los de la clase original. Como el constructor nunca se llama a un objeto, eso no es un problema.

De todos modos, volviendo a `DebuggableSightingScorer`, devolvamos nuestra nueva clase`DebuggableBigFootSightingScore` con una duración ficticia. Digamos `$bfScore =
parent::score()`... y luego devolvamos un `new DebuggableBigFootSightingScore` pasando la puntuación de `int` - `$bfScore->getScore()` - y `100` para una duración falsa. Anunciemos también que devolvemos esta nueva clase `DebuggableBigFootSightingScore`

[[[ code('9a95ce22e8') ]]]

Pero espera: ¡acabamos de cambiar el tipo de retorno a algo diferente de nuestra clase madre! ¿Está permitido?

## Se permiten tipos de retorno más estrechos

Busca tu navegador, actualiza y... ¡PHP lo permite totalmente! Porque esto sí sigue el principio de Liskov: estamos haciendo el tipo de retorno más estrecho... o más específico.

¿Pero por qué se permite hacer más estrecho un tipo de retorno? Fíjate en`BigFootSightingController`: la clase que utiliza el `SightingScorer`. Este código requiere una instancia de `SightingScorer`. Y así, cuando llamemos al método `score()` más adelante, sabemos que devolverá un objeto `BigFootSightingScore`. Lo sabemos porque, si saltamos a la clase `SightingScorer`, ¡sí! El método `score()` devuelve un `BigFootSightingScore`.

Y así, sabemos que la variable `$bfsScore` es una instancia de `BigFootSightingScore`... y sabemos que esa clase tiene un método `getScore()`. Una vez más, saltaré a la clase. Este es el original `BigFootSightingScore` y aquí está su método`getScore()`. Lo utilizamos en nuestro controlador para obtener la puntuación entera y... ¡todo es feliz!

Pero ahora sabemos que hemos sustituido el `SightingScorer` por un`DebuggableSightingScorer`... y sabemos que su método `score()` devuelve un `DebuggableBigFootSightingScore`. ¡Pero no pasa nada! ¿Por qué? Porque`DebuggableBigFootSightingScore` extiende a `BigFootSightingScore`. Así que seguimos devolviendo una instancia de `BigFootSightingScore`, que, por supuesto, sigue teniendo un método `getScore()`. El hecho de que devolvamos una subclase... que potencialmente tiene métodos adicionales, no rompe su sustituibilidad.

Pero si hubiéramos cambiado su tipo de retorno a algo menos específico, como cualquier objeto, entonces no habría garantía de que lo que devolvemos de este método tiene un método `getScore()`. Y entonces, eso rompería el principio de Liskov. PHP se enfadaría tanto con nosotros, que generaría un error de sintaxis. Vamos a deshacer eso.

No hablaremos de ello en detalle, pero la misma filosofía puede aplicarse a los tipos de argumentos, pero en sentido contrario. Está bien cambiar un tipo de argumento siempre que admita al menos el tipo original. No está bien ser más restrictivo con el tipo que permites, pero sí está bien ser menos específico: se me permite decir que el método `score()` admite cualquier objeto. Bueno, en este ejemplo, eso sería problemático porque estamos pasando el argumento a la clase padre... que sigue requiriendo un `BigFootSighting`... pero en general, permitir un tipo de argumento menos específico, o más amplio, está permitido por Liskov. Y puedes ver esto si refrescamos: no hay error de sintaxis de PHP.

Volvamos a cambiarlo.

A continuación: es hora de celebrar nuestro nuevo sistema utilizando el nuevo valor de la duración, ajustando algunas cosas en la configuración de Symfony y enumerando las conclusiones del principio de Liskov.
