# Recommendations

Coming soon...

Let's go back to blood blackfire and. Look at our latest.

Profile that we just did after making the top query.

So now the critical path is much less clear you can actually just kind of two
critical paths here but they don't end in any like really big issue and there might
just mean that there's no more no really big performance winds on this page anymore.
Might be that we're good enough. By the way it's 270 milliseconds if you're not
satisfied with that we have to remember two things right now we're in the development
environment Symfony. So switching to production would be faster. And also it's
absolute time will never be quite as fast as it really is because the PHP extension
the probe slows things down. So do a paid attention to this time here. But this is
not an absolute measurement you want to think about performance improvements more
than absolute targets.

Now if we look though the number one thing here is something about a 
`DebugClassLoader`. OK. So one of the issue is that in Symfony right now we're we're profiling
things locally of course but we're in Symfony's development environment which loads
lots of debugging tools like the web debug toolbar down here. So that's going to make
profiling less useful because some of this stuff which won't be there in production
gets in the way. And so it's harder to see what the performance rooms are. So what
I'm to do is we are actually going to switch to the production environment right now
while we profile.

So I'm open on my `.env` file here for an `APP_ENV` and change that to `prod`. So things
are a little more realistic now. Whenever you do that pretty much after any change
you make.

We now need to go for a project and say `cache:clear` 

```terminal-silent
php bin/console cache:clear
```

and then `cache:warmup`.

```terminal-silent
php bin/console cache:warmup
```

All right. So let's profile again over here just refresh for good measure. BLACK
FIRE.

Profile. This time we'll name it. Name it again. Show page after.

In proud mode and you can see it's way faster 106 milliseconds sluts view to call.
Hit that call graph and I'll close the other one. All right great. So a little more
realistic. Hundred six milliseconds and now we are seeing a little bit more
information `PDOStatement::execute()` is actually the biggest deal before we look at this
though.

No so we're here. I mean look at these recommendations I actually saw this over a
second I saw there was this little exclamation thing that's actually telling you that
there are some recommendations that fail. This is a really cool thing. So because
black fires written for PDP and because black fire specializes in it's by far also
has special functions in it for a Symfony magenta layer but whatever using it it
actually looks at the all the raw data and based on what you're using for us we're
using doctrine and we're using Symfony. It has special recommendations that I'll give
you. So for example down here it says hey you should probably execute less ask you
all queries. They have a recommendation. Every page should have less than 10. We have
43 which is pretty high. Does that mean we should absolutely run and change it. No
but it's a good thing to have in mind and maybe we would on optimize those queries
down lower.

There's also a thing up here about doctrine annotations should be cached and
production. Make sure you can ignore that for now. That shouldn't be there. Maybe a
false positive I'm not sure. In a Symfony project documented that it is cached by
default. But here's what I'm really interested in the composer auto little class and
app should be dumped in production.

And if you don't know what any of this stuff means you can click on this little
question mark is going to give you more details about what's going on. But. Even if
you're done with it is you go back to where the function lets you can see the number
two item on here is actually a composer autoload thing. So you may not know that one
of the things that you're supposed to do when you run production is to run a special
composer command that optimizes its autoloader. So this is right. Like if this were
this is just something I didn't think about and it's got a recommendation over here
that says hey this is probably something that you should do.

So let's do it because it's gonna make our call graph any even clear because we get
this item off of it somebody move over to a terminal again for an proposer dump dash
auto loader dash dash. Optimize. A couple different commands that'll do that same
thing. An hour a composer dump dash auto load. Dash dash optimize. Perfect art speed
makeover and now refresh that page let's make another profile just getting got fun
recording. Page. After. Mind. On a loader. And I view the call graph on that what's
done up close the old one and good.

It's actually not significantly faster but we kind of have that we have that's off of
our list we can focus on the real problem and if you look recommendations that
recommendation is gone we still may want to act and its other recommendations but at
least that's gone. So next let's actually look deeper at what is going on with this
PTO statement execute stuff and see if we can optimize it further and spoiler alert
we're actually going to make an optimization that's not going to work like fire is
going to help us notice that.

Oh, actually, before we do that, one of the things I wanted to mention is if you
hover over the upper left here, uh, give you some basic information about, um, uh,
this particular profile. What's really useful though is this cast information over on
the right. This is gonna be more important when we go to production. But if you've
ever wanted, for example, if you have enough op cache memory, uh, configure it on
your server later, when we actually use this in production, it'll tell us what our op
cache levels are. So those things called real path cache, PCR, cache and cache,
internal string buffers. Um, those are all optimizations you've made to your P to P
to I and I. And on production, you can see real values here to see if these are too
low and if they need to be tweaked instead of you actually needing to guess. Anyways,
let's now go and do the thing I said over here. 

```terminal
composer dump-autoload --optimize
```