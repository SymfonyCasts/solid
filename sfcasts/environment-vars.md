# Blackfire Environment Variables

Often, your production server will have different - hopefully *bigger* - hardware
than your staging server... which means that your staging builds may run slower
than production. That's going to be a problem if you have *time* based
metrics: the wall time of a build may be less than 100ms on production... but
*more* than that on staging. That means the staging builds will always fail.
Bummer!

## Hello Build Variables

No worries. To help, each environment can define *variables*. Check it out: inside
the metric expression, I'll add a set of parentheses around the `100ms` and then
say *times* and call a `var()` function. I'll invent a new variable: `speed_coefficient`
and give it a *default* value - via the 2nd argument - of 1.

*Now*, when this assertion is executed, it will assert that the wall time is less
than 100ms *times* whatever this `speed_coefficient` variable is. What *is*
`speed_coefficient`? It's *totally* something I just made up and it is *not* set
anywhere. Where *do* we set it? Inside our Blackfire environment!

Copy the variable name and go into the Non-Master environment. On the right,
near the bottom, click the pencil icon to edit our variables. Add the variable
set to... how about 2: that will allow the staging server to be *twice* as slow.

Do we *also* need to set this inside the "Production" environment? Nope: I'll just
let it use the default value of 1.

Let's try it! Spin back over to your terminal, add the change... and commit:

```terminal-silent
git add .
git commit -m "adding speed_coeffient variable for wall time assert"
```

As a reminder, we're on the `some_feature` branch. So when we run:

```terminal
symfony deploy --bypass-checks
```

We're deploying to *that* environment.

## Seeing the Variable in Action

When that finishes... move back over to the Blackfire environment, refresh and...
hello new build! Look inside. There are two cool things. First, under the homepage,
you can see the `speed_coefficient` in action - the little "2" tells us the value
it's using. So, in reality, it's asserting that 50.8ms is less than *200*
milliseconds.

## Feature Branch Comparisons

The *other* thing I want you to notice is that, if you go back to the builds page,
we have now built the `some_feature` branch *twice*. When you click on the second,
newer build, it has the *comparison* stuff! It allows us to *compare* this build
to the *previous* commit on the *same* branch. This allows you to see -
commit-by-commit - *when* a feature started having performance problems.

And... that's it for the Blackfire tutorial! I hope you *loved* this nerdy trip into
the *depths* of performance as much as I did. Blackfire can give you a *lot* of info
immediately... or you can *really* dive in and make it *sing*. Personally,
I love having the builds and this performance history for SymfonyCasts.com. Oh,
and a special thanks to [Jérôme Vieilledent](https://github.com/lolautruche) - I
almost *definitely* just slaughtered his name - for his endless patience
answering my hundreds of Blackfire questions.

And as always, if *you* have any questions... or we didn't explain something you
wanted to know about... or you want a cake recipe... we're here for you in the
comments. If you have any *serious* performance wins, we would *love* to hear
about them.

Alright friends - I wish you a *speedy* day! Seeya next time!
