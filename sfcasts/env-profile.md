# Production Profile: Cache Stats & More Recommendations

We just profiled our *first* page on production, which is using the Blackfire Server
Id and Token for the *environment* we created.

## Profiles Belong to the Environment

Go to https://blackfire.io, click "Environments", open our new environment...
and click the "Profiles" tab. Yep! Whenever *anyone* creates a profile using this
environment's credentials, it will now show up *here*: the profile *belongs* to
this environment. We haven't invited any other users to this environment yet, but
if we did, they would *immediately* be able to access this area *and* trigger new
profiles with *their* browser extension.

If you go to back to https://blackfire.io to see your dashboard, the new profile
*also* shows up here. But that's purely for convenience. The profile *truly*
belongs to the environment. You can even see that right here. But Blackfire
places *all* profiles that *I* create on this page... to make life nicer.

Click the profile to jump into it. Of course... this looks *exactly* like any
profile we created on our local machine. But it *does* have a few differences.

## Caching Information

Hover over the profile name to find... "Cache Information". We talked about
this earlier: it shows stats about *various* different caches on your server and
how much space each has available. Now that we're profiling on production, this
data is *super* valuable!

For example, if your OPcache filled up, your site would start to slow down
*considerably*... but it might not be very obvious when that happens. It's not
like there are alarms that go off once PHP runs out of OPcache space. But thanks
to this, you can easily see how things *really* look, right now, on production.
If any of these are full or nearly full, you can read documentation to see which
setting you need to tweak to make that cache bigger.

## Quality & Security Recommendations

The *other* thing I want to show you is under "Recommendations" on the left.
There are 3 *types* of recommendations... and we have one of each: the first is
a *security* recommendation, the second is a *quality* recommendation and the third
a *performance* recommendation. Only the *performance* recommendations come
standard: the other two require an "Add on"... which I didn't have until I
started using my organization's plan.

As always, to get a *lot* more info about a problem and how to fix it, you can
click the question mark icon.

## Converting Recommendations into Assertions

One of my *favorite*  things about recommendations is that you can easily
*convert* any of these into an *assertion*. If you click on assertions, you'll
remember that we created one "test" that said that every page should have - at
*maximum* - one HTTP request.

We configured that inside of our `.blackfire.yaml` file: we added `tests`,
configured this test to apply to every URL, and leveraged the metrics system to
write an expression.

Back on the recommendations, click to see more info on one of these... then
scroll down. *Every* recommendation contains code that you can copy into
your `.blackfire.yaml` file to *convert* that recommendation into a *test*...
or "assertion".

That *might* not seem important right now... because so far, it looks like doing
that would simply "move" this from a "warning" under "Recommendations" to a
"failure" under "Assertions"... which is cool... but just a visual difference.

*But*! In a few minutes, we'll discover that these assertions are *much* more
important than they seem. To see why, we need to talk about the *key* feature and
superpower of environments: *builds*.
