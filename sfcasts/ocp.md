# Open–Closed Principle

The second SOLID principle is the Open-Closed Principal. Or OCP. Ready for the super
understandable technical definition? Here we go.

## Technical and (Less) Technical Definition

> A module should be open for extension, but closed for modification.

As usual - and hopefully you're a bit quicker than I am - this definition makes
no sense to me.... at least at first. Let's try our own definition. OCP says:

> You should be able to change what a class does without actually changing its code.

If that sounds crazy... or downright impossible, it's actually not! And we'll
learn one common pattern that makes this possible.

But full disclosure, OCP is *not* my favorite SOLID principle. And later, we'll
talk about when it should be used and when... maybe it shouldn't. But more on that
once we've got a good understanding of what OCP really is.

## Updating our Believability Scoring Algorithm

Now, the whole point of Sasquatch Sightings is for people to be able to submit their
*own* sightings. To help sort through all of these, we've developed a proprietary
algorithm to give each sighting a "believability score". Ooh. How is that implemented?

Open `src/Service/SightingScorer.php`. After you submit a sighting, we call
`score()`... and all the logic lives right in this class. We look at the latitude
and longitude, title, and description for certain keywords. We call each of these
"scoring factors".

*Now*, we've received a change request. We need to add a new scoring factor where
we look at the *photos* included with the post. The easiest way to implement this
would be to go down here, create a new private method called `evaluatePhotos()`...
and then call that from up here in the `score()` method.

But doing that would violate OCP because we would be changing our existing code
in order to add the new feature. OCP tells us that a class's behavior should be
able to be modified *without* changing its code. How is that even possible?

The truth is that our class *already* violated OCP before we got this change request.
To be able to add the new feature without changing our existing code, we needed
to write our class differently from its very beginning. Since it's a little late
for that, let's walk through the OCP mindset and refactor this class so that it
*does* follow the rules.

## "Closing" a Class to a Change

First, we need to identify which kind of change we want to "close" this class against.
In other words, what kind of change do we want to allow a future developer to be
able to make without modifying this class. Based on the change request, we need to
be able to add more scoring factors without modifying the `score()` method itself.
Since there's no way to do that right now, we're going to change this method in order
to "close" it to this change. How? By separating each scoring factor into its own
class and injecting them into the `SightingScorer` service.

Step one is to create an interface that describes what each scoring factor should
do. In `src/`, for organization, create a new directory called `Scoring/`. And
inside of that, choose "new PHP class"... then change this to be an interface...
called `ScoringFactorInterface`.

Each factor *should* need only one method. Let's call it `score()`. It will accept
the `BigFootSighting` object that it's going to score.... and will return an integer,
which will be the amount to *add* to the total score.

[[[ code('355c366b63') ]]]

Perfect! You could also add some documentation above this to describe the method
of interface better: probably a good idea.

Step two is to create a new class for each scoring factor and make it implement
the new interface. For example, copy, `evaluateCoordinates()`, delete it and then
go into the `Scoring` directory and create a new class called `CoordinatesFactor`.
We'll make it implement `ScoringFactorInterface`... I'll paste the method - hit
okay to add the `use` statements - rename this to `score()` and make it
`public`. It already, correctly, returns an integer, so this is done!

[[[ code('031e57dc95') ]]]

Let's repeat this for `evaluateTitle()`. Create a class called `TitleFactor`,
implement the `ScoringFactorInterface`, paste, make it `public` and rename it to
`score()`.

[[[ code('0f23a32941') ]]]

And one more: copy, `evaluateDescription()`, delete that, create our last factor
class for now, which will be `DescriptionFactor`, implement `ScoringFactorInterface`
paste in the logic, clean things up... and rename to `score()`.

[[[ code('13171987fb') ]]]

That looks happy! Now we can work our magic in `SightingScorer`. Add a
`__construct()` method that will accept an `array` of scoring factors. I'll hit
Alt + Enter and go to "Initialize properties" to create that property and set it.
Above the property, I like to add extra PHPDoc so my editor knows this isn't just
an array of *anything*, it's an array of `ScoringFactorInterface[]` objects.

[[[ code('ed73d709ce') ]]]

Down in `score()`, instead of calling each method individually, we can now loop
over `$this->scoringFactors` and say `$score += $scoringFactor->score($sighting)`.

[[[ code('905ee06c75') ]]]

That's it! Our SightingScorer is now *closed* to one type of change that we may
need to make in the future: adding scoring factors. In other words, we can now
add *new* scoring factors, *without* modifying this method.

## Wiring the $scoringFactors Argument

Yaaay! But... on a technical level, this won't work yet. At your browser, click
to submit a new sighting. Instant error! Of course. This isn't really related
to OCP, but Symfony doesn't know what to *pass* for the new `$scoringFactors`
argument.

Next, let's look at two ways to fix this: the simple way... and the fancier way,
which involves a tagged iterator. After, we'll look at some takeaways for the
open-closed principle.
