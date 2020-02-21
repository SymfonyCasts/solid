# Per-Page Time Metrics & Custom Metrics

We know that the scenario will be executed against our *production*
server only. If we profiled a *local* page, this stuff has no effect.
That means that the results of these profiles *should* have *less* variability.
Not *no* variability: if your production server is under heavy traffic, the
profiles might be slower than normal. But, it will have less variability than trying
to compare a profile that you created on your local machine with a profile created
on production: those are totally different machines and setups.

***TIP
I also recommend adding `samples 10` to each scenario. This will then use
10 samples (like normal Blackfire profiles) and further reduce variability:

```
    visit url("/")
        name "Homepage"
        samples 10
        ...
```
***

## Cautiously Adding Time-Based Assertions

This means that you can... *maybe* add some time-based assertions... as long as
you're conservative. For example, on the homepage, let's `assert` that
`main.wall_time < 100ms`.

By the way, *most* metrics start with `metrics.` and you can look on the timeline
to see what's available. A *few* metrics - like wall time and peak memory - start
with `main.`.

Anyways, as you can see inside Blackfire, our homepage on production
*normally* has a wall time of about 50ms... so 100ms is *fairly* conservative.
But time-based metrics are *still* fragile. Doing this *will* likely result in
some random failures from time-to-time.

Let's commit this:

```terminal-silent
git add .
git commit -m "adding homepage time assertions"
```

And deploy:

```terminal-silent
symfony deploy --bypass-checks
```

## Custom Metrics

While that's deploying, I want to show you a *super* powerful feature that we
won't have time to experiment with: custom metrics. Google for "Blackfire metrics".
In addition to the timeline, this page *also* lists *all* of the metrics that
are available.

But you can also create your *own* metrics inside `.blackfire.yaml`. In addition
to `tests` and `scenarios`, we can have a `metrics` key. For example, this
creates a custom metric called "Markdown to HTML". The *real* magic is the
`matching_calls` config: any time the `toHtml` method of this made-up
`Markdown` class is called, its data will be *grouped* into the `markdown_to_html`
metric.

That's powerful because you can immediately use that metric in your tests. For
example, you could assert that this metric is called exactly zero times - as a
way to make sure that some caching system is *avoiding* the need for this to ever
happen on production. Or, you could check the memory usage... or other dimension.

You can use some pretty serious logic to create these metrics: making it match
only a specific *caller* for a function, OR logic, regex matching and ways to
match methods, calls from classes that implement an interface and many other
things. You can even create *separate* metrics for the *same* method based on which
*arguments* are passed to them. They went a little nuts.

## Checking the Time-Based Metric

Anyways, let's check on the deploy. Done! Go back - I'll close this tab -
and let's create a new build. Call it "With homepage wall time assert". Start build!

And... it passes! This time we can see an extra constraint on the homepage: wall
time needs to be less than 100ms. If it's *greater* than 100ms and you have
notifications configured, you'll know immediately.

Next: now that we have this idea of builds being created every 6 hours, we can
do some cool stuff, like *comparing* a build to the build that happened before
it. Heck we can even write *assertions* about this! Want a build to fail if
a page is 30% *slower* than the build before it? We can do that.
