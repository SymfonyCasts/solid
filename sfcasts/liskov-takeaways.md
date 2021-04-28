# Liskov Takeaways & Service Alias

To celebrate our new system, let's see it in action. In `BigFootSightingController`,
after the `addFlash()`, let's also add some duration information. Of course, I
don't know for sure if I'm using the "debuggable" version of my service, so add
if the `$bfsScore` is an instance of `DebuggableBigFootSightingScore`, then
`$this->addFlash('success', sprintf(...))` with:

> Btw, the scoring took %f milliseconds

And pass `$bfsScore->getCalculationTime()` times 1000 to convert from microseconds
to milliseconds.

Cool! But... wait: didn't I say that `instanceof` is a signal that we may be
breaking Liskov's principle? Yep! But I'm not too worried about it here, for a
few reasons. First, this is my controller... whose job is to tie all the ugly
pieces of my app together. Also, I'm using the `instanceof` to detect if I can
*add* functionality... not to work-around a misbehaving sub-class.

However, another solution, depending if you really *do* need to substitute this
class only in one environment, is to explicitly say that you require the debuggable
version of the service. So instead of saying, "I allow any `SightingScorer`", we
could say, "I specifically need a `DebuggableSightingScorer`".

If we did that, we wouldn't need the `instanceof` because we would know that *that*
service returns a `DebuggableBigFootSightingScore`, which has the
`getCalculationTime()`  method on it.

But... we're missing one tiny config detail in Symfony. Try to refresh the page.
Ah! It breaks!

> Cannot autowire service `DebuggableSightingScore`: argument $scoringFactors is
> type-hinted `iterable`. You should configure its value explicitly.

Wait... we hit this error when working on the open-closed principle. And, in
`config/services.yaml`, we fixed it by specifically wiring the `$scoringFactors`
argument. Why isn't that working anymore?

Thanks to auto-registration - the feature that automatically registers all classes
in `src/` as a service - there is a *separate* service in our container called
`DebuggableSightingScorer`. You can see that if you run:

```terminal
php bin/console debug:container Sighting
```

Yup! There's a `DebuggableSightingScorer` and a separate service called
`SightingScorer`. That is... *not* what we wanted. Instead, I want Symfony to
pass us the *same* service, regardless of whether we type-hint
`DebuggableSightingScorer` or `SightingScorer`.

We can do that by adding an alias. Inside `services.yaml`, say
`App\Service\DebuggableSightingScorer`, colon, an `@` symbol and then
`App\Service\SightingScorer`.

This says: whenever someone tries to autowire or use the `DebuggableSightingScorer`
service, you should *actually* pass them the `SightingScorer` service... which,
I know, is actually an *instance* of `DebuggableSightingScorer`. It *can* be a
bit confusing.

Back at your terminal, run `debug:container` again:

```terminal-silent
php bin/console debug:container Sighting
```

It *looks* like there are still 2 services, but if you hit "6" to look at the
"Debuggable" one, on top, it says:

> This is an alias for the service `App\Service\SightingScore`.

And over in the browser, when we refresh... it works again!

## Liskov Principle Takeaways

So the big takeaway from Liskov's principle is this: make sure that when you have
a "subtype" - a class that extends another or that implements an interface - it
follows the rules of that parent type. It doesn't do anything surprising. That's
it. And PHP will even prevent you from *most* Liskov violations.

The most interesting part of Liskov for *me* is learning about the things that we
*are* allowed to do. Like, you *are* allowed to change the return type of a method
as long as you make it more specific. Or, the opposite for argument types: you can
change them... as long as you make it *less* specific.

Okay, next up is solid principle number 4: the interface segregation principle.
