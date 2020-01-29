# Creating an Auto Probe Early in your Code

Coming soon...

and it's just that easy. The one downside of this is that it only profiles a small
bit of your code. The problem is that until we actually call create pro PHP, the PHP
extension, the pro but doesn't know that it's supposed to be collecting data. So it
only starts collecting data starting right here. To make matters worse, I think it
might even stop when it garbage collects that pro variable. So if you want to provide
more code, a great way to do this in Symfony is with a subscriber. So I'm gonna go
over to my terminal and run

```terminal
php bin/console make:subscriber
```

let's generate a subscriber first. Make sure that we are back in the dev environment.
I forgot to switch back to that. Uh, and I want the make commands to be available.

Let's spend over now then

```terminal
php bin/console make:subscriber
```

and let's call this the `BlackfireAutoProfileSubscriber`

and we are going to listen to this along and request event class. So I'll put that in
there. Perfect. Let's go check that out. `src/EventSubscriber/BlackfileAutoProfileSubscriber`
So on request event, which is an event that happens very early on
and Symfony, that's where we're going to start the profile. So I'm going to copy all
of my shit profile code, remove it from my controller and put it into here. Now we
can actually improve this a little bit. I'm going to say `$request = $event->getRequest()`
and we can say is uh, to make this a little more realistic, we'll only
profile for that specific Ajax request, which is has the URL
`/api/github-organization` So I can say, `$shouldProfile = $request->getPathInfo() ===`

that URL and I'll read type the TM client to make sure
we get the use statement for that. Now that should be ready, but before we try it,
I'm going to cover one other edge case situation. I'm going to say, if not
`$event->isMasterRequest()`, then `return`. It's might not be important for you,
but Symfony has this system of making sub requests. We don't want to profile those. We want to
profile just the actual request itself and ignore this being triggered multiple
times. That can make things screwy.

All right, so let's try this.
I'll close that and let's go over here and refresh. Once again, that will make the
Ajax request. You can see it's slow, should be triggering a new profile

and it did.

So let's open up that guy. Oh boy and Oh, weird. 281 microseconds. So let me give
this a name real quick. AutoFarm subscriber.

One of the things I've learned with the SDKs, you do need to be a little bit careful
when you work with it. This somehow actually confuse the profile in some really
strange things happen. You can see the entire request is actually talking about bra
`Blackfire\Probe::enable` to get the best results. What we actually want to do is always
make sure that we close the probe. So basically after we're done, we want to say
`$probe->close()`, but we don't want to do it here. We want to do it at the end of the
request. This is actually a cause because of the garbage control. So doing that is
actually going to be really easy. We're just going to listen to a second event and
I'll call it `TerminateEvent::class`. I'll say `onTerminateEvent()`,

I'll do a little Alt + Enter shortcut
and it create method. You create that method and I'll add a type
hint `TerminateEvent $event`. So now what we can do is we'll just set that probe on a
property. So here I'll say `private $probe` even get a little documentation here that
says this is either going to be a `Probe` instance from `\Blackfire` or `null`, because if we're
inside the inside of her, if save, and I'll say
`$this->probe = $blackfire->createProbe()`
So it may be said or may be no, and very simply in `onTerminateEvent`, we can
say if `$this->probe`,

`$this->probe->close()`. So if we did open that and create that probe, then we will close
it. This should fix the problem. Also, while we're here, I'm going to make this a
little bit cooler, this `onRequestEvent`. I'm going to turn that into an array and
add this and add a second image to the right, which is `1000` this syntax looks a
little strange, but we're saying is we're giving this event a priority of 1000 so
that will make this event run even earlier in Symfony so that even more code will get
profiled. And also before we try this, I'm gonna add one other little nice thing
here, which is that optionally you can create some configuration about the profile.
So I'm gonna say `$configuration = new Configuration()` from `Blackfire\Profile`
And what I like to do is a, there's a number of different things that you
can do inside of here. A lot of them are more accustomed. What I like to do is at
least `setTitle()`. So here we can say automatic get hub org profile and we passed
this down here on recall call `createProbe()`.

All right, so let's see how all that stuff works together. So let's move over here.
I'm going to close that profile, refresh the page. That should trigger a profile for
this AJAXrequest. Let's go over refresh and
okay. See currently processing because I refresh too quickly. There it is. Automatic
get up org profile. If I opened this up in a new tab, yeah, this looks much better.
This looks like a normal, uh, thing. You can see the main on request event. Um, you
do see some weird things with create probe and enables since they wrap everything. So
it's not going to look quite as clean as a normal request, but you do get all the
basic information that you want.

So to avoid making a million of these profiles, let's spin over here and I'm going to
add a [inaudible]

`$shouldProfile = false` We'll stop our testing code from profiling.

All right, next let's talk about running Blackfire in tests. This is actually a
really cool, we can run our unit tests or we can run our functional tests and
actually very use Blackfire to verify that certain performance things happens. This
is the best way to fix and prevent future performance bumps from happening. And now
that you understand the SDK system, how that system works is going to make a lot of
sense.

Okay.
