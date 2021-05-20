# Dependency Inversion Principle

We've made it to the fifth and final SOLID principle: the dependency inversion
principle, or DIP. This puppy has a *two* part definition. Ready? One:

> High level modules should not depend on low level modules, both should depend
> on abstractions - for example, interfaces.

And part two says:

> Abstractions should not depend on details. Details - meaning concrete
> implementations - should depend on abstractions.

Uhh... if that makes sense to you, you are *awesome*! And... I am jealous of you!

## Simpler Definition

How would *I* rephrase this? Um, yikes. How about this. One:

> Classes should depend on interfaces instead of concrete classes.

And two:

> Those interfaces should be designed by the class that *uses* them, not by the
> classes that will *implement* them.

That's probably still fuzzy... but don't sweat it. This requires a real example.

## Our Spam Detection System!

Here's our new problem. We've been getting *so* popular - no surprise - that
some of our sightings are getting a lot of spam comments... like comments that say
that Bigfoot is *not* real. Those are definitely bots!

So we need a way to determine whether or not a comment is spam based on some business
logic that we've created. If you downloaded the course code from this page, then
you should have a `tutorial/` directory with a `CommentSpamManager` class inside.
Copy that, then go create a new directory in `src/` called `Comment/`... and paste
the class there.

[[[ code('32e0e54a82') ]]]

This class basically determines if a comment should be flagged as spam by running
a regular expression on the content using a list of predefined spam words. If the
content contains two or more of those words, then we consider the comment as spam
and throw an exception.

If you think about the single responsibility principle, you could argue that this
class *already* has two responsibilities: the low-level regular expression logic
that looks for the spam words and a higher level business logic that decides that
two spam words is the limit.

## Splitting the Class

Let's pretend that we *do* think that these are two different responsibilities. And
so, we decide to split this class into two pieces. In the `Service/` directory, create
a new class called `RegexSpamWordHelper`. Let's see: move the private `spamWords()`
method to the new class... and then create a new public function called
`getMatchedSpamWords()` where we pass it the `string $content` and return an array
of the matched spam words.

[[[ code('1ef8ffef26') ]]]

Next, move the regex logic itself into the class. Copy the entire contents
of the existing method.... but leave it... then paste. Let's see... we don't need
`$comment->getContent()` anymore.... it's just called `$content`... and the 0 index
of `$badWordsOnComment` will contain the matches, so we can return that.

[[[ code('3dfa42c2fd') ]]]

Beautiful! Now that this class is ready, let's inject it into
`CommentSpamManager`. Add public function `__construct()` with `RegexSpamWordHelper`
`$spamWordHelper`. I'll press Alt + Enter and select "Initialize properties"
to create that property and set it. 

[[[ code('dc742bb186') ]]]

Below, now we can say `$badWordsOnComment = $this->spamWordHelper->getMatchedSpamWords()` and 
pass that `$content` from above. We don't need any of the logic in the middle anymore. Finally,
`$badWordsOnComment` will contain the array of matches, so we don't need to use the
0 index anymore: just count that entire variable.

[[[ code('306b502000') ]]]

Done!

## High Level and Low Level Modules

At this point, we've separated the high-level business logic - deciding how many
spam words should cause a comment to be marked as spam - from the low level
*details*: matching and finding the spam words. The dependency inversion principle
doesn't necessarily tell us whether or not we should split the original logic into
two classes like we just did. That's probably more the concern of the single
responsibility principle.

But DIP *does* teach us to think about our code in terms of "high-level" modules (or
classes) like `CommentSpamManager` - that depend on "low level" modules (or classes)
like `RegexSpamWordHelper`. And it gives us concrete rules about *how* this
relationship should be handled.

Next, let's refactor the relationship between these two classes to be dependency
inversion principle compliant. We'll see, in real terms, *exactly* what changes
each of the two parts of this principle want us to make.
