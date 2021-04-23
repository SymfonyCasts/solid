# LSP: Exception

Coming soon...

Let's jump into our first example that shows up how we can violate the Liskov
principle and why we should care in the source. Scoring directory. Let's create a new
scoring factor class called photo factor. Make this implement the scoring factor
interface. Thanks to our work with OCP. We can now add a new scoring factor to our
system without touching citing score. And thanks to this tag iterator thing in
services .yaml this new photo factor service will instantly be passed into citing
score. Yay. Yeah. In photo factor, I'll go do code generate or Command + N on a Mac
and select "Implement Methods" to generate the score method inside I'll paste some
code.

Okay. This is pretty simple. We loop over the images and pretend that we're analyzing
them in some super advanced way. Shh. Don't tell our users. Oh, and if there are no
photos, no images for this, uh, post, for some reason, we throw an exception. All
right, let's try it. I'll go back to our homepage, go to Smith and post fill in some
dominator. I'll leave images empty for simplicity and Oh 500 air. That's our new
exception. We broke our app and it broke because we just violated Liskov's principle.
Our new scoring factor class four subtype to use a more technical word, just did
something, unexpected it through an exception. One way to fix this, which might seem
silly, but a highlights, a warning sign for the scoff is inside of sighting score.

Okay. It says photo factor. Doesn't like posts with zero images. Let's just skip it.
So right inside forage, I'm going to say if scoring factor is an instance of photo
factor and count of citing arrow, get images = zero, then we'll just continue. And by
the way, you probably are also realizing that that's a violation of OCP since we just
had to modify this code, but it does fix our app. I go over here and refresh and
resubmit. Got it. It did submit, but let's back up a minute. First open the scoring
factor interface, unlike argument types and return types. There's no way in PHP to
codify whether or not a method should throw an exception or under, under certain
situations or which exception types should be used. But this can be described in the
documentation above the method, which we kind of skipped. When we, when we created
this, let's fill that in. Now. I don't need the out return to the at parama because
they are redundant. Unless I wanted to add some more information about their meeting.
I want to say on this, add a quick description of the method.

And down here, you won't necessarily always see this, but let's be very clear about
the exception behavior that we expect. This method should not throw an exception for
any normal reason. That's kind of redundant more commonly. If you are allowed to
throw an exception, you'll see an add throws that describes that and which exception
you can throw. And if you don't see that, you should probably assume that you're not
allowed to throw an exception for any normal reason. Anyways, now that we've
clarified this, it's easy to see that our photo factor breaks list gobs principle,
photo factor behaves in a way that the client at the class that uses it citing score
sometimes called the client class was not expecting, which is why we had to hack in
this code afterwards. Another way to think about it, which explains why this is
called Liskov's substitution principle is that right now photo factor could not
replace or substitute any of the other scoring factors without breaking things.

If this is substitution part, doesn't make complete sense yet don't worry. Our next
example will illustrate that even better. Anyways, our work around was to add some
instant, an instance of con incidence of code to citing score, to literally work
around a problem. When you have an instance of conditional like this, it's often a
signal that you're violating lists. Goff. You have some specific implementation of a
class or interface that is behaving differently, which you then need to code for. So
let's remove this, I'll remove the F statement and I'll even go clean out this extra
use statement on top V real solution. Now that we've clarified that the score method
shouldn't throw an exception in normal situations is to replace the exception with a
return statement. We should add zero extra point score when there are no images,
that's it. The class now acts like we expect it to no surprises, by the way, this
does not mean that it is illegal for our score method to ever throw an exception. If
this class method, for example, needed to query a database and the database
connection was down, then yeah, you should totally throw an exception. That is an
unexpected situation, but for all the expected normal cases, we should follow the
rules of our parent class or interface. Next let's look at one more example of
Liskov's principle, where we create a subclass of an existing class and secretly
substitute it into our system without breaking anything. Ms. Goff would be so proud.

We want to make her proud.

