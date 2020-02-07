# Assertions / Profile "Tests"

Adding specific assertions inside a test is really cool. But you can *also*
add assertions *globally*. What I mean is, whenever you trigger a *real* Blackfire
profile - like through your browser - you can set up *assertions* that you want
to run against that profile.

## Recommendations Versus Assertions

Actually, we've *already* seen a system that's *similar* to this. Click into
one of the profiles. Every profile has a "Recommendations" tab on the left, which
tells us changes that we should *probably* make. In reality, recommendations are
*assertions* in disguise! For example, the "Symfony debug mode should be disabled
in production" is displayed here because the *assertion* that
`metrics.symfony.kernel.debug.count` equals zero, failed. Yep, metrics are
*everywhere*!

I *love* that Blackfire gives us so many of these recommendations for free. But
we can *also* define our own. When we do, they'll show up under the assertions tab.

## Hello .blackfire.yaml

How do we do that? Just send an email to `assertion-requests@blackfire.io`, pay
$19.95 for shipping and handling, and wait 6-8 weeks for delivery. If you order
now, we'll *double* your order and include a signed-copy of the `blackfire-player`
source code printed as a book.

*Or* you can configure global assertions with a special Blackfire config file.
At the root of your project, create a new file called `.blackfire.yaml`. A few
different things will eventually go here - the first is `tests:`.

Honestly, the *trickiest* thing about writing assertions is trying to figure out...
a good assertion to use! Writing *time-based* assertions is the easiest... but
because they're fragile, we want to avoid those.

## Adding your first "Test"

Let's start with one we've already done. Say:
`"HTTP requests should be limited to 1 per page":`. Below this, add `path` set
to the regular expression `/.*`. This means that this assertion will be executed
against *any* profile for *any* page. Only want the assertion to run against a
single page or section? Use this option.

Now add `assertions:` with one item below. Go steal the metrics expression from
our test... and paste it here. Change this to be *less than* or equal to 1.

That's it! Let's try it out! Back in your browser... go back to our site, refresh,
and create a new profile. I'll call it: `[Recording] Added first assertion`.

Click into the call graph. Actually, go back. See this little green check mark?
That *already* tells us that this profile passed *all* our "tests". We can see
that on the "Assertions" tab: `metrics.http.requests.count` was 0, which *is* less
than or equal to 1.

So at this point, these "tests" are basically a nice way to create your *own*
custom recommendations. These will become *more* interesting later when we talk
about environments and builds.

Next, let's talk about a tool from the Blackfire ecosystem called the Blackfire
player. It's a command line utility that allows us to write simple files and
execute them as functional tests... *completely* independent of the Blackfire
profiling system. What we learn from it will form the foundation for the rest
of the tutorial.
