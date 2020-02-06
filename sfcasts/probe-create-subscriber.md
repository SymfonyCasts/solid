# Creating an Automatic Probe Early in your Code

Once we determine that we want to *create* a probe dynamically in our code,
we *really* want to do that as *early* as possible so that Blackfire can
"instrument" as much of our *code* as possible.

## Generating the Event Subscriber

In Symfony, we can do that with an event subscriber... which we will *generate*
to be super lazy. First, in `.env`, make sure that you're back in the `dev`
environment. Then, find your terminal and run:

```terminal
php bin/console make:subscriber
```

Call it `BlackfireAutoProfileSubscriber`... and we want to listen to
`RequestEvent`: Go check out the code
`src/EventSubscriber/BlackfireAutoProfileSubscriber.php`.

So, when this `RequestEvent` happens - which Symfony dispatches *super* early
when handling a request, we want to create & enable the probe. Copy all
of the `$shouldProfile` code, remove it from the controller and paste it here.

## Creating the Prove in the Subscriber

Now add `$request = $event->getRequest()`. To make this *only* profile the GitHub
organization AJAX call - whose URL is `/api/github-organization` - set
`$shouldProfile` equal to `$request->getPathInfo() === '/api/github-organization'`.
In a real app, I would add *more* code to make sure `$shouldProfile` is *only*
true on the *very* specific requests we want to profile.

Now I'll re-type the `t` on `Client` and select the correct `Client` class so
that PhpStorm adds that `use` statement to the top of the class for me. Thanks
PhpStorm!

But before we try this, I want to code for one edge case: if *not*
`$event->isMasterRequest()`, then `return`. It might not be important in your
app, but Symfony has a "sub-request" system... and the short explanation is that
we don't want to profile those: they are not *real* requests... and would make a
big mess of things.

Ok, let's try this! I'll close a tab... then refresh the homepage... which
causes the AJAX request to be made. You can see it's slow. Now reload the list
of profiles on Blackfire... there it is! Open it up.

And... oh wow, oh weird! 281 *microseconds*. Give this a name:
`[Recording] Auto from subscriber`: http://bit.ly/sf-bf-broken-auto-profile

This profile is... broken. That's 281 *microseconds* - so .281 milliseconds.
And the entire profile is just the `Probe::enable()` call itself!

## Probe Auto-Close Too Early

What happened!? Well... remember: the `$probe` object automatically calls
`close()` on *itself* as soon as that variable is garbage collected... which
happens at the end of the subscriber method. That means.... we profiled exactly
*one* line of code.

The solution is to call `$probe->close()` manually... which - more importantly -
will require us to store the `Probe` object in a way where PHP *won't* garbage collect
it too early.

So here's the goal: call `$probe->close()` as *late* as possible during the request
lifecycle. We can do this by listening to a *different* event: when
`TerminateEvent::class` is dispatched - that's *very* late in Symfony - call
the `onTerminateEvent()` method.

I'll hit an Alt + Enter shortcut to create that method... then add the argument
`TerminateEvent $event`.

To be able to call `$probe->close()`, we need to store the probe object on a property.
Add `private $probe` with some documentation that says that this will either be
a `Probe` instance from `Blackfire` or `null`. Update the code below to be
`$this->probe = $blackfire->createProbe()`.

Finally, inside `onTerminateEvent`, if `$this->probe` - I should *not* have put
that exclamation point, that's a mistake - then `$this->probe->close()`.

If you assume that I did *not* include the exclamation point... then this makes
sense! *If* we created the probe, then we will close it. Problem solved. And...
*really*... the fact that we set the probe onto a *property* is the real magic:
that will prevent PHP from garbage-collecting that object... which will prevent
it from closing itself until we're ready.

## Increasing the Event Priority

While we're here, let's make this a little bit cooler. Change `onRequestEvent`
to be an array... and add `1000` as the second item. This syntax is... weird. But
the result is good: it says that we want to listen to this event with a priority
of 1000. That will make our code run even *earlier* so that even *more* code will
get profiled.

## Configuration: Name your Profile

Oh, and there's one other cool thing we can do: we can *configure* the profile.
Add `$configuration = new Configuration()` from `Blackfire\Profile`. Thanks to
this, we can control a number of things about the profile... the best being
`->setTitle()`: `Automatic GitHub org Profile`. Pass this to `createProbe()`.

That's it! Let's see how things whole thing works. Back at the browser, I'll
close the old profile... and refresh the homepage. Once the AJAX call finishes...
reload the Blackfire profile list. Ah! We were too fast - it's still processing.
Try again and... there it is!

Open it up! http://bit.ly/sf-bf-auto-profile-subscriber

*Much* better. A few things might still look a *bit* odd... because we're
still not profiling *every* single line of code. For example, `Probe::enable()`
seems to wrap everything. But all the important data is there.

To avoid making a *million* of these profiles as we keep coding, I'll go back to
the subscriber and avoid profiling entirely by setting `$shouldProfile = false`.

Next: you already write automated tests for your app to help *prove* that key
features never have bugs. You... ah... do write tests right? Let's... say you
do. Me too.

Anyways, have you ever thought about writing automated tests to prevent
*performance* bugs? Yep, that's possible! We can use Blackfire *inside* our test
suite to add performance *assertions*. It's pretty sweet... and now that we
understand the SDK, it will feel great.
