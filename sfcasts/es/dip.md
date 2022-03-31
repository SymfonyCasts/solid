Hemos llegado al quinto y último principio SOLID: el principio de inversión de la dependencia, o DIP. Este cachorro tiene una definición en dos partes. ¿Preparado? Una:

> Los módulos de alto nivel no deben depender de los de bajo nivel, ambos deben depender
> de abstracciones, por ejemplo, de interfaces.

Y la segunda parte dice:

> Las abstracciones no deben depender de los detalles. Los detalles -es decir, las implementaciones
> implementaciones concretas, deberían depender de las abstracciones.

Uhh... si eso tiene sentido para ti, ¡eres increíble! Y... ¡estoy celoso de ti!

## Definición más sencilla

¿Cómo podría reformular esto? Eh, vaya. ¿Qué te parece esto? Uno:

> Las clases deben depender de las interfaces en lugar de las clases concretas.

Y dos:

> Esas interfaces deberían ser diseñadas por la clase que las utiliza, no por las
> clases que las implementarán.

Es probable que esto siga siendo confuso... pero no te preocupes. Esto requiere un ejemplo real.

#¡Nuestro sistema de detección de spam!

Este es nuestro nuevo problema. Nos hemos vuelto tan populares -no es una sorpresa- que algunos de nuestros avistamientos están recibiendo muchos comentarios de spam... como los comentarios que dicen que Pie grande no es real. ¡Esos son definitivamente bots!

Así que necesitamos una forma de determinar si un comentario es spam o no, basándonos en una lógica de negocio que hemos creado. Si has descargado el código del curso desde esta página, deberías tener un directorio `tutorial/` con una clase `CommentSpamManager` dentro. Cópiala, luego ve a crear un nuevo directorio en `src/` llamado `Comment/`... y pega la clase allí.

[[[ code('32e0e54a82') ]]]

Esta clase básicamente determina si un comentario debe ser marcado como spam ejecutando una expresión regular sobre el contenido utilizando una lista de palabras spam predefinidas. Si el contenido contiene dos o más de esas palabras, entonces consideramos el comentario como spam y lanzamos una excepción.

Si piensas en el principio de responsabilidad única, podrías argumentar que esta clase ya tiene dos responsabilidades: la lógica de expresión regular de bajo nivel que busca las palabras de spam y una lógica de negocio de nivel superior que decide que dos palabras de spam es el límite.

## Dividir la clase

Imaginemos que sí pensamos que se trata de dos responsabilidades diferentes. Por lo tanto, decidimos dividir esta clase en dos partes. En el directorio `Service/`, crea una nueva clase llamada `RegexSpamWordHelper`. Veamos: traslada el método privado `spamWords()`a la nueva clase... y luego crea una nueva función pública llamada`getMatchedSpamWords()` a la que le pasamos el `string $content` y devuelve un array de las palabras de spam coincidentes.

[[[ code('1ef8ffef26') ]]]

A continuación, traslada la propia lógica regex a la clase. Copia todo el contenido del método existente.... pero déjalo... y pégalo. Veamos... ya no necesitamos`$comment->getContent()`.... sólo se llama `$content`... y el índice 0 de `$badWordsOnComment` contendrá las coincidencias, así que podemos devolverlo.

[[[ code('3dfa42c2fd') ]]]

¡Qué bien! Ahora que esta clase está lista, vamos a inyectarla en`CommentSpamManager`. Añade la función pública `__construct()` con `RegexSpamWordHelper``$spamWordHelper` . Pulsaré Alt + Enter y seleccionaré "Inicializar propiedades" para crear esa propiedad y establecerla 

[[[ code('dc742bb186') ]]]

Abajo, ahora podemos decir `$badWordsOnComment = $this->spamWordHelper->getMatchedSpamWords()` y pasar ese `$content` de arriba. Ya no necesitamos nada de la lógica del medio. Por último,`$badWordsOnComment` contendrá la matriz de coincidencias, por lo que ya no necesitamos utilizar el índice 0: basta con contar toda esa variable.

[[[ code('306b502000') ]]]

¡Ya está!

## Módulos de alto y bajo nivel

Llegados a este punto, hemos separado la lógica de negocio de alto nivel -decidir cuántas palabras de spam deben hacer que un comentario se marque como spam- de los detalles de bajo nivel: la coincidencia y la búsqueda de las palabras de spam. El principio de inversión de la dependencia no nos dice necesariamente si debemos o no dividir la lógica original en dos clases como acabamos de hacer. Eso es probablemente más propio del principio de responsabilidad única.

Pero el DIP nos enseña a pensar en nuestro código en términos de módulos (o clases) de "alto nivel", como `CommentSpamManager`, que dependen de módulos (o clases) de "bajo nivel", como `RegexSpamWordHelper`. Y nos da reglas concretas sobre cómo debe manejarse esta relación.

A continuación, vamos a refactorizar la relación entre estas dos clases para que cumpla el principio de inversión de la dependencia. Veremos, en términos reales, qué cambios quiere que hagamos cada una de las dos partes de este principio.
