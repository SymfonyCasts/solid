# LSP: Changes

Coming soon...

Calculating how long it takes for this parent score method to execute will be easy.
But then what do we do with that number? This method returns a big foot sighting
score instance. So we can't suddenly change this to return in it for the duration.
How can this method return the Bigfoot sighting score and information about how long
it took the score to calculate the answer is, do you create another subclass, a
subclass of big foot score that holds this extra information? This class lives in
these source model directory. There it is. So right next to it, let's create a new
class called the bubble big foot citing score and make it extend the normal big foot
siding score.

Now we have two subclasses to play with override the constructor here. Can we do that
with code generate or command N override construct, call the parent constructor with
the score and now at a new argument, which will be the float calculation time. And
I'll hit enter and go to initialize the properties, select just calculation time to
create that property and set it to make the Caicos time accessible down here. I'll go
back to code generate or command end and go to getters. And we will generate a getter
for get calculation time, by the way, adding a required argument to a method that you
are overriding like we're doing construct is normally another way to violate Liskov's
principle. Let's think about it using a different example, citing score. If I can
normally call score and pass it a single arguments. And suddenly you substitute that
with a different class whose score method requires two arguments. That's going to
make my code explode. The new classes, not substitutable for the old one.

However, the constructor does not need to follow Liskov's principle, which took me a
minute to wrap my head around originally. Why not? Because if you are instantiating a
debugger, double Bigfoot sighting score with new DUI available, big foot citing
score, then you know exactly which glass you are instantiating. And so you configure
out exactly which arguments need to pass. This is different than being past a big
foot setting score object, where the true class might be a subclass. And so you need
any of the methods on that subclass do P to behave like the original classes methods.
Anyways, back in anyways, in debugger ball setting score Let's return our new
debugger bubble Bigfoot sighting score class with a dummy duration. So we can say
something like dollar sign, big foot score = parents score, and then return new debug
bubble Bigfoot sighting score. And then since this has the score into your own can as
BS score arrow, get scored, kind of get that int and then I'm going to pass it 100 as
our fake duration time. And let's advertise that we return this. So we now return
eight debug bubble Bigfoot sighting score is that legal go over,

Go over and refresh the page to find out it is PHP. Totally allows it. And that's
because this does follow Liskov's principle. We are making the return type more
narrow or more specific. Why is more narrow? Okay. Look at Bigfoot sighting
controller. The class that uses the setting score

[inaudible]

This code requires a siting score instance. And so when we call the score method
later, we know that this is going to return a set, a big foot citing score class. We
know this because that's what these settings score class tells us. So if I hold
command, uh, or control to open this, this is the original sighting score. It score
method says we get a big foot sighting score, uh, instance back. So in this case, in
the controller, we know that this is a big foot sighting score

Class. Okay.

And we know that that class has a good score method on it. I'll once again, jump into
this class. So this is the big, original big plus I didn't score. It has a score of F
get score method on it. So we can use that in our controller to get the integer a
score And everything is fine. But now we know that we've actually substituted these
citing score for a debug double setting score and it's score method returns, a debug
double Bigfoot sighting score, but that's okay. Why? Because this is an even more
specific return type. We're still returning a big foot siting score instance, which
has, which will have it gets score method. The fact that what it returns is actually
a subclass of that with potentially extra methods does not break its substitute
ability.

But

If we had changed its return type to something less specific, like an any object,
then there would be no guarantee that what we returned from this method has a good
score method on it that would break Liskov's principle and PHP would be so mad about
it, that we would get a syntax error. So we will undo that. Now we won't talk about
it in detail, but the same philosophy can be applied to argument types, but in the
opposite direction, it's okay to change an argument type. As long as you support, at
least the original type, it's not okay to be more restrictive with the type with the
type you allow, But it is okay to be less specific. I could suddenly say that the
score math in this class supports any object, of course, in reality, cause I'm
calling the parents score method that would explode if I passed any object, but on I
and I object oriented level, this is allowed and you can see that on a refresh. The
page PHP is fine with

That, but I will change it back.

Okay. To celebrate our new system, let's see an action in Bigfoot sighting

Controller after the ad flash, let's also add some, uh, duration information. Um, I'm
actually going to say if the BFS score is okay,

An instance of debugger bubble, big foot sighting score, then I'm going to say this
arrow, add flash

Success,

Sprint F and down here, I'll say by the way, the scoring

Took percent F milliseconds,

Big fuss, high score, arrow, get calculation time, which we know we can call this
because I just did the instance of, and I want times a thousand. So that's in
milliseconds. Oh, wait. Didn't I say that instance of is kind of a signal that we're
breaking LISC, right?

Principal. Yep. But

Since this is my controller, which whose job is to kind of tie pieces together. And
since this only adds extra functionality for this one case, I'm okay with it.

However,

Another solution, depending on your needs would be to explicitly say that you require
the debugger double service. So instead of saying, I allow any setting score, we
could say, we're always going to use this even in production. So we require a debug
bubble

Citing score.

If we did that, we would not need the incidence of, cause we would know that that
service returns the debug bubble, a Bigfoot sighting score, which has that get
calculation time method on it.

But

Well, one tiny last little detail. If we refresh now that doesn't work can not
resolve argument. That sighting score cannot auto automize service, debug double
settings score arguments, scoring factors is type hinted, Iterable. You should
configure its value explicitly. We hit this air at the beginning of creating our, uh,
in the OCP section in services, IMO. We are passing specifically the scoring factors
that we want, but for some reason that's not working anymore. This is thanks to auto
registration. Thanks to our registration. There is actually a separate service in our
container called de bugaboo citing score. You can see that if you run big console,
debug container citing, there is a diva global site is Gore and a separate service
called siting score. But when we really want something to do is to pass us the same
one service, regardless of whether we type into debug double setting score or citing
score. So we'll add an alias inside services.yaml here. We're going to say

App

Service /debug bubble. Actually here, I'm going to actually copy the class name for
our debugging setting score and then say colon and then an at symbol. And here you
can say app service setting score. In other words, whenever somebody auto tries to
Ottawa or this service, they actually get this service, which uses the debug mobile
setting score class. I know a little bit hard to follow a little fancy. If you go
back to the run debug container. Now you'll see that it looks like there are still
two services, but if you hit six here to look at the debugger bubble, one on top, you
can say, this is an alias for the service app service setting scores. They really
pointed the same thing in over in the browser when we refreshed it works. So the big
takeaway from LS from Liskov's principle is this make sure that when you have a
subclass, I have a class that extends another or implements an interface. It follows
the rules of that parent type. It doesn't do anything surprising, that's it. And PHP
will even prevent you from most Liz cough violations. The most interesting part of
list off for me is learning the things that you are allowed to do. The fact that you
are allowed to change the return type in an overwritten method, as long as you make
it more specific or the opposite for the type argument types. Okay. Next up is solid
principle. Number four, the interface segregation principle.

