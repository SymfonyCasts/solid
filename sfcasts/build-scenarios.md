# Builds with Custom Scenarios

Coming soon...

A few minutes ago, we created this scenario that becameF file, which is a language
which can be used by the Blackfire player to execute these nice little scenarios and
this tool, it really has nothing to do with Blackfire. It's just a tool that read can
read these scenarios and visit this page, do some assertions on it, click link and so
on. We ran this, you ran this by saying Blackfire player run and then pointing it at
that file and we also added a little dash dash SSL dash, no verify just because we
have, it isn't like our SSL certificate locally and that's how it works, but we can
do well. We can use this scenario actually now to power the build behind our
environment. Here's how I want you to copy this entire scenario. Then I'll close this
BKI file and go to that black Friday Yammel and add a new key here called scenarios.
This has a bit of a funny syntax here. We're going to put a pipe, which means it's a
multiline, a format and emo and then pound sign, exclamation point, Blackfire dash
player. That's just the format they want in this file. Now we can paste that into
here. Make sure that this scenario is just like this. It's invented out four spaces.

Now as soon as we do this, we can still, if we want to just execute this via the
Blackfire player. Now I can say Blackfire player and instead of running scenario that
BKF I can point this to the Blackford at Yammel file and it's smart enough to know
that it can look under this scenario is key for our scenarios. If I run that, the
only thing it's missing is that it says unable to crawl a non absolute URI. /did you
forget to set an endpoint when you have these individual files, there is an end point
configuration. That endpoint configuration doesn't exist in that Blackfire .yaml so
you just need to pass as an option. So I can say dash dash endpoint = HTTPS colon
//local host colon 8,000 perfect. Okay, so the really important thing though is like
what does this allow us to do? So I'm actually going to commit this moving scenarios
into Blackfire config file and then do a Symfony deploy to get that up on production
with my dash dash bypass checks.

Once that finishes, let's go see what that changed. Now, first of all, if we just
went to our site right now and for example, profiled the homepage, that would make no
difference at all. Having these scenarios instead of our black Friday ammo makes no
difference when you just want to manually create a profile. It does affect when we
create bills. So let's start a new build here. I'm actually going to give this a
title call with custom scenarios. Um, this time, instead of doing that untitled
scenario where it just tests those two URLs we gave it, now it actually does the
basic visit scenario does the homepage and does the login page. In other words, as
soon as we have this scenario's key, that black Friday Yammel, it no longer just goes
and T no longer goes and tests these URLs here. These are actually meaningless.

Now as soon as we have the scenarios key, we are actually taking control and instead
it's executing our scenarios in creating one build for each of these pages inside of
there. What's better is that we have a lot more control note now over the constraints
or the tests that make this build pass or fail. So for example, each of these is
going to use the global HTTP requests should be limited to one per page. So both of
these have that constraint because this is, it's going to run all these tests up here
against all of those profiles. But the homepage also has two other things that they
expect. Stats go do a hundred and it expects, for example, not expect it has this
extra cert down here that the SQL queries on this page should be less than 30 and if
you look over here, it does actually have that assertion. We can even click into open
that profile and if you looked over here on the assertions part, you can see those
two assertions showing up.

So not only do we have a lot of control now over exactly how we want to test the
pages, we can even fill out forms, but now we can do custom assertions on a page by
page basis in addition to having these global ones up here. That's super powerful.
I'm gonna remove this comment down here because now the reason the black player
environment that assert it does work. So now that we're only, now that we know that
these scenarios are going to be run only on our production environment, they're not
going to be executed. For example, if we just profile a local page, we can maybe do
some time based metrics, but because we are, because the production environment is
just your production machine that will have less variability than multiple machines,
but you need to be conservative with them. So let's done add down here and assert
that main, that wall time is less than a hundred milliseconds. And I noticed that
most metrics start with metrics dot. And we can go look at the timeline for all of
those metrics. There are a couple of metrics that a wall time and a peak memory that
start with Maine.

So as you can see over here, our homepage on production is normally coming about 50
seconds. So a hundred milliseconds is a fairly conservative measure to look for. So
let's start over here. I that and deploy, by the way, while that's deploying, one of
the thing that we're not going to have time to talk about, but our, I mentioned our
custom metrics. So if you search for Blackfire metrics, Mmm. In addition to the
timeline, this page is actually going to give you all of the metrics that are fully
possible in the system, which is super nice. But you can also make your own custom
metrics. So if we look, custom metrics can be defined in a dot. Black Friday Yammel
file. So right inside of our.by Friday Yammel file. In addition to tests and
scenarios, we're allowed to have a metrics key. And this is really cool because you
can create a metric like markdown to HTML and give it a name, give it a description.

And then you can use very specific logic down here to say when this metrics called.
So in this case, the markdown to HTML metric happens whenever the two HTML method is
called on some markdown class. And you'll immediately able to be able to start using
that inside of your tests or inside of your asserts down here on your scenarios. And
this can get very, very complex and you can look for multiple things. You can even,
uh, group things with a Boolean logic or logic, um, classes, functions basically any
way you can think of to, uh, can you do anything, even separating metrics by
different arguments, uh, to get as close control as you want to. So if you wanna
create your own custom metrics and it's very powerful API to do that. All right,
let's go back. Okay, this finished deploying. So let's go back now and I'll close
this up and creates another bill.

So let's start build, I'll call this one with homepage wall time assert, start that
new build and perfect it passes, and now you can see that there's an extra constraint
on the homepage that the wall time needs to be less than a hundred milliseconds. If
it's not and you have your notifications configured, you're immediately going to get
a, um, an air. Now, next, now that we have this idea of creating builds and actually
builds happening every six hours, we can do a couple of interesting things. The first
thing we can do is actually start writing a search, a comparison assertions. We can
actually add an assertion up here that says that a particular profile shouldn't, for
example, be more than 20% slower than the previous, that same profile on the previous
build. We can also instruct builds to happen every single time that we deploy. So we
can immediately have a record here of the performance after that deployed. Let's talk
about how to do those things next.
