# Expectations/Tests with Blackfire Player

We just used `blackfire-player` to execute our first "scenario". It's pretty
simple: it goes to the homepage then clicks the "Log In" link:

[[[ code('e3eaee2e22') ]]]

It works... but... we're not *doing* anything after we visit these pages. The
*true* power of `blackfire-player` is that you can add *tests* to your scenario -
or even scrape pages and save that data somewhere.

## Adding an Expectation/Test to a Page

To add a "test" - or "assertion", or "expectation"... I *love* when things have 5
names... - say `expect` followed by - you guessed it! - an *expression*!
`status_code() == 200`. Copy that and add it to the login page as well:

[[[ code('9ea20a81d4') ]]]

Ok, try `blackfire-player` again!

```terminal-silent
blackfire-player run scenario.bkf --ssl-no-verify -v
```

Woo! It still passes and *now* it's starting to be useful!

## What's Possible in the expect Expression?

Let's break this down. First, *just* like we saw with the `metrics` stuff:

[[[ code('03970f47cc') ]]]

This is an *expression* - it's Symfony's ExpressionLanguage once again - basically
JavaScript. And second... this expression has a *ton* of built-in functions.

Search the `blackfire-player` docs for "status_code"... and keep searching until
you find a big function list. Here it is. Yep, we can use `current_url()`,
`header()` to get a header value and many others. The `css()` function is
especially useful: it allows us to dig into the HTML on the page. We'll use that
in a minute. The docs also have good examples of how to do more complex things.
But we're not going to become Blackfire player experts right now... I just want
you to get comfortable with writing scenarios.

## Asserting HTML Elements with css()

Let's try to write a *failing* expectation to see what it looks like. Let's see...
we could find this table and assert that it has more than 500 rows... which it
definitely does *not*. Let's find a CSS selector we can use... hmm. Ok, we could
look for a `<tbody>` with this `js-sightings-list` class and then count its
`<tr>` elements.

Back inside the scenario file, add another expect. This time use the `css()`
function and pass it a CSS selector: `tbody.js-sightings-list tr`:

[[[ code('b1b3369bed') ]]]

Internally, The `blackfire-player` uses Symfony's `Crawler` object from the `DomCrawler`
component, which has a `count()` method on it. Assert that this is `> 500`.

Let's see what happens!

```terminal-silent
blackfire-player run scenario.bkf --ssl-no-verify -v
```

And... yes! It fails - with a nice error:

> The `count()` of that CSS element is 25, which is not greater than 500.

Go back and change this to 10:

[[[ code('9c2788b557') ]]]

The data is dynamic data... so we don't *really* know how many rows it will have.
But since our fixtures add more than 10 sightings... and because there will probably
be at least 10 sightings if we ever ran this against production, this is probably
a safe value.

Try it now:

```terminal-silent
blackfire-player run scenario.bkf --ssl-no-verify -v
```

All better!

## Typos in Expressions

Another thing that `blackfire-player` does well is its *errors* when I... do
something silly. Make a typo: change `count()` to `ount()`:

[[[ code('83aab1d706') ]]]

And rerun the scenario:

```terminal-silent
blackfire-player run scenario.bkf --ssl-no-verify -v
```

> Unable to call method `ount` of object `Crawler`.

That's a *huge* hint to tell you what object you're working with so you can figure
out what methods it *does* have. Change that back to `count()`:

[[[ code('9a09765c16') ]]]

## Performance Assertions in the Scenarios?

So... `blackfire-player` has *nothing* to do with the Blackfire profiler. It's
just a useful tool for visiting pages, clicking on links and adding expectations.
But... if it *truly* had nothing to do with the profiler, I probably wouldn't
have talked about it. In reality, the concept of "scenarios" is *about* to become
*very* important - it's a fundamental part of a topic we'll talk about soon:
Blackfire "builds".

And actually, there is one *little* integration between `blackfire-player` and
the profiler: you can add *performance* assertions to your scenario. To do that,
instead of `expect`, say `assert` and then use any performance expression you want: the
same strings that you can use inside a test. For example:
`metrics.sql.queries.count < 30`:

[[[ code('a8ebecf563') ]]]

When we execute this:

```terminal-silent
blackfire-player run scenario.bkf --ssl-no-verify -v
```

It *does* still pass. But if you played with this value - like set it to `< 1`
and re-ran the scenario:

```terminal-silent
blackfire-player run scenario.bkf --ssl-no-verify -v
```

Hmm, it *still* passes... even though this page is *definitely* making more than
one query. The reason is that the `assert` functionality *won't* work inside
a scenario until we introduce Blackfire "environments" - which we will soon.
They are one of my absolute *favorite* parts of Blackfire.

For now, I'll leave a comment that this *won't* work until then:

[[[ code('93686b00f1') ]]]

Next, let's deploy to production! Because once our site is deployed, we can
*finally* talk about cool things like "environments" and "builds". You can use
anything to deploy, of course, but *we* will use SymfonyCloud.
