# DIP: Takeaways

The two rules of the dependency inversion principle give us clear instructions
on how two classes - like `CommentSpamManager` and `RegexSpamWordHelper` - should
interact.

## "Inversion"? What got Inverted?

But before we talk about the pros and cons of DIP... why is this called dependency
*inversion*? What is the "inversion"

This took me a *long* time to wrap my head around. I expected that dependency
inversion somehow meant that the two classes *literally* started depending on each
other in some... different way. Like suddenly we would inject the
`CommentSpamManager` into `RegexSpamWordHelper` instead of the other way around,
actually "inverting" the dependency.

But as you can see... that is *not* the case. On a high level, these two classes
depend on each other in the exact same way as they always did: the low level, details
class - `RegexSpamWordHelper` - is injected into the high-level class -
`CommentSpamManager`.

The "inversion" part is... more of an abstract concept. Before we refactored our
code to create and use the interface, I would have said:

> `CommentSpamManager` depends on `RegexSpamWordHelper`. If we decide to modify
> `RegexSpamWordHelper`, we will then need to update `CommentSpamManager`
> to make it work with those changed. `RegexSpamWordHelper` is the boss.

But *after* the refactoring, specifically, after we created an interface based on
the needs of `CommentSpamManager`, I would *now* say:

> `CommentSpamManager` depends on any class that implements
> `CommentSpamCounterInterface`. In reality, in our app, this is the
> `RegexSpamWordHelper` class. But if we decided to refactor how
> `RegexSpamWordHelper` works, it would *still* be responsible for implementing
> `CommentSpamCounterInterface`. In other words, when `RegexSpamWordHelper` changes,
> our high level `CommentSpamManager` class will *not* need to change.

*That* is the inversion: it's an inversion of control: a "reversal" of who is in
charge.

## Pros and Cons of DIP

So now that we understand the dependency inversion principle, what are its benefits?

Simply put: DIP is all about *decoupling*. Our `CommentSpamManager` is now *decoupled*
from `RegexSpamWordHelper`. We could even *replace* it with a different class that
implements this interface without touching *any* code from our high-level class.

This is one of the core strategies to writing "framework agnostic" code. In this
situation, developers will create interfaces in *their* code and only depend on
*those* interfaces, instead of the interfaces or classes from whatever framework
they're using.

However, in my code, I rarely follow the dependency inversion principle. Well,
let me clarify. If I were working on an open source, reusable library, like Symfony
itself, I would *definitely* create interfaces, like we just did. Why? Because I
want to allow the users of my code to replace this service with some other class,
like maybe someone wants to replace our simple `RegexSpamWordHelper` in their app
with a class that uses an API to find these spam words.

But if I were writing this in my *own* application, I would almost skip creating
the interface: I would make my code look like it originally did with
`CommentSpamManager` relying directly on `RegexSpamWordHelper` with no interface.

## Most Dependencies Don't Need Inverting

Why? As Dan North points out in his blog post: not all dependencies *need* to be
inverted. If something you depend on will *truly* need to be swapped out for a
different class or implementation later, then that dependency is almost more of
an "option". In that situation, we probably *would* want to apply DIP: by
type-hinting an interface, we're saying:

> Please pass me the "option" that you would like to use for counting spam words.

But, most of the time, to quote partially Dan:

> Dependencies aren't options, they're just the way we are going to count spam words
> in this situation.

If you followed DIP perfectly, you would end up with a code base with a lot of
interfaces which are implemented by only one class each. That adds flexibility...
which you likely won't need. The "cost" is misdirection: your code is harder to
follow.

For example, in `CommentSpamManager`, it now takes a bit more work to actually figure
out what class is behind this and how it's working. And if you ever do try to change
a dependency to use a different concrete class, you might find out that, even though
you followed DIP, it's not so easy change.

For example, changing from one database system to another is probably going to be
an ugly job even if you created an interface to abstract away the differences
beforehand.

So my advice is this: unless you're writing code that will be shared across projects,
do not create interfaces until you need the ability to have more than one class
implement it, which we actually saw earlier with our scoring factors. This is a
perfectly nice use of interfaces,

*But*! I will fully admit that not everyone will agree with my opinion on this. And
if you do disagree, that's great! Do what you think is best. There are plenty of
smart people out there that *do* create extra interfaces in their code to decouple
from whatever frameworks or libraries that they're using. I'm just not one of them.

## SOLID in Review

Ok friends, that's it! We are done with the SOLID principles!

Let's take a quick recap... using our simplified definitions.

One: the single responsibility principle says:

> write classes so that they fitsin your head.

Two: the open-closed principle says:

> design your classes so that you can extend them without modifying them.

This is never entirely possible... and in my app code, I rarely follow this.

Three: the Liskov substitution principle says:

> if a class extends a base class or implement an interface, make your class behave
> like it is supposed to.

PHP protects against most violations of this principle by throwing syntax errors.

Four: the interface segregation principle says:

> if a class has a large interface - so a lot of methods - and you often inject the
> class and only use some of those methods - consider splitting your class into
> smaller pieces.

And five: the dependency inversion principle says:

> Prefer type-hinting interfaces and allow the interfaces to be created for the
> "high level" class that will use it, instead of by the low-level class that
> will implement the interface.

In my app, I *do* type-hint interfaces whenever they exist, usually because services
from Symfony or other libraries provide an interface. But I don't create my *own*
interfaces until I have multiple classes that need to implement them.

My opinions are, of course, just that: opinions... and I tend to be more pragmatic
and less dogmatic, for better or worse. People will definitely disagree... and
that's great! SOLID forces you to think critically.

Also the SOLID principles aren't the only "game" in town when it comes to writing
clean code. There are design patterns, composition over inheritance, the law of
demeter and other principles to guide your path.

If you have any questions or ideas as always, we would *love* to hear from you down
in the comments.

Alright, friends, see you next time!
