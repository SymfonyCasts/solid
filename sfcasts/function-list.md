# Wall Time, Exclusive Time & Other Wonders

We just made Blackfire profile our first page. One of the *best* things about
Blackfire is that, instead of just... giving me some raw data-dump and saying:

> Good luck navigating *that* black pit of data!

... they expose this *treasure trove* of info on their site with a beautiful
interface. This is called the "call graph". The most challenging part of Blackfire
for me was learning what all this stuff means... so I could *really* get the most
out of it. If you stick with me for the next few minutes, your profiling game
will get a *huge* boost.

By the way, throughout the tutorial, I'll give you links to view the *exact*
profile on Blackfire that I'm navigating in the video. Feel free to open it up and
play around. The first one is here:
https://bit.ly/sfcasts-bf-profile1.

And yes, I know, the cool-looking graph in the middle is *calling* to us, but
let's start by looking at the the left side: the list of function calls, ordered
from the functions that took the longest to execute on top... down to the quickest
on the bottom. Well actually, Blackfire "prunes" or "removes" function calls that
took *very* little time... so you won't see *everything* here.

## Viewing by Different Dimensions

The functions are ordered by "time" because we're viewing the call graph in the
time "dimension". You can also look at all of this information ordered by several
other dimensions - like which functions took the most *memory*. It's kind of like
the process manager on your computer: you can see which applications are currently
taking up the most CPU, the most memory, reading the most info from your disk or
even using the most network. But more on these dimensions later.

## Wall Time

In the profiling world, time is called "wall time". But, it's *nothing* fancy:
wall time is the difference between the time at which a function was entered and
the time at which the function was left. So... wall time is a fancy word for... um...
time: the amount of "time" a function took to run.

## Inclusive vs Exclusive

So... we just find the function with the highest wall time and optimize it, right?
Well... what if a function is taking a really long time... but actually, 99% of
that time is due to a function that *it* calls. In that case, the *other* function
is probably the problem.

To help sort this all out, wall time is divided into two parts: *exclusive* time
and *inclusive* time. If you hover over the red graph, you'll see this: exclusive
time 37.9 milliseconds, inclusive time 101 milliseconds.

Inclusive time is the *full* time it took for the function to execute. Exclusive
time is more interesting: it's the time a function took to execute *excluding*
the time spent inside *other* functions it called: it's a *pure* measurement
of the time that the code inside *this* function took.

Right now, we're *actually* ordering this list by *exclusive* time, because that
usually shows you the biggest problems. You can also order by inclusive time...
which is probably not very useful: the top item is where our script starts executing,
the second is the next function call, and so on. Go back to exclusive.

## Navigating What Calls What

So apparently, the biggest problem, according to exclusive time, is this
`UnitOfWork::createEntity` function... whatever that is. If you use Doctrine,
you might know what this is - but let's pretend we have *no* idea.

Before we dive further into the root cause behind this slow function, the
*other* way to order the calls is by the *number* of times each is called.
Wow! Apparently the function that's called the most times - over 6 *thousand* times -
is `ReflectionProperty::setValue`. Huh. I wonder who calls that?

## Deeper Function Details

Click to expand that function. I love this! Even though we're viewing the call
graph in the "time" dimension, this gives us *all* the info about this function:
the wall time, I/O wait time, CPU time, memory footprint and network.

## Wall Time = I/O Time + CPU Time

This isn't a particularly *time* consuming function - its wall time is 9.13
milliseconds. Wall time itself is broken down into two pieces, and this is important:
wall time = I/O time + CPU time. There is nothing else: either a function is using
CPU or it's doing some I/O operation, like talking to the filesystem or making network
calls. In this case, the 9.13 milliseconds wall time is *all* CPU time.

## Finding Callers

Okay, but who actually *calls* this function so many times? Above this, see
those 3 down arrow buttons? These represent the *three* other functions that
call this one - the size is relative to how many *times* each calls this. Click
the first one. Ah ha! It's `UnitOfWork::createEntity`! That's the function with
the highest exclusive time - it calls this function 4,959 times. Wow. So... it's
definitely a problem.

If you click the other two arrows, you can see the other two callers: one calls this
984 times and the other 216 times. Both are from Doctrine.

## Viewing Callees

Close all of this up and go back to ordering by the highest exclusive time. Open
up `UnitOfWork::createEntity()`. As I mentioned, even though we're currently
viewing the call graph in the "time" dimension, we can see *all* this function's
dimensions right here.

Hover over the time graph: even though the exclusive time is significant - 37.9
milliseconds - most of this function's time is still *inclusive*: it's taken up
by other functions that *it* calls. That helps give us a hint as to if the problem
is *inside* this function... or inside something it calls.

And actually, *every* dimension has inclusive and exclusive measurements: like
CPU time and even memory. If any of these had a high *inclusive* value - meaning
some function *it* calls is *really* taking up that resource - you can see what
functions it calls by clicking one of the arrow buttons *below* this.

What I *really* want to know though is... what's happening in our code to cause
*this* function - `UnitOfWork::createEntity()` - be called so many times? Click
the biggest arrow above. Ah: `ObjectHydrator::getEntity()` is the main culprit.

But... honestly... I don't know what that function is either: this is still
*way* too low-level in Doctrine - I have no idea what's really going on.
So next, let's use the call graph - the pretty diagram on the right - to get a
full picture of what's happening going on... and how to fix it.
