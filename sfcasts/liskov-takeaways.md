# Liskov Takeaways

Coming soon...

Okay. To celebrate our new system, let's see an action in `BigFootSightingController`
after the `addFlash()`, let's also add some, uh, duration information. Um, I'm
actually going to say if the `$bfsScore` is  An instance of `DebuggableBigFootSightingScore`
then I'm going to say `$this->addFlash('success', sprintf(...))` and down here,
I'll say:

> Btw, the scoring took %f milliseconds

`$bfsScore->getCalculationTime()`, which we know we can call this
because I just did the instance of, and I want times a thousand. So that's in
milliseconds. Oh, wait. Didn't I say that instance of is kind of a signal that we're
breaking LISC, right?  Principal. Yep. But

Since this is my controller, which whose job is to kind of tie pieces together. And
since this only adds extra functionality for this one case, I'm okay with it.

However,

Another solution, depending on your needs would be to explicitly say that you require
the debuggable service. So instead of saying, I allow any `SightingScorer`, we
could say, we're always going to use this even in production. So we require a
`DebuggableSightingScorer`
If we did that, we would not need the incidenceof, cause we would know that that
service returns the `DebuggableBigFootSightingScore`, which has that `getCalculationTime()`
method on it.

But

Well, one tiny last little detail. If we refresh now that doesn't work can not
resolve argument. That sighting score cannot auto automize service,
`DebuggableSightingScore` arguments, scoring factors is type hinted, Iterable. You should
configure its value explicitly. We hit this air at the beginning of creating our, uh,
in the OCP section in services, IMO. We are passing specifically the scoring factors
that we want, but for some reason that's not working anymore. This is thanks to auto
registration. Thanks to our registration. There is actually a separate service in our
container called de `DebuggableSightingScorer`. You can see that if you run

```terminal
php bin/console debug:container Sighting
```

there is a `DebuggableSightingScorer` and a separate service
called `SightingScorer`. But when we really want something to do is to pass us the same
one service, regardless of whether we type into `DebuggableSightingScorer` or `SightingScorer`
So we'll add an alias inside services.yaml here. We're going to say

`App\Service\DebuggableSightingScorer`. Actually here, I'm going to actually copy the class name for
our `DebuggableSightingScorer` and then say colon and then an `@` symbol. And here you
can say `App\Service\SightingScore`. In other words, whenever somebody auto tries to
Ottawa or this service, they actually get this service, which uses the debug mobile
setting score class. I know a little bit hard to follow a little fancy. If you go
back to the run `debug:container`. Now

```terminal-silent
php bin/console debug:container Sighting
```

you'll see that it looks like there are still
two services, but if you hit six here to look at the Debuggable one on top, you
can say, this is an alias for the service `App\Service\SightingScore`. They really
pointed the same thing in over in the browser when we refreshed it works. So the big
takeaway from LS from Liskov's principle is this make sure that when you have a
subclass, I have a class that extends another or implements an interface. It follows
the rules of that parent type. It doesn't do anything surprising, that's it. And PHP
will even prevent you from most Liz cough violations. The most interesting part of
list off for me is learning the things that you are allowed to do. The fact that you
are allowed to change the return type in an overwritten method, as long as you make
it more specific or the opposite for the type argument types. Okay. Next up is solid
principle. Number four, the interface segregation principle.
