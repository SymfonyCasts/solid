# Expectations/Tests with Blackfire Player

Coming soon...

and we see a little bit
more verbosity and he and get request in both of those things. So that's pretty sweet
to make this better. We can add a test to this. So we can say `expect`.

And then inside of here we can give it an expression. So one of the things we can say
is `status_code() == 200` and I'll copy that and put it in both places. And we can have
as many expect lines as we want. So now I want you to run the test.

```terminal-silent
blackfire-player run scenario.bkf --ssl-no-verify -v
```

Yep. It's still
passes. So two things I want to say about this. The first thing is that just like
with art metrics expression here, once you get inside of the expecting, this is an X
in expression language, it's actually the Symfony expression language. So this, it's
a JavaScript like language. Um, yeah. The second thing I want to say is if you search
this page for

if you search this phage or `status_code` and keep searching. There we go. Eventually
you find information about these expectations. There are a bunch of functions built
in and get the current URL headers. Um, you can use a CSS. We'll see. I'll show you
that in a second to actually look at the CSS structure of your page and a number of
things internally. If you're familiar with it, you're actually working with Symfonys
Dom crawler object. And you'll see an example of that in a second. So this has really
good examples of some more complex things that you can do. Um, inside of there. We're
not going to become experts in Blackfire player. I just want to get you a nice
introduction into how it works.

so let's look at what a failing example is. I'm gonna use this `css()` function which
allows us to use a CSS selector. And we want to do here is let's look at this table
and assert that this table, uh, has 500 rows, which it definitely does not have 500
rows. So what we can do here is we can look for, there we go. Let me scroll down a
little bit. We look for this `<tbody>` that has this `js-sightings-list` class and then
count the `<tr>` elements. So basically we'll go to the homepage and then I'll say `expect`
and then `css()`. And instead of hero pass at the CSS selector, which is going to be
`"tbody.js-sightings-list tr` and this would turn a `DomCrawler` object, which
has a `count()` method on it is `> 500` all right, so let's see what happens.

```terminal-silent
blackfire-player run scenario.bkf --ssl-no-verify -v
```

That should not pass. And when we run, it doesn't in the error is really nice. I can
see that it didn't fail 500 because the count was actually equal to 25 so let's go
back now and just change that to 10 this is actually dynamic data, so we don't really
know how many rows are going to be in there. So we'll say there should probably be at
least 10 at any time.

Now when I run that

```terminal-silent
blackfire-player run scenario.bkf --ssl-no-verify -v
```

it passes. Another thing I really like is the errors on this. So
if I do a typo, like a type `count()` to just `ount()` and rerun the tests,

```terminal-silent
blackfire-player run scenario.bkf --ssl-no-verify -v
```

you get unable to
call method, I don't have object `Symfony\Component\DomCrawler\Crawler`. So that's a
nice hint that you're working with this object. So you can go look up on the internet
to see what methods that they actually has. And then you can use the expression
language to dive in deeper. So let's change that back to `count()`. So as you've seen,
this has nothing to do with the Blackfire profiler. This is just a fun little tool
for being able to visit pages, click on links, fill out forms and do assertions about
uh, about your, your site. But this idea of creating these scenarios is about to
become very important in Blackfire. It actually, there is a one little integration
with um, the lackfire player and Blackfire itself. And that is that you can add
performance assertions here. So when you as `expect`, these are actually like test
assertions, but you can also say `assert`.

And then here you can do one of the types of things that you do inside of your
Blackfire tests. So here we can say something like, uh, `metrics.sql.queries.count`
is less than or equal to 30. Cause maybe we just say, Hey, the homepage
should always have less than 30 queries. Now if we ran this right now,

```terminal-silent
blackfire-player run scenario.bkf --ssl-no-verify -v
```

it would still
pass. But if you started playing with this value, like you said, less than one and
rerun it again,

```terminal-silent
blackfire-player run scenario.bkf --ssl-no-verify -v
```

you would notice that it's still passing even though this page is
making more than one query. And the reason is that this, this special functionality
is not going to work until we use something in Blackfire called environments, which
is a super awesome, powerful topic. And that we are going to start talking about
next. So leave this here for now, but it's not going to work, but let's get this
working. Next, we're going to deploy this site to production and started talking
about how we profile things on production, and that's going to include introducing us
to this very important Blackfire environments system.
