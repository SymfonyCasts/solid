# Blackfire Environments

Coming soon...

Hey, we have a fancy new diploid site. So how can we profile this in Blackfire?
Actually, we already know the answer I searched for on Blackfire install, I can find
the Blackfire installation page and that's very easily helps me figure it out. I
don't want to install Blackfire on a server and if we pretend that my server was a
Ubuntu right here, then down here it's going to show me below all the different
commands we need to run to install the Blackfire probe, the Blackford agent and our
configuration. So super easy, but some Blackfire account levels have a kick ass
feature called environments in if you have the environment feature level, you
absolutely want to use it. And environment is basically an isolated at Blackfire
account. You sit in, when you create a profile, you actually send it to that
environment and then you can invite multiple people to the environment so that
multiple people on your team can view the profiles and create their own profiles. It
also has various other superpowers, which we're going to talk about over the next few
chapters.

Okay,

so let's get in and actually create an environment. So I'm going to go to black
fire.io and I have an environment stab here and actually the first thing you need to
do is create an organization and an organization. Um, well actually have is a lot
like a good hub organization and you'll attach your um, billing information to the
get hub organization to the Blackfire organization. Once you have an organization,

oops,

what'd you have? An organization, you can click into it and then you can click create
an environment. You see I already have a Symfony, cast.com production environment
down here, let's create a new one.

Let's call it SAS Squatch sightings production. And here it's going to want an
environment. Endpoints actually wants the URL to our environment. Again, if this were
a real project, I would attach a real domain and use the real domain. So paste it
there and I will set elect my time zone. All right. Then we'll hit create
environment. On the second step it tells us to provide URLs to test and you can
provide several of these. Right now it just has one, which is the homepage. We're
going to talk more about this later. So let's just leave that there down here. You
can configure build notifications. I'm going uncheck those for now. We'll talk more
about those later. So save settings. Now this is really important. Uh, as soon as you
create an environment it has a different server ID and server token than your
personal than your personal account suite.

We're going to see more, we'll talk about this in a second, Brian. Now let's click
and view environment. To me now I have two types of credentials. We have personal
credentials for our personal account, personal server ID and server token and you
should continue to use those features that those locally. And then we have our new
environment, server ID and server token and that's what we should use on production.
By the way, if your organization has a higher account, if your organization has a
paid account and your and you personally don't, when you profile using your personal
server ID and server token, you are going to inherit whatever features your
organization has so you'll automatically get those. All right. So now we can take
this new environment, server ID and server token, and we can follow the Blackfire
installation page. So I'll say server ID and suddenly you can actually click which
environments you have.

Okay?

And down there you're going to get all these specific commands that you need to run
to actually use that environment. Now in case because we're using Symfony cloud, we
have some special instructions. I can actually click down here to Symfony cloud.

Okay?

And it takes me to the Symfony cloud extension page. So first thing all I need to do
with Symfony cloud if I want to have the black back extension, is update my dot
Symfony clown.yaml file. So let's go over here that Sidney cloud, .yaml, I already
have an extensions and we'll just add another one here by fire. Boom, that's it. Now
move over and let's commit that adding Blackfire extension. So that takes care of
installing what I need to install. The other thing we need to do is pass the
credentials. So down here again has a dropdown menu. I'm going to click our Sasquatch
production and we're to use, this is going to basically set an environment variable
with a Blackfire server ID in Blackfire server token, saw competent clipboard. Run
over.

Okay,

paste and we're good. Now I'll clear the screen and we can run it. Symfony deploy,
which is gonna redeploy the environment with those environment variables. And it's
also going to

okay,

redeploy with the Blackfire extension. I heard a pass that dash says bypass checks.
Cause right now we're ignoring some security problems. Cool. Once that finishes,
let's move over and refresh the page. Everything still works, no surprise. And the
moment of truth. Let's see if the profile works. So hit profile. I'll call this one
recording. First profile on mass on production, and it worked. So next, let's look at
this, uh, profile and see a couple of other different things that are going to happen
now that we're on production and now that we're profiling inside an environment.
