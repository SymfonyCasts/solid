El segundo principio SOLID es el principio abierto-cerrado. O PCO. ¿Listo para la definición técnica súper comprensible? Allá vamos.

## Definición técnica y (menos) técnica

> Un módulo debe estar abierto para su ampliación, pero cerrado para su modificación.

Como siempre -y espero que seas un poco más rápido que yo- esta definición no tiene sentido para mí.... al menos al principio. Probemos nuestra propia definición. OCP dice:

> Deberías poder cambiar lo que hace una clase sin cambiar realmente su código.

Si eso parece una locura... o directamente imposible, ¡en realidad no lo es! Y aprenderemos un patrón común que lo hace posible.

Pero, para que quede claro, OCP no es mi principio SOLID favorito. Y más adelante hablaremos de cuándo debe utilizarse y cuándo... quizá no. Pero hablaremos más de ello cuando hayamos entendido bien lo que es realmente OCP.

## Actualización de nuestro algoritmo de puntuación de la credibilidad

El objetivo de Avistamientos de Sasquatch es que la gente pueda enviar sus propios avistamientos. Para ayudar a clasificarlos, hemos desarrollado un algoritmo propio para dar a cada avistamiento una "puntuación de credibilidad". Ooh. ¿Cómo se implementa eso?

Abre `src/Service/SightingScorer.php`. Después de enviar un avistamiento, llamamos a`score()`... y toda la lógica vive en esta clase. Miramos la latitud y la longitud, el título y la descripción para determinadas palabras clave. Llamamos a cada una de ellas "factores de puntuación".

Ahora, hemos recibido una petición de cambio. Necesitamos añadir un nuevo factor de puntuación en el que nos fijemos en las fotos incluidas en el post. La forma más fácil de implementarlo sería ir aquí abajo, crear un nuevo método privado llamado `evaluatePhotos()`... y luego llamarlo desde aquí arriba en el método `score()`.

Pero hacer eso violaría la OCP porque estaríamos cambiando nuestro código existente para añadir la nueva función. OCP nos dice que el comportamiento de una clase debe poder modificarse sin cambiar su código. ¿Cómo es posible?

La verdad es que nuestra clase ya violaba la OCP antes de que recibiéramos esta petición de cambio. Para poder añadir la nueva función sin cambiar nuestro código existente, teníamos que escribir nuestra clase de forma diferente desde el principio. Como ya es un poco tarde para eso, vamos a repasar la mentalidad OCP y a refactorizar esta clase para que sí siga las reglas.

## "Cerrar" una clase a un cambio

En primer lugar, tenemos que identificar contra qué tipo de cambio queremos "cerrar" esta clase. En otras palabras, qué tipo de cambio queremos permitir que un futuro desarrollador pueda realizar sin modificar esta clase. Según la petición de cambio, necesitamos poder añadir más factores de puntuación sin modificar el propio método `score()`. Como no hay forma de hacerlo ahora, vamos a cambiar este método para "cerrarlo" a este cambio. ¿Cómo? Separando cada factor de puntuación en su propia clase e inyectándolos en el servicio `SightingScorer`.

El primer paso es crear una interfaz que describa lo que debe hacer cada factor de puntuación. En `src/`, para la organización, crea un nuevo directorio llamado `Scoring/`. Y dentro de éste, elige "nueva clase PHP"... y cambia ésta por una interfaz... llamada `ScoringFactorInterface`.

Cada factor debería necesitar sólo un método. Llamémoslo `score()`. Aceptará el objeto `BigFootSighting` que va a puntuar.... y devolverá un número entero, que será la cantidad a sumar a la puntuación total.

[[[ code('355c366b63') ]]]

¡Perfecto! También podrías añadir algo de documentación por encima de esto para describir mejor el método de la interfaz: probablemente sea una buena idea.

El segundo paso es crear una nueva clase para cada factor de puntuación y hacer que implemente la nueva interfaz. Por ejemplo, copia, `evaluateCoordinates()`, bórrala y luego ve al directorio `Scoring` y crea una nueva clase llamada `CoordinatesFactor`. Haremos que implemente `ScoringFactorInterface`... Pondré el método - pulsa OK para añadir las sentencias `use` - cambia el nombre a `score()` y hazlo`public`. Ya devuelve, correctamente, un número entero, ¡así que ya está hecho!

[[[ code('031e57dc95') ]]]

Repitamos esto para `evaluateTitle()`. Crea una clase llamada `TitleFactor`, implementa la `ScoringFactorInterface`, pégala, hazla `public` y renómbrala a`score()`.

[[[ code('0f23a32941') ]]]

Y una más: copia, `evaluateDescription()`, borra eso, crea nuestra última clase factorial por ahora, que será `DescriptionFactor`, implementa `ScoringFactorInterface`pega la lógica, limpia las cosas... y renombra a `score()`.

[[[ code('13171987fb') ]]]

¡Eso parece feliz! Ahora podemos hacer nuestra magia en `SightingScorer`. Añade un método`__construct()` que acepte un `array` de factores de puntuación. Le daré a Alt + Enter y me iré a "Inicializar propiedades" para crear esa propiedad y establecerla. Por encima de la propiedad, me gusta añadir PHPDoc extra para que mi editor sepa que esto no es una matriz de cualquier cosa, sino una matriz de objetos `ScoringFactorInterface[]`.

[[[ code('ed73d709ce') ]]]

Abajo, en `score()`, en lugar de llamar a cada método individualmente, ahora podemos hacer un bucle sobre `$this->scoringFactors` y decir `$score += $scoringFactor->score($sighting)`.

[[[ code('905ee06c75') ]]]

Y ya está Nuestro Avistador-Escritor está ahora cerrado a un tipo de cambio que podemos necesitar en el futuro: añadir factores de puntuación. En otras palabras, ahora podemos añadir nuevos factores de puntuación, sin modificar este método.

## Cableado del argumento $scoringFactors

¡Ya! Pero... a nivel técnico, esto todavía no funciona. En tu navegador, haz clic para enviar un nuevo avistamiento. ¡Error instantáneo! Por supuesto. Esto no está realmente relacionado con OCP, pero Symfony no sabe qué pasar por el nuevo argumento `$scoringFactors`.

A continuación, vamos a ver dos formas de arreglar esto: la forma simple... y la forma más elegante, que implica un iterador etiquetado. Después, veremos algunos puntos de partida para el principio de abierto-cerrado.
