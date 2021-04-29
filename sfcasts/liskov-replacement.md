# Liskov: Substituting a Class

Our highly-advanced, proprietary, believability score system is having some performance
problems. To help debug it, we want to measure how long calculating a score takes.
The simplest way to implement this would be almost entirely inside `SightingScorer`.
We could set a start time on top, then use that down here to calculate a duration.
And then we could pass that `$duration` into the `BigFootSightingScore` class. Hold
Command or Ctrl and click to open it: it's in the `src/Model/` directory. Inside
here, we could create a new property called `$duration`... with a getter so that
we could use that value.

## Lets: Substitute a Class!

But... let me undo that. Let's make things more interesting! To keep our application
as *skinny* as possible on production, I only want to run this new timing code when
we're in Symfony's `dev` environment. And yes, we *could* inject some
`$shouldCalculateDuration` value into `SightingScorer` *based* on the environment
and use *it* to determine if we should do that work.

But, in the spirit of Liskov, instead of *changing* `SightingScorer`, I want to
create a *subclass* that does the timing and *substitute* that class into our system
as the `SightingScorer` service.

It's gonna be kinda fun! And it's a pattern you'll find inside Symfony itself,
like with the `TraceableEventDispatcher`: a class that is substituted in for the
*real* event dispatcher only while developing. It adds debugging info. Well,
*technically*, that class uses *decoration* instead of being a subclass. That's a
different, and usually better design pattern when you want to *replace* an existing
class. But, to really understand Liskov, we'll use a subclass.

## Creating the Subclass

Let's start by creating that new subclass. Over in the `Service/` directory... so
that it's right next to our normal `SightingScorer`, add a new class called
`DebuggableSightingScorer`. Make it extend the normal `SightingScorer`.

Since our subtype is currently making *no* changes to the parent class, Liskov would
definitely be happy with it. What I mean is: we should *definitely* be able to
*substitute* this class into our app in place of the original, with no problems.

## Substituting the Real Class

But where *is* the normal `SightingScorer` service actually used? Open
`src/Controller/BigFootSightingController.php`. This `upload()` action is the one
that is executed when, from the homepage, we click to submit a sighting. Yep, down
here, you can see that this is the `upload()` method.

One of the arguments that's being autowired to this method is the `SightingScorer`...
which is used down here on submit to calculate the score.

*Now* I want to change this service to use our new class: I want to substitute it.
How? Open `config/services.yaml`. I mentioned earlier that we were going to swap
in our `DebuggableSightingScorer` *only* in the `dev` environment. But to keep
things simple, I'm *actually* going to do it in *all* environments. If you *did*
want to have this only affect your `dev` environment, you could make the same
changes we're about to make in a `services_dev.yaml` file.

*Anyways*, to suddenly start using our new class everywhere that the
`SightingScorer` is used, add `class:` and then
`App\Service\DebuggableSightingScorer`.

I know, this looks a little funny. This first line is still the service id. But
now instead of using that as the class, Symfony will use `DebuggableSightingScorer`.
The end result is that whenever someone autowires `SightingScorer` - like we do
in our controller - Symfony will instantiate an instance of our
`DebuggableSightingScorer`... and pass the normal `$scoringFactors` argument. Yep,
we just substituted our subclass into the system!

To prove it, find your terminal and run:

```terminal
php bin/console debug:container Sighting
```

I want to look at the `SightingScorer` service, so I'll hit 5. And... perfect! The
service id is `App\Service\SightingScorer`, but the class is
`App\Service\DebuggableSightingScorer`.

Another way to show this would be to go into our `BigFootSightingController`
and temporarily `dd($sightingScorer)`.

Back at your browser, refresh and... there it is! `DebuggableSightingScorer`

Let's go take that out... then refresh again. The page works and... even though
I won't test it, if we submitted, our `DebuggableSightingScorer` *would* correctly
calculate the believability score.

In other words, no surprise: if you create a subclass and change *nothing* in it,
you *can* substitute that class for its parent class. It follow's Liskov's principle.

## Method Changes that are NOT Allowed

Let's start adding our timing mechanism. In the class, go to Code -> Generate -
or Command + N on a Mac - select "Override methods" and override the `score()` method.
If you override a method and keep the same argument type hints and return type,
this class is *still* substitutable: I can refresh and PHP is still happy.

But if we *did* change the argument type-hints or return type to something
totally *different*, then even PHP will tell us to knock it off. For example, let's
completely change the return type to `int`. PhpStorm is mad! And if we refresh,
PHP is mad too!

> `DebuggableSightingScorer::score()` must be compatible with the parent
> `score()`, which returns `BigFootSightingScore`.

Our signature is incompatible and, nicely, PHP does *not* let us violate Liskov's
principle in this way. Go and undo that change.

So does this mean that we can *never* change the return type or argument type-hints
in a subclass? Actually... no! Remember the rules from earlier: you *can* change a
return type if you make it more *narrow*, meaning more *specific*. And you can
*also* change an argument type-hint... as long as you make it accept a *wider*, or
*less* specific type.

Let's see this in action by finishing our timing feature next.
