# Testing a Build Compared to the Last Build

A *long* time ago in this tutorial, we talked about Blackfire's *truly* awesome
"comparison" feature. If you profile a page, make a change, then profile it
again, you can *compare* those two profiles to see *exactly* how that change
impacted performance.

When you use the build system, you can do the *exact* same thing... and you can
*even* write "tests" that compare a build to the *previous* build. For example,
you could say:

> Yo! If the wall time on the homepage is *suddenly* 30% *slower* than the
> previous build, I want this build to fail.

## Adding a Comparison Test with percent()

*How* can we do that? It's dead simple. Add a new global metric - how about
"Pages are not suddenly much slower" - and set this to run on every page:
`path: /.*`. For the assertion, we can use a special function called percent:
`percent(main.wall_time) < 30%`:

[[[ code('a136e55781') ]]]

That's it! There's also a function called `diff()`. If you said
`diff(metrics.sql.queries.count) < 2` it means that the *difference* between
the number of SQL queries on the new profile minus the old profile should be
less than 2.

Let's see what this looks like! Find your terminal and commit these changes:

```terminal-silent
git status
git add .
git commit -m "adding global wall time diff assert"
```

Now... deploy!

```terminal-silent
symfony deploy --bypass-checks
```

## Comparison Tests: Not for Manual Builds

But... bad news. If we waited for that to finish deploying... and then triggered
a new custom build... that test would *not* run. In fact, I want you to see
that. Wait for the deploy to finish - okay, good - then move back over and start
a build.

This does what we expect: it executes our scenario and creates 2 profiles.
Look at the 3 successful constraints for the homepage: we see the other global
test about "HTTP requests should be limited"... but we don't see the new one.
What gives?

So... when you create a build, you can specify a "previous" build that it should
be compared to by using an internal "build id". Our project is too new to see it,
but this happens automatically with "periodic" builds: our comparison assertion
*will* execute on periodic builds.

***TIP
Triggering builds via a webhook requires an Enterprise plan.
***

But when we create a manual build... there's no way to specify a "previous" build...
which is why the comparison stuff doesn't work. *Fortunately*, since I don't want
to wait 12 hours to see if this is working, there is *another* way to trigger
a build: through a webhook. Basically, if you want to create a build from outside
the Blackfire UI, you can do that by making a request to a specific URL. And when
you do that, you can *optionally* specify the "previous build" that this new build
should be compared to.

## Automatic Build on Deploy

This webhook-triggered-build is *especially* useful in one specific situation:
creating a build each time you *deploy*. If you did that correctly, your
comparison assertion would compare the latest deploy to the *previous* deploy...
which is pretty awesome.

Because we're using SymfonyCloud, this is dead-simple to set up.

Find the Blackfire SymfonyCloud documentation and, down here under "Builds",
I'll select our environment. Basically, by running this command, we can tell
SymfonyCloud to send a webhook to create a Blackfire build *each* time we deploy.

Copy it, move over to your terminal and... paste:

```terminal-silent
symfony integration:add --type=webhook --url='https://USER:PASS@blackfire.io/api/v2/builds/env/aaaabbee-abcd-abcd-abcd-c49b32bb8f17/symfonycloud'
```

Hit enter to report all events and enter again to report all states. For the
environments - this is asking which *SymfonyCloud* environments should trigger
builds. Answer with just `master` - I'll explain why soon.

And... done! Let's redeploy our app. Oh, but before we do, refresh our builds
page. Ok, we have 5 builds right now. Now run:

```terminal
symfony redeploy --bypass-checks
```

This should be pretty quick. Then... go refresh the page. Yes! A new build -
number 6 - triggered by SymfonyCloud. And it *passes*. Awesome! Let's redeploy
again:

```terminal-silent
symfony redeploy --bypass-checks
```

When that finishes... there's build 7! But to see the comparison stuff in action,
I need to do a *real* deploy so that the next build is tied to a *new* Git sha.
I'll do a meaningless change, commit, then deploy:

```terminal-silent
git commit -m "triggering deploy" --allow-empty
symfony deploy --bypass-checks
```

## Seeing the Compared Builds

Actually, I could have skipped changing *any* files and committed with
`--allow-empty` to create an empty commit. When this finishes... no surprise!
We have build 8!

On *this* build, it's super cool: each profile has a "Show Comparison" link to open
the "comparison" view of that profile compared to the *same* profile on the build
from the *last* deploy - which - if you click "latest successful build" - is
build 7.

Back on build 8, click the "Show 4 successful constraints" link. There it is!
We can see our "Pages are not suddenly *much* slower" assertion! It's comparing
the wall time of this profile to the one from the last build.

Click to open up the profile... and make sure you're on the Assertions tab.
I love this: 2 page-specific assertions from the scenario, and 2 global assertions:
one using the `percent()` function.

The "Recommendations" *also* got a bit better: Blackfire automatically has some
built-in recommendations using `diff`: this *recommends* that the new profile
should have less than 2 *additional* queries compared to the last build. It
*looks* like it failed... but that's just because the *other* part of this
recommendation - not making more than 10 total queries - failed.

Next: what about running builds on your staging server so you can catch performance
issues before going to production? Or what about executing Blackfire builds on
each pull request? We can *totally* do that - with a *second* environment.
