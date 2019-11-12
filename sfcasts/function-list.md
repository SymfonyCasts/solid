# Function List

Coming soon...

We just had Blackfire profile our first page and it's taken us to the view of that
profile on their site and it's beautiful and there is so much information in here.
Um, that's honestly the most challenging thing for Blackfire is just knowing all the
massive amounts of information we can digest and then taking actions on that. So
let's learn a couple of really critical terms words so that we can understand what's
going on and start to figure out what's going on with this page and how we can make
it faster.

Let's start by looking over here on the left side here. And this is, um, of a call
graph. These are the function calls and right now they're actually ordered by the
highest time that they took. So the highest time on top, all the way down to the
lowest. And that's because we are on what's called the time dimension. You can
actually look at the call graph on the time dimension or for example, um, in the
memory dimension. And we're going to talk about all of these in a little bit. But if
I re clicked over to the memory, we're actually going to see the same thing, but now
it's going to show us which calls are taking up the most memory. So let's start with
time though. This is actually called wall time. That's a term you're going to hear in
the wall. Wall time

is the amount of time, the amount of time, um, between when a function is entered in
a function is exited. So the amount of time that it takes for a function to be
called, but while time is only part of the story, because if you think about it, what
if a function is called, but the 99% of the time that it took for that function to do
its work was actually because it called another function. That function took all the
time. So the problem is not in the function that took the most time. It's actually in
that other function at call the diff. There's a diff to help with this. There's a
differentiation between time. There is exclusive time in inclusive time and if I
hover over the little time here, you're going to see that exclusive time is the
amount of time that a function that that code inside the function itself actually ran
inclusive time is the amount of time spent in functions it called. So the total time
of a function is it's exclusive time versus it's inclusive time. So right now we're
actually ordering this list by exclusive time highest cause. That's usually uh, the
most, um, interesting. Now you can also order by inclusive time. Um, that's probably
not useful. That's just going to basically the, the first function that's called is
always going to be a, is always going to have the most and the second function, then
the third function and so on. So let's go back and look at exclusive time

[inaudible].

So apparently the biggest problem with the biggest problem according to exclusive
time is this unit of work, create entity, whatever that is. If you use doctrine bit,
you might know about that. But let's pretend we have no idea what that is. So before
we dive in and look more about that. Well it's actually looking at another way that
we can't order this, which is by calls. This is actually really interesting so we can
see which functions we're called the most times. Apparently the function is called
the most times 6,159 times is reflection property set value. Huh. I wonder who calls
this. So I'm actually going to click and we can expand this. This is really cool
cause it's gonna lay down. Uh, give us information about the amount of tone, wall
time, total time. This took the amount of I Oh wait time this took, which is actually
zero and the amount of CPU time this took as well as the amount of memory this took
up and the amount of network, uh, this took up. Now this is not a particularly time
consuming, uh, function. You can see it says 9.13 milliseconds.

Um,

um, this wall time is always a wall time always = I, Oh wait, time plus CPU time. So
you're going to see that in the futures right now. This one apparently is all CPU
time. It doesn't touch the uh, uh, disk at all. Okay, so, but who actually calls
this? I'm just curious. Like what's going on here? So above this, these three areas
here, you can see that there are three different functions that call this and they
call, you can see the size of these is because they call them different times. So I'm
gonna click this first one and Whoa, check it out. We know it. That's that unit of
work. Create entity. It calls this function 4,959 times. Wow. So it's definitely
looking like this is a problem area. If I click on the other two arrows, you can see
the other two calls. This call is at 984 times and this calls at 216 times. Those are
both from doctrine as well. So doctrine is clearly the problem in this case. All
right, so let's close this stuff up and I'm going to go back and reordered by
exclusive.

Okay.

And then let's click into create entity. Now, like I said, we are in the wall time
dimension, which means everything is being ordered and visualized, uh, by time. But
once you created into a function, you're going to be able to see, uh, the wall time,
the I Oh wait time, the CPU time, the memory and the network time. So you can see all
the information right there. Even within each dimension. It's really cool because you
can see exclusive versus inclusive. So you can see actually the total of time that
this function took, the exclusive time is pretty significant, but most of it's
actually in inclusive time and that can and getting exclusive versus inclusive. It's
going to give you a hint as to whether or not the problem is in this function or
maybe further down, which is a little bit more useful when you're looking at your own
code. And as before, if we are wanting to know who this calls, we can go down here
and actually see, uh, kind of what methods this calls internally to do all of its
work.

[inaudible]

what I'm really curious about is not who this calls but who is calling this function.
Because we're calling this a thousand times. That's actually probably a problem. So
if a click up here, we can actually see, start to see some information about who
calls this. But it's not really giving me a lot of information yet cause this is
still kind of low level doctrine stuff. So we've got a lot of information about this
function and we still don't know what, I have a big picture of what's going on. So
next, let's use the actual call graph over here to really identify what the problem
is and then let's fix it.