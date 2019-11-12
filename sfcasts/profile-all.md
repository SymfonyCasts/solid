# Profile All

Coming soon...

Now one we've been profiling, you may not have noticed. A couple of options on here
that I just want to mention. Um, and the options you see here may depend on what your
black fire level is. So they do have something called a debugging mode where you can
actually disable pruning and anonymization. So you're going to call trees that are
full of garbage and have real information on them. Um, which is not as useful for,
for debugging but is useful for um, uh, D not useful for profiling but better for
debugging sells something. It's called the dis distributed profiling. This is
incredible if you have a micro service architecture where you're making API requests
in the background, if you install Blackfire in all of them, um, then by default when
you enable a page, if that page makes an eight, makes an API request to one of your
other microservice apps.

The final, uh, profile is going to contain a sub profiles which are going to show you
how your entire infrastructure is working together. You can also do disable
aggregation. Uh, it only take one request information instead of multiple in case or
as side effects. But it's a really cool thing. I'm gonna show you this profile all
requests. So right now this looks like a pre traditional page here. Uh, but I'm
actually going to hit uh, profile requests. And here you're actually going to start
recording. So I'm gonna hit record and I'm going to refresh. And cool. You can
already see there's actually two requests on here and if I scroll down a little bit,
suddenly there's a third request. So let's just stop right there.

And when I click see these three here, these are the three profiles that were just
created in the background. This one here is the homepage in these two here, actually
Ajax calls. So without even thinking about it, surprise, we were able to discover two
HS calls happening on the site. This first one here, the API /get hub organization.
This is actually a little Ajax call that happens on load. You can kind of see it that
loads all of this repository information over here. This is just a simple little, a
API call that goes and finds the, uh, most popular repositories over from the Symfony
casts a get hub page. It's a really good example for net. Rick never requests this
other one here and this slash_sightings that's actually powering the forever scroll
on this page as I scroll down more low. So that's just a really great way to actually
get an idea like what's going on behind your scenes.

It is not the only way, however, it's also a really great way to profile form
submits. You can hit record right before you submit the form. Now it's not the only
way though, to a profile. Ajax calls. I'm gonna show you a really cool way to profile
Ajax calls in a few seconds, in a few minutes. But let's check out over here the, uh,
kind of get hub organization. Uh, one, as I mentioned, this goes and makes an Ajax
call, uh, an API call to the get hub API to load repository information about the
Symfony. A repositor on there. And this one is almost comical. You can see 438
milliseconds, uh, 82% of it is curl multi-select. In other words, 82% of it is the
actual time it's taking to make the API call pretty obvious. Um, now kind of fun
thing is if you look at the CPU time, which is only 74 milliseconds of that curl,
multi-select is still the biggest offender, but you can see it's a lot less obvious
what the critical path is here.

Whereas if you click on IO /wait, because this includes network time, it's comically
obvious. Now, one of the interesting things here is this is not the full call tree
like right? You can see it goes right from Handel, the beginning of the framework
being done all the way into the controller. Normally you see more layers than that.
And if you switch to the CPU, you can see all kinds of extra layers. This is
something that Blackfire does, which is really nice, and it's called pruning. It's
gonna prune the, it removes the node information that's less important. So the more
obvious your critical path is, the more stuff is going to be able to remove because
it's just noise, it's garbage.

So in this case, it's incredibly easy to see what the path is. Also, you can see the
a network calls themselves up here. So in here you can see actually there's two
network calls here and there's one, uh, that returns a 1.5 kilobytes and another one
that recurrence returns five, uh, kilobytes behind the scenes. You can say the
network time, the time here is not actually honest. It's because of the asynchronous
nature of the, uh, request I'm making. Um, but you can see that there's two API
calls. So how do we fix this? Do we cache? Do we somehow try to make only one AP call
API call both. We're actually gonna revisit and fix this problem later. For now, I
wanted you to be aware of the profile all as a way to see what's going on your app
and et cetera, et cetera, snacks. So we're actually gonna use the Blackfire command
line tool, which is the second and my preferred way to profile Ajax requests as well
as profile command line applications.