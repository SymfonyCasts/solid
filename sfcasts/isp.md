# Interface Segregation Principle

Coming soon...

Ready for principle number four, it's the interface segregation principle. It says
clients should not be forced to depend on interfaces that they do not use. That's not
a bad definition, but I want to clarify that word interface. It's not necessarily
referring to a literal interface. It's referring to the abstract concept of an
interface, which generally means the public methods of a class. Even if it doesn't
technically implement an interface. So let me try to give this an even simpler
definition, build small focused classes instead of big giant classes. This definition
reminds me a lot of the single responsibility principle and that's true, but the
interface segregation principle kind of looks at this from the other direction.
Again, the original definition is clients should not be forced to depend upon
interfaces that they do not use. So for example, suppose you've accidentally built a
giant class called API client with a ton of methods on it.

Then somewhere in your code,

You need to call just one of those methods. This other class is called the client
because it, because it is using our giant API client class. And unfortunately, even
though it only needs one method from the API client, it needs to inject the whole
giant object.

It's forced to depend on

An object whose interface whose public methods are many more than it actually needs.
Why is this a problem? Let's answer that question a bit later after we play with a
real world example, because management has asked us to make a change to our
believability score system, if a big foot setting receives a score of less than 50
points, but it has three or more photos, we will give it a boost, five extra points
per photo. This was not a change that we anticipated. Our scoring factors have the
ability to add to the score, but they don't have the ability to,

I see the final score and then modify it. No problem. Let's add a second method to
the interface that has the ability to do that. Let's call it. How about pelvic
function adjust score. And in this case, what it's going to receive is the int final
score that's been calculated. And then of course the big foot sighting that we're
working on and it will return the new to final score. And you can add some PHP doc
above this to better explain the purpose of this method. In a minute, we're going to
call this method from inside of our siting score. After the initial scoring is done,
but first let's open photo factor and add the new bonus logic.

So at the bottom, I'm going to co go to co-generation or Command + N on a Mac select
"Implement Methods" and implement the adjusted score. And then very simply I'll say
photos count = citing arrow, get images. We're not religious getting images, but
actually count those images. Then if the final score is less than 50 and photos count
is greater than two or greater than equal than three, then the final score should get
plus = photos, count times five. And at bottom we will return the file score.
Perfect. So there is our new bonus logic, but now what do we do to all the other
classes that implement photo factor interface, scoring factor interface,
unfortunately for PHP to even run, we do need to add this new method to each class,
but we can just make it return the final score. So at the bottom of coordinates
factor, I'll go back to code generate or command and go to emblem methods, generate
adjust score, and we're just going to return final score. And now I can copy this
close coordinates factor with this at the bottom of description factor. And then also
at the bottom of title factor.

Finally, we can update sighting score at a second loop after calculating the score.
So for each this scoring factors as scoring factor this time, we're gonna say score
equals

Scoring factor

Aero adjust score, and we'll pass in the score. And then we'll pass in the big foot
sighting and done. By the way, you might argue correctly that the execution order of
these scoring factors is now relevant, but we're not going to worry about that for
simplicity though, there is a way to give a tag service, a higher priority in Symfony
so that it is passed in earlier or later than the other scoring factors.

Yeah.

So if at this point something is itching you, that might be because we just violated
the open closed principle. We had to modify the score method in order to add this new
behavior, but that's okay. It highlights the tricky nature of OCP. We didn't
enticingly this kind of change. You can't close a class against all kinds of changes.
You can only close it against the changes that you CA correctly predicted.

Okay.

Looking at our new interface and the ma and the classes that implement it, you can
probably feel that it's not ideal for all of these classes need to need to implement
this method, even though they don't really care about it. Next we're going to make
this even more obvious re factor to a better solution, and finally discuss the key
takeaways from the interface segregation principle.

Okay.

