# ISP: Refactoring & Takeaways

We've just finished adding the ability to add a bonus to the score if the score is
less than 50 *and* there are 3 photos or more on a sighting. And... management is
*already* requesting another change: we need to make sure that - no matter what -
a score *never* receives more than a 100 points.

No problem! We can create another scoring factor class to check for this. In the
`Scoring/` directory, add a class called, how about, `MaxScoreAdjuster`. I'm giving
this a slightly different name, even though it's a scoring factor, because it's
real job is going to be to adjust the score. Make it implement
`ScoringFactorInterface`.

Now go to Code -> Generate - or Command + N on a Mac - and just generate,
`adjustScore()` to start. For the logic, return the minimum of `$finalScore`
or 100. So if the `$finalScore` is over a hundred, this will return 100.

Now, setting the priority of the scoring factors so that this is the final one
would be *especially* important. But since that doesn't relate to ISP, we won't
worry about it.

Of course, in this new class, we *also* need to implement the *other* method:
`score()`. We can just return 0 since we don't care about that.

Okay, we've got this working! But we've violated ISP! A lot of the classes that
implement `ScoringFactorInterface` - like `MaxScoreAdjuster` and `CoordinatesFactor` -
have a dummy method... which we added *just* to satisfy the needs of the interface.

## The Signs that You're Violating ISP

When you see something like this, it's a signal that your interface is polluted...
or has gotten fat. But again, even though we're using an interface in our example,
this also applies to classes in general. If you have a class with multiple public
methods... and other parts of your code only use one or some of its methods...
that's *also* a violation of ISP. In fact, that's the *main* purpose of ISP. You're
requiring clients of your class to depend on interfaces - in other words, methods -
that they do *not* need.

What's the solution? Categorize the methods based on their purpose and how they're
used... and split them into multiple classes.

For example, if you have a class with 3 methods and 2 of those methods are always
called together, then the class should be split into only two pieces: one class with
those 2 methods and another class with only the third method.

## Splitting our Interface

In our example, it's pretty obvious that splitting the interface into two pieces
would make the classes that implement them simpler. So in this `Scoring/` directory,
create a new class - or really an interface - and call it `ScoreAdjusterInterface`.
What we'll do is go into `ScoringFactorInterface`, steal the `adjustScore()` method
and move it into the new interface. Hit okay to import that `use` statement.

Thanks to this, we can now go into `CoordinatesFactor` and remove the dummy
`adjustScore()`... and then do the same thing in `TitleFactor`... and also in
`DescriptionFactor`, which feels pretty good! In `MaxScoreAdjuster`, change this
to implement `ScoreAdjusterInterface`... and then we no longer need the dummy
`score()` method.

## Injecting the Collection of Scoring Adjusters

Finally, the `PhotoFactor` class is interesting: it needs to implement both
interfaces, which is totally allowed. Add `ScoreAdjusterInterface`.

The last thing to do is make our `SightingScorer` support using *both* interfaces
by repeating the trick of injecting a collection of services for
`ScoreAdjusterInterface`. In other words, we're now going to inject an `iterable`
of scoring factors and a *second* `iterable` of scoring adjusters.

Start in: `src/Kernel.php`. Copy the `registerForAutoConfiguration()`... and we're
going to repeat the same thing, but this time for `ScoreAdjusterInterface` and
call the tag `scoring.adjuster`.

Next, over in `services.yaml`, down on our service, copy the `$scoringFactors`
argument, paste, rename to `$scoringAdjusters` and use the new tag name:
`scoring.adjuster`.

Copy that argument name and head into `SightingScorer`. Add this as a second
`iterable` argument. Then hit Alt + Enter and go to Initialize Properties
to create that property and set it. I'll steal the PHPDoc from above the old
property to help my editor know that this will hold an iterable of
`ScoreAdjusterInterface` objects.

Now loop over *these* instead. You can already see that PhpStorm is not happy
because there is no `adjustScore()` method on the scoring factors. Change this
to `$scoringAdjusters`... and I'll rename the variable to `$scoringAdjuster` here
and here.

Done! We made our interface smaller, which allowed us to remove all of the dummy
methods.

## Why Should We Care about ISP?

So, other than being forced to create dummy methods just to make an interface happy,
why should we care about ISP? I can think of three reasons.

The first is *naming*. Whether you have a class that's too big or an interface like
in our example, splitting it into smaller pieces allows you to give each a more
descriptive name that fits its purposes. We can see this in `SightingScorer`.
We're now working with scoring *adjusters*, which better describes the purpose of
those services than just a "scoring factor"... which does multiple things.

The second is that ISP is a good signal that you might be violating the single
responsibility principle. If you notice that you often only call one or two methods
from a class... but not it's *other* public methods, that is a violation of ISP.
This forces you to think about the *responsibilities* of that class, which may
result in organizing into smaller classes *based* on those responsibilities.

The third reason we should care about ISP is that it keeps your dependencies
*lighter*. We didn't see that in *this* specific example, but we *did* see it
earlier when we talked about SRP. In that case... let me actually close all of my
classes... we split a `UserManager` class into two pieces: `UserManager` and
`ConfirmationEmailSender`. The `send()` method simply sends the confirmation email,
and we use it both after registration *and* when requesting a re-send of that
email.

If we had kept these two public function inside of `UserManager` - then resending
the confirmation would have be a violation of the interface segregation principle.
That would have been a situation where we only needed to call *one* of the two
public methods on the class.

And, in order to resend the email, Symfony would need to instantiate a class
which depends on, for example, the password encoder service. Why is that a problem?
Well, it's minor, but this would force Symfony to instantiate the password encoder
so that it could instantiate the `UserManager`... so that we could send a confirmation
email... but we would never actually *use* the password encoder. That's a waste
of resources!

*Anyways*, the tl;dr on the interface segregation principle is this: when you have
an interface with a method that not all of its classes need... *or* if you have
a class where you routinely use only *some* of its public methods... it may be time
to split it into smaller pieces. Or, more simply, you can remember to not build
giant classes. But, like everything, it's not an absolute rule. If I had, for example,
a `GitHubApiClient` that helped me talked to GitHub's API... I might be ok putting
5 methods in this service, even though I routinely only use one or two of them
at a time. After all, the name of the class is still pretty clear... and having
more methods probably doesn't increase the number of dependencies that I need
to inject into that service.

Next: we're on to principle number five! And this one *really* made my head spin
at first. It's: the dependency inversion principle!
