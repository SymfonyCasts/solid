# Timeline

Coming soon...

Click log in to find our super secure login system where we not only give you a valid
email address, but even the valid password hit enter and we are now logged in and
even though we can't tell, this actually triggered some new code that's running in
the background

and Rick is Blackfire to see exactly what's going on. Before we profile this page, I
also want to go into my `.env` file and switch it back to the dev environment. What
I'm about to show you is more of a debugging tool than a profiling tool, so we're
going to switch back to the dev mode just to make our life a little bit easier. All
right, let's switch over. I'll refresh this page to make sure that it works. Yep. We
have the web debug toolbar on the bottom and let's profile, I'll call this one
[Recording] Homepage authenticated dev poetry.

When that finishes click to view a timeline. When that finishes click to view the
call graph. Okay. There's not too much interesting here, especially with the 
`DebugClassLoader` stuff back because we're in the dev environment. It's really not clear
what the critical path is. And this page at this point is probably fast enough. It's
fast enough for me. So instead of looking at these things, we are going to click a
new link over here. The timeline, the timeline, other than being beautiful is such a
cool tool. The template for me is more about figuring out what's actually going on
inside my application. For example, this page, apparently it has 28 SQL queries, but
where are these happening? Are they all in the controller? Are some in the
controller? And some of the template are some queries being made somewhere else that
I'm not even thinking about. What about the doctrine entities that are being
hydrated, where, where are those happening?

So let's break down this page. Cause the first time I looked at that, this page,
there was so much going on that I didn't really know how to use it. So the first
thing on the left here is you see these timeline metrics. Metrics are basically a way
that Blackfire groups things together. For example, Blackfire identifies any Symfony
events that are dispatched and labels them all as `symfony.events` and then gives them
this little purple color so that they show up here on the right. So there's a Symfony
event right there, and there's another one over here. It also does the same thing,
for example, for SQL queries that identify as SQL queries, puts them into this `sql`
metric kind of category. And then those are highlighted in yellow down here on the
spot. So there's a lot more to say about metrics and we're gonna talk a lot more
about metrics later. But the most important thing for the timeline is that just that
metrics on are a nice way that Blackfire color codes important things that are
happening in your app.

You also noticed this other metrics down here. There are, these are a whole bunch of
other metrics where you can see the raw data for these metrics, but these aren't
actually shown on the timeline. These, there'll be more interesting later when we
talk more about metrics.

so let's look at one of these `doctrine.entities.hydrated`. So this is actually saying
that there are three times in the system where doctrine makes a query and then turns
that into an object. So hydrates it into an object. Okay, cool. So where is that
happening in our system? Well, one of the cool things is that when you hover over a
one of these things, you can see it actually highlights the little box on the right
with a darker border. It's kind of subtle, but it's a way for you to see, uh, boxes
that correspond to that metric. Unfortunately, I wish you could double click this and
actually zoom in over there, but it doesn't work that way for chil for doctrine and
she's hydrated. You don't really see anything over here, but if you kind of move down
and do a little digging, I'll hover back over there. You can't, you can see the three
boxes right there. So it turns out that our, our entities are being hydrated, not all
at once, but in three separate spots in the system. It's being hydrated here, which
is part of the firewall. That's probably our user being queried for. And then another
one here and another one down here.

You want to zoom in a little bit further on this. You can. So for example, this is
all of, this is everything that's happening inside of running the tweak templates. A
lot of these things are really small inside of here. So we can do up here on the
actual timeline itself. As you can see, here's the entire timeline. We can just hover
over the spot that's interesting to us. I'm going to click drag and boom, it zooms us
in and now it's a lot easier to see what's going on. You can see the uh, uh, the D,
this is doctrine Parson, the DQL. you can see this is actually where the SQL query
is being made and you can see which SQL is being made there versus which SQL is being
made. A query is being made down here. So as far as getting insight into what's
really going on in your application, like you can't really get much better than this.
You can even see our n plus 1 problem as it queries for all of the comments along
the way. All right, so I'm going to hit home, um, and scroll back up. And one of the
things I wanna look at is actually the `RequestEvent`. So this is the first event that
Symfony dispatches and anything that listens to this happens before your controller.
You can actually see that in here.

I'm going to zoom in by double clicking on this. Perfect. And you can see a couple of
cool things. You can actually see the `RouterListener`. This is the routing layer
happening. You can see the `Firewall`, this is where authentication takes place. And
you can even dive in here and see what's going on. You can see the entity repository.
It's actually eventually querying for our user object. So really cool stuff. But
check out this almost half of the request of is something called `AgreeToTermsSubscriber`
which is taking 30 milliseconds. Let me show you what that does. That
lives in `src/EventSubscriber/AgreeToTermSubscriber.php`. So the idea is that every
now and then maybe the terms of service update on your site. And so when a user comes
to your site, you need to ask them once again to agree to your terms of service.

I know it's very boring lawyer stuff, but this is something that sometimes this has
to happen. So you can see this goes and gets the authenticated user. If they're not
logged in, it just doesn't, it does nothing. But if they are logged in it then some
of this logic is fake here, but it then basically a renders a twig template with a
form that has the new information about the agree to terms and a checkbox to agree to
them and then goes down here and eventually renders that and actually sets that as a
response. So instead of running the controller, it would show you the agree to terms
box. But the key thing is that we store the last time the user agreed to the terms
and we only showed them if the terms have been updated since they last agreed to
them. So for 99.9% of the requests, this code most of this code and never needs to
run. So the fact that this is taking 30 milliseconds is way too big.

Also see this blue background back here, that is actually the memory footprint.
That's one of the coolest things. So for example, I can actually trace over this. You
see, this is about where the agreed to term subscriber happens. And you can see it's
taken 3.44 megabytes. And as I trace over by the finish, it's taking 4.46 about one
megabyte higher. So that's a lot of memory just for that one little uh, uh, class
that isn't even rendering the, the form on, uh, in my situation. And if you hover
over here, you can see the same thing about one megabyte of extra memory. So this is
cool. This is an invisible layer. The timeline helped us find this. It's not a huge
performance problem, but it's, but it's, but it's not ideal. And the mistake I made
here is very obvious. I'm checking to see if I actually need to render this form, but
I'm doing all the work to run to the form even before, even when I don't need it. So
simple fix in this case. Now that we have this, I'm just going to move that code all
the way to the top a little bit further down after my latest terms date. There we go.
That looks better. So now this should exit earlier and we shouldn't have this
problem. So let's actually try that. I'll refresh the page profile again because one
[Recording] Homepage authenticated fixed subscriber.

I want to finish this. I'll jump this time straight to view the timeline and I'll
double clip again on the uh, request event and you can see this time look, it doesn't
even show up here. You can see the `RouterListener`, the `Firewall` listener, and you
don't even see our `AgreeToTermSubscriber` here. It's not because it's not being
executed, but because Blackfire is pruning data that takes no time, that functions
now so fast that it's not a problem at all. Next, we'll talk about something else.