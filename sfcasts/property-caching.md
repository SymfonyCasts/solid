# Property Caching

Now that we've got our application in production mode and we've dumped the autoloader,
it's easier to see what the biggest performance problem is with this page:
http://bit.ly/sf-bf-profile4

And actually, maybe there might *not* be any more problems worth solving. I mean,
it's loading in 104 milliseconds... *even* with the Probe doing all the profiling
work.

But... let's see for sure. The function with the highest exclusive time *now* is
`PDOStatement::execute()`... which is a low-level function that *executes* SQL
queries.

***TIP
The SQL Query information requires a Profiler plan or higher.
***

If we hover over the query info, these are only taking 12.5 milliseconds... but
we *are* making 43 SQL calls on this page. Is that a problem? It's not ideal,
but is it worth fixing? I guess it depends on how much you care and whether the
fix would be easy or add a lot of complexity to our app.

## Navigating the Call Graph: Top to Bottom, Bottom to Top

When you're trying to identify where the problem is, there are two ways to look
at the call graph - and I often do both to help me understand what's going on.
First, you can read from top to bottom - trace through your *whole* application
flow to figure out what's going on down the hot path. Or, you can do the opposite:
start at the bottom - start *where* the problem is... and trace up to find where
your code starts, as needed.

Let's start from the top: `handleRaw()` is the framework booting up... and as
we trace down... it renders our controller, that renders our template... and
we're once again inside the `body` block. This is really the same as last time!
Our `AppExtension::getUserActivityText()` calls the `countForUser()` function 23
times. That makes sense: we probably have 23 comments on the page... and for
each comment, we need to count the author's comments to print out this label.

## Navigating Dimensions

Before we think about if, and *how* we might fix this, let's back up and look
at other *dimensions* of this profile. In addition to wall time, we can completely
re-draw the call graph based on only I/O time or CPU time. Remember, wall time
is I/O time + CPI time. Or we could do something totally different: look at
which functions are using the most *memory*... or even the most network bandwidth.

When we look at this in the network dimension, you'll notice that the
`PDOStatement::execute()` function - the function that makes the SQL call - shows
up here as a *big* problem. That's because SQL queries are technically network
requests.

Re-draw the call graph for the I/O Wait time dimension. We see the same problem
here because network calls - and SQL calls are actually part of I/O wait time.

The point is: while "wall time" is *typically* the most useful dimension, don't
forget about these other ones: they can give us more information about what's
going on. Is a function slow because of the code inside? Or is it because of a
network call?

Click back to I/O wait time - `PDOStatement::execute()` is *definitely* the issue
according to this - and the hot-path is pretty clear. This *one* function is
taking over *half* the I/O wait time... but it's still only 6 milliseconds.
Optimizing this might not be worth it... but let's at least see if we can figure
out how to call it less times.

As we already discovered, the problem is coming from
`CommentRepository::countForUser()` which is called by
`AppExtension::getUserActivityText()`.

Over in `src/Twig/AppExtension.php`, each time we render a comment, it calls
`countForUser()` and passes the `User` object attached to this comment.

## Property Caching

Can we optimize this? Well... sometimes, the same user will comment many times
on the same sighting - like this `vborer` user. When this happens, we're
making a query to count that user's comments *multiple* times on the same
request. That's wasteful!

So here's one idea: leverage "property caching". Basically, we'll keep track of
the "status" string for each user on a property... and avoid calculating it
*multiple* times for the same user.

Start by moving most of the logic into a private function called
`calculateUserActivityText()`: this will have a `User` argument and return a
string.

Next, add a new property to the top of the file: `private $userStatuses = []`.
Back in the public function, here's the magic: if *not*
`isset($this->userStatuses[$user->getId()])`, then set it by saying
`$this->userStatuses[$user->getId()] = $this->calculateUserActivityText($user)`.
At the bottom of the function, return `$this->userStatuses[$user->getId()]`.

This is one of my *favorite* performance tricks because it has *no* downside
at all, except for some extra code. If `getUserActivityText()` is called and
passed the same User multiple times within a single request, we won't duplicate
any work.

So... we probably made our site faster, right? Let's find out! Since we're in
Symfony's `prod` environment, just to be safe, let's clear the cache:

```terminal
php bin/console cache:clear
```

and warm it up:

```terminal-silent
php bin/console cache:warmup
```

Back in the browser, refresh the page and... let's profile! I'll name this one
`[Recording] show page try property caching`. View the call graph:
http://bit.ly/sf-bf-profile-prop-caching.

Ok - `PDOStatement` still looks like a bigger problem... but I think we're a
*little* faster. You know what? Let's just compare the two profiles. Go back
to the dashboard and compare from before to this profile:
http://bit.ly/sf-bf-compare-prop-caching. I'll close the old profile.

Ok, so it *did* help - lower time in each dimension... and we saved 5 queries.
So, this is a win, right? *Maybe*. If you profiled other Big foot sighting pages,
which I did, I found that this often did *not* help... or helped *very* little.
In fact, this is the *first* time I've seen it help nearly this much.

So, does the improvement justify the added complexity in our code? If we can
repeat this 13% improvement consistently, probably. But if it's more like 1%,
probably not. And even 13% is not *that* much... and `PDOStatement::execute()`
is *still* the biggest problem. The profile is asking us: is there a *better*
way to optimize this?

Next, let's try another approach: using a *real* cache layer. *Truly* caching
things has its own downside: added complexity in your code and *possibly* - depending
on what you're caching - the need to worry about *invalidating* cache. We'll
want to be sure it's worth it.
