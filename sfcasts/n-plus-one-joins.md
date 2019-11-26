# Fixing N+1 With a Join?

We made a *huge* leap forward by telling Doctrine to make `COUNT` queries to
count the comments for each `BigFootSighting` instead of querying for *all* the
comments *just* to count them. That's a big win.

Could we go further... and make a smarter query that could grab all this data
in *one* query? I mean, that *is* the *classic* solution to the N+1 problem: need
the data for some Bigfoot sighting *and* their comments? Add a JOIN and get all
the data at once! Let's give that a try!

## Adding he JOIN

The controller for this page lives at `src/Controller/MainController.php` - it's
the `homepage()` method. To help make the query, this uses a function in
`src/Repository/BigfootSightingRepository.php` - this `findLatestQueryBuilder()`.
*This* method ... if you did some digging ... creates the query that returns
these results.

And... it's fairly simple query: it grabs all the records from the `big_foot_sighting`
table, orders them by `createdAt` and sets a max result - a `LIMIT`.

To *also* get the comment data, add `leftJoin()` on `big_foot_sighting.comments`
and alias that joined table as `comments`. Then use `addSelect('comments')` to
not only *join*, but also *select* the comment data.

Let's... see what happens! To be safe, let's clear the cache:

```terminal-silent
php bin/console cache:clear
```

And warm it up:

```terminal-silent
php bin/console cache:warmup
```

Now, move over, refresh and profile! I'll call this one: `[Recording] Homepage with join`:
https://bit.ly/sf-bf-join.

Ok, go check it out! Woh! This... looks weird... it looks *worse*! Let's do a
compare from the `EXTRA_LAZY` profile to the new one: https://bit.ly/sf-bf-join-compare.

Wow... this is much, much worse: CPU is way up, I/O... it's up in every category,
especially network: the amount of data that went over the network. We *did* make
less queries - victory! - but they took 8 milliseconds longer. We're now returning
*way* more data than before.

So this was a *bad* change. It seems obvious now - but in other situation where
you're doing *different* things with the data, this *same* solution might work!
Let's go back to the `EXTRA_LAZY` solution.

## A Smarter Join?

Yes, this *will* mean that we will once again have 27 queries. If you don't like
that, there *is* one other solution: you could make the `JOIN` query smarter - it
would look like this:

```
// src/Repository/BigFootSightingRepository.php
public function findLatestQueryBuilder(int $maxResults): QueryBuilder
{
    return $this->createQueryBuilder('big_foot_sighting')
        ->leftJoin('big_foot_sighting.comments', 'comments')
        ->groupBy('big_foot_sighting.id')
        ->addSelect('COUNT(comments.id) as comment_count')
        ->setMaxResults($maxResults)
        ->orderBy('big_foot_sighting.createdAt', 'DESC');
}
```

In this case, instead of selecting *all* the comment data... which we don't need...
this selects *only* the count. It gets the *exact* data we need, in one query.
From a performance standpoint, it's probably the perfect solution.

But... it has a downside: complexity. Instead of returning an array of
`BigFootSighting` objects, this will return an array of arrays... where each
has a `0` key that is the `BigFootSighting` object and a `1` key with the count.
It's just... a bit weird to deal with. For example, the template would need to
be updated to take this into account:

```
{% for sightingData in sightings %}
    {% set sighting = sightingData[0] %}
    {% set commentCount = sightingData[1] %}

    {# ... #}
		{{ sighting.title }}

		{{ commentCount }}
	{# ... #}
{% endfor %}
```

*And*... because of the pagination that this app is using... this new query would
actually produce a query error. So let's keep things how they are now. If the extra
queries ever become a *real* problem on production, *then* we can think about spending
time improving this.
