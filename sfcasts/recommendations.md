# Recommendations

Head back to the Blackfire dashboard... and click into the latest profile - the
one with our COUNT query improvement - http://bit.ly/sfcast-bf-profile2.

The critical path is now much less clear... there are kind of two critical paths...
but neither end in a node with a red background... which would indicate an
obvious issue. This might mean that there aren't any more easy performance
"wins" on this page anymore... it might be fast enough!

## Focus in Improvement, Not Absolute Time

The response time from the profile was 270 milliseconds. If you're not satisfied
with that, you need to remember two things. First, we're profiling Symfony in its
`dev` environment Symfony. Switching to `prod` would be faster... we'll do that
soon. And second, the time you see in a profile will *never* be quite as fast as
the real thing because when the probe is activated - the PHP extension that does
all the profiling - it slows things down. So don't obsess over any *absolute*
numbers. Instead, focus on finding ways to *improve* those numbers.

## Switching to Symfony's prod Environment

The function that takes up the most *exclusive* time is from something called
`DebugClassLoader`. Ah. Our local Symfony app is currently running in its `dev`
environment, which adds a lot of debugging tools, like the web debug toolbar.
That stuff also slows things down... which makes profiling less useful... because
the data is full of extra work that won't *really* be there in production. That
extra noise makes finding the *real* performance issues harder.

So, let's switch our app to the `prod` environment while profiling.

Open up `.env`, find the `APP_ENV` variable, and change it to `prod`. That will
make things more realistic... but it also means that after... pretty much *any*
change to our code, we'll need to clear & warm the cache. No big deal: at your
termianl, run:

```terminal
php bin/console cache:clear
```

and then:

```terminal
php bin/console cache:warmup
```

Ok, let's profile things again. I'll refresh... just to make sure the page is
working and... profile! I'll all this one `[Recording] Show page in prod mode`.
Cool! 106 milliseconds is a huge improvement. Click to open the call graph:
http://bit.ly/sf-bf-profile3

*Now* the function list and the call graph look a bit more useful. There's no
*super* problematic, red-background node on the graph... but the function that
takes up the most exclusive time - `PDOStatement::execute()` - at least makes
sense - that's executing database queries.

## Hello Recommendations

***TIP
Recommendations require a "Profiler" plan level or higher.
***

Back on our site, you *may* have noticed that each time we've profiled, a little
exclamation icon has shown up. If you clicked that, it would take you to a
"Recommendations" section of the profile. The exclamation point was telling us
that we're failing one or more Blackfire recommendations.

This is a great feature. Because Blackfire is written *for* PHP, it has special
knowledge of how queries are made, how Composer works, Symfony, Magento and so
many other things. The Blackfire team has *used* that knowledge to add a bunch
of things that they call "recommendations". I call them "sanity checks".

For example, Blackfire counted our queries and says:

> Hey! You've got a bunch of queries on this page... maybe you should try to
> have less than 10!

Yea, our 43 queries *is* pretty high. Does that mean we should immediately run
into our code and fix it? Nah. It's just a good thing to keep on your radar.

There's also a recommendation that Doctrine annotation metadata should be cached
in production. Honestly... I'm not sure why that's there - a Symfony app *should*
be caching those automatically when you're in the `prod` environment. And when
I tried to reproduce this later... it went away. Let's forget it for now: if it
comes back *later* when we deploy to production, then I *will* want to look into
it further.

## Composer Autoloader Recommendation

The *last* recommendation is *awesome*:

> The Composer autoloader class map should be dumped in production

If you don't know what something means, the cute question mark can help. And if
you look back at the function list, the *second* highest function *was* something
related to Composer's autoload system. Blackfire *nailed* that this is an issue.

You may already know this, but when you deploy, you're *supposed* to run a special
command - or add a special option - that tells Composer to dump an *optimized*
autoload file. Blackfire is telling us we forgot to do this locally.

Let's do this - it'll help clean up even *more* stuff on the profile. At your
terminal, run:

```terminal
composer dump-autoload --optimize
```

Perfect! Refresh the page... it works... and create another profile - I'll
call this: `[Recording] Show page after optimized autoloader`. Click to view
the call graph: http://bit.ly/sf-bf-profile4 and close the old one.

It's not *significantly* faster, but we've removed at least one heavy-looking
function call from our list. That will help us focus on any *real* problems.
Check out the recommendations now. Yea! The Composer one is *gone*.

Next, let's look deeper at what's going on with this `PDOStatement::execute` stuff.
Is our page fast enough? Or can we discover some further, hidden optimizations.
