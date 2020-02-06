# Production Profile: Cache Stats & More Recommendations

We just profiled our *first* page on production, which is using the Blackfire Server
Id and Token for the Blackfire *environment* that we created.

## Profiles Belong to the Environment

Go to https://blackfire.io and click "Environments", open our new environment...
and click the "Profiles" tab. Yep! Whenever *anyone* creates a profile using this
environments credentials, it will now show up *here*: the profile *belongs* to
this environment. We haven't invited any other users to this environment yet, but
if we did, they would *immediately* be able to access this area *and* trigger new
profiles with *their* browser extension.

If you go to back to https://backfire.io to see your dashboard, the new profile
*also* shows up here. But that's purely for convenience. The profile *truly*
belongs to the environment - you can even see that right here - but Blackfire
places *all* profiles that *I* create on this page... to make life nicer.

Click the profile to jump into it. Of course... this basically looks *exactly*
like any profile we created on our local machine. But it *does* have a few
differences.

## Caching Information

Hover over the environment name to find... "Cache Information". We talked about
this earlier: it shows stats about *various* different caches on your server and
how much space each has available. Now that we're profiling production, this data
is *super* valuable!

For example, if your OPcache filled up, your site would start to slow down
*considerably*... but it's not very obvious when that happens. It's not like there
are alarms that go off once PHP runs out of OPcache space. But with this, you can
easily see how things *really* look, right now on production. If any of these are
full or nearly full, you can read documentation to see which settings you need to
tweak to make that cache bigger.

## Quality & Security Recommendations

The *other* thing I want to show you is under "Recommendations" on the left.
There are 3 *types* of recommendations... and we have one of each: the first is
a security recommendation, the second is a *quality* recommendation and the third
a *performance* recommendation. Only the *performance* recommendation comes standard:
the other two required an "Add on"... which I didn't have until I started using
my organization's plan.

As always, to get a *lot* more info about a problem and how to fix it, you can
click the question mark icon.

## Converting Recommendations into ASsertions

One of my *favorite*  things about recommendations is that you can *easily* turn
any of these into an *assertion*. If you click on assertions, you'll remember that
we created one "test" that said that every page should have - at *maximum* - one
HTTP request.

We configured that inside of our `.blackfire.yaml file`: we added `tests`, configured
this test to apply to every URL, and leveraged the metrics system to write an
expression.

Back on the recommendations, click to see more info... then scroll down. *Every*
recommendation contains code that you can copy into your `.blackfire.yaml` file
convert that recommendation into a *test* or "assertion".

That *might* not seem that important at first: it *does* look like doing that would
simple *move* this from a "warning" under the "Recommendations" to a "failure"
under "Assertions". That's cool... but at this point, that's just a visual difference.

*But*! In a few minutes, we'll discover that these assertions are *much* more
important than they seem. To see why, we need to talk about the *key* feature and
superpower of environments: *builds*.
