# Timeline: Go Behind-the-Scenes with your Code

Click log in to find our super-secure login system. We not only give you a valid
email address, but even the password! We're *very* generous to our users.

You can't tell, but now that we're logged in, a new piece of code is... silently
running in the background on each request. Blackfire is going to help us notice this.

## Back to the dev Environment

Before we profile this page, open up the `.env` file and switch *back* to the
`dev` environment. What I'm about to show you is more of a *debugging* tool than
a *profiling* tool. We're switching back to the `dev` environment both to make
our life a little bit easier - no need to clear the cache after changes - *and*
because when your code executes more slowly, Blackfire tends to prune, or remove,
less stuff. That's *bad* for trying to find performance issues, but *good* if your
goal is to debug something... or understand how your app is working.

I'll refresh the page to make sure that it works. Yep! Our handy web debug toolbar
on the bottom is back! Let's profile! I'll call this one
`[Recording] Homepage authenticated dev`: http://bit.ly/sf-bf-timeline. Poetry.

When that finishes, as usual, click to view the call graph. Okay: there's not too
much interesting here... especially because the `DebugClassLoader` stuff is once
again adding "noise" that won't be there on production. It's not clear what the
critical path is... and the page, at this point, is probably fast enough for me.

## Hello Timeline

So let's look at something else: click the "Timeline" link. OooOOOOo. The timeline...
other than just *looking* cool... is *the* place to go to... just... basically
figure out how your app is working: how does the code flow through all the layers?
What hidden things might be happening?

For example, this page apparently has 28 SQL queries. But where are these
happening? Are they all in the controller? Are some in the controller and others
are in the template? Are some coming from somewhere else we didn't even think of?
That's something that the call graph can't really tell us.

I love the timeline... but I'll admit that the first few times I looked at this
page... I didn't really understand what was going on... or how to make this useful.
It *looks* simple enough - we can see the function calls and their child calls
from left to right through the lifecycle of the request - but there's more to it.

## Metrics

Let's start on the left: these timeline metrics. Metrics are basically a way
that Blackfire groups function calls together and give them a label. For example,
Blackfire knows that a *specific* function call means that an *event* is being
dispatched. It finds those, labels all of them as `symfony.events` and give them
this purple color so that they show up more clearly on the right. Here's one
Symfony event right here... and there's another one.

It does the same thing for SQL queries: it knows that `PDOStatement::execute()`,
`PDO::query()` and several other functions mean that an SQL query is being made.
It groups them together, calls them `sql` and labels them as yellow. It's a great
idea... and is just that simple.

Below this, there is another section called "Other Metrics". These are the same
thing: meaningful groups of function calls. The only difference is that Blackfire
does *not* give these a special color and they don't show up on the timeline.
These are... just... raw data... that sit right here. If you're wondering why
that would *useful*... I was too! For the purpose of the timeline, they are *not*
useful. They'll come in handy later when we talk more about metrics. Metrics are
their own big topic.

## Finding Metrics in the Timeline

Let's look at one of the timeline metrics `doctrine.entities.hydrated`. What does
this one mean? Sometimes the title of a metric will tell you a bit more... but
often the metric name is all you really have. Most metrics are self-explanatory.

Depending on how well you know Doctrine, this might be obvious... or not. This
metric refers to whenever one or more entities are *hydrated* into an object.
Notice the count is 3. For this metric, it's not that there are only 3 *objects*
being hydrated during this request, but that our code asks Doctrine to hydrate one
or more objects on *three* occasions.

So where are the 3 times that we're hydrating objects? One of the cool things is
that, when you hover over a timeline metric, it adds a border to the matching
boxes on the right. It's... a little subtle... but it does the trick. I wish
you could double-click and... maybe zoom to the matching boxes... but it's tricky
because they may be spread out over the whole request.

If we hover over `doctrine.entities.hydrated`... hmm... I don't see those. You
need to do a little bit of digging... I'll hover back over. There they are. It
turns out that the 3 calls are *not* all in the same place: they're coming from
three very different *parts* of our code. The first is part of the firewall...
probably querying for the logged in user... and the other two are down in some
template rendering... along with a few similarly-colored `doctrine.dql.parsed`
items.

I want to look at what's happening inside of this template... but a lot of
these things are *really* small. On top, we can see the entire timeline. Click
where we want to start, move over, and let go! Zoom!

Much easier to see! In this spot, Doctrine parses its DQL, it makes an SQL query
here... and a different query a bit later.

So as far as getting insight into what's *really* going on in your application,
you can't get much better than this. You can even see our N+1 problem visually:
it makes a query to count the comments little-by-little as the template renders.

Hit the "Home" icon to zoom back out. This is cool... but I mentioned that, as soon
as we logged in, there was some *new* code that was now running in the background.
Next, let's look a bit closer at the timeline to discover what that is *and*
a hidden performance problem.
