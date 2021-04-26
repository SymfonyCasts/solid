# OCP: Autoconfiguration & tagged_iterator

When we went to the "submit" page, we got this gigantic error. It's the middle
that's most relevant:

> Cannot autowire service `SightingScorer`, argument `$scoringFactors` of method
> `__construct` is type-hinted array. You should configure its value explicitly.

That makes sense! We haven't told Symfony what to pass to the new argument of
`SightingScorer`.

## Manually Wiring the Argument

What *do* we want to pass there? An array of all of our "scoring factor" services.
The simplest way to do that is to configure it manually in `config/services.yaml`.
Down at the bottom, we want to configure the `App\Service\SightingScorer`... service
and we want to control its `arguments:`, specifically this `$scoringFactors` argument.
Copy that, paste, and this will be an array: I'll use the multi-line syntax. Each
entry in the array with be one of the scoring factor services. So
`@App\Scoring\TitleFactor`, copy that, paste... fix the indentation... then pass
`DescriptionFactor` and `CoordinatesFactor`.

This will now pass an array with these three service objects inside.

Try it again. Refresh and... the error is gone... and now it kicked us to the log-in
page. Copy the email above, enter the password, hit "sign in" and... beautiful!
The page loads. Let's give it a try. Fill in the details of your most recent
interaction with Bigfoot. Oh, but before I submit this, I'm going to add some
keywords to the description that I know our scoring factor is looking for.

Submit and... it works! Ah man, a believability score of only 10!? I really thought
that was a Bigfoot.

## Enabling Autoconfiguration

Before we talk more about OCP, on a technical, Symfony level, there is one other
way to inject these services. It's called a "tagged iterator"... and it's a pretty
cool idea. It's also commonly used in the core of Symfony itself.

Open up `src/Kernel.php`. I know, we almost never open this file. Inside, go to
Code -> Generate, or Command + N on a Mac, and select Override methods. Override
one called `build()`... let me find it. There it is.

This is a hook where we can do extra processing on the container while it's being
built. The parent method is empty... but I'll leave the parent call. Add
`$container->registerForAutoconfiguration()`, pass this
`ScoringFactorInterface::class`, then `->addTag('scoring.factor')`.

Thanks to this, any autoconfigurable service, which is all of our services,
that implements `ScoringFactorInterface`, will automatically be tagged with
`scoring.factor`. That `scoring.factor` is a name that I *totally* just made up.

This line, on its own, won't make any real change. But now, back in `services.yaml`
we can simplify: set the `$scoringFactors` argument to a special YAML syntax:
`!tagged_iterator scoring.factor`.

This says: please inject all services that are tagged with `scoring.factor`. So
autoconfiguration adds the tag to our scoring factor services... and this handles
passing them in. Pretty cool, right?

The only gotcha is that we need to change the type-hint in `SightingScorer` to be
an `iterable`. This won't pass us an array... but it *will* pass us something that
we can `foreach` over. As a bonus, it's a "lazy" iterable: the scoring factor
services won't be instantiated until and unless we run the `foreach`. Oh, and
change the property type to `iterable` also.

Next: now that we understand the type of change that OCP wants us to make to our
code, let's talk about why we should care - or not care - about OCP and when we
should and should not follow it.
