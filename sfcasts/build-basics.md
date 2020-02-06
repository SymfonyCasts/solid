# Automatic Performance Checks: Builds

Coming soon...

I back by Fred IO and let's click environments and let's click and look at our new
environments. So interesting. It actually takes us not to have profiles tab but to
builds tab and if you look over here, look at periodic builds, builds are started
every six hours and we can even change that to a different levels and down below
there was a bunch of notification channels where you can set up lots of different
ways that you want to be notified of the results of this build that thing. So what
the heck is a build anyways? Well it's find out, let's trigger one manually. We can
actually hit start a build. It has our end part right there. We can give it a title
if we want. I'll just hit start a new build and interesting it says untitled scenario
and then it went to the homepage. Chickens out. What it actually did is actually went
and created a single Blackfire profile for our homepage on production and hold on a
second. There's a lot of interesting things going on here. First of all, we've seen
this word scenario before. This isn't word that we saw inside of the Blackfire player
file that we created earlier. More on that soon. Second, the reason that it created a
pro when we, the reason that the build made a single

profile to the homepage is because if you look back and builds, when we set up our
environment, we created one URL to test this as a step one. We went through very
quickly, well, we just configured one URL. Let's actually create a second. You were
out here. One of the other pages that we've been working with is /API /get hub
organization. So that's just this JSON end point. So let's copy that you well, let's
add that as a second URL to test. It's now let's save and let's start a second build.
So it's like before it creates a one untitled scenario. Ah, but this time it actually
profiled both pages and the glory and shit. And check this out.

The bill shows up as green and the reason is you can see one successful constraint.
Remember we added a constraints that a constraint or a test that every page should
have one or zero HTTP requests. So thanks to the one test that we put in
Blackfire.yaml

every our bills can actually pass or fail. This build is passing because all the URLs
are being configured or making one or fewer HTTP requests. This is the real beauty of
his test section. You can actually start to configure, um, different constraints that
should pass whenever these builds happen. But there's even more cool stuff going on
here. If you click down here, you can see player output. Check this out. It actually
shows us what's going on behind the background to make this happen. The Blackfire
servers are actually using the Blackfire player

and kind of look closely here. Look at this. It says input scenario visit URL method
gets, and then visit URL, get hub organization. You kind of use your imagination
here. What this is doing is it's actually running in the Blackfire player and it's
writing scenarios just like the scenarios that we have in our BKF file. It's actually
writing scenarios and passing them to the Blackfire player. So behind the scenes it's
using the Blackfire player. You can even see it reloading the homepage and they get
up organization page multiple times. So that's the um, uh, the 10 times that each
profile creates.

So with just a very tiny bit of configuration, we now have Blackfire creating a build
every six hours, which means it creates a profile for these two, these two pages and
thanks to our one test, which we only have one right now, but we at least have one.
Our builds can show as passing or failing and we can even set up notifications to
notify us whenever, for example, a build fails, but the fact that this is using a,
the Blackfire player behind the scenes makes me wonder, instead of just configuring
these URLs, can we actually have a build run our scenario file. I mean, this is a lot
more powerful. We can click on link so we can fill out forms and we can add asserts
on specific pages instead of having only these global, uh, tests. And the answer is
yes. And that is really how you start to unlock the power.

We're going to talk about that next. But before we do, I want to show you what it
looks like after you've let your system create a number of profiles. So I'm just
going on here and give you a peek into the Symfony cast production. Once you have a
number of profiles, you can see here, I have about a week where their profiles, you
actually start getting this really cool graph, some of the bills. So if I scroll down
here, you can actually see I have many bills here that are having every six hours and
we can actually see some history over time of how our OPC cache is performing. Our
cache hits, um, our cache settings, you can see my op cache intern strings Buffer's
actually full, so I need to fix that. And anything else that you can think of over
time, um, on those, on your site. So it's a great way to keep an eye on things and I
can click into any of these bills if I want to and actually go and check out the
profile that was created, um, for that particular thing. All right, so next, let's go
back to our environment and make the build smarter by executing our custom scenarios.
