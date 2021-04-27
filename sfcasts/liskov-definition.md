# Liskov Substitution Principle

Solid principle number three is, I think, a pretty cool one. It's the Liskov
Substitution Principle, developed by Barbara Liskov: a researcher at MIT and winner
of the Turing award, which is, I've learned, sort of the Nobel prize for computer
science. No biggie.

## Liskov Defined

Liskov's principle states:

> Subtypes must be substitutable for their base types.

That's... actually not a terrible definition. By the way, a "subtype" basically
means a class: any class that extends a base class *or* a that implements an interface.

So let me rephrase the definition. I'm going to stick to just talking about classes
and parent classes, but this applies equally to classes that implement an interface.
Here it is:

> A class should be able to replace its parent class without breaking your app.

Dan North refers to this as simply:

> The principle of least surprise, applied to classes that have a parent class or
> implement an interface.

In other words, a class should behave in a way that most users expect it to
behave: it should behave like its parent class or interface *intended* for it
to behave.

Okay, that sounds great... but what does that mean *specifically*?

## The 4 Aspects that (Mostly) Define Liskov

It means basically four things. Pretend that we have a class that extends a base
class or implements an interface. It also has a protected property and a method,
both of which live in that parent class. Or in the case of the method, it lives
on the interface.

Given this setup, Liskov says 4 things.

One: you cannot change the *type* of a protected property.

Two: you can't *narrow* the type hint of an argument. Like, if the parent class uses
the `object` type-hint, you can't make this *narrower* in your subclass by requiring
something more specific, like a `DateTime` object.

Three, which is both similar and *opposite* to the previous rule, you can't *widen*
the *return* type. If the parent class says it returns a `DateTime` object, you
can't change this in the subclass to suddenly return something *wider*, like *any*
object.

And finally, four, you should follow your parent class's - or interface's - rules
around whether or not you should throw an exception under certain conditions.

There may be some edge-case things that I've missed with these 4 rules, but this
is the basic idea. Buy violating any of these rules, you are making your class
behave *differently* than its parent class or interface *intended* for you to
behave. So if part of your code expects an instance of this interface, and you
pass in your class... even though it implements the interface, the class's
violations may cause weird stuff to happen. We'll see *specific* examples of this
over the next few chapters.

Now here's what I really *love* about this principle. Those first three rules?
Yeah, they're *impossible* to violate in PHP. If you change the property type
on a protected property, narrow the type-hint on an argument or widen a return
type on a method, PHP will give you a syntax error. Yup, Liskov's principle makes
*so* much sense, that its rules have been codified right into the language.

So, we now know the rules of Liskov. But to get a deeper feeling for *why* these
rules exist and - almost more importantly - what things you *are* allowed to do in
a "subtype", let's jump into two real-world examples next.
