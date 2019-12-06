# Service Subscriber

Coming soon...

[inaudible]

real quick, I want to go back to the prod environment. We're going to look at a
really subtle performance thing. Uh, that's a little bit specific to Symfony's
container. So I've changed the environment back to prod. I'm not going to run 
`cache:clear` `cache:warmup`. 

```terminal-silent
php bin/console cache:clear
```

```terminal-silent
php bin/console cache:warmup
```

And also a 

```terminal
composer dump-autoload --optimize
```

so that we have a fully optimized, uh, page that we can profile. All right, go back over to the
homepage. I'll refresh to make sure it works good. And then let's profile, I'll call
this one [Recording] Homepage prod, and then we'll click to view the, all
right. So what I want to look at specifically here is actually not the timeline, but
the, uh, the memory and overall this request is pretty fast. So if you look at the
memory, what you're going to notice is that because it's already so optimized, the
biggest things are, for example, `Composer\Autoload\includeFile`.

And you know, you can't really, op can only optimize that so far at some point,
composer is just needs to load files, um, just needs to load files and load files.
Uh, puts things in memory. The interesting thing about this is most of what you're
going to see down the memory here is actually related to the containers. So see this
line here is actually the main line that instantiates the core services in the
container. And if you go down here, the fourth one, this is the function and the
container that instantiates the entity manager. The next one is the function in the
container that instantiates doctrines connection. So it's kind of a fascinating way
to see how long it actually takes to instantiate objects, which is not something that
you always think about. And if you go down a little bit here, there it is, you're
eventually gonna find something called `getLoginFormAuthenticatorService`. It's not
a particularly, it's about the 10 down in a list. And you can see it takes, it only
takes 2.56 milliseconds, takes about um, 500 kilobytes.

Let me show you the code for this. This is the class that's responsible for
authenticating us when we use the login form. So it's in 
`src/Security/LoginFormAuthenticator.php`. Now, the key thing about this authenticator 
is that due to the way Symfony security system works, it's instantiated in the supports 
Methodist called on every single request, but the really this service doesn't do any 
work unless we're on the login page and this is a post request. That's the only time 
it really actually processes the form submit in every other situation supports 
returns false and none of these other methods are called.

What's interesting though is that it's taking three milliseconds, which is not a lot,
but 500 kilobytes on every single request just to check the URL and return. The
reason is that instantiating the service is actually a little bit heavy. In order to
do a to the login form authenticator Symfony needs to make sure the `EntityManager`
is instantiated, the `UrlGenerator`  and Sanjay did the `CsrfTokenManager` is
instantiated and they `UserPasswordEncoder` is instantiated and if those services
depend on other services, it needs to instantiate those. The point does it sometimes
the instantiation of a service can be heavy and while this one isn't that heavy, it's
a really good example of this. Now in the case of the `EntityManager` and the `UrlGenerator`
those are probably going to need to be instantiated during this request at
some point. Anyways, those are two services that we probably use on just about every
page load, but the `CsrfTokenManager` and the `UserPasswordEncoder`, it was probably
aren't.

So those two services are being instantiated on every single request, even though we
don't really need them. So we're going to fix this to show how to fix this problem.
We're going to refactor this service into a service subscriber. That's a way where
you can get the dependencies that you need into your service, but only actually
instantiate them if and when you need them. It's a great thing for performance, but
like many things, that adds a little bit of complexity. So here's why we do it. We
need to implement a new interface called here called `ServiceSubscriberInterface`.

Now I'm gonna go down to the bottom of the class. I'll go to the Code -> Generate
menu or Command + N on a Mac go to "Implement Methods" and implement the get the
method we need, which is `getSubscribedServices`. What's your return? Here is an
array of all of the type pins that you need, so our service depends on these four
services, so we're actually going to return these four type pens. So down the bottom
here I'll return `EntityManagerInterface::class`, `UrlGeneratorInterface::class`,
`CsrfTokenManagerInterface::class` and `UserPasswordEncoderInterface::class`,
As soon as we do this, what this allows us to do is remove all of these
arguments up here and instead add a `ContainerInterface` argument. The one from 
`Psr/Container` `$container`. What Symfony is going to do is it's going to pass, it's going
to look at these four type ins here. It's going to take those four services and then
put them into this container object. It's going to be kind of a mini container that
just contains those four services and you'll see how to use that in a second. So now
I'm going to move all the properties. Just have one call `$container` and say
`$this->container = $container`.

Now whenever we use those services, it's going to be a little bit different. So for
example, down here for `CsrfTokenManager`, that's not a property anymore. So now we
need to say `$this->container->get()` in the pass at `CsrfTokenManagerInterface::class`
That's how you fetch things out. And the important thing is that
this actually the CSRF token manager won't be instantiated until this line is hit. So
on most requests we never get, we never actually use this line so it won't be
instantiated if it's not needed for `EntityManager`, 
`$this->container->get(EntityManagerInterface::class)` and then a couple other spots, 
password encoder. `$this->container->get(UserPasswordEncoderInterface::class)` and 
then down to more for the UrlGenerator `$this->container->get->(UrlGeneratorInterface::class)`

Now copy that and use it down here for `getLoginUrl()`. Perfect. So a little
bit more complicated, but now it's going to be a E it's going to take less work to
instantiate this service or it's like everything. The question is did that make
enough difference for us to care? So let's move over. Let's clear the cache again.
Warm up the cache again 

```terminal-silent
php bin/console cache:clear
```

```terminal-silent
php bin/console cache:warmup
```

and then spin over. So we can compare this. I'm actually
going to close the last to refresh the page. Let's profile call this one recording
homepage service subscriber view, the call graph on that.

Excellent. The first thing we'll look at here is under memory, if we search for
login, you can see it's still on there. Um, but it looks like it's taking a lot less
memory and less time. Let's compare this though to be sure. So I'll go back to the
homepage here. I'll go from to and yeah, cool. You can check it out. So the memory
time went down by four milliseconds. Um, CPU went down a little bit and memory, which
is actually what we are trying to optimize went down ever so slightly. So was this
worth it? Maybe, maybe not. But you are going to run into situations where this is
going to be worth it. So I wanted you to know the pattern. By the way, we also could
have done this for our agree to term subscriber. This is another situation where you
have a class that's instantiated on every single request because it's an event
subscriber.

But in the vast majority of the situations, this class actually doesn't do anything.
So instantiating the form factory on every single request for example is wasteful.
The reason I didn't do this one is that if you go back and even on, if you go back to
the um, the updated profile we did and go to memory, if you search here for agree to
term subscriber, it isn't here but it was already, it was already a little bit
smaller and our already wasn't taking a lot of work to instantiate this. So I could
tell this by looking at this originally that this was not going to be a spot that was
where the optimization was going to have a lot. So this is a service subscriber is a
really cool pattern. And in some cases it can be a lifesaver, but in many cases it's
going to add complexity and not be worth it. So make sure you actually need it before
you use it.