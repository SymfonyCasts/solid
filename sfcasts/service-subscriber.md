# Service Subscribers

Because this service is instantiated on every request... it means that all four of
the objects in its constructor *also* need to be instantiated. That's not a *huge*
deal... except that two of these services *probably* wouldn't be instantiated
during a normal request *and* aren't even used unless the current request is a
login form submit. In other words, we're *always* instantiating these objects...
even though we don't need them!

How can we fix this? By using a service subscriber: it's a strategy in Symfony
that allows you to get a service you need... but *delay* its instantiation until -
and unless - you actually need to use it. It's great for performance. But, like many
things, it comes at a cost: a bit more complexity.

## Implementing ServiceSubscriberInterface

Start by adding an interface to this class: `ServiceSubscriberInterface`. Then
I'll move to the bottom of the file, go to the "Code -> Generate" menu - or
Command + N on a Mac - and select "Implement Methods" to generate the *one* method
this interface requires: `getSubscribedServices()`.

What does this return? An array of type-hints for all the services we need. For
this class, it's these four. So, return `EntityManagerInterface::class`,
`UrlGeneratorInterface::class`, `CsrfTokenManagerInterface::class` and
`OtherLongInterfaceName::class`. Uh,
`UserPasswordEncoderInterface::class`.

By doing this, we can now *remove* these four arguments. Replace them with
`ContainerInterface` - the one from `Psr\Container` - `$container`. When Symfony
sees the new interface and this argument, it will pass us a, sort of,
"mini-container" that holds the 4 objects we need. But it does this in a way where
those 4 objects aren't *created* until we use them.

Finish this by removing the old properties... and having just one: `$container`.
Set it with `$this->container = $container`.

## Using the Container Locator

Because those properties are gone, *using* the services looks a bit different. For
example, down here for `CsrfTokenManager`, now we need to say
`$this->container->get()` and pass it the type-hint `CsrfTokenManagerInterface::class`.

This will work *just* like before *except* that the `CsrfTokenManager` won't be
instantiated *until* this line is hit... and if this line *isn't* hit, it *won't*
be instantiated.

For `entityManager`, use
`$this->container->get(EntityManagerInterface::class)`, for
`passwordEncoder`, `$this->container->get(UserPasswordEncoderInterface::class)` and
finally, for `urlGenerator`, use
`$this->container->get->(UrlGeneratorInterface::class)`. I'll copy that and
use it again inside `getLoginUrl()`.

So, a *little* bit more complicated... but it *should* take less resources to
create this class. The question is: did this make *enough* difference for us to
*want* this added complexity? Let's find out. First, clear the cache:

```terminal-silent
php bin/console cache:clear
```

And warm it up:

```terminal-silent
php bin/console cache:warmup
```

## Comparing the Results

Move back over... I'll close some tabs and... refresh. Profile again: I'll call
this one: `[Recording] Homepage service subscriber`:
http://bit.ly/sf-bf-service-subscriber. View the call graph.

Excellent! Go back to the "Memory" dimension and search for "login". The call is
still here but it's taking a lot less memory *and* less time. Let's compare this to
be sure though. Click back to the homepage and go from the previous profile to
this one: http://bit.ly/sf-bf-service-subscriber-compare.

Nice! The wall time is down by 4%... CPU is down and memory *also* decreased...
but *just* a little bit.

So was this change worth it? Probably. But this doesn't mean you should run around
and use service subscribers *everywhere*. Why? Because they add complexity to your
code *and*, unless you have a specific situation, it won't help much or at all.
Use Blackfire to find the *real* problems and target those.

For example, we also could have made this same change to our `AgreeToTermsSubscriber`.
This class is *also* instantiated on every request... but rarely needs to do
its work. That means we are causing the `FormFactory` object to be instantiated
on every request.

But, go back to the latest profile... click to view the memory dimension... and
search for "agree". There it is! It took 1.61 milliseconds and 41 kilobytes to
instantiate this. That's... a lot less than the login authenticator. So, is
making this class a service subscriber worth it? For me, no. I'd rather get back
to writing features or fixing bigger performance issues.

Next, we can take a lot more control of the profiling process, like profiling just
a *portion* of our code *or* automatically triggering a profile based on
some condition, instead of needing to manually use the browser extension. Let's
talk about the Blackfire SDK next.
