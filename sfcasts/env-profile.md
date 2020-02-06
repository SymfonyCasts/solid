# Production Profile: Cache Stats & More Recommendations

Coming soon...

We've just created our first profile on production and we configured our production
server, not to use our personal Blackfire server ID and server token, but a server ID
and server token for hour production Blackfire environment that we created. So if you
go to blackfire.io and click over to environments and click into our environments,

you'll see that there's a profiles tab here. And now our profiles actually show up
directly under this environment. So that's what I mean when the profiles are sent to
the environment, this is where they live. And the nice thing about the environment is
that we can add members to the environment and they're all gonna be able to, uh, get
and they're all gonna be able to get access to these profiles for convene. If you go
to backfire.io your homepage, the profile also shows up on your, um, main dashboard.
But that's purely confer convenience. I want you to think this lives in this
environment. You can even see that it talks about that right here. All right, so
let's actually click into look at this profile. Of course, it's basically the same
that we've primarily the same that we've been seeing. Uh, when we've been, um,
profiling locally. The first thing I want you to, uh, I want to point out though is
that if you hover over and look at the cache information, we talked about this
earlier, this shows you various different caches on your system and whether or not
they have available space. Now that you're on production, this is very valuable
information. For example, if your op cache fills fills up, your site is going to
start running slowly and you might not even realize that happens. But if this is a
super easy way to see the status of things. So if any of these fill up, you can read
some documentation about them and they'll tell you what setting you need to change to
make that cache bigger.

The other thing I want to show you is that my account, if you click on
recommendations, we've now unlocked these security and quality recommendations.
Security and quality recommendations are actually Blackfire add ons. And now that in
my, in my environment has those add on. So I now see them. So you can actually see
this first recommendation here is coming from the security recommendation. Second
one's a quality recommendation. The third one is the normal performance
recommendation that we've been seeing. So the first one is actually telling us that
our composer dependencies are out of date. The second one is talking about some
garbage collector that should be disabled in production. And if you're not sure about
that, as always, you can click in and go a lot more information about what's going
on. Now one of the really cool things about all of these recommendations is that you
can easily turn them into tests. So if you click on assertions, you remember that we
created one test that said that everything that's profiled should, uh, have one HTTP
request or less. And we configure that inside of our dot Blackfire.yaml file. We set
up this test here and it said every time we run any profile, um, we should make sure
that this, uh, expression passes.

If you look over on the recommendations and click on it every, all more information,
all of these at the bottom and includes something that you can copy into your
Blackboard..yaml file to make that type of test. So it's a great way to, uh, if you
like some of these recommendations to actually turn them into tests so you see them
as, um, uh, under the assertion section. And in a minute, these assertions are going
to become much more important because we're going to talk, because next we're going
to talk about something very important that environments allows us to do. And that is
to create builds. That's where Blackfire automatically profiles your site at every
few hours, and those builds will fail or pass based on your assertions. This is a
fundamentally important concept with environments, and we're going to talk about it
next.
