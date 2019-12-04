# Profiling Command Line scripts

As handy as the CLI tool is for profiling AJAX requests, its *true* purpose is
something different: it's to allow us to profiling our custom command-line scripts.
Let's check out an example. I've already created a command line script that you
can execute by calling `bin/console app:update-sighting-scores`.

What does it do? Let me show you! Each Bigfoot sighting on the site has, what we
call, a "Bigfoot believability score". Right now, this shows zero for *every* sighting.
That's because we use a highly-complex and proprietary algorithm to calculate
this. It's *such* a heavy process that instead of figuring it out on page-load,
we store the current value on a column in the table. To *populate* that column,
we run this command once a day: it loops over all the sightings, calculates the
newest "believability score" and saves it back to the database. Try it:

```terminal-silent
php bin/console app:update-sighting-scores
```

It takes a few seconds... and when we go back to the site and refresh... we find
out that this Bigfoot sighting in *kind of* believable - a score of 5 out of 10.

The code for this lives at `src/Command/UpdateSightingScoresCommand.php`. You
*might* already see a problem. But if you don't... that's ok! Let's see what
Blackfire things. This time, run that *same* command, but put `blackfire run`
at the beginning:

```terminal-silent
blackfire run php bin/console app:update-sighting-scores
```

Woh. It's a *lot* slower this time - we're seeing evidence of how the PHP extension
slows down the process... and wow... it's just getting slower, and slower. I'm
going to use the magic of TV to speed things up.

Ok, let's go check out that profile! http://bit.ly/sf-bf-console-original

Woh! Some `computeChangeSet()` function was called almost 500,000 times! Ah! That's
taking up *half* of the exclusive time! Because this call is *such* a problem.
Blackfire pruned a *lot* of data. The result is a *super* simple call graph:
here's our command... here's `EntityManager::flush()`... and then it goes
into deep Doctrine stuff.

Ok, let's check out the command and look for the `EntityManager::flush()` call.
Yep! I flush once each time at the end of the loop, which updates that database
row. If you're familiar with Doctrine, you might know the problem: you don't
*need* to call `flush()` inside the loop. Instead, move this *after* the loop.

With this change, Doctrine will try to perform *all* update queries at the *same*
time... which even lets it try to *optimize* those queries if it can. But the *big*
problem with our old code was something related to Doctrine's
`UnitOfWork::computeChangeSet()`. *Each* time you call `flush()` in Doctrine, it
looks at *all* the objects it has queried for - so *all* of the `BigFootSighting`
objects - and checks *every single one* to see if any data has changed that needs
to be updated. Yep, with the *old* code, it was checking *every* record for updated
data on *every* loop. Hence...the nearly 450,000 calls!

Let's profile again with the updated code.

```terminal-silent
blackfire run php bin/console app:update-sighting-scores
```

This time it's *much* faster - I don't even think we need to compare the profiles:
56 seconds down to 1. Open it up: http://bit.ly/sf-bf-console2

Could we optimize this further? Maybe! But this performance enhancement already
came at a cost of added complexity. I originally put the call to `flush()` inside
the loop *not* because I didn't know better... but to make the command a little
bit more resilient. If, for example, the command gets through *half* of the records
and then has an error, with the *new* code, *none* of the scores will be saved.

It's beyond the scope of this tutorial, but I *love* to make my command-line
scripts *super* forgiving. If this were a real app, I would probably save the
datetime that I last calculated the score for each record and use that to query
*only* for rows that haven't been updated in the last 24 hours. I would *also*
move the `flush()` back into loop. Doing that would mean that if this command
fails, we could run it again to *resume* progress.

Wouldn't that make the command super-slow again? Yep! And with the help of
Blackfire, you can test solutions that improve performance without making the
command less reliable. For example, we could make the first query only return
an array of ids. Then, inside the loop, use that id to query for the *one* object
you need. That would mean we only have *one* object in memory at a time instead
of all of them. You can go further by calling `EntityManager::clear()` after
flush to, sort of, "clear" Doctrine's memory of the `BigFootSighting` you just
finished so that it doesn't check it for changes on the `flush()` of the *next*
item.

The point is: like with *everything*, make your code do what it needs to do...
then use Blackfire to solve the real performance issues if you have any.

Next, there's a *giant* screen in Blackfire that we haven't even looked at yet:
the Timeline!
