# LSP: Changes

Coming soon...

Calculating how long it takes for this parent `score()` method to execute will be easy.
But then what do we do with that number? This method returns a `BigFootSightingScore`
instance. So we can't suddenly change this to return in it for the duration.
How can this method return the `BigFootSightingScore` and information about how long
it took the score to calculate the answer is, do you create another subclass, a
subclass of `BigFootSightingScore` that holds this extra information? This class lives in
these source model directory. There it is. So right next to it, let's create a new
class called the `DebuggableBigFootSightingScore` and make it extend the normal
`BigFootSightingScore`

Now we have two subclasses to play with override the constructor here. Can we do that
with Code -> Generate or Command + N override `__construct()`, call the parent constructor with
the score and now at a new argument, which will be the `float calculationTime`. And
I'll hit Alt + Enter and go to "Initialize properties", select just `$calculationTime` to
create that property and set it to make the `$calculationTime` accessible down here. I'll go
back to Code -> Generate or Command + N end and go to getters. And we will generate a getter
for get `$calculationTime`, by the way, adding a required argument to a method that you
are overriding like we're doing construct is normally another way to violate Liskov's
principle. Let's think about it using a different example, `SightingScorer`. If I can
normally call score and pass it a single arguments. And suddenly you substitute that
with a different class whose score method requires two arguments. That's going to
make my code explode. The new classes, not substitutable for the old one.

However, the constructor does not need to follow Liskov's principle, which took me a
minute to wrap my head around originally. Why not? Because if you are instantiating a
`DebuggableSightingScorer` with new `DebuggableBigFootSightingScore`
score, then you know exactly which glass you are instantiating. And so you configure
out exactly which arguments need to pass. This is different than being past a
`BigFootSightingScore` object, where the true class might be a subclass. And so you need
any of the methods on that subclass do P to behave like the original classes methods.
Anyways, back in anyways, in `DebuggableSightingScorer` Let's return our new
`DebuggableBigFootSightingScore` class with a dummy duration. So we can say
something like dollar sign `$bfScore = parent::score()`, and then return new
`DebuggableBigFootSightingScore`. And then since this has the score into your own can as
`$bfScore->getScore()`, kind of get that int and then I'm going to pass it `100` as
our fake duration time. And let's advertise that we return this. So we now return
eight `DebuggableBigFootSightingScore` is that legal go over,

Go over and refresh the page to find out it is PHP. Totally allows it. And that's
because this does follow Liskov's principle. We are making the return type more
narrow or more specific. Why is more narrow? Okay. Look at 
`BigFootSightingController` The class that uses the `SightingScorer`

This code requires a `SightingScorer` instance. And so when we call the `score()` method
later, we know that this is going to return a set, a `BigFootSightingScore` class. We
know this because that's what these settings score class tells us. So if I hold
command, uh, or control to open this, this is the original `SightingScorer`. It score
method says we get a `BigFootSightingScore`, uh, instance back. So in this case, in
the controller, we know that this is a `BigFootSightingScore` Class.

And we know that that class has a good`getScore()` method on it. I'll once again, jump into
this class. So this is the original `BigFootSightingScore` it has `getScore()`. 
method on it. So we can use that in our controller to get the integer a
score And everything is fine. But now we know that we've actually substituted these
`SightingScorer` for a `DebuggableSightingScorer` and it's `score()` method returns, 
a `DebuggableBigFootSightingScore`, but that's okay. Why? Because this is an even more
specific return type. We're still returning a `BigFootSightingScore` instance, which
has, which will have it `getScore()` method. The fact that what it returns is actually
a subclass of that with potentially extra methods does not break its substitute
ability.

But

If we had changed its return type to something less specific, like an any object,
then there would be no guarantee that what we returned from this method has a good
score method on it that would break Liskov's principle and PHP would be so mad about
it, that we would get a syntax error. So we will undo that. Now we won't talk about
it in detail, but the same philosophy can be applied to argument types, but in the
opposite direction, it's okay to change an argument type. As long as you support, at
least the original type, it's not okay to be more restrictive with the type with the
type you allow, But it is okay to be less specific. I could suddenly say that the
score math in this class supports any object, of course, in reality, cause I'm
calling the parents score method that would explode if I passed any object, but on I
and I object oriented level, this is allowed and you can see that on a refresh. The
page PHP is fine with

That, but I will change it back.

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

