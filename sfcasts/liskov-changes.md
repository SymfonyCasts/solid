# Liskov: What Changes *Are* Allowed?

Calculating how long it takes for the parent `score()` method to execute will be
easy. But then... what do we *do* with that number? This method returns a
`BigFootSightingScore` instance.... so we can't suddenly change this to return
an `int` for the duration. How can this method return both the `BigFootSightingScore`
*and* info about how long it took for the score to calculate?

## Creating a Subclass for the Return Value

The answer is: create another subclass! A subclass of `BigFootSightingScore` that
holds the extra info. `BigFootSightingScore` lives in the `src/Model/` directory:
there it is. Right next to it, add a new class called, how about,
`DebuggableBigFootSightingScore`. Make it extend the normal `BigFootSightingScore`.

[[[ code('d37a1c6545') ]]]

Now we have two subclasses to play with! This time, override the constructor: do
that by going to Code -> Generate - or Command + N on a Mac. Override `__construct()`.

This calls the parent constructor with the score, which is great! Add a new
argument: `float $calculationTime`. I'll hit Alt + Enter and go to "Initialize
properties"... select just `$calculationTime`... to create that property and set
it. To make the `$calculationTime` accessible, at the bottom, go back to Code ->
Generate and make a "getter" method for this!

[[[ code('b7a45b5867') ]]]

## Wait: Does __construct need to Follow Liskov's Rules?

By the way, adding a required argument to a method that you are overriding - like
we're doing in `__construct` - is normally *another* way to violate Liskov's
principle. Let's think about it using a different example: `SightingScorer`. When
we use this, we can normally call `score()` and pass it a single argument. If
we suddenly substituted in a *different* class whose `score()` method required
*two* arguments... well, that would make our code explode. That new class would
*not* be substitutable for the old one.

*However*, the constructor does *not* need to follow Liskov's principle... which
took me a minute to wrap my head around. Why not? Because if you are instantiating
a `DebuggableBigFootSightingScore` - with `new DebuggableBigFootSightingScore` -
then you know *exactly* which class you are instantiating. And so, you can figure
out *exactly* which arguments you need to pass.

This is different than being *passed* a `BigFootSightingScore` object... where the
*true* class might be a *subclass*. In that situation, you need any of the methods
that you *call* on that object to behave like the *original* class's methods. Since
the constructor is never called on an object, that's not an issue.

*Anyways*, back in `DebuggableSightingScorer`, let's return our new
`DebuggableBigFootSightingScore` class with a dummy duration. Say `$bfScore =
parent::score()`... and then return a `new DebuggableBigFootSightingScore` passing
the `int` score - `$bfScore->getScore()` - and `100` for a fake duration. Let's
also advertise that we return this new class: `DebuggableBigFootSightingScore`

[[[ code('9a95ce22e8') ]]]

But wait: we just changed the return-type to something *different* than our parent
class! Is that allowed?

## Narrower Return Types are Allowed

Find your browser, refresh and... PHP totally *does* allow this! That's because
this *does* follow Liskov's principle: we are making the return type more
*narrow*... or more specific.

But why is making a return type more *narrow* allowed? Look at
`BigFootSightingController`: the class that uses the `SightingScorer`. This code
requires a `SightingScorer` instance. And so, when we call the `score()` method
later, we know that it will return a `BigFootSightingScore` object. We know that
because, if we jump into the `SightingScorer` class, yep! The `score()` method
returns a `BigFootSightingScore`.

And so, we know the `$bfsScore` variable is an instance of `BigFootSightingScore`...
and we know that *that* class has a `getScore()` method on it. I'll, once again,
jump into the class. This is the original `BigFootSightingScore` and here is its
`getScore()` method. We use that in our controller to get the integer score and...
everything is happy!

But *now* we know that we have *substituted* the `SightingScorer` for a
`DebuggableSightingScorer`... and we know that *its* `score()` method returns
a `DebuggableBigFootSightingScore`. But that's okay! Why? Because
`DebuggableBigFootSightingScore` extends `BigFootSightingScore`. So we are
*still* returning a `BigFootSightingScore` instance, which, of course, *still* has
a `getScore()` method. The fact that we return a subclass... that potentially
has extra methods on it, does *not* break its substitutability.

But if we had changed its return type to something *less* specific, like *any*
object, then there would be no guarantee that what we return from this method
has a `getScore()` method. And so, that *would* break Liskov's principle.
PHP would be *so* mad at us, that it would generate a syntax error. Let's undo that.

We won't talk about it in detail, but the same philosophy can be applied to
argument types, but in the opposite direction. It's okay to change an argument
type as long as you support at *least* the original type. It's not okay to be
*more* restrictive with the type you allow, but it *is* okay to be *less* specific:
I *am* allowed to say that the `score()` method supports *any* object. Well, in
*this* example, that would be problematic because we're passing the argument to
the parent class... which still *does* require a `BigFootSighting`... but in
general, allowing for a *less* specific, or *wider* argument type *is* allowed
by Liskov. And you can see this if we refresh: no syntax error from PHP.

Let's change that back.

Next: it's time to celebrate our new system by *using* the new duration value,
tweaking a few things in Symfony's config and listing the takeaways from
Liskov's principle.
