Lo que OCP quiere que saquemos de esta conversación es lo siguiente: trata de imaginar los cambios futuros que probablemente necesites hacer, y diseña tu código de forma que puedas hacer esos cambios sin modificar las clases existentes.

## Patrones de diseño OCP

Mostramos un patrón común para hacer esto: inyectando una matriz o -iterable- de servicios en lugar de codificar toda la lógica dentro de la clase. También hay otros patrones que puedes utilizar para lograr la OCP, incluyendo el "patrón de estrategia" -que es similar a lo que hicimos, pero en el que permites que se pase sólo un servicio para manejar algún trabajo- y el patrón de método de plantilla. Todos ellos son diferentes sabores de la misma cosa: permitir que la funcionalidad se pase a una clase, en lugar de vivir dentro de la clase.

## La OCP nunca es totalmente realizable

Pero la verdad es que no me gusta el OCP. Y tengo tres razones. En primer lugar, hasta el tío Bob -el padre de los principios SOLID- sabe que OCP es una "mentira". OCP promete que, si lo sigues correctamente, no tendrás que volver a trastear con tu antiguo código. Pero un sistema no puede ser 100% compatible con OCP. Nuestra clase `SightingScorer`está "cerrada" contra el cambio de "añadir nuevos factores de puntuación". Pero qué pasaría si de repente necesitáramos un factor de puntuación para poder multiplicar la puntuación existente por un número... en lugar de sólo sumarla 

[[[ code('e20867c86d') ]]]

Este cambio inesperado nos obligaría a, sí, modificar el código en `SightingScorer`. Si hubiéramos previsto este cambio, podríamos haber añadido una abstracción a `SightingScorer`para protegernos de este nuevo tipo de cambio. Pero nadie puede predecir perfectamente el futuro: podemos hacerlo lo mejor posible... pero a menudo, nos equivocaremos.

## Las abstracciones innecesarias añaden complejidad

Por supuesto, que un principio no sea perfecto no significa que no debamos utilizarlo nunca. Pero eso me lleva a la segunda razón por la que no me gusta OCP: crea abstracciones innecesarias... que hacen que nuestro código sea más difícil de entender.

`SightingScorer` ahora está cerrada contra nuevos factores de puntuación, lo que significa que podemos añadir nuevos factores de puntuación a nuestro sistema sin modificar la clase. ¿Pero a qué precio? Ya no puedo abrir esta clase y entender rápidamente cómo se calcula la puntuación de credibilidad. Ahora tengo que rebuscar para saber qué factores se inyectan... y luego ir a ver cada clase de factor individual.

Si tienes un equipo grande, poder separar las cosas en trozos más pequeños como éste resulta más deseable. Pero, por ejemplo, aquí en SymfonyCasts -con nuestro valiente equipo de unos cuatro- probablemente no haríamos este cambio. Añade desorientación a nuestro código, con un beneficio limitado.

## Cambiar el código es... ¡Bien!

Y eso me lleva a mi tercera y última razón para no amar a OCP. Y ésta proviene de la entrada del blog de [Dan North](https://dannorth.net/2021/03/16/cupid-the-back-story/amp/).

Sostiene que el principio de abierto-cerrado procede de una época en la que los cambios eran caros debido a la necesidad de compilar el código, al hecho de que aún no dominábamos la ciencia de la refactorización del código y a que el control de versiones se hacía con CVS, lo que, según él, contribuía a una mentalidad de querer hacer cambios añadiendo código nuevo, en lugar de modificar el existente.

En otras palabras... ¡OCP es un dinosaurio! El consejo de Dan, con el que estoy de acuerdo, es bastante diferente al de OCP. Él dice

> Si necesitas que el código haga otra cosa, modifica el código para que haga otra cosa.

Citando a Dan, dice

> El código no es un activo que hay que envolver cuidadosamente y preservar, sino un coste,
> una deuda. Todo código es un coste. Así que si puedo coger un gran montón de código existente y sustituirlo
> con costes más pequeños y específicos, entonces estoy ganando en código.

Eso me encanta.

Entonces, ¿cómo navego personalmente por la PCO en el mundo real? Es bastante sencillo. Si estoy construyendo una biblioteca de código abierto en la que las personas que utilicen mi código no podrán literalmente modificarlo, entonces sigo un patrón como el que utilizamos en`SightingScorer` cada vez que identifico un cambio que un usuario podría necesitar hacer. Esto da a mis usuarios la posibilidad de hacer ese cambio... sin modificar el código de la clase... lo que sería imposible para ellos.

Pero si estoy codificando en una aplicación privada, es mucho más probable que mantenga todo el código dentro de la clase. Pero esto no es una regla absoluta. Separar el código facilita las pruebas unitarias y puede ayudarnos a seguir el consejo de SRP: escribir código que "quepa en tu cabeza". Los equipos más grandes también querrán probablemente dividir las cosas más fácilmente que los equipos más pequeños. Como con todos los principios SOLID, haz lo posible por escribir código sencillo y... no lo pienses demasiado.

A continuación, pasemos al principio SOLID número tres: el Principio de Sustitución de Liskov.
