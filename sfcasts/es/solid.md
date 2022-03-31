¡Hola amigos! Bienvenidos a nuestro esperado tutorial sobre los principios SOLID: principio de responsabilidad única, principio de abierto-cerrado, principio de sustitución de Liskov, principio de segregación de la interfaz y, mi favorito personal: el principio del donut en la cara, probablemente... conocido en realidad como el principio de inversión de la dependencia.

Quiero dar las gracias a mi coautor Diego por haberme ayudado a elaborar finalmente este tutorial. ¡Y lo siento mucho si has estado esperando esto!

## Principios SOLID: No los amo

Entonces... ¿por qué hemos tardado tanto en hacer este tutorial? La respuesta corta es: I.... como que no me gustan los principios SOLID. Vale, déjame decirlo de otra manera. Los principios SOLID son difíciles de entender. Y, en mi más humilde opinión, ¡no siempre son un buen consejo! Depende de la situación. Por ejemplo, debes escribir el código de tu aplicación de forma diferente a como escribirías el código destinado a ser de código abierto y compartido.

Si quieres saber un poco más sobre por qué SOLID no siempre es correcto, puedes leer una reciente entrada de blog escrita por Dan North llamada [CUPID - THE BACK STORY](https://dannorth.net/2021/03/16/cupid-the-back-story/). Dan North es conocido por ser la persona que primero hizo famoso el desarrollo orientado al comportamiento. Puede que hayas oído hablar de él si eres usuario de Behat.

De todos modos, este tutorial no va a ser otro más en el que leamos la definición de cada principio SOLID con voz monótona... y poco a poco nos perdamos, nos aburramos y finalmente nos quedemos dormidos. No. Vamos a sumergirnos en cada principio, aprender lo que realmente significan -utilizando palabras humanas normales-, codificar algunos ejemplos reales y discutir por qué y cuándo seguir estos principios tiene sentido y no tiene sentido. Pero incluso cuando los principios SOLID no deben seguirse, tienen mucho que enseñarnos. Así que prepárate para un viaje salvaje.

## Configuración del proyecto

Ya que vamos a hacer algo de codificación real, vamos a preparar el proyecto y a darle caña. Hazme un favor descargando el código del curso desde esta página y descomprimiéndolo. Después de hacerlo, encontrarás un directorio `start/` con el mismo código que ves aquí. Este elegante archivo `README.md` tiene todos los detalles sobre cómo poner en marcha el proyecto. El último paso será encontrar un terminal, entrar en el proyecto e iniciar un servidor web local. Para ello utilizaré el binario de Symfony:

```terminal
symfony serve -d
```

Una vez que esto termine, copia esa URL, vuelve a tu navegador, pégala y... ¡saluda a "Sasquatch Sightings"! Nuestro último esfuerzo por encontrar al infame Pie grande. Lo que este código hace en realidad es... no demasiado importante. Habla con una base de datos, enumera algunos avistamientos de pies grandes y tiene algunos cálculos. Será nuestro terreno de juego para sumergirnos en los principios SOLID.

Así que, a continuación, vamos a empezar con el primero: ¡el principio de responsabilidad única!
