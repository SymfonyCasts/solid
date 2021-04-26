# DIP: Takeaways

Coming soon...

Okay. So the dependency in version principles, two rules give us clear instructions
on how to classes like comment, spam manager and Reddick spam word helper should
interact. But before we talk about the pros and cons of dip, why is this called
dependency? Inversion? What is the inversion part, mate? This took me a long time to
wrap my head around. I expected that dependency inversion somehow meant that the two
classes literally started depending each other on each other in some different way.
Like suddenly we would inject the

Comments, spam

Manager into Reddick spam word helper instead of the other way around actually
inverting the dependency. Okay. It's as you can see, this is not the case. The
inversion part is more of an abstract concept. Before we refactored our code to
create the interface and use the interface. I would have said common spam manager
depends on Reggie spam word helper. If we decide to re factor how rederick spam word
helper works, we're going to then need to go into comments, van manager, after an
update it to work rejects spam word helper is the boss. But after the refactoring
specifically, after we created an interface based on the needs of comment, spam
manager, I would now say comment. Spam manager depends on any class that implements
comment, spam counter interface. In reality, in my app, this is the reject spam word
helper class. But now if we decided to refactor, uh, how redneck spam word helper
works, it would still, it would be responsible for still implementing comments, spam
counter interface. And so when Reddick spam word helper changes,

Our high level comments may have manager class will not need to change. That is the
inversion of control. Okay? And now that we understand dependency inversion
principle, what are its benefits? Simply put dip is all about decoupling. Our
comments. My manager is now decoupled from rederick spam ward helper, and we could
even replace it with a different class that implements this interface without
touching any code from our high level class. This is one of the core strategies to
writing framework agnostic code people will create interfaces in their application
and only depend on those interfaces on those interfaces. Instead of the interfaces
are classes from whatever framework they're using. Okay.

However, in my code, I rarely follow the dependency and version of principle. Well,
let me clarify. If I were working on an open source reusable code base, like Symfony
itself, I would definitely create interfaces. Like we just did. Why? Because I want
to allow the users of my code to replace this service with some, with some other
class, like maybe someone wants to replace our simple rejects spam word helper in
their app with a class that uses an API to do, to find these spam words. But if I
were writing this in my own application, I would almost definitely not create the
interface. I would make my code look like it originally did with comments, fan
manager, relying directly on rejects spam or helper with no interface. Why as Dan
North points out in his blog post, okay,

[inaudible]

Not all dependencies need to be inverted. If something you depend on will truly need
to be swapped out for a different class or implementation later, then that
dependencies almost more of an option. It's like, please pass me the option that you
would like to use for counting spam words. But most of the time to quote Dan
dependencies aren't options, they're just the way we are going to count spam words in
this situation. If you followed dip perfectly, you end up with a code base with a lot
of interfaces each, which is implemented by only one class that adds flexibility that
you likely won't need. And the cost is misdirection.

Okay?

Comments, my manager it's now it takes a little bit more work to actually figure out
what class is behind this and how it's working. And if you ever do try to change a
dependency to use a different concrete class, you might find out that even though you
followed dip, it's not so easy change. For example, changing from one database system
to another is probably going to be an ugly job. Even if you created an interface to
abstract away the differences beforehand. So my advice is this, unless you're writing
code that will be shared across projects, do not create interfaces until you need the
ability to have more than one class implement it, which we actually saw earlier with
our scoring factors. This is a perfectly nice use of interfaces, But not everyone
agrees with this opinion. And if you do disagree, that's great. Do what you think is
best our friends, that's it. We are done with the solid principles. So let's take a
quick review, these single responsibility principle, right classes so that their size
fits in your head.

The open closed principle design your classes so that you can extend them without
modifying them. So this is never entirely possible. And in my application code, I
rarely follow this, this cough substitution principle, if a class extends a base
class or implement an interface, make your class behave like it is supposed to PHP
protects against most violations of this principle by throwing syntax errors, the
interface segregation principle, if a class has a large interface, so a lot of
methods and you often inject the class and only use some of those methods consider
splitting your class into smaller pieces and the dependency in version principle,
prefer type hinting interfaces and allow the interfaces to be created for the high
level class that we'll use it instead of the low level class that we'll implement it
in my app, I do type hint interfaces whenever they exist, usually because Symfony
services provide an interface, but I don't create interfaces until I have multiple
classes that need to implement them. My opinions are of course, just those and people
will definitely disagree. And that's great solid forces you to think critically. Also
the solid principles are far from the only game in town. When it comes to writing
clean code, there are design patterns, composition over inheritance the law of
Demeter and other things to guide your path. If you have any questions or ideas as
always, we would love to hear from you down in the comments section. All right,
friends, see you next time.

