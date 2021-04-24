# SRP: Takeaways

We decided that the confirmation email functionality and user creation functionality
are likely to change for different reasons. And so, we split these two
responsibilities into two separate classes.

## Over-Separation & Cohesion

Now, I have some questions. Should we separate the password-hashing logic from the
user-persistence responsibility? Meaning, should we move it into its own class? And
should be treat the confirmation token generation as *its* own responsibility
and move *it* somewhere separate?

If you look quickly at SRP, it kinda sounds like the rule is:

> Put every tiny piece of functionality into its own class and method.

But, thankfully, SRP is *not* saying this... this would make our code a disaster!
There's another concept called cohesion, which says:\

> keep things together that are related.

At first, it seems like cohesion and SRP are opposites. I mean, SRP says
"separate things" and cohesion says "no, keep things together!". But on closer
inspection, SRP and cohesion are two ways of saying the same thing: keep only
related things together. This is the push-and-pull of SRP: separate things that will
change for different reasons... but do *not* separate any further.

Looking at `UserManager`, we're already somewhat protected from changes to the
password hashing functionality, because it relies on a service that's behind an
interface: `UserPasswordEncoderInterface`. How that service works could *completely*
change and we wouldn't need to update any code in this class. So the risk of that
changing in some way that would cause us to need to change *this* class is probably
very low.

What about the token generation logic? Well, do you think it's very likely
that you might change how your tokens are generated? This to me feels like a weak
candidate to separate. It's already simple: one line of code down here... and two
lines of code up here. And it's unlikely change, especially for a reason that's
different than the other code in this class.

Overall, my advice is: don't over-anticipate potential future changes.

## Write Code that Fits in your Head

At the beginning of this tutorial, I mentioned a
[blog post by Dan North](https://dannorth.net/2021/03/16/cupid-the-back-story/amp/),
the father of the behavior-driven development movement. He has something delightfully
refreshing to say about the single responsibility principle. Instead of thinking
about possible changes... and organizing things into responsibilities - which *is*
tricky - He suggests something simpler: write simple code.... using the mesuring
stick of "does this code fit in my head?".

I *love* this. If a method or class has too many things in it, then the total logic
of that method won't "fit in your head"... and it will be difficult to think about
and work with. So, you should separate it into smaller pieces. On the other hand,
if you split the code for registering a user into 10 different classes, that's *also*
going to become complex to think about. The overall goal is to create units of code
that fit in your head... so that you can have an overall application that "fits in
your head".

If you follow this general advice, you'll find that you'll probably create
classes and methods that follow SRP pretty nicely... without the stress of trying
to perfect it.

Okay, it's time to dive into the next solid principle: the open closed principle.
