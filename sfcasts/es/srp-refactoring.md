Hemos identificado que `UserManager::register()` maneja dos cosas que pueden cambiar por diferentes razones. Estas son sus dos responsabilidades: una, crear y enviar un correo de confirmación y dos, configurar los datos de un usuario y guardarlos en la base de datos.

Ahora vamos a seguir el consejo de SRP y "separar las cosas que cambian por motivos diferentes".

## Aclarar la responsabilidad de UserManager

Lo primero que quiero hacer es cambiar el nombre de `register()` por `create()`... o podrías usar `save()`... o incluso cambiar el nombre de toda la clase en sí. La cuestión es que Quiero dejar más clara su responsabilidad: establecer todos los datos necesarios en el objeto usuario y guardarlos en la base de datos.

Haz clic con el botón derecho en `register()`, ve a Refactorizar->Renombrar y llama a esto `create()`.

[[[ code('568f94a771') ]]]

Al pulsar enter, en `RegistrationController`, PhpStorm también renombró el método allí.

[[[ code('85f4dabf91') ]]]

## Creación de la clase ConfirmationEmailSender

A continuación, vamos a trasladar la lógica relacionada con el correo electrónico a una nueva clase en el directorio `Service/`... aunque no importa dónde viva. Crea una nueva clase PHP llamada, qué tal, `ConfirmationEmailSender`. Esta clase necesitará dos servicios: el enrutador para poder generar el enlace y el correo. Añade una función pública`__construct()` con esos dos argumentos: `MailerInterface $mailer`, y `RouterInterface $router`. Pulsa Alt + Enter y ve a "Inicializar propiedades" para crear esas dos propiedades y establecerlas. No necesitamos este PHPDoc adicional aquí arriba.

[[[ code('974c98bc0c') ]]]

Ahora podemos crear una función pública llamada, qué tal, `send()`, con un argumento de objeto `User` que devolverá `void`.

[[[ code('5fdee3b8f4') ]]]

Para el interior de esto, vamos a robar toda la lógica relacionada con el correo electrónico de`UserManager`. Así que... copia las partes `$confirmationLink` y `$confirmationEmail`... borra esas... y pega. Sí PhpStorm: Definitivamente quiero que importes las declaraciones de `use` por mí.

La última línea que tenemos que robar es la de `$mailer->send()`. Pégala en la nueva clase.

[[[ code('247d2f9c51') ]]]

¡Muy bien! Vamos a celebrarlo limpiando las cosas en `UserManager`: podemos eliminar los dos últimos argumentos del constructor - `$router` y `$mailer` - sus propiedades... e incluso algunas declaraciones `use` en la parte superior.

[[[ code('a9364f2b00') ]]]

## ¿Quién debe generar el token de confirmación?

¡Ya está! Ahora... veamos... ¿quién debe encargarse de crear y establecer el token de confirmación en el Usuario? No estoy... exactamente seguro. Pero invirtamos la pregunta: ¿quién no debería ser responsable de crear el token?

Eso es un poco más fácil: probablemente no tiene sentido que el servicio cuya única responsabilidad es crear un correo electrónico... sea también responsable de generar este token criptográficamente seguro y guardarlo en la base de datos. Sí, este servicio se ocupa del enlace de confirmación... pero parece que esa lógica cambiaría por razones muy diferentes a las del propio correo electrónico.

Así que si descartamos `ConfirmationEmailSender` de nuestras opciones, entonces sólo queda un lugar lógico `UserManager::create()`. Y... tiene sentido: este método crea nuevos objetos `User` con todos los datos que necesitan y luego los guarda. También podrías optar por aislar la lógica de la creación de fichas de confirmación en una tercera clase... ¡no hay una respuesta correcta o incorrecta, que es lo que hace que estas cosas sean tan complicadas! Pero sobreoptimizar, dividiendo las cosas en demasiados trozos, también es algo que no queremos hacer. Hablaremos más de ello en el próximo capítulo.

De todos modos, ahora que hemos dividido todo nuestro código en dos lugares, en`RegistrationController`, tenemos que llamar a ambos métodos. Autocablea un nuevo argumento en el método: `ConfirmationEmailSender $confirmationEmailSender`. Entonces, abajo, justo después de llamar a `$userManager->create()`, di `$confirmationEmailSender->send()`y pasa el objeto `$user`.

[[[ code('deb3d37d47') ]]]

Ya está Nuestra función original -el envío de un correo electrónico de confirmación- se implementa ahora de una forma más amigable con el SRP.

## ¿Crear un servicio "que se encargue de todo"?

Por cierto, si no te gusta que tengas que llamar a dos métodos cada vez que registres a un nuevo usuario... ¡Estoy un poco de acuerdo! Y no hay problema: podrías extraer estas dos llamadas en una nueva clase... quizá llamada `UserRegistrationHandler`.

Su única responsabilidad sería "orquestar" todas las tareas relacionadas con el registro de un usuario. Se trata de una sola responsabilidad, no de muchas, porque no hace ningún trabajo real. Así que, por ejemplo, si tuviéramos que hacer un cambio en el correo electrónico de confirmación... o cambiar cómo se persiguen los usuarios en la base de datos... nada de eso requeriría que tuviéramos que modificar esta nueva clase. La nueva clase sólo cambiaría si añadiéramos algún nuevo "paso" al registro del usuario, como enviar una llamada a la API de nuestro servicio de boletín informativo.

## Disfrutar de SRP: añadir la función de reenvío

De todos modos, ahora que hemos refactorizado para ser compatibles con SRP, podemos disfrutar de nuestro duro trabajo añadiendo por fin la nueva función que nuestro equipo pedía: la posibilidad de reenviar un correo electrónico de confirmación.

Si has descargado el código del curso desde esta página, deberías tener un directorio `tutorial/`con un archivo `ResendConfirmationController` dentro. Cópialo, sube al directorio `Controller/`... y pégalo. Esto viene con la plantilla necesaria para una ruta a la que un usuario podría hacer un POST para reenviar su correo electrónico de confirmación.

[[[ code('532e422619') ]]]

Pero... el envío real de ese correo de confirmación sigue siendo un "TODO". Elimina ese comentario, autocablea el servicio `ConfirmationEmailSender`... y luego di`$confirmationEmailSender->send($user)`.

[[[ code('46edfae743') ]]]

¡Es así de fácil! No me molestaré en probar esto... pero repetiré las palabras que a todo desarrollador le encanta decir "debería funcionar".

Lo importante es que, gracias a nuestra nueva organización, si, por ejemplo, una persona de marketing quisiera modificar el asunto de nuestro correo electrónico de bienvenida, podemos hacer ese cambio sin tener que andar cerca del código que guarda cosas en la base de datos o hace el hash de las contraseñas.

Pero... Tengo más cosas que decir sobre la PRS... como los riesgos de la sobreoptimización, que viola un concepto llamado cohesión. También creo que, gracias a la inspiración de Dan North, hay una forma más fácil de pensar en la PRS. A continuación explicaré todo eso.
