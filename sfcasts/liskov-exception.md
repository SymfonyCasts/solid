# Liskov: Unexpected Exceptions

Let's jump into our first example where we learn how we can violate the Liskov
principle! And... maybe more importantly, why we should care.

## Creating a new Scoring Factor

In the `src/Scoring/` directory, create a new scoring factor class called
`PhotoFactor`... and make this implement the `ScoringFactorInterface`. We'll
finally fulfill the change request we received earlier: to add a scoring factor
that reads the images posted for this sighting.

Thanks to our work with the open-closed principle, we can now add a new scoring factor
to our system without touching `SightingScore`. And to be extra cool, thanks to this
`tagged_iterator` thing in `services.yaml`, this new `PhotoFactor` service will be
instantly passed into `SightingScore`. Yay!

In `PhotoFactor`, I'll go do Code -> Generate - or Command + N on a Mac - and select
"Implement Methods" to generate the `score()` method. Inside, I'll paste some code.

This is pretty simple: we loop over the images... and pretend that we're analyzing
them in some super advanced way. Shh, don't tell our users. Oh, and if there are no
images for this listing, we throw an exception.

Cool! Let's try it. Go back to our homepage, click to add a new post and fill in
some details. I'll leave images empty for simplicity. And... ah! A 500 error! That's
our new exception! We broke our app! And it broke because we violated Liskov's
principle! Our new scoring factor class - or subtype - to use a more technical word,
just did something unexpected: it threw an exception!

## The Ugly Work-Around

One way to fix this, which might seem silly... but there's a reason we're doing
this... is to add some conditional code inside of `SightingScorer`: if `PhotoFactor`
doesn't like sightings with zero images, let's just skip that factor in that
situation!

Inside the `foreach`, if `ScoringFactor` is an `instanceof` `PhotoFactor` and
count of `$sighting->getImages()` equals zero, then `continue`.

In addition to this *not* being the best way to fix this - more on that in a minute -
this also violates the open-closed principle. But... it *does* fix things: if
we resubmit the form... it *did* submit.

## Exceptions are a "Soft" Part of an Interface

But let's back up a minute. Open `ScoringFactorInterface`. Unlike argument types
and return types, there's no way in PHP to *codify* whether or not a method should
throw an exception or which types of exceptions should be used. But this *can*,
at least, be described in the documentation above the method... which we totally
skipped!

Let's fill that in. We don't need the `@return` or `@param` because they're
redundant... unless we wanted to add some more information about their meaning.
I'll add a quick description... and then let's be very clear about the exception
behavior we expect:

> This method should not throw an exception for any normal reason.

In the real-world, if a method *is* allowed to throw an exception for some expected
reason, you would typically see an `@throws` that describes that. And if you
*don't* see that, you can assume that you are *not* allowed to throw an exception
for any normal situation.

## Our Class Behaves Unexpectedly

*Anyways*, now that we've clarified this, it's easy to see that our `PhotoFactor`
breaks Liskov's principle: `PhotoFactor` behaves in a way that the the class that
uses it - `SightingScorer`, sometimes called the "client class" - was not expecting...
which is why we had to hack in this code afterwards.

Another way to think about it, which explains why this is called Liskov's substitution
principle, is that, if any of our code currently relies on a `ScoringFactorInterface`
object - like `DescriptionFactor` - we could *not* "replace" or "substitute" that
object for our `PhootFactor` without breaking things.

If this substitution aspect doesn't make complete sense yet, don't worry. Our next
example will illustrate that even better.

## instanceof Checks Indicate Liskov Violation

So: we violated Liskov's principle by throwing an exception. And then, I lazily
worked around the problem by adding some `instanceof` code to `SightingScore`...
to *literally* work "around" the problem.

When you have an `instanceof` conditional like this, it's often a signal that you're
violating Liskov because it means that you have specific implementation of a class
or interface that is behaving *differently* than the rest... which you then need
to code for.

So let's remove this: take out the if statement and let's even go clean out the extra
`use` statement on top. Now that we've clarified that the `score()` method
should *not* throw an exception in normal situations, the real fix is... kinda
obvious: stop throwing the exception! Replace the exception with `return 0`.

That's it. The class now acts like we expect it to: no surprises.

By the way, all of this does does not mean that it is *illegal* for our `score()`
method to *ever* throw an exception. If the method, for example, needed to query
a database... and the database connection was down... then yeah! You should totally
throw an exception! That is an *unexpected* situation. But for all the, expected,
normal cases, we should follow the rules of our parent class or interface.

Next let's look at one more example of Liskov's principle, where we create a subclass
of an existing class... then secretly substitute it into our system without breaking
anything. Let's make Liskov proud!
