# Comparisons: Validate Performance Changes, Find Side Effects

We've just updated our code to make a COUNT query instead of querying for *all*
the comments for a user... just to count them. So, the page will *definitely* be
faster. Right? Are you *absolutely* sure? Well, I *think* it will be faster... but
sometimes making one part of your code faster... will make other parts slower.
Fortunately, Blackfire has a special way to *prove* that a performance tweak *does*
in fact help.

Let's profile the page now - I'll refresh... then click to profile. Give it a name
to stay organized `[Recording] Show page after count query`.

Ok! Let's go see the call graph! http://bit.ly/sfcast-bf-profile2

Hey! 270 milliseconds total time. The last one was 415. So it *is* faster. We win!

Well... yeah, I agree: it does look faster. But an important aspect of optimization
is understanding *why* something is faster. Like, did this reduce CPU time, I/O
wait time? And, maybe more importantly, did this change cause anything to be
*worse*? For example, a change might decrease CPU time, but *increase* memory.
If that happened, would the change *really* be a good one? It depends.

## Comparing Profiles

This leads me to one of my *favorite* tools from Blackfire: the ability to
*compare* profiles. Click back to my dashboard: the top two profiles are from the
initial page and then the page after using the COUNT query. On the right, hover
over the "Compare" button on the original, click, then click the updated profile.

Say hello to the *comparison* view: http://bit.ly/sf-bf-compare1-2.
Everything that's faster, or "better" is in blue. Anything that's slower or worse
will be in red. And yea, it looks like the new profile is better in *every* single
category. Ok, the I/O wait is higher - but .1 millisecond higher - that's probably
just "noise".

Anyways, the comparison *proves* that this *was* a good change. Really, it's a huge
win! On the call graph, in the darkest blue, the critical path *this* time is the
path that improved the most. Click the `UnitOfWork` call now. Wow. The inclusive
time is down by 90 milliseconds and even the memory plummeted - down
1.39 megabytes.

But wait. One of the items on top is called "SQL Queries". The total query *time*
is less than before... but we've *added* 5 *more* queries. We removed these 18
queries... but added 23 new ones.

Is that a problem? Probably not. Overall, this change was good. And if having too
many queries *does* create a *real* problem - not just an imaginary one of "too
many queries" - Blackfire will help us discover that. The big takeaway here is:
don't just assume that a performance enhancement... *is* actually better. We'll
see this later - not *every* change we'll do in this tutorial will prove to be
an improvement.

Next: Blackfire has a deep understanding of PHP, database queries, Redis calls
and even libraries, like Symfony, Doctrine, Magento, Composer, eZ platform,
Wordpress and others. Thanks to that, without thinking, it notices problems
and recommends solutions.
