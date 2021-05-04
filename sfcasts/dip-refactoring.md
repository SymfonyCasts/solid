# Refactoring Towards Dependency Inversion

Our code, specifically the code in these two classes, does not follow the dependency
inversion principle. Why not? Let's go through the two parts of the DIP definition,
one by one.

The first part is:

> High level modules should not depend on low level modules. Both should depend on
> abstractions, for example, interfaces.

This is a fancy way of saying that classes should depend on interfaces instead of
concrete classes. Yep! This part of the rule, is that simple. It says that instead
of type-hinting - so "depending on" - the concrete `RegexSpamWordHelper`, we should
type-hint an interface.

Okay! So we just need to create a new interface, make `RegexSpamWordHelper` implement
that interface, then change the type-hint to use that interface, right? Yes, exactly!

## Thinking about the Design of your Interface

But... the *second* part of DIP tells us something about how we should create and
*design* that interface. That part says:

> Abstractions should not depend on details, details - which are concrete
> implementations - should depend on abstractions.

We simplified this to:

> Interfaces should be designed by the classes that use them, not by the classes
> that will implement them.

Let me explain. The most natural way to create the new interface would be to look
at the class that will implement it - so `RegexSpamWordHelper` - and create an
interface that matches it! So a `RegexSpamWordHelperInterface` with a
`getMatchedSpamWords()` method. Done!

But by doing this, we are allowing the interface to, sort of, be "owned" by the lower
level class, sometimes known as the class with the "details". In other words, the
way the interface *looks* is being "controlled" by the lower-level
`RegexSpamWordHelper` class.

But DIP says that the *higher* level class - `CommentSpamManager` - should be in
charge of creating the interface, allowing *it* to design the dependency in *just*
the way that *it* wants.

## Creating the Interface

Let's put this into practice. If you look at `CommentSpamManager`, all it really
needs is to be able to call a method that will return the *number* of spammy words...
because that count is ultimately all we use: we don't *really* need the matched
words themselves.

So in the `Comment/` directory, which I'm using to highlight that this interface
is *owned* by `CommentSpamManager`, create a new interface: select PHP class,
change to interface and call it, how about, `CommentSpamCounterInterface`.

Inside, add one method: public function `countSpamWords()`, which will accept the
`string $content` and will return `int`. Beautiful! Notice that just by inverting,
*who* we think should be in charge of creating the interface - or who should "own"
it - we ended up with a very different result. Instead of forcing the interface
to look like the low level `RegexSpamWordHelper` class, that class is now going
to be forced to change *itself* to implement the interface.

Add implements `CommentSpamCounterInterface`, then I'll go to Code -> Generate -
or Command + N on the Mac - and select "Implement Methods" to generate
`countSpamWords()`. Inside, return the `count()` of
`$this->getMatchedSpamWords($content)`.

Back in `CommentSpamManager`, let's follow the first part of DIP and change this
to depend on the new interface. Change the type-hint to `CommentSpamCounterInterface`...
change the type on the property... and let's also rename the property itself to be
more clear: call it `$spamWordCounter`. Rename the argument also.

Down in `validate()`, rename `$badWordsOnComment` to `$badWordsCount`. Then, instead
of calling `getMatchedSpamWords()`, call the new `countSpamWords()`. Below,
we don't need the `count()` anymore: just check if `$badWordsCount` is greater than
or equal to 2.

Congratulations! Our code now follows the two parts of the dependency inversion
principle. One, our high level class - `CommentSpamManager` - depends on an interface.
And two, that interface was designed for - and is controlled by - the high-level
class instead of being designed and controlled by the low level, or "details" class
`RegexSpamWordHelper`.

## How Symfony Autowires Interfaces

Before we talk about the takeaways from the dependency inversion principle, I want
to mention two things.

First, over in `RegexSpamWordHelper`, you *are* allowed to have this public function
`getMatchedSpamWords()` method if you're using it somewhere else in your code. Since
we're not, I'm going to clean things up and make this `private`.

Second... well... this is more of a question: will Symfony know which service to
autowire when it sees the `CommentSpamCounterInterface` type-hint? Will it know
that it should actually pass us the `RegexSpamWordHelper` service?

Actually... it will! Find your terminal and run:

```terminal
php bin/console debug:autowiring Comments --all
```

I'm passing `--all` just so we can see *all* the results. And... this proves it!
As this shows, when Symfony sees a `CommentSpamCounterInterface` type-hint,
it will autowire the `RegexSpamWordHelper` service.

This works thanks to a nice feature inside Symfony's container. If Symfony sees an
interface in *our* code - like `CommentSpamCounterInterface` - and only one of our
classes implements it, then it automatically assumes that this class should be
autowired for that interface. If you ever created a *second* class that implemented
the interface, Symfony would throw a clear exception telling us that we need to
choose which one to autowire.

Next: let's talk about the takeaways of the dependency inversion principle, and
also, what that word "inversion" means and doesn't mean.
