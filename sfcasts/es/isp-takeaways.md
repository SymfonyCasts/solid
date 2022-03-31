Acabamos de añadir la posibilidad de añadir una bonificación a la puntuación si ésta es inferior a 50 y hay 3 fotos o más en un avistamiento. Y... la dirección ya está solicitando otro cambio: tenemos que asegurarnos de que, pase lo que pase, una puntuación nunca reciba más de 100 puntos.

¡No hay problema! Podemos crear otra clase de factor de puntuación para comprobarlo. En el directorio`Scoring/`, añade una clase llamada, qué tal, `MaxScoreAdjuster`. Le voy a dar un nombre ligeramente diferente, aunque sea un factor de puntuación, porque su verdadero trabajo va a ser ajustar la puntuación. Haz que implemente`ScoringFactorInterface`.

Ahora ve a Código -> Generar -o Comando + N en un Mac- y simplemente genera,`adjustScore()` para empezar. Para la lógica, devuelve el mínimo de `$finalScore`o 100. Así que si el `$finalScore` es superior a cien, esto devolverá 100.

[[[ code('276250749e') ]]]

Ahora bien, establecer la prioridad de los factores de puntuación para que éste sea el último sería especialmente importante. Pero como eso no está relacionado con el ISP, no nos preocuparemos por ello.

Por supuesto, en esta nueva clase, también tenemos que implementar el otro método:`score()`. Podemos devolver simplemente 0, ya que no nos importa.

[[[ code('3f8e7e4478') ]]]

Bien, ¡ya tenemos esto funcionando! ¡Pero hemos violado el ISP! Muchas de las clases que implementan `ScoringFactorInterface` -como `MaxScoreAdjuster` y `CoordinatesFactor` - tienen un método ficticio... que añadimos sólo para satisfacer las necesidades de la interfaz.

## Las señales de que estás violando el ISP

Cuando ves algo así, es una señal de que tu interfaz está contaminada... o ha engordado. Pero, de nuevo, aunque estemos utilizando una interfaz en nuestro ejemplo, esto también se aplica a las clases en general. Si tienes una clase con múltiples métodos públicos... y otras partes de tu código sólo utilizan uno o algunos de sus métodos... eso también es una violación de la ISP. De hecho, ése es el principal objetivo de ISP. Estás exigiendo a los clientes de tu clase que dependan de interfaces -en otras palabras, de métodos- que no necesitan.

¿Cuál es la solución? Clasificar los métodos en función de su finalidad y de su uso... y dividirlos en varias clases.

Por ejemplo, si tienes una clase con 3 métodos y 2 de esos métodos se llaman siempre juntos, entonces la clase debe dividirse en sólo dos trozos: una clase con esos 2 métodos y otra clase con sólo el tercer método.

## Dividir nuestra interfaz

En nuestro ejemplo, es bastante obvio que dividir la interfaz en dos trozos simplificaría las clases que los implementan. Así que en este directorio `Scoring/`, crea una nueva clase -o en realidad una interfaz- y llámala `ScoreAdjusterInterface`. Lo que haremos será entrar en `ScoringFactorInterface`, robar el método `adjustScore()` y trasladarlo a la nueva interfaz. Pulsa OK para importar esa declaración `use`.

[[[ code('38af214cc8') ]]]

Gracias a esto, ahora podemos entrar en `CoordinatesFactor` y eliminar el método ficticio`adjustScore()`... y luego hacer lo mismo en `TitleFactor`... y también en`DescriptionFactor`, ¡lo cual sienta bastante bien! En `MaxScoreAdjuster`, cambia esto para implementar `ScoreAdjusterInterface`... y entonces ya no necesitaremos el método ficticio`score()`.

[[[ code('45aa28587e') ]]]

## Inyección de la colección de ajustadores de puntuación

Por último, la clase `PhotoFactor` es interesante: necesita implementar ambas interfaces, lo que está totalmente permitido. Añade `ScoreAdjusterInterface`.

[[[ code('91ce3d95ef') ]]]

Lo último que hay que hacer es que nuestro `SightingScorer` soporte el uso de ambas interfaces repitiendo el truco de inyectar una colección de servicios para`ScoreAdjusterInterface`. En otras palabras, ahora vamos a inyectar un `iterable`de factores de puntuación y un segundo `iterable` de ajustadores de puntuación.

Empieza en: `src/Kernel.php`. Copia el `registerForAutoConfiguration()`... y vamos a repetir lo mismo, pero esta vez para `ScoreAdjusterInterface` y llamaremos a la etiqueta `scoring.adjuster`.

[[[ code('558d142e06') ]]]

A continuación, en `services.yaml`, abajo en nuestro servicio, copia el argumento `$scoringFactors`, pégalo, renómbralo a `$scoringAdjusters` y utiliza el nuevo nombre de la etiqueta:`scoring.adjuster`.

[[[ code('708f08fe0e') ]]]

Copia ese nombre de argumento y dirígete a `SightingScorer`. Añade esto como un segundo argumento`iterable`. Luego pulsa Alt + Enter y ve a Inicializar Propiedades para crear esa propiedad y establecerla. Robaré el PHPDoc de encima de la antigua propiedad para que mi editor sepa que esto contendrá un iterable de objetos`ScoreAdjusterInterface`.

[[[ code('40157ccd65') ]]]

Ahora haz un bucle sobre estos en su lugar. Ya puedes ver que PhpStorm no está contento porque no hay un método `adjustScore()` en los factores de puntuación. Cambia esto por `$scoringAdjusters`... y cambiaré el nombre de la variable a `$scoringAdjuster` aquí y aquí.

[[[ code('0d32cb73d9') ]]]

Ya está Hemos hecho nuestra interfaz más pequeña, lo que nos ha permitido eliminar todos los métodos ficticios.

## ¿Por qué deberíamos preocuparnos por el ISP?

Así que, aparte de vernos obligados a crear métodos ficticios para contentar a la interfaz, ¿por qué debería importarnos el ISP? Se me ocurren tres razones.

La primera es la denominación. Si tienes una clase demasiado grande o una interfaz como la de nuestro ejemplo, dividirla en trozos más pequeños te permite dar a cada uno un nombre más descriptivo que se ajuste a sus propósitos. Podemos ver esto en `SightingScorer`. Ahora trabajamos con ajustadores de puntuación, lo que describe mejor el propósito de esos servicios que un simple "factor de puntuación"... que hace múltiples cosas.

La segunda es que el ISP es una buena señal de que puedes estar violando el principio de responsabilidad única. Si notas que a menudo sólo llamas a uno o dos métodos de una clase... pero no a sus otros métodos públicos, eso es una violación del ISP. Esto te obliga a pensar en las responsabilidades de esa clase, lo que puede dar lugar a que te organices en clases más pequeñas en función de esas responsabilidades.

La tercera razón por la que deberíamos preocuparnos por el ISP es que mantiene tus dependencias más ligeras. No lo hemos visto en este ejemplo concreto, pero sí lo hemos visto antes, cuando hemos hablado de SRP. En ese caso... permíteme cerrar todas mis clases... dividimos una clase `UserManager` en dos piezas: `UserManager` y`ConfirmationEmailSender`. El método `send()` simplemente envía el correo electrónico de confirmación, y lo utilizamos tanto después de la inscripción como cuando solicitamos un reenvío de ese correo.

Si hubiéramos mantenido estas dos funciones públicas dentro de `UserManager`, el reenvío de la confirmación habría supuesto una violación del principio de segregación de interfaces, ya que sólo tendríamos que llamar a uno de los dos métodos públicos de la clase.

Y, para reenviar el correo electrónico, Symfony tendría que instanciar una clase que depende, por ejemplo, del servicio de codificación de contraseñas. ¿Por qué es esto un problema? Bueno, es algo menor, pero esto obligaría a Symfony a instanciar el codificador de contraseñas para poder instanciar el `UserManager`... para que pudiéramos enviar un correo de confirmación... pero nunca utilizaríamos realmente el codificador de contraseñas. ¡Eso es un desperdicio de recursos!

De todos modos, el tl;dr sobre el principio de segregación de la interfaz es el siguiente: cuando tienes una interfaz con un método que no todas sus clases necesitan... o si tienes una clase en la que utilizas habitualmente sólo algunos de sus métodos públicos... puede ser el momento de dividirla en trozos más pequeños. O, más sencillamente, puedes acordarte de no construir clases gigantescas. Pero, como todo, no es una regla absoluta. Si tuviera, por ejemplo, un `GitHubApiClient` que me ayudara a hablar con la API de GitHub... Podría estar bien poniendo 5 métodos en este servicio, aunque habitualmente sólo utilice uno o dos de ellos a la vez. Al fin y al cabo, el nombre de la clase sigue siendo bastante claro... y tener más métodos probablemente no aumenta el número de dependencias que necesito inyectar en ese servicio.

Siguiente: ¡pasamos al principio número cinco! Y éste sí que me hizo girar la cabeza al principio. Es: ¡el principio de inversión de la dependencia!
