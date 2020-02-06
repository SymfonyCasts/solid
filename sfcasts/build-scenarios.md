# Builds with Custom Scenarios

A few chapters ago, we created this `scenario.bkf` file. It's written in a special
`blackfire-player` *language* where we write one or more "scenarios" that, sort
of, "crawl" a webpage, asserting things, clicking on links and even submitting
forms. This a simple scenario - the tool can do a lot more.

On the surface, apart from its name, this has *nothing* to do with the Blackfire
profiler system: `blackfire-player` is just a cool tool that can read these
scenarios and do what they say. At your terminal, run this file:

```terminal
blackfire-player run scenario.bkf --ssl-no-verify
```

That last flag avoids an SSL problem with our local web server. When we hit enter...
it goes to the homepage, clicks the "Log In" link and... it passes.

## Scenarios in .blackfire.yaml

This is cool... but we can do something *way* more interesting. Copy the entire
scenario from this file, close it, and open up `.blackfire.yaml`. Add a new key
called `scenarios` set to a `|` - that's a YAML way of saying that we will use
multiple lines to set this.

Below, indent, then say `#!blackfire-player` - that tells Blackfire that we're
about to use the `blackfire-player` syntax... which is the *only* format supported
here... but it's needed anyways. Below, paste the scenario. Make sure it's
indented 4 spaces.

The *cool* thing is that we can *still* execute the scenario locally: just replace
`scenario.bkf` with `.blackfire.yaml`:

```terminal-silent
blackfire-player run .blackfire.yaml --ssl-no-verify
```

The player is smart enough to know that it can look under this `scenarios` key
for our scenarios. But if you run this... error!

> Unable to crawl a non-absolute URI /. Did you forget to set an endpoint

Duh! Our `scenario.bkf` file had some `endpoint` config. You *can* copy this
into your `.blackfire.yaml` file. *Or* you can define the endpoint by adding
`--endpoint=https://localhost:8000`

```terminal-silent
blackfire-player run .blackfire.yaml --ssl-no-verify --endpoint=https://localhost:8000
```

Now... it works!

## Building the Custom Scenario

So... *why* did we move the scenario into this file? To find out, add and commit
the changes:

```terminal-silent
git add .
git commit -m "moving scenarios into blackfire config file"
```

And deploy the change:

```terminal
symfony deploy --bypass-checks
```

Once that finishes... let's go see what changed. First, if we just went to our
site and manually created a profile - like for the homepage - the new
`scenarios` config would have absolutely *no* effect. Scenarios don't do
*anything* to an individual profile. Instead, it affects *builds*.

Let's start a new build: I'll give this one a title: "With custom scenarios". Go!

Nice! This time, instead of that "Untitled Scenario" that tested the two URLs we
configured, it's using our "Basic visit" scenario! IT goes to the homepage, then
clicks "Log In" to go to that page.

Yep, as *soon* as we add this `scenarios` key to `.blackfire.yaml`, it
*no longer* tests these URLs. In fact, these are now meaningless. Instead, we're
now in the driver's seat: *we* control the scenario or scenarios that a build
will execute.

## Per Page Assertions/Tests

Even *better*, we have a lot more control *now* over the assertions - or "tests"...
Blackfire uses both words - that make a build pass or fail.

For example, the "HTTP requests should be limited to one per page" test will be
run against *all* pages in the scenarios - that's 2 pages for us right now.
But the homepage *also* has its *own* `assert`: that the SQL queries on this page
should be less than 30. If you look back at the build... we can see that assertion!
We can even click into the profile, click on "Assertions", and see both there.

So not *only* do we have a lot of control over *which* pages we want to test - even
including filling out forms - but we can *also* do custom assertions on a
page-by-page basis in addition to having global tests. I *love* that. And now I
can remove the comment I put earlier above the `assert`: now that we're running
this from inside an environment, this *does* work.

Next, let's use our power to *carefully* add more time-based assertions on a
page-by-page basis. We'll also learn how you can add your *own* metrics to,
well, write performance assertions about pretty much *anything* you can dream up.
