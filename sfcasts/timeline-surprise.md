# Timeline: Finding a Hidden Surprise

One of the big spots on the timeline is the `RequestEvent`. It's purple because
this is an event: the *first* event that Symfony dispatches. It happens before
the controller is called... which is pretty obvious in this view.

Let's zoom in: by double-clicking the square. Beautiful! What happens inside this
event? Apparently... the routing layer happens! That's `RouterListener`. You can
also see `Firewall`: this is where authentication takes place. Security is a complex
system... so being able to see a bit about what happens inside of it is pretty
cool. At some point... it calls a method on `EntityRepository` and we can see
the query for the `User` object that we're logged in as. Pretty cool.

## The Hidden Slow Listener

There's one more big chunk under `RequestEvent`: something called
`AgreeToTermsSubscriber`... which is taking 30 milliseconds. Let's open that
class and see what it does: `src/EventSubscriber/AgreeToTermsSubscriber.php`.

Ah yes. Every now and then, we update the "terms of service" on our site. When
we do that, our lovely lawyers have told us that we need to require people to
agree to the updated terms. *This* class handles that: it gets the authenticated
user and, if they're not logged in, it does nothing. But if they *are* logged in,
then it renders a twig template with an "agree to the terms" form. Eventually,
*if* the terms have been updated since the last time this `User` agreed to them,
it sets that form as the response instead of rendering the *real* page.

We haven't seen this form yet... and... it's not really that important. Because
we *rarely* update our terms, 99.99% of the requests to the site will *not*
display the form.

So... the fact that this is taking 30 milliseconds... even though it will almost
*never* do anything... is kind of a lot!

## Blue Memory Footprint

Oh, and see this blue background? I love this: it's the memory footprint. If we
trace over this call - this is about when the `AgreeToTermsSubscriber` happens -
the memory starts at 3.44 megabytes... and finishes around 4.46. That's 1 megabyte
of memory - kinda high for such a rarely-used function.

The point is: this method doesn't take *that* long to run. And so, it may not have
shown up as a performance critical path on the call graph. But thanks to the timeline,
this invisible layer jumped out at us. And... I think it *is* taking a bit too
long.

## Fixing the Slow Code

Back in the code, the mistake I made is pretty embarrassing. I'm using some pretend
logic to see whether or not we need to render the form. But... I put the check too
late! We're doing all the work of rendering the form... even if we don't use it.

Let's move that code all the way to the top. Ah, too far - it needs to be after
the fake `$latestTermsDate` variable.

That looks better. Let's try it! I'll refresh the page. Profile again and call
it `[Recording] Homepage authenticated fixed subscriber`: http://bit.ly/sf-bf-timeline-fix

Let's jump straight to view the Timeline... double-click `RequestEvent` and this
time... `AgreeToTermsSubscriber` is gone! We can see `RouterListener` and `Firewall`...
but *not* `AgreeToTermsSubscriber`. That's not because our app isn't *calling*
it anymore: it is. It's because Blackfire hides function calls that take almost
no resources. That's great.

Next, we know that we can write code inside a function that is slow. But did you
know that sometimes even the *instantiation* of an object can eat a lot of resources?
Let's see how that looks in Blackfire and leverage a Symfony feature - service
subscribers - to make instantiation lighter.
