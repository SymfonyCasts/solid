# Liskov Substitution Principle Definition

Coming soon...

Solid principle. Number three is I think a pretty cool one. It's the LISC of
substitution principle developed by Barbara Liskov researcher at MIT and winner of
the Turing award, which is sort of the Nobel prize for computer science. No, no
biggie. Ms. Scarves principle States that subtypes must be substitutable for their
base types. That's actually not a terrible definition, but let me rephrase it. A
subclass should be able to replace its parent class without breaking the application
or functionality. Then North refers to this

As

Just the principle of least surprise, applied to classes that have a parent class or
implement an interface. In other words, a class that should be Haven in a way that
most users will expect it to behave. Okay. That sounds great. But what does that mean
specifically?

It means basically four things pretend that we have a class that extends a base class
or implements an interface. It also has a protected property and a method, both of
which live in that parent class or in the case of the method, potentially in an
interface for PHP lists. Comp says these four things first, you cannot, you cannot
change the type of a protected property to you. Can't narrow the type hint on an
argument. Like if the parent class uses the object type, hint, you can't make this
narrower in your subclass by requiring something more specific, like a date time,
object three, you also can't widen the return type again. If the parent class says it
returns a date, time object, you can't change this in the subclass to suddenly return
something wider, like any object. And finally for you should follow your parent class
or interfaces rules around whether or not you should throw an exception under certain
situations or conditions. Now here's what I love about this principle and those first
three rules. Yeah, they're impossible to violate and PHP PHP literally will give you
a syntax error, but to get a deeper feeling for the rules of Liskov and why these
rules exist and also what you are allowed to do in a subclass let's jump into two
real-world examples next.

