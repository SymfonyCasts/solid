# LSP: Replacement

Coming soon...

Our highly advanced proprietary believability score system is having some performance
problems to help debug it. We want to measure how long calculating a score takes the
simplest way to implement. This would be almost entirely inside citing score. We
could set a start time on top and then use that down here To calculate a duration of
the bottom. And then maybe we pass that new `$duration` into this `BigFootSightingScore`
class, which I'll hold command to open that I was in the `src/Model/` directory, and
we would maybe have like a new property up here called `$duration` and a getter down
here so that we could read the duration off of this object and use it anywhere we
want. But let me undo that. Let's make things more interesting to keep our
application as skinny as possible on production. I only want to run this new timing
code when we're in Symfonys dev environment. In other words, instead of changing
`SightingScorer`, I want to create a subclass that does the timing and substitute that
class into the system as the `SightingScorer` in the dev environment only. Well, really,
we're going to do it in all environments.

It's going to be kind of fun. And it's a pattern you'll find inside Symfony itself
like with the `TraceableEventDispatcher`, which adds debugging info to the event,
this patcher, but only in the dev environment start recreating the subclass that will
do the timing over here in these `Service/` directory. So that it's right next to our
normal setting score. Let's create a new class called `DebuggableSightingScorer`

Make it extend the normal `SightingScorer`.

Since our subtype is making no changes to the parent class Liskov would
definitely be happy with it. We should be able to substitute this class into our
application with no problem, but where is the normal `SightingScorer` service actually
used in our app? If you've got a `src/Controller/BigFootSightingController`, this
`upload()` action is the one that is hit when we click to the homepage and then hit
submit. Yep. And down here, you can see that this is the `upload()` method. One of the
arguments that's being auto wire to this method is the `SightingScorer`, which it's used
down here on submit to calculate the score.

Now, I want to change this service to use our new class. I want to substitute it. How
open `config/services.yaml` Now I mentioned the beginning. We were going to swap in
our debugger citing score only in the dev environment. I'm actually going to do it in
all environments, but you can make these same changes. We're about to make to eight
services, underscored dev Diane we'll file. If you want these to happen only in the
dev environment anyways, to suddenly start using our new class everywhere in the
whole system, we can add `class: `  and then `App\Service\DebuggableSightingScorer`
I know this looks a little funny. This first line is still the service ID, but
now instead of using that as the class, it's going to use `DebuggableSightingScorer`. The
end result of this is that whenever someone auto wires `SightingScorer` like in our
controller, Symfony is now going to pass them in instance of our `DebuggableSightingScorer`
and then past the normal `$scoringFactors` argument to it. Yep. We just
substituted in our subclass. You can see it. If you find your terminal by running 

```terminal
php bin/console debug:container Sighting
```

And Right now we want to use the sighting score. So I'll hit five and perfect. You can
say the service id `SightingScorer`, but the class is actually going to use is
`DebuggableSightingScorer`. Another way to show that would be to go into our
`BigFootSightingController`. And I'll just temporarily the `dd($sightingScorer)` over it,
refresh. And there it is. `DebuggableSightingScorer` Let's go take that out.

Wow.

Good. Take that out and then refresh again. The fact that this page loads means that
Symfony is successfully auto wiring, that new `DebuggableSightingScorer` into our
class. How the words it works. So no surprise. If you create a subclass and change
nothing, that class is of course, substitutable, let's start implementing our timing
mechanism in a new class. I'm going to go to go to Code -> Generate or Command + N on
the Mac, go to "Override methods" and override the `score()` method. If you override a
method and keep this same argument type hints and return type, this class is still
substitutable over here. I can refresh and PHP is still happy,

But If we try to change the argument type pens, arguments, or return type to something
totally different, then even PHP will tell us to cut it up. For example, let's
completely change the return type to it. You can already see PHP. Storm is mad, and
if we refresh huge air coming from PHP itself, the `DebuggableSightingScorer::score()`, it
must be compatible with score, uh, returning this `BigfootSightingScore`. Our
signature is incompatible. In other words, PHP does not let us violate Liskov
principle. Let's go in and do that change. So does this mean that we can never change
the return type or argument types in a subclass? Actually, no. Remember our rules
from earlier, you can change a return type if you make it more narrow or specific And
you can also change in argument type hint, as long as you make it accept a wider or
less specific type. Let's see this in action by finishing our timing feature next.

Okay.

