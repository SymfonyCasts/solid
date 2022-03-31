Para celebrar nuestro nuevo sistema, vamos a verlo en acción. En `BigFootSightingController`, después de `addFlash()`, añadamos también información sobre la duración. Pero como no sabemos con certeza si estamos utilizando la versión "depurable" del servicio, añade si `$bfsScore` es una instancia de `DebuggableBigFootSightingScore`, entonces`$this->addFlash('success', sprintf(...))` con:

> Además, la puntuación ha tardado %f milisegundos

Pasando `$bfsScore->getCalculationTime()` por 1000 para convertir de microsegundos a milisegundos.

[[[ code('78d89a9187') ]]]

¡Genial! Pero... espera: ¿no he dicho que `instanceof` es una señal de que podemos romper el principio de Liskov? Sí Pero en este caso no me preocupa demasiado, por varias razones. En primer lugar, este es mi controlador... cuyo trabajo es unir todas las piezas feas de mi aplicación. Y en segundo lugar, estoy utilizando el `instanceof` para detectar si puedo añadir funcionalidad... no para solucionar una subclase que se comporta mal.

Sin embargo, otra solución, dependiendo de si realmente necesitas sustituir esta clase sólo en un entorno, es decir explícitamente que necesitas la versión depurable del servicio. Así, en lugar de decir "permito cualquier `SightingScorer`", podríamos decir "necesito específicamente un `DebuggableSightingScorer`".

Si hiciéramos eso, no necesitaríamos el `instanceof` porque sabríamos que ese servicio devuelve un `DebuggableBigFootSightingScore`, que tiene el método`getCalculationTime()`.

[[[ code('39de176da6') ]]]

Pero... nos falta un pequeño detalle de configuración en Symfony. Intenta refrescar la página. ¡Se rompe!

> No se puede autoconducir el servicio `DebuggableSightingScorer`: el argumento $scoringFactors es
> de tipo `iterable`. Debes configurar su valor explícitamente.

Espera... nos encontramos con este error cuando trabajamos con el principio de abierto-cerrado. Y, en`config/services.yaml`, lo solucionamos cableando específicamente el argumento `$scoringFactors`. ¿Por qué ya no funciona?

Gracias al auto-registro -la característica que registra automáticamente todas las clases en `src/` como un servicio- hay un servicio separado en nuestro contenedor llamado`DebuggableSightingScorer`. Puedes verlo si ejecutas

```terminal
php bin/console debug:container Sighting
```

¡Si! Hay un servicio `DebuggableSightingScorer` y un servicio separado para`SightingScorer`. Esto... no es lo que queremos. En realidad, quiero que Symfony nos pase el mismo servicio, independientemente de si escribimos`DebuggableSightingScorer` o `SightingScorer`.

Podemos hacerlo añadiendo un alias. Dentro de `services.yaml`, digamos`App\Service\DebuggableSightingScorer`, dos puntos, un símbolo `@` y luego`App\Service\SightingScorer`.

[[[ code('d5d4dec356') ]]]

Esto dice: siempre que alguien intente autoconectar o utilizar el servicio `DebuggableSightingScorer`, en realidad debes pasarle el servicio `SightingScorer`... que, ya sé, es en realidad una instancia de la clase `DebuggableSightingScorer`. Puede ser un poco confuso.

De vuelta a tu terminal, ejecuta de nuevo `debug:container`:

```terminal-silent
php bin/console debug:container Sighting
```

Parece que sigue habiendo 2 servicios, pero si le das a "6" para ver el "Depurable", en la parte superior, dice

> Este es un alias del servicio `App\Service\SightingScorer`.

Y en el navegador, cuando refrescamos... ¡vuelve a funcionar!

## Conclusiones del Principio de Liskov

Así que la gran conclusión del principio de Liskov es la siguiente: asegúrate de que cuando tengas un "subtipo" -una clase que extienda a otra o que implemente una interfaz- siga las reglas de ese tipo padre. No hace nada sorprendente. Eso es todo. Y PHP incluso nos evita la mayoría de las violaciones de Liskov.

La parte más interesante de Liskov para mí es conocer las cosas que se nos permiten hacer. Por ejemplo, está permitido cambiar el tipo de retorno de un método siempre que lo hagas más específico. O lo contrario para los tipos de argumento: puedes cambiarlos... siempre que los hagas menos específicos.

Bien, el siguiente es el principio sólido número 4: el principio de segregación de la interfaz.
