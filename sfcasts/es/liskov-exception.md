Pasemos a nuestro primer ejemplo, en el que aprenderemos cómo podemos violar el principio de Liskov Y... tal vez más importante, por qué... no es tan buena idea.

## Creación de un nuevo factor de puntuación

En el directorio `src/Scoring/`, crea una nueva clase de factor de puntuación llamada`PhotoFactor`... y haz que implemente la clase `ScoringFactorInterface`. Por fin cumpliremos la petición de cambio que recibimos antes: añadir un factor de puntuación que lea las imágenes de cada avistamiento.

[[[ code('8b8f13ed4e') ]]]

Gracias a nuestro trabajo con el principio de abierto-cerrado, ahora podemos añadir este factor de puntuación sin tocar `SightingScorer`. Y para ser más guay, gracias a esta cosa de`tagged_iterator` en `services.yaml`, el nuevo servicio `PhotoFactor` se pasará instantáneamente a `SightingScorer`. ¡Sí!

En `PhotoFactor`, ve a Código -> Generar -o Comando + N en un Mac- y selecciona "Implementar métodos" para generar el método `score()`. Dentro, pegaré algo de código.

[[[ code('95748cd52a') ]]]

Es bastante sencillo: hacemos un bucle sobre las imágenes... y fingimos que las estamos analizando de alguna manera súper avanzada. Shh, no se lo digas a nuestros usuarios. Ah, y si no hay imágenes para este avistamiento, lanzamos una excepción.

¡Genial! Vamos a probarlo. Vuelve a nuestra página de inicio, haz clic para añadir una nueva entrada y rellena algunos detalles. Dejaré las imágenes vacías para simplificar. Y... ¡ah! ¡Un error 500! ¡Esa es nuestra nueva excepción! ¡Hemos roto nuestra aplicación! ¡Y se ha roto porque hemos violado el principio de Liskov! ¡Intentó advertirnos!

Nuestra nueva clase de factor de puntuación -o subtipo-, para usar la palabra más técnica, acaba de hacer algo inesperado: ¡lanzó una excepción!

## La fea solución

Una forma de arreglar esto, que puede parecer una tontería... pero hay una razón por la que estamos haciendo esto... es añadir algo de código condicional dentro de `SightingScorer`. Si a `PhotoFactor`no le gustan los avistamientos con cero imágenes, ¡simplemente saltemos ese factor cuando eso ocurra!

Dentro de `foreach`, si `ScoringFactor` es un `instanceof PhotoFactor` y la cuenta de `$sighting->getImages()` es igual a cero, entonces `continue`.

[[[ code('1f657ed6c8') ]]]

Además de no ser la mejor manera de arreglar esto -más sobre esto en un minuto-, esto también viola el principio de abierto-cerrado. Pero... sí arregla las cosas: si volvemos a enviar el formulario... ¡nuestra aplicación vuelve a funcionar!

## Las excepciones son una parte "suave" de la interfaz

Pero... retrocedamos. Abre `ScoringFactorInterface`. A diferencia de los tipos de argumento y los tipos de retorno, en PHP no hay forma de codificar si un método debe lanzar una excepción o no, ni qué tipos de excepción deben utilizarse. Pero esto puede, al menos, describirse en la documentación sobre el método... ¡que nos hemos saltado totalmente!

Vamos a completarla. No necesitamos el `@return` o el `@param` porque son redundantes... a no ser que queramos añadir algo más de información sobre su significado. Añadiré una descripción rápida... y luego seamos muy claros sobre el comportamiento de las excepciones que esperamos:

> Este método no debería lanzar una excepción por ningún motivo normal.

[[[ code('1db7cc1b65') ]]]

En el mundo real, si un método puede lanzar una excepción cuando se produce alguna situación esperada, normalmente verás un `@throws` que lo describe. Y si no ves eso, puedes asumir que no está permitido lanzar una excepción por ninguna situación normal.

## Nuestra clase se comporta de forma inesperada

De todos modos, ahora que hemos aclarado esto, es fácil ver que nuestro `PhotoFactor`rompe el principio de Liskov: `PhotoFactor` se comporta de una manera que la clase que lo utiliza - `SightingScorer`, a veces llamada "clase cliente" - no esperaba. Ese "mal comportamiento" hizo que tuviéramos que hackear este código para que funcionara.

Otra forma de pensarlo, que explica por qué se llama principio de sustitución de Liskov, es que, si algo de nuestro código depende de un objeto`ScoringFactorInterface` -como `DescriptionFactor` - no podríamos "reemplazar" o "sustituir" ese objeto por nuestro `PhotoFactor` sin romper las cosas.

Si este aspecto de la sustitución todavía no tiene mucho sentido, no te preocupes. Nuestro siguiente ejemplo lo ilustrará aún mejor.

## Las comprobaciones de instanceof indican la violación de Liskov

Entonces: hemos violado el principio de Liskov lanzando una excepción. Y luego, he trabajado perezosamente alrededor del problema añadiendo algo de código `instanceof` a `SightingScorer`... para trabajar literalmente "alrededor" del problema.

Cuando tienes un condicional `instanceof` como éste, suele ser una señal de que estás violando Liskov, porque significa que tienes una implementación específica de una clase o interfaz que se comporta de forma diferente al resto... para la que tienes que codificar.

Así que vamos a eliminar esto: quita la sentencia if e incluso vamos a limpiar la sentencia extra`use` de la parte superior 

[[[ code('06843e075e') ]]]

Ahora que hemos aclarado que el método `score()` no debe lanzar una excepción en situaciones normales, la verdadera solución es... un poco obvia: ¡deja de lanzar la excepción! Sustituye la excepción por `return 0`.

[[[ code('aac8979001') ]]]

Y ya está. Ahora la clase actúa como esperamos: sin sorpresas.

Por cierto, todo esto no significa que sea ilegal que nuestro método `score()`lance alguna vez una excepción. Si el método, por ejemplo, necesitara consultar una base de datos... y la conexión a la base de datos estuviera caída... ¡entonces sí! ¡Deberías lanzar una excepción! Esa es una situación inesperada. Pero para todos los casos normales, esperados, debemos seguir las reglas de nuestra clase o interfaz madre.

A continuación, veamos otro ejemplo del principio de Liskov, en el que creamos una subclase de una clase existente... y la sustituimos secretamente en nuestro sistema sin romper nada. ¡Liskov estaría muy orgulloso!
