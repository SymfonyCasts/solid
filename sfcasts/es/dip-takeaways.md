Las dos reglas del principio de inversión de la dependencia nos dan instrucciones claras sobre cómo deben interactuar dos clases, como `CommentSpamManager` y `RegexSpamWordHelper`.

## ¿Inversión? ¿Qué se invierte?

Pero antes de hablar de los pros y los contras del DIP... ¿por qué se llama inversión de la dependencia? ¿Qué es la "inversión"?

Esto me costó mucho tiempo entenderlo. Esperaba que la inversión de la dependencia significara de algún modo que las dos clases empezaran a depender literalmente la una de la otra de alguna manera... diferente. Como si de repente inyectáramos el`CommentSpamManager` en `RegexSpamWordHelper`... en lugar de hacerlo al revés, "invirtiendo" realmente la dependencia.

Pero, como puedes ver... ese no es el caso. A alto nivel, estas dos clases dependen la una de la otra exactamente igual que siempre: la clase de bajo nivel, de detalles - `RegexSpamWordHelper` - se inyecta en la clase de alto nivel -`CommentSpamManager`.

La parte de la "inversión" es... más bien un concepto abstracto. Antes de refactorizar nuestro código para crear y utilizar la interfaz, habría dicho

> `CommentSpamManager` depende de `RegexSpamWordHelper`. Si decidimos modificar
> `RegexSpamWordHelper`, tendremos que actualizar `CommentSpamManager`
> para que funcione con esos cambios. `RegexSpamWordHelper` es el jefe.

Pero después de la refactorización, en concreto, después de crear una interfaz basada en las necesidades de `CommentSpamManager`, ahora diría lo siguiente

> `CommentSpamManager` depende de cualquier clase que implemente
> `CommentSpamCounterInterface`. En realidad, se trata de la clase
> clase `RegexSpamWordHelper`. Pero si decidimos refactorizar el funcionamiento de
> `RegexSpamWordHelper` funciona, seguiría siendo responsable de implementar
> `CommentSpamCounterInterface`. En otras palabras, cuando `RegexSpamWordHelper` cambie,
> nuestra clase de alto nivel `CommentSpamManager` no tendrá que cambiar.

Esa es la inversión: es una inversión de control: una "inversión" de quién está al mando. Gracias a la nueva interfaz, la clase de alto nivel - `CommentSpamManager` - ha tomado el control sobre el aspecto que debe tener su dependencia.

## Pros y contras del DIP

Así que, ahora que entendemos el principio de inversión de la dependencia, ¿cuáles son sus ventajas?

En pocas palabras: el DIP consiste en desacoplar. `CommentSpamManager` está ahora desacoplado de `RegexSpamWordHelper`. Incluso podríamos sustituirlo por una clase diferente que implemente esta interfaz sin tocar ningún código de la clase de alto nivel.

Ésta es una de las estrategias fundamentales para escribir código "agnóstico al marco". En esta situación, los desarrolladores crean interfaces en su código y sólo dependen de esas interfaces, en lugar de las interfaces o clases de cualquier marco que estén utilizando.

Sin embargo, en mi código, rara vez sigo el principio de inversión de la dependencia. Bueno, permíteme que lo aclare. Si estuviera trabajando en una biblioteca de código abierto y reutilizable, como el propio Symfony, definitivamente crearía interfaces, como acabamos de hacer. ¿Por qué? Porque quiero permitir que los usuarios de mi código sustituyan este servicio por alguna otra clase, como por ejemplo, si alguien quiere sustituir nuestro sencillo `RegexSpamWordHelper` en su aplicación por una clase que utilice una API para encontrar estas palabras de spam.

Pero si yo estuviera escribiendo esto en mi propia aplicación, me saltaría la creación de la interfaz: Haría que mi código se pareciera al original con`CommentSpamManager` dependiendo directamente de `RegexSpamWordHelper` sin interfaz.

## La mayoría de las dependencias no necesitan ser invertidas

¿Por qué? Como señala Dan North en su entrada del blog: no todas las dependencias necesitan ser invertidas. Si algo de lo que dependes va a necesitar realmente ser cambiado por una clase o implementación diferente más adelante, entonces esa dependencia es casi más una "opción". Si tuviéramos esa situación, probablemente querríamos aplicar DIP. Al crear y tipificar una interfaz, estamos diciendo

> Por favor, pásame la "opción" que te gustaría utilizar para contar las palabras de spam.

Pero, la mayoría de las veces, citando parcialmente a Dan

> Las dependencias no son opciones: son simplemente la forma en que vamos a contar las palabras spam
> en esta situación.

Si siguieras el DIP a la perfección, acabarías teniendo una base de código con un montón de interfaces que son implementadas por una sola clase cada una. Eso añade flexibilidad... que probablemente no necesitarás. El "coste" es el despiste: tu código es más difícil de seguir.

Por ejemplo, en `CommentSpamManager`, ahora cuesta un poco más de trabajo averiguar qué clase cuenta las palabras de spam y cómo funciona todo. Y si alguna vez intentas cambiar una dependencia para utilizar una clase diferente y concreta, puedes descubrir que, aunque hayas seguido el DIP, ¡el cambio no es tan fácil!

Por ejemplo, cambiar de un sistema de base de datos a otro probablemente será un trabajo feo... aunque hayas creado una interfaz para abstraer las diferencias de antemano. Aun así, puede valer la pena hacerlo... si crees que tu base de datos va a cambiar, pero no es una bala de plata que lo convierta en una tarea fácil.

Así que mi consejo es el siguiente: a menos que estés escribiendo código que vaya a ser compartido en todos los proyectos, no crees una interfaz hasta que tengas más de una clase que la implemente... lo que de hecho hemos visto antes con nuestros factores de puntuación. Este es un buen uso de las interfaces.

Pero admito que no todo el mundo está de acuerdo con mi opinión al respecto Y si no estás de acuerdo, ¡genial! Haz lo que creas que es mejor. Hay muchas personas inteligentes que crean interfaces adicionales en su código para desvincularse de los marcos o bibliotecas que utilizan. Yo no soy uno de ellos.

## SOLID in Review

Bien amigos, ¡eso es todo! ¡Hemos terminado con los principios SOLID! Hagamos un rápido repaso... utilizando nuestras definiciones simplificadas.

Uno: el principio de responsabilidad única dice

> Escribe clases para que tu código "quepa en tu cabeza".

Dos: el principio de abierto-cerrado dice:

> Diseña tus clases de forma que puedas cambiar su comportamiento sin cambiar
> su código.

Esto nunca es del todo posible... y en el código de mi aplicación, rara vez lo sigo.

Tres: el principio de sustitución de Liskov dice:

> Si una clase extiende una clase base o implementa una interfaz, haz que tu clase se comporte
> como se supone que debe hacerlo.

PHP protege contra la mayoría de las violaciones de este principio lanzando errores de sintaxis.

Cuatro: el principio de segregación de la interfaz dice:

> Si una clase tiene una gran interfaz -por tanto, muchos métodos- y a menudo inyectas la
> clase y sólo utilizas algunos de estos métodos, considera la posibilidad de dividir tu clase en
> trozos más pequeños.

Y cinco: el principio de inversión de la dependencia dice

> Prefiere las interfaces de tipo y permite que cada interfaz se diseñe para la
> clase de "alto nivel" que la utilizará, en lugar de para la clase de bajo nivel que
> la implementará.

En mi aplicación, hago interfaces de sugerencia de tipo siempre que existen, normalmente porque los servicios de Symfony u otras bibliotecas proporcionan una interfaz. Pero no creo mis propias interfaces hasta que tengo varias clases que necesitan implementarlas.

Mis opiniones son, por supuesto, sólo eso: ¡opiniones! Y tiendo a ser mucho más pragmático que dogmático... para bien o para mal. Sin duda, la gente no estará de acuerdo... ¡y eso es genial! SOLID nos obliga a pensar de forma crítica.

Además, los principios SOLID no son el único "juego" en la ciudad cuando se trata de escribir código limpio. Hay patrones de diseño, composición sobre herencia, la ley de Demeter y otros principios para guiar tu camino.

Si tienes alguna pregunta o idea, como siempre, nos encantaría que nos lo dijeras en los comentarios.

Muy bien, amigos, ¡hasta la próxima!
