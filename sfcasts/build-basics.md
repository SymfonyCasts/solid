# Automatic Performance Checks: Builds

Head back to https://blackfire.io, click "Environments" and click into our
"Sasquatch Sightings Production" environment.

Interesting. By default, it takes us *not* to the profiles tab... but to a tab
called "Builds". And, look on the right: "Periodic Builds": "Builds are started
every 6 hours"... which we could change to a different interval.

Further below, there are a bunch of "notification channels" where you can tell
Blackfire that you want to be *notified* - like via Slack - of the results of this
"build" thingy.

## Hello Builds

Ok, what the *heck* is a build anyways? To find out, let's trigger one manually,
then stand back and see what happens. Click "Start a Build". The form pre-fills
the URL to our site... cool... and we can apparently give it a title if we want.
Let's... just start the build.

This takes us to a new page where.... interesting: it's running an
"Untitled Scenario"... then it looks like it went to the homepage... and created
a profile?

Let's... back up: there are a *lot* of interesting things going on. And I *love*
interesting things!

First, we've *seen* this word "scenario" before! Earlier, we used the
`blackfire-player`: a command-line tool that's *made* by the Blackfire
people... but can be used totally outside of the profiling tool. We created a
`scenario.bkf` file where we defined a *scenario* and used the special
`blackfire-player` language to tell it to go to the homepage, assert a few things,
then click on the "Log In" link and check something else. At that time, this was
a nice way to "crawl" a site and test some things on it. The "build" used the
same "scenario" word. That's not an accident. More on that soon.

## Build "URLs to Test"

The *second* important thing is that this profiled the *homepage* because, when
we created our environment, we configured one "URL to test": the homepage. That's
what the build is doing: "testing" - meaning *profiling* - that page.

Let's add a second URL. One other page we've been working on a lot is
`/api/github-organization`: this JSON endpoint. Copy that URL and add it as a
*second* "URL to test". Click save... then manually create a *second* build.

Like before, it creates this "Untitled Scenario" thing. Ah! But *this* time it
profiled *both* pages! The build *also* shows up as green: the build "passed".

This is a *critical* thing about builds. It's not *simply* that a build is an
automated way to create a profile for a few pages. That would be pretty worthless.
The *real* value is that you can write performance *tests* that cause a build to
pass or fail.

Check it out "1 successful constraint" - which is that "HTTP Requests should
be limited to 1 per page". Hey! That's the "test" that *we* set up inside
`.blackfire.yaml`!

The *real* beauty of `tests` is *not* that the "Assertions" tab will look red when
you're looking inside a profile. The *real* beauty is that you can configure
performance *constraints* that should pass *whenever* these builds happen. If a
build *fails* - maybe because you introduced some slow code - you can be notified.

## Build Log: blackfire-player

But there's even *more* cool stuff going on. Near the bottom, click to see the
"Player output". Woh! It shows us how builds work behind-the-scenes: the Blackfire
server *uses* the `blackfire-player`!

Look closer: it's running a *scenario*: `visit url()`, `method 'GET'`, then
`visit url()` of `/api/github-organization`. It's a bit hard to read, but this
*converted* our 2 "URLs to test" into a scenario - using the same format as the
`scenario.bkf` file - then *passed* that to `blackfire-player`. You can even see
it *reloading* both pages multiple times to get 10 samples. That's one of the
options it added in the scenario.

So with just a *tiny* bit of configuration, Blackfire is now creating a build
every six hours. Each time, it profiles these two pages and, thanks to our one test,
if either page makes more than one HTTP request, the build will fail. By setting
up a notification, we'll know about it.

The fact that the build system uses `blackfire-player` makes me wonder: instead
of configuring these URLs, could we *instead* have the build system run our custom
scenario file? I mean, it's a *lot* more powerful: we can visit pages, but also
click links and fill out forms. We can *also* add *specific* assertions to *each*
page... in addition to our one "global" test about HTTP requests.

The answer to this question is... of course! And it's where the build system
*really* starts to shine. We'll talk about that next.

## History & Graphs from Automated Builds

But before we do, I want you to see what the build page looks like once it's
had enough time to execute a few automated builds. Let's check out the
SymfonyCasts environment. Woh! It's graph time! Because this environment has
a *history* of automated builds, Blackfire creates some super cool graphs:
like our cache hit percentage and our cache levels. You can see that my
`OPcache Interned Strings Buffer` cache is full. I really need to tweak some
config to increase that.

I can also see how the different URLs are performing over time for wall time,
I/O, CPU, Memory & network as well as other stuff. We can click to see more
details about any build... and even look at any of its profiles.

*Anyways*, next: let's make the build system smarter by executing our custom
scenario.
