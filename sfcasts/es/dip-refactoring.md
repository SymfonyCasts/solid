Nuestro código, concretamente el de estas dos clases, no sigue el principio de inversión de la dependencia. ¿Por qué no? Repasemos las dos partes de la definición, una por una.

La primera parte es:

> Los módulos de alto nivel no deben depender de los módulos de bajo nivel. Ambos deben depender de
> abstracciones, por ejemplo, las interfaces.

Esto es una forma elegante de decir que las clases deben depender de interfaces en lugar de clases concretas. Sí Esta parte de la regla es así de sencilla. Dice que, en lugar de indicar el tipo -por tanto, "depender de"- de la clase concreta `RegexSpamWordHelper`, debemos indicar el tipo de una interfaz.

¡De acuerdo! Así que sólo tenemos que crear una nueva interfaz, hacer que `RegexSpamWordHelper`implemente la interfaz, y luego cambiar la sugerencia de tipo para utilizar esa interfaz, ¿verdad? Sí, ¡exactamente!

## Pensando en el diseño de tu interfaz

Pero... la segunda parte del DIP nos dice algo sobre cómo debemos crear y diseñar esa interfaz. Esa parte dice

> Las abstracciones no deben depender de los detalles. Los detalles -que son implementaciones concretas
> implementaciones - deben depender de las abstracciones.

Simplificamos esto a:

> Una interfaz debe ser diseñada por la clase que la utilizará, no por la
> clase que la implementará.

Me explico. La forma más natural de crear la nueva interfaz sería fijarse en la clase que la implementará -así que `RegexSpamWordHelper` - y crear una interfaz que se ajuste a ella Así que un `RegexSpamWordHelperInterface` con un método`getMatchedSpamWords()`. ¡Ya está hecho!

Pero al hacer esto, estamos permitiendo que la interfaz sea, en cierto modo, "propiedad" de la clase de nivel inferior, a veces conocida como clase "detalles". En otras palabras, el aspecto de la interfaz está siendo "controlado" por la clase de nivel inferior`RegexSpamWordHelper`.

Pero el DIP dice que la clase de nivel superior - `CommentSpamManager` - debe encargarse de crear la interfaz, permitiéndole diseñar su dependencia justo como quiere.

## Creación de la interfaz

Pongamos esto en práctica. Si te fijas en `CommentSpamManager`, lo único que necesita realmente es poder llamar a un método que devuelva el número de palabras que se han convertido en spam... porque ese recuento es, en última instancia, lo único que utilizamos: no necesitamos realmente las palabras coincidentes en sí mismas.

Así que en el directorio `Comment/`, que utilizo para resaltar que esta interfaz es propiedad de `CommentSpamManager`, crea una nueva interfaz: selecciona la clase PHP, cambia a interfaz y llámala, qué tal, `CommentSpamCounterInterface`.

Dentro, añade un método: la función pública `countSpamWords()`, que aceptará el`string $content` y devolverá un `int`.

[[[ code('fc8218ee31') ]]]

¡Qué bonito! Fíjate en que sólo invirtiendo quién creemos que debe encargarse de crear la interfaz -o quién debe "poseerla"- acabamos con un resultado muy diferente. En lugar de obligar a la interfaz a parecerse a la clase de bajo nivel`RegexSpamWordHelper`, ahora se va a obligar a esa clase a cambiarse a sí misma para implementar la interfaz.

Añade implementa `CommentSpamCounterInterface`, luego iré a Código -> Generar - o Comando + N en un Mac - y seleccionaré "Implementar métodos" para generar`countSpamWords()`. Dentro, devuelve el `count()` de`$this->getMatchedSpamWords($content)`.

[[[ code('d7818960c8') ]]]

De vuelta a `CommentSpamManager`, vamos a seguir la primera parte del DIP y cambiar esto para que dependa de la nueva interfaz. Cambia el tipo-indicación a`CommentSpamCounterInterface`... cambia el tipo en la propiedad... y también cambiemos el nombre de la propia propiedad para que sea más clara: llámala `$spamWordCounter`. Cambia también el nombre del argumento.

[[[ code('3c54572a60') ]]]

Abajo, en `validate()`, cambia `$badWordsOnComment` por `$badWordsCount`. Luego, en lugar de llamar a `getMatchedSpamWords()`, llama al nuevo `countSpamWords()`. Abajo, ya no necesitamos el `count()`: sólo comprueba si `$badWordsCount` es mayor o igual que 2.

[[[ code('4bf5f6efba') ]]]

¡Enhorabuena! ¡Nuestro código sigue ahora las dos partes del principio de inversión de la dependencia! Una, nuestra clase de alto nivel - `CommentSpamManager` - depende de una interfaz. Y dos, esa interfaz fue diseñada para - y es controlada por - la clase de alto nivel, en lugar de ser diseñada y controlada por la clase de bajo nivel, o "detalles": `RegexSpamWordHelper`.

## Cómo Symfony autocablea las interfaces

Antes de hablar de las conclusiones del principio de inversión de la dependencia, quiero mencionar dos cosas.

En primer lugar, en `RegexSpamWordHelper`, se te permite tener esta función pública`getMatchedSpamWords()` método si lo estás utilizando en algún otro lugar de tu código. Como no es así, voy a limpiar las cosas y hacer que sea `private`.

[[[ code('bf11f80bee') ]]]

En segundo lugar... bueno... esto es más bien una pregunta: ¿sabrá Symfony qué servicio debe autoconectar cuando vea la sugerencia de tipo `CommentSpamCounterInterface`? ¿Sabrá que debe pasarnos el servicio `RegexSpamWordHelper`?

En realidad... ¡lo hará! Busca tu terminal y ejecuta:

```terminal
php bin/console debug:autowiring Comment --all
```

Paso `--all` sólo para que podamos ver todos los resultados. Y... ¡esto lo demuestra! Como muestra esto, cuando Symfony ve una sugerencia de tipo `CommentSpamCounterInterface`, se autoconecta con el servicio `RegexSpamWordHelper`.

Esto funciona gracias a una bonita característica dentro del contenedor de Symfony. Si Symfony ve una interfaz en nuestro código -como `CommentSpamCounterInterface` - y sólo una de nuestras clases la implementa, entonces asume automáticamente que esta clase debe ser autocableada para esa interfaz. Si creara una segunda clase que implementara la interfaz, Symfony lanzaría una clara excepción indicándonos que debemos elegir cuál autocablear.

A continuación: vamos a hablar de lo que se desprende del principio de inversión de la dependencia, y también... de lo que significa y no significa esa palabra "inversión".
