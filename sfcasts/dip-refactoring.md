# DIP: Refactoring

Coming soon...

Our code specifically, the code in these two classes does not follow the dependency
inversion principle. Why not? Let's go through the two parts of the, of dip
definition. One by one. The first part is high level modules should not depend on low
level modules. Both should depend on abstractions, for example, interfaces, which is
a fancy way of saying classes should depend on interfaces instead of concrete
classes. Yep. This part of the rule, isn't that simple. It says that instead of type
hinting or depending on the concrete rejects spam word helper, we should type hint in
interface. Okay. So we just need to create a new interface, make reject spam or
helper, implement that interface, then change the type in to use that interface,
right? Yes, exactly. But the second part of D I P tells us something about how we
should create that interface. That part says, abstractions should not depend on
details, details, which are concrete implementations should depend on abstractions,
which I simplified. Two interfaces should be designed by the classes that use them,
not by the classes that will implement them. Let me explain the most natural way to
create the new interface would be to look at the class that will implement it. So
rejects fan word helper, and create an interface that matches it. So a red jacks,
spam word helper interface with a good match to spam or words method done. But by
doing this, we are allowing the interface to sort of be owned by the lower level
class, sometimes known as the class with the details.

In other words, the way that interface looks is being dictated by this lower level
rejects spam word helper. Instead dip says that the higher level class comments, spam
manager should be in charge of creating the interface, allowing it to decide to
design the dependency in just the way that it wants. Let's put this into practice. If
you look at comments, span manager, all it really needs to know. All it really needs
is to be able to call a method that will return the number of spammy words. The
number is ultimately the count is ultimately what we use. It doesn't actually need a
method that returns the word themselves, which is what's happening now. So in the
comments directory,

Which

I'm doing to highlight that this interface is owned by a comment, spam manager create
a new interface. So I'll go to class changes to interface, let's call it. How about
comment, spam counter interface

Inside,

Add one method, Public function, count spam words, which will accept the string
content and we'll return in it. Beautiful. Notice that just by inverting, who we
think should be in charge of creating this interface or who should own it. We ended
up with a very different results instead of forcing the new interface to look like
this low level rejects spam word helper class. This class is now going to be forced
to change itself, to implement the interface and the interface of that class
implements comment, spam counter interface, Then add the new method. So I'll go to
code generate or command and on the Mac, go to "Implement Methods" and generate count
to spam words. And this can be as simple as return. The count of this arrow. Get
matched spam words, content. Now in comment, spam manager, Let's follow the first
part of dip in change this to depend on the new interface. So I'll change this type
into be comment, spam men, spam counter interface. Of course I'll need to change the
title on the property and also going to rename the property itself to be more clear.
So we'll call it spam word counter, and then same thing here. I'll change this
argument to spam word counter

[inaudible].

I'll take the logic down and validate to use the new method. So now it's bad words
changes to like a bad or words count equals. And then instead of calling get match
spam words, well now call the new count spam words And below we don't need a count
anymore around this. It's just going to be literally if bad words count is greater
than equal to two, we throw the exception and done this class now follows the two
parts of dependency injection principle. Our high level class depends on an interface
and that interface is owned Or was designed for this same high-level class instead of
being designed by the low level or details class

[inaudible].

Before we talk about the takeaways from the dependency injection principle,
dependency and diversion principle, I want to mention two things first over in
rederick spam word helper, you are allowed to have this public function get matched
to spam words, method. If you're using it somewhere else in your coat, since we're
not, I'm going to clean things up and make this private. And second kind of a
question will Symfony know which service to auto wire, when it sees this comment,
spam counter interface type hint, actually it will find your terminal, find your
terminal run bin console, debug auto wiring Comments. And I'm in the past dash dash
all just so we can see all the different things that will come up. Okay.

[inaudible]

And yes, this proves it. As you can see, when Symfony sees the comment spam counter
interface, it's going to pass us our rejects spam word helper service. This is
because of a nice feature inside Symfony's container. If Symfony sees an interface in
our code, like comment, spam Connor interface, and only one of our classes implements
it. Then it automatically assumes that this class should be auto wired. That
interface. If you ever created a second class that implemented the interface, Symfony
would throw a clear exception telling you that you need to choose which one to
Ottawa.

Yeah.

Let's talk about the takeaways of the dependency, injection inversion principle, and
also why w what, what that word inversion means and doesn't mean,

Yeah.

