# Blackfire Environment Variables

The last little feature that I want to talk about now that we have these two
environments is that in reality, and this is true of Symfony cloud, sometimes the
your production infrastructure is a lot bigger and more powerful than your staging
infrastructure, which means that your builds on staging might run slower than your
production bills and that's a problem if you have things like this, a certain main
not wall time has less than a hundred milliseconds. That might be true in production,
but it might not, but on staging it might run slower and so maybe on staging you only
really care that it's faster than 200 milliseconds. If you want. Inside of here you
can use it variables, so I'll add some parentheses here and I'll say times and you
can say VAR and then inside of here I'm going to invent a new variable called speed
co efficient and then give us a second argument, which is one, what does this thing
is that when it does this assertion, it's doable. It should actually assert it
against a hundred milliseconds times whatever the value of this speed coefficient
variable is, which we haven't set yet and if that variable isn't available, then just
default to one.

So in the master environment on Blackfire, we won't set this variable, but I'll copy
this and over in my non master environment down here, I can set a variable, must set
this to two and it's safe. All right, let's swing back over. Adding speed coefficient
variable for a wall time cert. As a reminder, we're on our some feature branch, so
I'll say Symfony deploy bypass checks. All right. When that finishes, let's go over
here and because we just deployed the feature. Yup. Cool. We have a new build for
some feature and if we look inside of here, there's two cool things I want you to
notice. The first thing is that under the homepage you can see that the speed
coefficient, it kind of puts a little too here. It's saying that that = two, so
really it's me checking to make sure it's less than 200 milliseconds, which is really
cool.

The other thing I want you to notice is that if you go back to builds, we've now
built the some feature branch twice. So when you click on the second one, it actually
has the comparison to the latest successful bill. Now late a successful build is the
original on that branch. So when you're looking at the comparison on a branch, the
comparison is to that same branch, not to, you know, just the build right before,
which would be master. So we can do all of our diff stuff and look at the comparisons
and it's doing a really smart job of comparing them to things on that branch. Our
friends. That's it for the black bar tutorial. I hope you had as much fun as I did.
Uh, using Blackfire and a lightweight is a super fun way to find performance things.
But boy, if you dive in, you can really get a reach, a rich feature set with this
build system. I personally had been loving having Symfony casts, production, having
these graphs and watching my memory usage, uh, and tweaking things over time. And
we're just getting started with [inaudible],

our, um, [inaudible]

set up here and I'm excited to see where it goes. As always, if you have any
questions or we didn't explain something, we're here for you in the comments, so let
us know. All right. I wish you a very, very

fast day,

right friends. See you next time.
