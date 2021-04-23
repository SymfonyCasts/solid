# Open–Closed Principle

Coming soon...

The second solid principle is the open closed principal or O C P ready for the super
understandable technical definition. Here we go. A module should be open for
extension, but closed for modification. Since at least to me, that definition makes
no sense. At least at first, let's try a different definition. OCP says you should be
able to change what a class does without actually changing its code. If that sounds
crazy or impossible, it's actually not. And we'll learn a common pattern that makes
this possible, but as a word of warning, Oh, OCP is not my favorite solid principle
and it should be used carefully, but more on that once we've got a good understanding
of what OCP really is.

Now, the whole point of big foot sightings is for people to be able to submit their
sightings, to help sort through all these many settings. We've developed a
proprietary algorithm to give each setting a believability score. Ooh, how is that
implemented? Open source service citing score dot PHP. After you submit a setting we
call score and all the logic lives right in this class, we look at the latitude and
longitude name, title, and description factors for keywords. We call each of these
different scoring factors. Now we've received a change request. We need to add a new
scoring factor where we look at the photos included with the post. The easiest way to
implement this would be to go down here, create a new private method called evaluate
photos, and then call it up from here and the score method, but doing that would
violate OCP because we would be changing our existing code in order to add the new
feature. OCP tells us that a class's behavior should be able to be modified by adding
new codes

Only.

How is that even possible? Well, the truth is that our class already violates OCP for
this change, to be able to add it, to be able to add this new feature without
changing our existing code, we needed to write our class differently from its very
beginning, since it's a little late for that, let's walk through the OCP mindset and
refactor this class so that it does follow the rules. First, we need to identify
which kind of change we want to close this class against. In other words, which kind
of change do we want to allow a future developer to be able to make without modifying
this class based on the change request, we need to be able to add more scoring
factors without modifying the score method itself. Since there's no way to do that
right now, we're going to change this method in order to close it to this change. How
by separating each scoring factor into its own class and injecting them into the
sighting score service step one is to create an interface that describes what each
scoring factors should do. Maybe do directory and source for organization

Called the scoring.

And then inside of that, a new interface about a PHP class then changes the interface
called scoring factor interface. Each factor should just, just need one method. Let's
call it score. It will accept a big, the big foot sighting object that it is going to
score. And then it's going to return an integer, which will be the amount to that
should be added to that sighting score. Perfect. And you could add some documentation
above this to describe it better. Step two is to create a new class for each of these
scoring factors and make it implement that new interface. For example, let's copy,
evaluate, coordinates, and then I'm actually going to delete it and then go into this
directory and create a new class called coordinates factor. We'll make it implement
the scoring factor interface. I'll paste the method it okay to add the, and then
rename this to score and make it public.

And yeah, that's perfect that already returns an integer. So this is done. Let's
repeat this now, close this for evaluate title one credit class called title factor,
implement the scoring factor, interface paste my method, make it public, rename it to
score. And then one more, which is going to read a copy, evaluate description, delete
that and create our last factor for now, which is going to be description factor,
which will once again, implement scoring factor interface paste in the logic, clean
things up and rename it to score. That looks happy. Okay, now we can work our magic
back in siting score, add a construct method that will accept an array of scoring
factors.

I'll hit all enter on this and go to initialize properties to create that property
and sentence. And then I'm going to go above this and actually add a little bit of
extra peach doc. We know that this is not just going to be an array of anything. It's
going to be a array of scoring factor, interface objects. So I can use that to help
out my editor down in score. Instead of calling each method individually, we can now
loop over these scoring factors and say score plus = scoring factor,->score capacity
citing. That's it. Well, at least for this class, our site is score is now closed to
new scoring factors, which is a fancy way of saying that if we need to add a new
scoring factor into our system, we are going to be able to do it without modifying
this class. Of course, if you go over and I should try this, it's not going to quite
work yet. I'll hit submit right here. And, um, immediately we see an error can not
resolve arguments, citing score. Uh, the middle of the error is the most important
part cannot auto wire service citing score argument scoring factors of method
construct is type into the rate. You should configure its value explicitly. We
haven't told Symfony which objects we want to pass to our siting score.

The easiest thing we we can do, the easiest solution is to configure this manually
and config /services diameter. So down here we will say app /service /citing score.

Sure.

Then arguments. And then we are going to specifically configure the scoring factors
argument. So I'll copy that. And then this is going to be set to an array. So below
this we'll use dashes and here we're going to inject each of those services. So at
app /scoring /title factor, then I'm going to copy that, paste it and fix my
indentation. There. We have description factor and we have coordinates factor. So
that should pass an array, which is what these dashes mean of these three services
for your fresh now, yes, the error has gone. It did kick us over to the log-in page.
Let's copy the email above and password hit, sign in and beautiful. The page loads.
Let's give it a try.

We'll fill in some details and hit upload. And before actually fill this in. Actually
I can go up here,

But in some key words in the description that will help with this. Okay.

And all right, something a and beautiful

Big foot believability score of 10.

Come on. Only a score of 10, four. Talk more about OCP on a technical Symfony level.
There is one other way to inject these services into, um, the score. In fact, your
services in deciding score, it's called a tagged iterator. It's pretty cool. And it's
really commonly done in the core of Symfony. First open up source kernel dot PHP. I
know we almost never even open up this file. Uh, I'm going to go to code generate or
command and in the Mac and go to override methods in override one called build. Let
me see if I can find it. There it is.

This is a hook where we can do some extra processing on a container. The paramedic is
actually empty, but I'll leave the parent call now at the container->register for
auto configuration, pass this scoring factor interface, call Hong Kong class. The
name is Reiner face and then->add tag, scoring that factor. Thanks to this, any auto
configurable services, which is all of our services that implement scoring factor
interface will automatically be tagged with scoring that factor, which is a name I
totally just made up this on its own. Won't make any real change, but now back in
services.yaml we can simplify. So for scoring the scoring factors argument, we're
going to set this to exclamation point. That's a special syntax in Yammel, uh,
tagged_iterator scoring dot factor. This says please inject all services that are
tagged with scoring that factor. Pretty cool, right? The only gotcha is that we need
to change the type hint in scoring setting score to be an Iterable. It's not actually
going to pass us a real Ray, but it's still going to be something that we can loop
over. So I'll also change the Iterable

Up here

Next, now that we understand the type of changes that OCP wants us to make, let's
talk about why we should care or not care about OCP and when we should and shouldn't
follow it.

Okay.

