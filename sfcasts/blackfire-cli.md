# Blackfire Cli

Coming soon...

We know that the probe, the PHP extension, the Blackfire PHP extension, it doesn't
run on every single request. It only runs when it detects that our browser extension
is telling it to run. Then it can run and do lots of heavy things and it's not
slowing down real traffic. There's actually a second way that you can instruct the
probe to do its work, and that's via a command line tool. So I'm gonna go black to
back to Blackfire, click on their docs and go back to the installation. One of the
things, the one thing we skipped on this document earlier was something called, we
scroll down and the Blackfire CLI tool. It's technically optional, which is why we
didn't do it yet. I'm on a Mac, at least. It's actually already installed. You see
this brew install Blackfire agent. This is the same command that we ran earlier to
install the Blackfire agent. The wildfire agent actually comes with the Blackfire
command line tool. So on your system, if you need to install it, go ahead and install
it. Ultimately you should be able to, ultimately, you should be able to, in a run

```terminal
blackfire version
```

and you see that it's
working. Now in this case, did you need a little bit extra configuration? So you can
see right here, we're gonna run `blackfire config`. So I'm gonna copy this client ID
and run 

```terminal
blackfire config
```

It's gonna ask us for the client ID. We'll paste that and
we'll go over here and copy the token and we'll paste the token as well. So the
Blackfire client and Blackfire token are almost like your username and password to
Blackfire itself. So when we're using the browser extension, we're actually logged
into Blackfire via the browser and that allows, when we click profile, um, for
Blackfire server to give us some, some credentials that it passes to our probe. And
that proves to the probe that we can in fact profile this page. The client ID and
client token are kind of a username and password that the command line tool can pass
to the probe to do the same thing.

So it's I, it's identifying us, uh, as our Blackfire user and uh, which then does
something, all right, so this Blackfire tool has two superpowers. The first thing is,
is that you can actually run `blackfire curl` and then you could make a web request to
something and it will profile that page. Now that might seem totally worthless, you
know, after all, once if we have this awesome, uh, uh, browser extension one, or
would we ever want to go to the command line and run Blackfire curl? But actually
it's the key, the best way to profile Ajax requests. So check this out. I'm going to
go to inspect on my page, open up my network tools here, and then refresh. Notice I'm
already on the XHR thing here. So you can see the `github-organization` requests come
up that loaded this sidebar over here. Now the fun fact is, if you right click on
this, you can say, copy as curl.

That's gonna create a curl request over here, I'll kick command + C. that includes even
all of your cookies. So you're going to be authenticated and that is going to return
that page. Now with Blackfire we can say `blackfire` space paste that. So `blackfire curl`
back command and that is actually going to profile that page. This is my
favorite way to profile, uh, Ajax requests. And down here it gives us the graph and
you were out pop over there just to make sure that that worked and it did awesome. So
the second super power of the black fire utility is really the main thing it's
supposed to be used for. It's really great for profiling command line scripts. So
check this out. Right now I have a built in command line script right around 
`bin/console app:update-sighting-scores` Now before I run that, just give you a
little explanation.

Whenever you look at a specific Bigfoot sighting, it has a big foot believability
score and right now they're all set to zero. Uh, our site has a very custom
proprietary complex solution, um, to figure out what the score believability score of
each big site big society is. But it's such a big thing to record. Instead of loading
it on page load, it's actually saved as a column on that table. And then we run this
command once a day. And this updates all of the scores for all of the big settings in
the database. So if I run this, 

```terminal-silent
php bin/console app:update-sighting-scores
```

there we go. Takes a few seconds. Now if I go back
over and refreshing and see and set the big foot, we're about big foot believability
score.

The code for this is in `src/Command/UpdateSightingScoresCommand.php`, uh, down here.
Alright, so is this performance, could it be more performance? Well let's find out.
Let's run that same command again, but I'm going to put Blackfire run in front of it.

```terminal-silent
blackfire run php bin/console app:update-sighting-scores
```

Do Amelie notice it's a lot slower than it was before. And that's the evidence of the
PHP extension running. That's the effect it has on it. And we don't really care about
that cause it's not about absolute speed. It's all about just finding where we can
improve things. In fact, this is interesting. It's getting slower and slower and
slower. I'm actually gonna speed it up here at the end just to save time. All right,
so let's go check that out. So the time is not really meaningful, but let's go see if
there's any obvious ways that we can make this better.

So let's load it up and into arresting `computeChangeSet()` was called almost 500,000
times and it's taking up half of the stuff here. Uh, so let's actually look down here
and it's a really, because it's such a big problem, you can see it's pruned a lot of
the stuff here. So here's our command, here's `EntityManager::flush()`, and then it goes
into doctrine stuff. So let's go check out the command and look for entity manager
`flush()`. So over here, and I start flush here, it isn't exactly one spot. If you use
doctrine before, you've probably spotted. My mistake here we're doing is we're
querying for all the Bigfoot sightings looping over them. I'm updating the score here
and then in order to make an update and it actually saved that I'm calling `flush()`. The
problem is that with doctrine, you don't need a call flush on every single loop.

It's totally fine to move this down here at the end. What it's going to do then is
it's going to try to make just one query to do all the queries at one time instead of
doing the queries over and over and and over again to make things even more complex.
Doctrine has something called a `UnitOfWork::changeSet`. Every time you call `flush()`,
it actually has to go through all of the things it's queried for and check if they've
changed. That's actually why we see this big `computeChangeSet()`. It's doctor and
trying to figure out which objects it actually needs to update in the background. So
with that, let's go back and try that again.

```terminal-silent
blackfire run php bin/console app:update-sighting-scores
```

This time it's much faster. A lot of profile and I don't even think we need to
compare it. Um, it's crazy faster. So 56 seconds down to one second. Could we
optimize this further? Maybe now again, there's always a cost of this complexity
here. I originally put this flush up in the four loop because that means that even if
this process maybe, um, only gets halfway through, it would still, and then an air
happened, it would still update half of the records down here. It's not going to
update any of the records unless we get through this algorithm successfully for
everyone. So it's beyond the scope of this tutorial. But with long running scripts
like this, there are multiple different solutions. For example, we could query for an
array of all the big foot siting IDs in here. And then in each, for each, we could
actually then query for the one siding updates its comments `flush()` and then call
`EntityManager::clear()`. That makes it, keeps the chain set a nice and small, um, but
then allows us to flush still on every loop. The point is way what you actually need
with your application, um, with the performance stuff that you're seeing from them.
One shouldn't dominate the other one. [inaudible].