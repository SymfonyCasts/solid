# ISP: Takeaways

Coming soon...

We've just finished adding the ability to add a bonus to the score. If the score is
less than 50, and there are three photos or more on a setting and management is
already requesting another change, we need to make sure that no matter what a score
never receives more than a hundred points, no problem. We can create another scoring
factor class to check for this in the `Scoring/` directory, let's add a class called how
about `MaxScoreAdjuster` I'm giving this one a slightly different name, even though
it's a scoring factor because it's real job is going to be to adjust the score. We
will make this implement `ScoringFactorInterface`.

Now I've got a Code -> Generate or Command + N on a Mac and generate, just generate,
adjust the score to start logic. And here is that we're going to return the minimum
of `$finalScore` or 100. So the `$finalScore` is over a hundred, then it would return
just a hundred setting the priority for the of this class. So that is the last
scoring factor injected into citing score would now truly be important. But since
that doesn't relate to ISP, we're not going to worry about the detail. Of course, in
this new class, we're also going to need to implement the other method score. And we
can just return zero since we don't care about that. Okay, we've got this working no
more under or over scored records, but we've violated. ISP a lot of the classes
that implement scoring factor interface, like `MaxScoreAdjuster` and `CoordinatesFactor`
have a dummy method, which we added just to satisfy the needs of the interface.

When you see something like this, it's a signal that your interface is polluted or
has gotten fat. But again, even though we're using an interface in our example, this
also applies to classes in general. If you have a class with multiple public methods
and other parts of your code use only one or some of its methods, that's also a
violation of ISP. In fact, that's the main purpose of ISP. You're requiring clients
of your class to depend on interfaces. In other words, methods that they don't need.
What's the solution, categorize the methods and split them based on their purpose and
how they're used. For example, if you have a class with three methods and two of
those methods are always called together, then the class should be split into only
two pieces, one class with those two methods and another class would be third method.
And our example, it's pretty obvious that splitting the interface into two pieces
would make the classes that implement them simpler. So in this `Scoring/` directory,
let's create a new class or really an interface and call it `ScoreAdjusterInterface`. 
And what we'll do is we'll go into our scoring factor interface of what
was steal that `adjustScore()` method

And move

It over into our new interface and how we'll hit. Okay. To import that you statement,
Thanks to this. We can now go into `CoordinatesFactor` and remove the dummy 
`adjustScore()`, and then do the same thing in `TitleFactor`, And also in `DescriptionFactor`,
which feels pretty good. And `MaxScoreAdjuster`, we now are going to change this to
`ScoreAdjusterInterface` And Then we no longer need the dummy `score()` method.

Finally, `PhotoFactor` class is actually interesting. It implements both of them, which is
totally allowed. So we just need to make it also implement the `ScoreAdjusterInterface`
interface. The last thing to do is make our

Estimate

Our `SightingScorer`, Both interfaces. We'll repeat the trick of injecting a collection
of services for score adjuster interface. So in other words, we're not going to
inject in Iterable, these scoring factors, any second interval of just the scoring
adjusters start in our kernel `src/Kenel.php` class, copy the registered for all
configuration. And we're going to repeat the same thing, but this time force or
`ScoreAdjusterInterface`, And we'll call on the tag about `scoring.adjuster`. Now over
in services dynamo down on our service confidence `$scoringFactors` argument, and let's
say, we'll have a second argument called `$scoringAdjusters`. And then we will pass in
the new tag `scoring.adjuster`, cognitive of that arguments and head into our
siting score. And we'll add this now as a second Iterable argument. So interval
scoring adjusters I'll then hit, enter and go to initialize properties to create that
property and set it I'll steal the PHP dock from above this, just to help my editor
know that this is a `ScoreAdjusterInterface` to Iterable of `ScoreAdjusterInterface`
objects. Now we can loop over these instead, so you can already see that Peter storm
is not happy because there is no adjust score method on the scoring factors. So let's
change this to `$scoringAdjusters`, and I'll also rename my whoops

Also rename this to `$scoringAdjuster` here and here

And done.

We made our interface smaller, which allowed us to remove all of the dummy methods.

So other than having

Dummy methods, just to make an interface, happy is kind of silly. Why should we care
about ISP? I can think of three reasons. The first is naming. Whether you have a
class that's too big or an interface Like an hour example,

Splitting it into smaller pieces, allows you to give each a more descriptive name
that fits its purposes. We can see this `SightingScorer`. We're now working with
scoring adjusters, which better describes the purpose of those services than just
scoring factor

To no.

When you notice that you often only call one or two methods from a class, but not,
not it's other public methods, it forces you to think about the responsibilities of
that class, which will often result in organizing into smaller classes based on those
responsibilities.

In other words,

Noticing that you're only using some of EI services, public methods is a good way to
realize that you might have SRP violations and three, five, it keeps your
dependencies lighter. We didn't see that in this specific example, but we did see it
earlier. When we talked about SRP, in that case, let me actually close all of my
classes. We split a `UserManager` class into two pieces, `UserManager` and 
`ConfirmationEmailSender`. The send method simply sends the confirmation email.

If

We had not done that in all of this code live in `UserManager`. Then in order to
resend the confirmation email, we would have needed to instantiate a class, which
depends on, for example, the password encoder service. Why is that a problem? Well,
it's minor, but this would force Symfony to in-stage you the password encoder so that
it could end stage of the `UserManager` so that we could send a confirmation email.

Yeah.

Being that we would never, we spent all those resources in staging this service
without ever using it. That's a waste of resources. Anyways, the TLDR on the
interface, segregation principle is this when you have an interface with a method
that not all of his classes need, or if you have just a class where you routinely use
only some of its methods, it may be time to split it into smaller pieces. Next we're
on to principle. Number five in this one really made my head spin at first. It's the
dependency in version principle.

