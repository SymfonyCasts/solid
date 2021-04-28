# Liskov: Substituting a Class

Our highly-advanced, proprietary, believability score system is having some performance
problems. To help debug it, we want to measure how long calculating a score takes.
The simplest way to implement this would be almost entirely inside `SightingScorer`.
We could set a start time on top, then use that down here to calculate a duration.
And then maybe we pass that `$duration` into the `BigFootSightingScore` class. Hold
Command or Ctrl and click to open that: it's in the `src/Model/` directory. Inside
here, we could create a new property called `$duration` and add a getter down here
so we could use that value somewhere.

## Lets: Substitute a Class!

But... let me undo that. Let's make things more interesting. To keep our application
as *skinny* as possible on production, I only want to run this new timing code when
we're in Symfony's `dev` environment. And yes, we *could* inject some
`$shouldCalculateDuration` value into `SightingScorer` based on the environment
and use *it* to determine if we should do that work.

But, in the spirit of Liskov instead of *changing* `SightingScorer`, I want to
create a subclass that does the timing and *substitute* that class into the system
as the `SightingScorer` in the `dev` environment only.

It's going to be kinda fun! And it's a pattern you'll find inside Symfony itself,
like with the `TraceableEventDispatcher`: a class that is substituted in for the
*real* event dispatcher only while developing, which adds debugging info.

## Creating the Subclass

Let's start by creating the subclass that will do the timing. Over in the `Service/`
directory... so that it's right next to our normal `SightingScorer`, add a new
class called `DebuggableSightingScorer`. Make it extend the normal `SightingScorer`.

Since our subtype is currently making *no* changes to the parent class, Liskov would
definitely be happy with it. What I mean is: it's not changing *any* behavior and
so we should *definitely* be able to *substitute* this class into our app in place
of the original with no problems.

## Substituting the Real Class

But where *is* the normal `SightingScorer` service actually used? Open
`src/Controller/BigFootSightingController.php`. This `upload()` action is the one
that is executed when, from the homepage, we click to submit a sighting. Yep, down
here, you can see that this is the `upload()` method.

One of the arguments that's being autowired to this method is the `SightingScorer`...
which is used down here on submit to calculate the score.

*Now* I want to change this service to use our new class: i want to substitute it.
How? Open `config/services.yaml`. I mentioned earlier that we were going to swap
in our `DebuggableSightingScorer` *only* in the `dev` environment... but to keep
things simple, I'm *actually* going to do it in *all* environments. But if you made
this same change to a `services_dev.yaml` file, then it would only affect the
`dev` environment.

*Anyways*: to suddenly start using our new class everywhere that the
`SightingScorer` is used, add `class: ` and then `App\Service\DebuggableSightingScorer`.

I know, this looks a little funny. This first line is still the service id. But
now instead of using that as the class, Symfony will use `DebuggableSightingScorer`.
The end result is that whenever someone autowires `SightingScorer` - like we do
in our controller - Symfony will now instantiate an instance of our
`DebuggableSightingScorer`... and pass the normal `$scoringFactors` argument. Yep,
we just substituted our subclass into the system!

To prove it, find your terminal and run:

```terminal
php bin/console debug:container Sighting
```

I want to look at the `SightingScorer` service, so I'll hit 5. And perfect! The
service id is `App\Service\SightingScorer`, but the class is
`DebuggableSightingScorer`.

Another way to show this would be to go into our `BigFootSightingController`
and temporarily `dd($sightingScorer)`.

Back at your browser, refresh and... there it is! `DebuggableSightingScorer`

Let's go take that out... then refresh again. The page works and... even though
I won't test it if we submitted, our `DebuggableSightingScorer` *would* correctly
calculate the believability score.

In other words, no surprise: if you create a subclass and change *nothing* in it,
you *can* substitute that class for its parent class.

## Method Changes that are NOT Allowed

Let's start adding our timing mechanism. In the class, go to Code -> Generate -
or Command + N on a Mac - select "Override methods" and override the `score()` method.
If you override a method and keep this same argument type hints and return type,
this class is *still* substitutable: i can refresh and PHP is still happy.

But if we *did* to change the argument type-hints or return type to something
totally *different*, then even PHP will tell us to cut it out. For example, let's
completely change the return type to `int`. You can already see that PhpStorm is
mad! And if we refresh, we get a huge error coming from PHP itself:

> `DebuggableSightingScorer::score()` must be compatible with `score()`, which
> returns `BigfootSightingScore`.

Our signature is incompatible and, nicely, PHP does not let us violate Liskov's
principle in this way. Go and undo that change.

So does this mean that we can *never* change the return type or argument type-hints
in a subclass? Actually... no! Remember our rules from earlier: you *can* change a
return type if you make it more *narrow*, meaning more *specific*. And you can
*also* change an argument type-hint as long as you make it accept a *wider*, or
*less* specific type.

Let's see this in action by finishing our timing feature next.
