# Interface Segregation Principle

Ready for principle number 4? It's the interface segregation principle - or ISP.
It says:

> Clients should not be forced to depend on interfaces that they do not use.

That's not a bad definition! But I want to clarify that word "interface". It is
*not* necessarily referring to a *literal* interface. It's referring to the abstract
concept of an interface, which generally means "the public methods" of a class...
even if it doesn't technically implement an interface. The meaning of interface
*here* is: the "stuff that you can do with an object" when I give one to you.

## The Simpler Definition

So let me try to give this an even simpler definition:

> Build small, focused classes instead of big, giant classes.

This definition reminds me a lot of the single responsibility principle... and that's
true! But the interface segregation principle kind of looks at this from the other
direction: from the perspective of who *uses* the class, not from the perspective
of the class itself. Again, the original definition is:

> *Clients* should not be forced to depend upon interfaces that they do not use.

For example, suppose you've accidentally built a giant class called `ProductManager`
with a *ton* of methods on it. Then, somewhere in your code, you need to call just
*one* of those methods. This other class is called the "client" because it is *using*
our giant `ProductManager` class. And unfortunately, even though it only needs one
method from the `ProductManager`, it needs to inject the whole giant object. It's
forced to depend on an object whose interface - whose public methods - are many
more than it actually needs.

## New Feature: Adjusting a Score

Why is this a problem? Let's answer that question a bit later after we play with
a real world example. Because... management has asked us to make yet *another* change
to our believability score system! If a sighting receives a score of *less*
than 50 points... but it has three or more photos, we will give it a boost: 5 extra
points per photo. This... was not a change that we anticipated! Darn! Our scoring
factors *do* have the ability to add to the score... but they don't have the ability
to *see* the final score and then modify it.

## Adding another Method to the Interface

No problem: let's add a second method to the interface that has the ability to do
that. Call it, how about, public function `adjustScore()`. And in this case,
it's going to receive the  `int $finalScore` that's just been calculated and
the the `BigFootSighting` that we're scoring. It will return the new `int` final
score. And you can add some PHPDoc above this to better explain the purpose of this
method.

In a minute, we're going to call this method from inside of `SightingScorer` after
the initial scoring is done. But first, let's open `PhotoFactor` and add the new
bonus logic.

## Implementing the new Method

At the bottom, go to Code -> Generate - or Command + N on a Mac - select
"Implement Methods" and implement `adjustScore()`. Then say
`$photosCount = $sighting->getImages()` - don't forget to *count* these - then if
the `$finalScore` is less than 50 and `$photosCount` is greater than two - then
the `$finalScore` should get plus equals `$photosCount * 5`. At the bottom, return
`$finalScore`.

New logic done! But now what, do we do with all the other classes that implement
`ScoringFactorInterface`? Unfortunately, for PHP to even run, we do need to add this
new method to each class. But we can just make it return `$finalScore`.

So at the bottom of `CoordinatesFactor`, go back to Code -> Generate - select
"implement methods", generate `adjustScore()`, and return `$finalScore`. Copy, this
close `CoordinatesFactor`, go to `DescriptionFactor` and add it to the bottom.
Do the same thing inside of `TitleFactor`.

*Finally*, we can update `SightingScorer`. Add a second loop after calculating the
score: for each `$this->scoringFactors` as `$scoringFactor`, this time say
say `$score = $scoringFactor->adjustScore()` and pass in `$score` and `$sighting`.

Done! By the way, you might argue that the execution order of these scoring
factors is now relevant. That's true! But we're not going to worry about that for
simplicity - that isn't relevant to this principle. But, there *is* a way to give
a tagged service a higher priority in Symfony so that it is passed earlier or later
than the other scoring factors.

## We Violated OCP!

If, at this point, something is itching you, that might be because we just violated
the open-closed principle! We had to modify the `score()` method in order to add
this new behavior. But that's okay! It highlights the tricky nature of OCP: we didn't
anticipate this kind of change! You can't "close" a class against *all* kinds of
changes: you can only close it against the changes that you can correctly predict.

Looking at our new interface and the classes that implement it, you can probably
feel that it's not ideal that all of these classes need to implement this method...
even though they don't really *care* about it. Next: we're going to make this even
*more* obvious, refactor to a better solution, and finally discuss the key takeaways
from the interface segregation principle.
