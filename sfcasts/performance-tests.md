# Performance Tests

Coming soon...

Let's profile our get hub a pair request. Again, I can cheat and go to /API /get hub
organization and let's click to profile this. I'll call it recording good hub Ajax
HTTP requests because that's actually what we're going to talk about. And then I'll
head to view the call graph. Okay. This requests actually you can see it was very
slow, 1.83 seconds, a lot slower than we've been seeing and that was probably just by
chance. We can see curl multi-select as the problem because the GitHub API is a
little bit slow right now. And that's what I want to talk about. If you look up here,
I could actually see that there were two um, HTTP requests to my application. And
actually if you kind of look into these different API endpoints that we're using, we
can actually get all of the information that we need in our application with just one
HTTP request. So basically what I'm saying is our page is making one more HTTP
requests than it needs to. And you could kind of consider this a performance bug.
We're making two HTP requests and we own when we only need one. Now normally with
bugs, if we're being awesome and following best practices before fixing above, we
write a test for it and actually we can do the same thing with Blackfire. We can
write, we can add assertions to our tests that assert that this end point only makes
one HTTP request. It's actually pretty awesome.

So move over and go to the test controller main controller. Test that PHP. I've
already gone ahead and set up a functional test here that goes to /API /get up
organization and check some basic data on that. We can run this by going to my
running PHP bin, PHP units and then I'll point directly at this one test class. So it
just runs those tests the first time you run the script. That'll probably need to
download PHP unit in the background. Once it finishes, it runs our tests. The
assertions pass life is good. So check this out. We can add assertions about our
performance right inside of this test. The way to do it is first make sure you have
the SDK installed, which we just installed because we're going to use a trip from it
called test case traits. Next, inside the method, well credit, a new Blackfire config
object, like what our config variable, which is a new configuration, the one from
Blackfire /profile, the same one that we used earlier. Need to double check on that
and then we can call methyl on this called a cert. We actually add assertions. These
are very interesting. I'm going to put the assertion first and then we'll talk about
it. Metrics dot HTTP dot requests dot count = = one

and that's it.

Finally down here we're going to say this arrow, a circuit Blackfire and we do as you
pass this, that BioFire configuration object and then a call back. So this is a
little confusing at first, but what's going to happen here is that when we call this
era cert Blackfire, it will call this callback. Inside of that callback, we will do
whatever work we want to. We can make one to 10 age to give you the requests and then
ultimately one that's called back finishes. It will run this assertion against the
HTTP requests that was made here to get this to work. I also need to use the client
variable. I'll show you a little bit more about what's going on behind the scenes
there if it's still not clear to you. All right, so check this out. We can now run
our tests again

and

it fails. Check us out. Failed that metrics. ATV requests count = one. This is really
cool. What happened is when this made the request to /API /get hub organization, this
made a real Blackfire profile behind the screens. Behind the scenes. You can even
copy this URL and go check it out and over here you can see where on theS this
assertions tab, which is something we're going to talk more about later. And you can
see that this too, we're actually making to eight spirit quest did not equal one. So
there are several things I want to say about this. First of all, when you run this
test, it does make a real Blackfire profile in the background. However, if you go to
your homepage, you won't see them.

That's simply because if I'll hold command or control and click assert Blackfire. If
you look at the assert live-fire method, it actually uses the SDK just like we did to
create a probe and end a probe. It actually creates a profile in the background. When
it does that, it also adds this skip timeline thing by default, which basically means
make a profile but don't put it on our my profiles page, which just to make sure that
it doesn't get cluttered up with extra garbage. You could totally override that if
you wanted on that configuration object that we passed if you didn't want it to show
up.

[inaudible].

More importantly though, I want to talk about this metric stuff here. So two things I
want to know is by far has a great metric system and inside the metric what you're
using is an expression and what this basically is is a language that's very similar
to JavaScript. It's technically Symfony's expression language if you want to read
more about it. So metrics is probably an object and then we're calling an HTTP
property or request property account property and asserting that = one. The second
thing, probably even more important is how the heck did I know that this was the
exact string I need to use here to do this assertion? This goes back if you look at
our profile to the timeline. Remember when we checked on the timeline, we talked
about how on the left side there are these timeline metrics, which at that point were
just a nice fancy way to color different sections of the timeline.

But really the most useful part of these metrics is that these metrics just give you
access to low level information about what's going on. So for example, there's a
metric called Symfony.events dot count which = seven. And you could use that in a
metric if you for some reason wanted to assert that certain number of methods were
called. So if I were looking to do an assertion about HTD period HTTP requests, I
would go up the search here and to search for HTTP. And I say there's curl requests
and requests. If you're not sure which one to use, you can look at the difference
between them because this is going to show you the real data. So here you can see
that there's a metric called HTTP dot requests and then there's a bunch of metrics
about it. So I can say HTTP dot requests dot count or HTTP dot requests dot memory
or.io, any of these things on here. So that was the key. I started with metrics dot.
We can then reference any of the metrics in the system, which is super powerful.

Okay. So we now have proven that we have a performance bug in our application because
our tests fail so that we can fix this. And the fix for this is not really that
important because Hey, we don't care. This is a tutorial at Blackfire, but B, we have
a, um, if a test now, which is going to prove that once we have the fixed done that
it does work correctly. So the logic is H keep your questions in source, get hub, get
hub, API helper, and we have two public functions in here that make the two API
requests. And basically we can get all the information from this second HDB request.
So I'm not gonna fill you in with the exact details, but we're basically going to add
some caching via new PRI property called get hub organizations.

And then as we loop over the repositories for a specific organization, we can
actually store information about this organization because it's on that endpoint. So
what I'll do here is I can add a new variable here called public repo count. It's one
of the things that we put on our record for our organization. And then down here I
can say if repo data left, square bracket, private = false. That's one of the keys on
the repo data. Then we'll say plus plus public repo comp. So as we're looping over
the repositories, we're just counting how many public repositories there are. And
finally down here we'll say if not is set this->get hub organizations left square
bracket organization. So this is an organization that we haven't seen yet on this
request we're going to say this->get up organizations, lots of record organization =
new get hub organization.

And this needs two pieces of information. The organization name, which we can
actually probably just use the organization variable, but you can also get this off
of the data with data zero to get the first repository owner login. And then the
other thing we are recording as the public was the public repo count. So now every
time we call this method will actually capture this organization's information and
store it on this property. So if we call this method first and then this method, we
can actually do less work by looking that up, check this out. We can say if is set
this->get up organizations, last square bracket organization, then we can just return
that immediately without doing any work. And in fact, if you looked in the
controller, we're already calling things in this order. The controller will just swap
the order of these things so that the repository is the one it's called first and
gets all that cache data set up. Phew. Okay. So let's say that helps. It was a bit of
a complicated fix, but thanks to our test we can know for sure and it does. So that
proves that we have reduced the query count from two to one.

Okay. So

thanks these metrics, there's a lots of things that we can assert and one of the
things I love about this system is, you know, if you, if even if you do typo one of
these, uh, variables, when you run it, you're going to get a really clear air.
Specifically, you're going to get an error about profiling and you're going to get a
link directly to go look at that profile. And that's going attain, going to contain
the information that you need. So you can see here the following assertions are not
valid metrics, HV requests, counts, property count does not exist. Available ones
are, and it tells you all the available things and even as documentation to more
about the assertion. So just really, really friendly how that was

works.

So let's fix our typo. Now, the one downside to the BioFire tests, they do slow your
test down because it needs to talk to Blackfire and have it create that profile. So
as a best practice, we usually like to isolate our performance tests from our normal
tests. So check this out. I'm going to copy the method name here, make a new one
called test and get get hub organization, Blackfire HTTP requests. And what we'll put
inside of here, I'll copy everything from the previous method, but really all we need
here is just, uh, creating the client, creating Blackfire config. And then inside of
a SERP blind fire, we just make the request. Once this black callback finishes, it
will then run the asserts on it. Now up here in our original method, we can simplify
things a little bit. So I can, I'll copy all my code that's inside the callback and
just replace it with what we had before.

So this is a very straightforward test that actually test the endpoint and this is
testing just the performance now above the Blackfire one we can say at group
Blackfire that's really powerful because now we can run just the Blackfire tests or
we could run our tests with a dash dash exclude dash group = Blackfire and that would
Jen just our one test, not the other one. You can see just one test, two assertions
on that and while we're here, another nice thing to do is say at requires extension
that Blackfire. So if for some reason somebody is running this on a system that
doesn't have Blackfire, it'll automatically Mark those tests as skipped a. My last
thing that I want to tell you, and we're going to talk more about this in the next
video, is that you really need to be careful with these assertions to try not to use
time based assertions.

One of the easiest things to do is create a test and say that the test should take
the request should take less than 500 milliseconds, but there's so much variability
with time. It depends on your machine ends on if the cache was warmed up, that those
tests tend to be very fragile and fail a lot. What you want to be looking for is more
specific things that will not just randomly change over time, like the number of HTTP
requests or the number of database requests or something else. So next, we're
actually going to talk about this metrics, these assertions system a bit more. You
can add these assertions and tests obviously, but you can also globally add these
assertions so that anytime you run a profile from any page, if that page makes too
many HTTP requests or it makes too many database requests, you're going to see a big
metric failure inside that profile.