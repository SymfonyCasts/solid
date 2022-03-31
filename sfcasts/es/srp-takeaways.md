Decidimos que la funcionalidad del correo electrónico de confirmación y la funcionalidad de la creación de usuarios es probable que cambien por razones diferentes. Por ello, dividimos estas dos responsabilidades en dos clases distintas.

## Sobre-separación y cohesión

Ahora, tengo algunas preguntas. ¿Deberíamos separar la lógica del cacheo de contraseñas de la responsabilidad de la persistencia de usuarios? Es decir, ¿deberíamos trasladarla a su propia clase? ¿Y deberíamos tratar la generación de tokens de confirmación como una responsabilidad propia y trasladarla a otro lugar?

Si miras rápidamente el SRP, parece que la regla es

> Poner cada pequeña pieza de funcionalidad en su propia clase y método.

Pero, afortunadamente, SRP no dice eso... ¡eso convertiría nuestro código en un desastre! Hay otro concepto llamado "cohesión". Dice así:

> Mantener juntas las cosas que están relacionadas.

Al principio, parece que la cohesión y el SRP son opuestos. Es decir, el SRP dice "separa las cosas" y la cohesión dice "¡no, mantén las cosas juntas!". Pero si lo analizamos más detenidamente, la PRS y la cohesión son dos formas de decir lo mismo: mantener juntas sólo las cosas relacionadas. Éste es el empuje de la PRS: separa las cosas que van a cambiar por diferentes motivos... pero no las separes más.

Si nos fijamos en `UserManager`, ya estamos un poco protegidos de los cambios en la funcionalidad del bloqueo de contraseñas, porque dependemos de un servicio que está detrás de una interfaz: `UserPasswordEncoderInterface`. El funcionamiento de ese servicio podría cambiar completamente y no tendríamos que actualizar ningún código de esta clase. Así que el riesgo de que eso cambie de alguna manera que nos obligue a cambiar esta clase es probablemente muy bajo.

[[[ code('819b4c52c3') ]]]

¿Qué pasa con la lógica de generación de fichas? ¿Creemos que es muy probable que cambiemos la forma en que se generan los tokens? Esto... a mí me parece un candidato débil para separar. Ya es sencillo: una línea de código aquí abajo... y dos líneas de código aquí arriba. Y es poco probable que cambie, sobre todo por una razón diferente al resto del código de esta clase.

[[[ code('56d00e83d2') ]]]

En general, mi consejo es el siguiente: no te adelantes a los posibles cambios futuros.

## Escribe un código que encaje en tu cabeza

Al principio de este tutorial, mencioné un [post del blog de Dan North](https://dannorth.net/2021/03/16/cupid-the-back-story/amp/), el padre del desarrollo basado en el comportamiento. Tiene algo deliciosamente refrescante que decir sobre el principio de responsabilidad única. En lugar de pensar en posibles cambios... y organizar las cosas en responsabilidades -lo cual es complicado-, sugiere algo más sencillo: escribir un código sencillo.... utilizando la vara de medir de: "¿este código cabe en mi cabeza?".

Esto me encanta. Si un método o clase tiene demasiadas cosas, entonces la lógica total de ese método no "cabrá en tu cabeza"... y será difícil pensar y trabajar con él. Por tanto, debes separarlo en trozos más pequeños que sí quepan en tu cabeza.

Por otro lado, si divides el código para registrar a un usuario en 10 clases diferentes, eso también va a ser complejo de pensar. El objetivo general es crear unidades de código que quepan en tu cabeza... para que puedas tener una aplicación global que también "quepa en nuestra cabeza".

Si sigues este consejo general, creo que te darás cuenta de que probablemente crearás clases y métodos que sigan la SRP bastante bien... sin el estrés de intentar perfeccionarla.

Bien, es hora de sumergirse en el siguiente principio sólido: el principio de abierto-cerrado.
