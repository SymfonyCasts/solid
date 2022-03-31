¿Listo para el principio número 4? Es el principio de segregación de interfaces, o ISP, que dice

> Los clientes no deben verse obligados a depender de interfaces que no utilizan.

¡No es una mala definición! Pero quiero aclarar la palabra "interfaz". No se refiere necesariamente a una interfaz literal. Se refiere al concepto abstracto de interfaz, que generalmente significa "los métodos públicos" de una clase... aunque no implemente técnicamente una interfaz. El significado de interfaz aquí es: las "cosas que puedes hacer con un objeto" cuando te lo doy.

## La definición más sencilla

Así que permíteme que intente dar una definición aún más sencilla:

> Construye clases pequeñas y centradas en lugar de clases grandes y gigantes.

Esta definición me recuerda mucho al principio de responsabilidad única... ¡y es cierto! Pero el principio de segregación de la interfaz lo contempla desde la otra dirección: desde la perspectiva de quién utiliza la clase, no desde la perspectiva de la propia clase. De nuevo, la definición original es:

> Los clientes no deben ser obligados a depender de interfaces -por tanto, básicamente métodos-
> que no utilizan.

Por ejemplo, supón que has construido accidentalmente una clase gigante llamada `ProductManager`con un montón de métodos. ¡Ups! Entonces, en algún lugar de tu código, necesitas llamar a uno de esos métodos. Esta otra clase se llama "cliente" porque está utilizando nuestra gigantesca clase `ProductManager`. Y, por desgracia, aunque sólo necesita un método de `ProductManager`, tiene que inyectar todo el objeto gigante. Se ve obligado a depender de un objeto cuya interfaz -cuyos métodos públicos- son muchos más de los que realmente necesita.

## Nueva característica: Ajustar una puntuación

¿Por qué es esto un problema? Responderemos a esa pregunta un poco más tarde, después de jugar con un ejemplo del mundo real. Porque... ¡la dirección nos ha pedido que hagamos otro cambio en nuestro sistema de puntuación de credibilidad! Si un avistamiento recibe una puntuación inferior a 50 puntos... pero tiene tres o más fotos, le daremos un impulso: 5 puntos extra por foto. Esto... ¡no era un cambio que esperábamos! ¡Maldita sea! Nuestros factores de puntuación tienen la capacidad de añadir a la puntuación... pero no tienen la capacidad de ver la puntuación final y luego modificarla.

## Añadir otro método a la interfaz

No hay problema: añadamos un segundo método a la interfaz que tenga la capacidad de hacer eso. Llámalo, qué tal, función pública `adjustScore()`. En este caso, va a recibir el `int $finalScore` que se acaba de calcular y el`BigFootSighting` que estamos puntuando. Devolverá la nueva puntuación final de `int`. Puedes añadir algo de PHPDoc encima de esto para explicar mejor el propósito del método si quieres.

[[[ code('6a2ac553fb') ]]]

En un minuto, vamos a llamar a esto desde dentro de `SightingScorer` después de que se haya hecho la puntuación inicial. Pero primero, vamos a abrir `PhotoFactor` y a añadir la nueva lógica de bonificación.

## Implementación del nuevo método

En la parte inferior, ve a Código -> Generar - o Comando + N en un Mac - selecciona "Implementar métodos" e implementa `adjustScore()`. Di`$photosCount = $sighting->getImages()` - no te olvides de contarlos - entonces si el `$finalScore` es menor que 50 y el `$photosCount` es mayor que dos - el`$finalScore` debe obtener más es igual a `$photosCount * 5`. Al final, devuelve`$finalScore`.

[[[ code('24c0f6cfa3') ]]]

¡Nueva lógica hecha! Pero ahora... ¿qué hacemos con todas las demás clases que implementan`ScoringFactorInterface`? Desgraciadamente, para que PHP funcione, tenemos que añadir el nuevo método a cada clase. Pero podemos hacer que devuelva `$finalScore`.

Así que en la parte inferior de `CoordinatesFactor`, vuelve a Código -> Generar - selecciona "Implementar métodos", genera `adjustScore()`, y devuelve `$finalScore`

[[[ code('905615159b') ]]]

Copia, esto cierra `CoordinatesFactor`, ve a `DescriptionFactor` y añádelo al final. Haz lo mismo dentro de `TitleFactor`.

[[[ code('d13c43c3d4') ]]]

Por último, podemos actualizar `SightingScorer`. Añade un segundo bucle después de calcular la puntuación: para cada `$this->scoringFactors` como `$scoringFactor`, esta vez di`$score = $scoringFactor->adjustScore()`... y pasa en `$score` y `$sighting`.

[[[ code('66a2d611fa') ]]]

Ya está Por cierto, podrías argumentar que el orden de los factores de puntuación es ahora relevante. ¡Es cierto! Pero... no vamos a preocuparnos de eso por simplicidad... y porque no es relevante para este principio. Pero, hay una manera de dar a un servicio etiquetado una mayor prioridad en Symfony para que se pase antes o después que otros factores de puntuación.

#¡Violamos la OCP!

Si, en este punto, algo te pica, ¡puede ser porque acabamos de violar el principio de abierto-cerrado! Hemos tenido que modificar el método `score()` para añadir este nuevo comportamiento. Pero ¡no pasa nada! Pone de manifiesto la naturaleza delicada de OCP: ¡no habíamos previsto este tipo de cambios! No puedes "cerrar" una clase contra todo tipo de cambios: sólo puedes cerrarla contra los cambios que preveas correctamente.

Si observas nuestra nueva interfaz y las clases que la implementan, probablemente sientas que no es... ideal que todas estas clases tengan que implementar este método... aunque no les importe realmente. A continuación: vamos a hacer que esto sea aún más obvio, refactorizaremos hacia una solución mejor y, finalmente, discutiremos los puntos clave del principio de segregación de la interfaz.
