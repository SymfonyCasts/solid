El principio sólido número tres es, en mi opinión, uno muy interesante. Es el Principio de Sustitución de Liskov, desarrollado por Barbara Liskov: una investigadora del MIT y ganadora del premio Turing, que es, según he sabido, una especie de premio Nobel de la informática. No es nada del otro mundo.

## Definición de Liskov

El principio de Liskov establece:

> Los subtipos deben ser sustituibles por sus tipos base.

En realidad, no es una definición terrible. Un "subtipo" significa básicamente una clase: cualquier clase que extienda una clase base o que implemente una interfaz.

Así que permíteme reformular la definición. Voy a ceñirme a hablar sólo de las clases y de las clases padre, pero esto se aplica igualmente a una clase que implemente una interfaz. Aquí está:

> Deberías poder sustituir una clase por una subclase sin romper tu aplicación
> o tener que cambiar ningún código.

Dan North se refiere a esto simplemente como

> El principio de la menor sorpresa, aplicado a las clases que tienen una clase padre o
> implementan una interfaz.

En otras palabras, una clase debe comportarse de la manera que la mayoría de los usuarios esperan: debe comportarse como su clase madre o su interfaz pretenden.

Vale, ¡eso suena muy bien! Pero... ¿qué significa eso concretamente?

## Los 4 aspectos que (mayoritariamente) definen a Liskov

Significa cuatro cosas concretas. Imagina que tenemos una clase que extiende una clase base o implementa una interfaz. También tiene una propiedad protegida y un método, que viven en esa clase base. O en el caso del método, vive en la interfaz.

Dada esta configuración, Liskov dice 4 cosas.

Una: no puedes cambiar el tipo de una propiedad protegida.

Dos: no puedes limitar el tipo de pista de un argumento. Por ejemplo, si la clase padre utiliza la sugerencia de tipo `object`, no puedes hacerla más estrecha en tu subclase exigiendo algo más específico, como un objeto `DateTime`.

Tres, que es a la vez similar y opuesto a la regla anterior, no puedes ampliar el tipo de retorno. Si la clase padre dice que un método devuelve un objeto `DateTime`, no puedes cambiarlo en la subclase para devolver de repente algo más amplio, como cualquier objeto.

Y, por último, en cuarto lugar, debes seguir las reglas de tu clase madre -o de la interfaz- sobre si debes lanzar o no una excepción en determinadas condiciones.

Puede que haya algunos casos extremos que se me hayan escapado con estas 4 reglas, pero ésta es la idea básica. Al violar cualquiera de estas reglas, estás haciendo que tu clase se comporte de forma diferente a la que pretendía su clase madre o su interfaz. Eso es malo porque si parte de tu código espera una instancia de esa interfaz y pasas tu clase, aunque implemente la interfaz, las violaciones de la clase pueden hacer que ocurran cosas raras. Veremos ejemplos concretos de esto en los próximos capítulos.

Esto es lo que realmente me gusta de este principio. ¿Esas tres primeras reglas? Sí, son imposibles de violar en PHP. Si cambias el tipo de propiedad de una propiedad protegida, reduces el tipo de un argumento o amplías el tipo de retorno de un método, PHP te dará un error de sintaxis. Sí, el principio de Liskov tiene tanto sentido que sus reglas están codificadas en el lenguaje.

Así que ya conocemos las reglas de Liskov. Pero para comprender mejor por qué existen estas reglas y, lo que es casi más importante, qué cosas podemos hacer en un "subtipo", pasemos a dos ejemplos del mundo real.
