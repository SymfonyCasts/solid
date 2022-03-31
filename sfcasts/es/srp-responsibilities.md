Nos acaban de informar de que, de vez en cuando, nuestro correo electrónico de confirmación no llega a la bandeja de entrada de nuestros usuarios ¡Ah! Y entonces: tenemos que implementar una función de reenvío.

## SRP: No debería ser necesario cambiar el código no relacionado

Esto debería ser fácil, ¿verdad? Después de todo, hemos encapsulado toda nuestra lógica para enviar un correo electrónico de confirmación en un solo método. Pero... hmm. Para que esto funcione, probablemente vamos a tener que extraer parte del método `register()` en una función pública independiente, de modo que podamos simplemente reenviar el correo electrónico... sin tener que crear también un nuevo token y volver a introducir la contraseña.

[[[ code('6b25087b1f') ]]]

¿No es un poco raro... o al menos "no ideal"... que para añadir esta función de "reenvío de correo electrónico", vayamos a estar trasteando y reorganizando el código que se ocupa del hash de las contraseñas y de la persistencia de los datos del usuario? En un mundo perfecto, ¿no debería poder crear esta función de "reenvío de correo electrónico" sin acercarme al código que no está relacionado con esta funcionalidad?

Esto es lo que intenta ayudarnos SRP. En ese mundo "perfecto" de SRP, cada vez que se solicite un cambio en nuestro proyecto, sólo tendríamos que tocar el código directamente relacionado con ese cambio: no tendríamos que cambiar -ni siquiera trabajar cerca- de código no relacionado. El hecho de que tengamos que modificar un método que también se ocupa de guardar usuarios y hacer hash de las contraseñas... para añadir una función que no tiene nada que ver con esas cosas... es una señal de que `UserManager` viola la SRP. Nuestra clase `UserManager`tiene demasiadas responsabilidades.

## ¿Qué es una "responsabilidad"?

Pero, ¿cuáles son las responsabilidades de esta clase? Se me ocurren 5 al menos: generar un enlace de confirmación... que también incluye la creación del token de confirmación, crear un correo electrónico, hacer un hash de la contraseña, guardar el usuario y enviar un correo electrónico.

Pero... espera un segundo. Y éste es un punto muy, muy importante -y confuso- sobre la PRS. Definir las responsabilidades no significa:

> Piensa en todas las diferentes y pequeñas cosas que hace tu clase.

No Una forma mejor de decirlo podría ser:

> Piensa en todas las diferentes razones por las que esta clase puede cambiar.

Eso es mucho más difícil... y depende completamente de tu aplicación y negocio. Para ayudarte con esto, a veces es útil pensar en lo que hace nuestra clase en un nivel superior. En mi opinión, nuestro método de registro hace dos cosas básicas (1) prepara y persigue al usuario y (2) envía un correo electrónico.

Ahora veamos si podemos pensar en una persona de nuestro negocio "totalmente falso" que pueda pedir un cambio en una de estas dos cosas.

Por ejemplo, para el "trabajo de alto nivel" de "preparar y persistir al usuario", nuestro administrador de la base de datos podría, en el futuro, querer cambiar cómo se almacenan los usuarios... o nuestro director de tecnología podría querer empezar a utilizar un proveedor de autenticación de terceros en lugar de almacenar a los usuarios en una base de datos local y gestionar sus contraseñas. Este tipo de cambio afectaría a la forma en que hacemos hash de las contraseñas y a la forma en que guardamos los usuarios. En otras palabras, dos de nuestras denominadas "responsabilidades" originales -el hash de la contraseña y la persistencia del usuario- probablemente cambiarán por la misma razón. Por tanto, en realidad forman parte de la misma y única responsabilidad: "preparar y persistir al usuario".

La otra cosa de "alto nivel" que hace el método es enviar el correo electrónico de confirmación. Lo más probable es que tenga que cambiar si una persona de marketing quiere retocar el asunto de un correo electrónico para que sea más divertido... o pasar algunas variables de "producto destacado" a la plantilla para intentar vender cosas. Esto significa que 3 de las otras llamadas "responsabilidades" originales -generar la URL de confirmación, crear el correo electrónico y enviar el correo electrónico- probablemente cambiarán por la misma razón. Y así, para nuestro proyecto, todas se considerarían una sola responsabilidad: "enviar el correo electrónico de confirmación".

## Organizar las responsabilidades es un arte... en el mejor de los casos

¿Es esto perfecto? Desde luego que no Podrías argumentar fácilmente que el envío del correo electrónico cambiaría por otra razón. Si alguien decide que vamos a empezar a enviar correos electrónicos utilizando un servicio de proveedor de correo electrónico diferente... ya estamos protegidos de ese cambio: eso sólo requeriría algunos ajustes de configuración en un archivo diferente. Pero, ¿y si pensamos que es probable que cambiemos el funcionamiento de nuestro sistema de verificación del correo electrónico en el futuro? En ese caso, tendríamos una razón legítima para pensar que la generación del token y el enlace de confirmación cambiarían por un motivo distinto al de la persistencia de nuestro usuario o la creación del correo electrónico.

Identificar las razones más probables por las que una función podría tener que cambiar y luego agrupar la funcionalidad en esas responsabilidades es la parte más difícil de la PRS. Incluso nuestra agrupación parece imperfecta. Pero, sinceramente, ¡es suficientemente buena! Mi consejo es que lo hagas lo mejor posible y no lo pienses demasiado. También vamos a hablar de la sobreoptimización de la PRS más adelante... que puede conducir a un problema diferente.

También es útil tener en cuenta nuestra definición "humana" original para la PRS:

> Reúne las cosas que cambian por la misma razón y separa las
> cosas que cambian por motivos diferentes.

A continuación: ahora que hemos identificado las dos responsabilidades que tiene actualmente `UserManager`, vamos a refactorizar nuestro código para que sea más compatible con el SRP.
