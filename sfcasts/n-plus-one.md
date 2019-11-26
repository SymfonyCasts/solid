# The N+1 Problem & EXTRA_LAZY

At this point, I'm pretty happy with the show page that we've been profiling. So
let's look at something different: let's profile the homepage at
https://localhost:8000/.

Ok, this page has a list of all of the sightings... and on the right, that shows
some SymfonyCasts repository info from GitHub. Let's refresh... though... that's
not really needed - and profile! I'll call this one: `[Recording] Original homepage` -
https://bit.ly/sf-bf-homepage-original.

Ok! 165 milliseconds! Let's view the call graph. Well... this looks familiar!
We have the *same* number 1 exclusive-time function as before:
`UnitOfWork::createEntity()`. In *that* situation, it meant that we were querying
for too many items and so Doctrine was *hydrating* too many objects. Is it the
same problem now? And if so, why? Can we optimize it?

Time to put on our profiling detective hats. Let's follow the hot path! We enter
`MainController::homepage()` and render a template... so the problem is coming
from our *template*. Interesting. Next `_sightings.html.twig` is rendered... and
then something called `twig_length_filter` executes `loadOneToManyCollection`, which
is from Doctrine. Let's do some digging in that template:
`templates/main/_sightings.html.twig`.

We saw that it was referencing something called `twig_length_filter`. Search the
template for `length`. Ah: `sighting.comments|length`.

## Finding the N+1 Problem

Look back on the site: one of the things it does is prints the number of
*comments* for each article. The `length` filter counts how many items are in
`sighting.comments`, which is a database relationship from the `big_foot_sighting`
table to the `comment` table.

If you're not familiar with Doctrine, when you call `sighting.comments`, at that
moment, Doctrine queries for *all* of the comments for that specific `BigFootSighting`
record. I'll open up `src/Entity/BigFootSighting.php`. Yep, we're accessing the
`comments` property, which is a `OneToMany` relationship to `Comment`.

The point is: for *each* `BigFootSighting` that we are rendering, Doctrine is making
an *extra* query to fetch *all* the comments for that sighting. This is basically
the classic N+1 problem. If we want to print 25 `BigFootSighting` rows, in addition
to the 1 query to fetch the 25 rows, the system will *also* make 25 *additional*
queries to fetch the *comments* for each sighting. That's 25 + 1 queries.

You can see this in the SQL queries in Blackfire: we have one query from
`big_foot_sighting` - the query above is related to the pagination logic - then
*25* queries from the `comment` table.

## Counting with fetch=EXTRA_LAZY

Okay, we have identified the problem: we are not only making a lot of queries...
but those queries are *also* fetching *all* the comment data... just to count them.
Silliness!

One *simple* solution might be... just to tell Doctrine to make a COUNT query
instead of fetching all the data. We would *still* have 25 extra queries... but
they would be much faster.

In Doctrine, we can do this really easily. If you access a relationship - like
the `comments` property - and *only* count it, we can *ask* Doctrine to do a
COUNT query instead of loading *all* the comment data. How? Above the
`comments` property, add `fetch="EXTRA_LAZY"`.

Before we try this, don't forget that we're in the `prod` environment:
run `cache:clear`:

```terminal-silent
php bin/console cache:clear
```

And `cache:warmup`:

```terminal-silent
php bin/console cache:warmup
```

Ok, let's see if this helps! Spin over, refresh the page and... profile! I'll call
this one: `[Recording] homepage EXTRA_LAZY` - https://bit.ly/sf-bf-extra-lazy.
I'll close the other tab and view the call graph.

Was this better? Well, `createEntity` isn't the biggest problem anymore...
so that's a good sign! Let's compare to be sure: go from the original homepage...
to the most recent profile: https://bit.ly/sf-bf-extra-lazy-compare.

And... wow! Yea, this is a *huge* win in every category! So, was this a good change?
Absolutely: this was an *awesome* change.

But, even though the queries are much faster... we're still making the same *number*
of queries. Is that something we care about? I don't know? But that's the great
thing about profiling with Blackfire: you don't need to *absolutely* optimize
everything. If you're not sure if something is a problem, you can deploy and
check it on production to see if it's *really* slowing things down under realistic
conditions. *Especially* because sometimes improving performance comes at a
cost of extra complexity.

Next, let's see if we *can* reduce the number of queries. Will it help performance?
If so, is it enough for the added complexity?
