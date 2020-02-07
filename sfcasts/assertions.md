# Assertions / Profile "Tests"

Adding assertions to specific situations inside a test is really cool. But
you can *also* configure assertions *globally*. What I mean is, when you trigger
a *real* Blackfire profile - like through your browser - you can setup some *tests*
that you want that profile to run against.

## Recommendations Versus Assertions

Actually, we've *already* seen a system that's *very* similar to this. Click into
one of the profiles. Every profile has a "Recommendations" tab on the left, which
tell us changes that we should *probably* make to this page. In reality,
recommendations are just *assertions* in disguise. For example, the "Symfony debug
mode should be disabled in production" shows up because the *assertion* that
`metrics.symfony.kernel.debug.count` equals zero, fails.

What's *awesome* is that Blackfire gives us so many of these recommendations for
free. But we can *also* define our own. When we do, they'll show up under this
assertions tab.

## Hello .blackfire.yaml

How do we do that? With a special Blackfire config file. At the root of your
project, create a new file called `.blackfire.yaml`. A few different things will
eventually go into this file. The first is `tests:`.

Honestly, the *trickiest* thing about writing assertions is trying to figure out...
a good assertion to use! Writing *time-based* assertions is the easiest... but
because they're fragile, we want to avoid those.

## Adding your first "Test"

Let's start with one we've already done. Say:
`"HTTP requests should be limited to 1 per page":`. Below this, add `path` set
to the regular expression `/.*`. This means that this assertion will be executed
against *any* profile for *any* page. Only want the assertion to run against a
single page or a section? This is the key.

Now add `assertions:` with one item below. Go steal the metrics expression from
our test... and paste it here. But change this to be *less than* or equal to 1.

That's it! Let's try it out! Back in your browser... go back to our site, refresh,
and create a new profile. I'll call it: `[Recording] Added first assertions`.

Click into the call graph. Actually, go back. See this little green check mark?
That *already* tells us that this profile passed *all* our "tests". We can see
that on the "Assertions" tab: `metrics.http.requests.count` was 0, which *is* less
than or equal to 1.

So at this point, these "tests" are basically a nice way to create your *own*
custom recommendations. These will become *more* interesting later when we talk
about environments and builds.

Next, let's talk about a tool from the Blackfire ecosystem called the Blackfire
player. It's a command line utility that allows you to write simple files and
execute them as functional tests... *completely* independent of the Blackfire
profiling system. It's a neat tool and will be *super* important later when we
talk about builds & scenarios.
