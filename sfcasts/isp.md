# Interface Segregation Principle

Coming soon...

Ready for principle number four, it's the interface segregation principle. It says
clients should not be forced to depend on interfaces that they do not use. That's not
a bad definition, but I want to clarify that word interface. It's not necessarily
referring to a literal interface. It's referring to the abstract concept of an
interface, which generally means the public methods of a class. Even if it doesn't
technically implement an interface. So let me try to give this an even simpler
definition, build small focused classes instead of big giant classes. This definition
reminds me a lot of the single responsibility principle and that's true, but the
interface segregation principle kind of looks at this from the other direction.
Again, the original definition is clients should not be forced to depend upon
interfaces that they do not use. So for example, suppose you've accidentally built a
giant class called `ApiClient` with a ton of methods on it.

Then somewhere in your code,

You need to call just one of those methods. This other class is called the client
because it, because it is using our giant `ApiClient` class. And unfortunately, even
though it only needs one method from the `ApiClient`, it needs to inject the whole
giant object.

It's forced to depend on

An object whose interface whose public methods are many more than it actually needs.
Why is this a problem? Let's answer that question a bit later after we play with a
real world example, because management has asked us to make a change to our
believability score system, if a big foot setting receives a score of less than 50
points, but it has three or more photos, we will give it a boost, five extra points
per photo. This was not a change that we anticipated. Our scoring factors have the
ability to add to the score, but they don't have the ability to,

I see the final score and then modify it. No problem. Let's add a second method to
the interface that has the ability to do that. Let's call it. How about pelvic
function `adjustScore()`. And in this case, what it's going to receive is the 
`int $finalScore` that's been calculated. And then of course the `BigFootSighting` that we're
working on and it will return the new to final score. And you can add some PHP doc
above this to better explain the purpose of this method. In a minute, we're going to
call this method from inside of our siting score. After the initial scoring is done,
but first let's open `PhotoFactor` and add the new bonus logic.

So at the bottom, I'm going to co go to Code -> Generate or Command + N on a Mac select
"Implement Methods" and implement the adjusted score. And then very simply I'll say
`$photosCount = $sighting->getImages()`. We're not religious getting images, but
actually `count()` those images. Then if the `$finalScore` is less than 50 and `$photosCount`
is greater than two or greater than equal than three, then the `$finalScore` should get
plus equals `$photosCount` times 5. And at bottom we will return the `$finalScore`
Perfect. So there is our new bonus logic, but now what do we do to all the other
classes that implement `ScoringFactorInterface`,
unfortunately for PHP to even run, we do need to add this new method to each class,
but we can just make it return the `$finalScore`. So at the bottom of `CoordinatesFactor`
I'll go back to Code Generate or Command + N go to emblem methods, generate
`adjustScore()`, and we're just going to return `$finalScore`. And now I can copy this
close coordinates factor with this at the bottom of `DescriptionFactor.` And then also
at the bottom of `TitleFactor`.

Finally, we can update `SightingScorer` at a second loop after calculating the score.
So for each `$this->scoringFactors` as `$scoringFactor` this time, we're gonna say `$score`
equals `$scoringFactor->adjustScore()`
and we'll pass in the `$score`. And then we'll pass in the `BigFootSighting` 
and done. By the way, you might argue correctly that the execution order of
these scoring factors is now relevant, but we're not going to worry about that for
simplicity though, there is a way to give a tag service, a higher priority in Symfony
so that it is passed in earlier or later than the other scoring factors.

So if at this point something is itching you, that might be because we just violated
the open closed principle. We had to modify the score method in order to add this new
behavior, but that's okay. It highlights the tricky nature of OCP. We didn't
enticingly this kind of change. You can't close a class against all kinds of changes.
You can only close it against the changes that you CA correctly predicted.

Okay.

Looking at our new interface and the ma and the classes that implement it, you can
probably feel that it's not ideal for all of these classes need to need to implement
this method, even though they don't really care about it. Next we're going to make
this even more obvious re factor to a better solution, and finally discuss the key
takeaways from the interface segregation principle.

Okay.

