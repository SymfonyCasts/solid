# Cache Compare

Coming soon...

Now that we've got our application in production mode, we've dumped the autoloader.
It's easier to see what the problem is with this page. And actually maybe there isn't
a problem with this page. I mean it's loading 104 milliseconds. Even with Blackfire
running book. Let's say that we do want to optimize this page further. Let's see if
we can find where the problem is. Now it's pretty obvious that you know, the biggest
thing here is `PDOStatement::execute()`, which is basically um, probably
related to just query is being run. And you can see it over here in the queries
thing. Uh, the query is only taking 12.5 milliseconds, but we do have 43 SQL calls on
this page. Is that a problem? It's not ideal but, but you know, if we could improve
it, that'd be great. But it's not taking a lot of time.

Now when you're trying to identify where the problem is, there's two ways to look at
this call graph over here and it's pretty simple, but they're both really good ways.
The first is to go from the top to the bottom, like actually trace through your
entire application to figure out the entire flow. It's a great way to understand
what's going on as you go down the hot path. The other one is to do the opposite is
basically to start with the bottom. So let me go back up here, start with where the
problem is and then back up from there and just go up and try to figure out. It's
kind of like where up in your code, your stuff kind of started. Um, they're both just
different ways of doing things, but you can get different ideas and get a better idea
for the overall problem if that's what you do.

So let's actually start from the top here. We can see `handleRaw`. This is just some
framework stuff. It's the same as last time we were renting a controller. We render a
template, we then it renders our blocked body and actually we're finding it's the
exact same problem as before. So it's still in `AppExtension::getUserActivityText()`.
Our `countForUser()` is making 23 calls and that makes sense. We're probably have on
this page, you probably have 23 comments and so for every single comment it's
calculating the number of a [inaudible]. It's counting the comments for these so it
configure out which text to print here.

Well, back up one second. In addition to looking, so one of the things I don't
remember is that we haven't looked at it a lot yet, but there are these things called
dimensions on top and the aspects, the different aspects of you can look at
performance wise or you can look at your wall time. I want to know, I want to see
everything in terms of my wall time or you can look at how long I'm going to look at
things in terms of the IO time or you can look at things in terms of the CPU time as
a reminder, a CPU time plus IO weight = the total time. So you can kind of look at
these two different dimensions, the CPU versus the IO wait. Or you can actually look
at their names in terms of memory, like which functions, uh, increase the memory the
most, or even by network call. Now, one of the interesting things is that notice that
`PDOStatement::execute()` that's making an SQL call that does show up under network call.
It is you're making an external network call. So it, and when you look at this view,
it's super obvious what the problem is.

over half of the network call data is coming from the database. Not surprisingly. The
other thing is that the, um, IO wait, that will include anything that's not CPU.
So that's gonna include network calls, um, disc calls or database calls. So you can
see on the IO you can see things, uh, that the database, the PDF statement is the
biggest problem. I'm just want you to show us because these are, it's all about
debugging and finding what the problem is. Uh, by leveraging all four or five of
these dimensions, it's going to be, you're going to be able to get a better idea of
like what the actual problem is. Because if you just look at wall time, you might see
a function as slow, but it might not be obvious why it's slow. It might be CPU, it
might be wall time, it might be because of a, it might be IO weight.

But really because of a slow network request for example. So just remember to use
these tools up here, uh, to get a different view about what's going on. Get a more
full understanding soon as if you click IO, you can see that `PDOStatement::execute()` is
in this thing. Definitely the hot path problem. Of course it's taking, it's taking
50% of, uh, the IO way, which is still only six milliseconds. So is this something we
really want to optimize? Honestly, I'm not sure, but let's see what we can do. So as
you know, there's two ways to optimize a function. You can optimize the function
itself or you can call a function. Last times, obviously this is a core PHP function,
so we can't optimize.

uh, we can't optimize it itself. But let's see if we can call it less times. Now as
you saw a second ago, we know the problem actually is coming from a 
`CommentRepository::countForUser()`  which is ultimately coming from 
`AppExtension::getUserActivityText()`. So
every time we render a comment here, if I move over our `AppExtension` in 
`src/Twig/AppExtension.php`, every single time we were in our comment, it's calling this passing. I
said `User`, we're counting the comments and the returning a different string based on
how many recent comments their work.

So let's see, what can we do to optimize this? Well, one of the things is that
oftentimes the same user will comment multiple times. So in this case you can
actually see this Vibhor here a and the V bar there. So that's actually a wasteful,
we're making two different count queries just for that same one user. So one idea
here is we could use some property caching basically within a single request. Makes
sure that we are not counting the comments for the same user more than once because
that's wasteful. So let's try this. I'm going to actually [inaudible] start down
here. By creating a private function `calculateUserActivityText()`. They'll take an a
user strip `User` object, return a `string`. So I've just basically moved all of my
actual logic, uh, down into this function.

And then up here, what I'm gonna do is I'm going to use a little a, I'm gonna add a
property at the top of this file. So private `$userStatuses = []`. And then
down below. Now inside this function we can say, if not isset 
`$this->userStatuses[$user->getId()]` Then we're going to set that 
`$this->userStatuses[$user->getId()] = $this->calculateUserActivityText()`
and pass the `$user`. Then at the bottom of the function we can return
`$this->userStatuses[$user->getId()]`. This is my favorite
little performance tricks here. There's no downside to this at all.

except for a little extra code. And now we're not going to be calling a, getting the
user status, uh, the same user multiple times and request. That's great. So this
probably help, right? I don't know. Let's find out. And I was reminded, we are in the
production mode for Symfony. We might not need to. We actually don't need to. In this
case, let's run just to be safe. Let's run 

```terminal
php bin/console cache:clear
```

and then `cache:warmup`

```terminal-silent
php bin/console cache:warmup
```

now let's move back over. I'll refresh this page and let's profile, I'll name this
one again. Try property caching and let's view the call graph on this one. Okay. All
right. So I think this help `PDOStatement` is still a problem. Um, we're a little bit
faster. You know what to actually look at this, the better way is not to try to guess
between those two. Let's just compare them. So let's go from here to here. I'll close
my other profile. Perfect. And now we can get an actual significant readout of
whether or not this helps. So it did actually help the time went down. 33% of the IO.
Wait not down 22% the CPU went down, um, a little bit as well and uh, we saved five
queries and two milliseconds on the queries. So this is a win, right? Well maybe
because we also, if we look increased our peak memory only by a tiny little bit, but
there is actual like a tiny little cost to this. And actually when I was recording
this and practicing it, I didn't have results there. Even nearly this good, it
actually depends on exactly which page you picked on. Hi Erin. How many repeat things
there are? So was this a good change or not? It did save us like 13% in the year
probably. But if you saw on these things, it's more like 1% or you saw higher memory
increase, then you might be thinking this might not be the best solution

we have to think about whenever we talk, whenever we make something more performance,
we also added like some complexity to our code. So is this one worth it? Prompt me.
Maybe I would probably profile a few more pages to make sure this actually has a
significant thing. But for me, I actually think that this might be over optimizing
too early. Sure, I'm saving 13% in this case, but it's only 10 milliseconds, so I'm
actually going to go over here and revert. This is going to take out my property
caching and return `$this->calculateUserActivityText($user)` take out my thing there.
This just is simpler code overall. So at this point we can either leave this and say,
look, it's not worth optimizing. That might be a proper solution or we might try
something different. So another thing that we can think of is, is actually just true
caching.

We can say, look, this little label here, it's not going to update that often. It's
what if we cache that per user for an hour? Let's see how that changes things. So
over inside of the service at the top, I'm going to auto wire in `CacheInterface`, the
one from Symfony contracts cache. I use a little Alt + Enter trick to "Initialize fields"
to create that property and set it. Now down here, I'll read a little bit of code
that's actually going to put this into Symfonys cache off to cache. Key with 
`$key = sprintf('user_activity_text_'.`and then the `$user->getId()`. So a little a
unique cache per user. And the way that you use somebodies cache is you say we're
gonna return `$this->cache->get()` we pass it the `$key`. And if that exists in cache
it'll return it.

Otherwise it's going to call this function. Pass us a `CacheItemInterface`. A object
in our job inside of here is going to be to return the new value so that can be
cacheed. So I'm actually going to say `use` over here cause I need to get the `$user`
variable inside of here. We'll return `$this->calculateUserActivityText()`, pass it
the `$user`. I'm also going to control the TTL here a little bit. I'll say 
`$item->expiresAfter(3600)` perfect. So does this help? I mean I'm sure it will, but is
this a more significant help? Let's find out. Let's go over here. Oh, of course, 500
eror. Cause I need to clear my cache

```terminal-silent
php bin/console cache:clear
```

```terminal-silent
php bin/console cache:warmup
```

area now spent over here and refresh. Awesome. Let's profile, I'll give that a name
as normal. Using a real cache view, the call collograph and yeah, this time it looks
way lower. Uh, but I'm not going to trust that. Let's actually go and let's compare
from the original one to this new one. And here you can see these are significant
changes minus 23 requests. Um, and basically there's no downside. Even our, even our
memory went down. Now if you want to, if you want to get even compare from the
property caching, uh, method to this, and you can see it's a, it's basically better
in every single category. So that's what I love about the comparison feature. Um,
it's kind of a, a lean way for performance, just a try things, but actually validate
that they work.