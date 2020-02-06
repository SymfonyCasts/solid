# Testing a Build Compared to the Last Build

Coming soon...

We already know that. For example, if you profile page and then make a change, then
profile it again. You can use the comparison feature in Blackfire to actually compare
those two profiles to see if the, if your site got faster or slower. When you use the
environment system, you can do the exact same thing with your bills and actually turn
them into tests. So for example, you could say, Hey, I want the on the homepage. I
want to make sure that between one build and the next build at the homepage doesn't,
for example, get, doesn't have a wall time that's 30% slower. You can actually write
an assertion to make sure that it didn't compare Tivoli get much worse.

Took it out. Let's add a conservative new global metric, I'll call it. Pages are not
suddenly much slower and we'll set this to run on every single page. So I'll use the
slash.star and for the assertions we can use a really cool thing called percent
percent of main dot wall time less than or equal to 30% there's also a diff, if you
wanted to say that the wall time was not 50 milliseconds slower. For example, I'm
going to go over here and let's commit that adding a global wall time dif assert and
I will do Symfony deploy. Now, if we dash dash bypass the checks.

Now, if we waited for that to finish deploying and then we went over here and created
a new custom build, we would not see that executed. In fact, let's see that once the
deploy finishes. Okay, good. Let's go over here and let's start a bill. I won't give
it a name and it does the exact same thing it did before. It goes through our one
scenario which profiles the homepage and also profiles the login page. Now if you
look at the three successful constraints here, you see the global one for HTTP
request should be limited, but we do not see the new one at all. There's a reason for
that. These diff constraints, percent and diff. They only run in two different
situations. First when your periodic builds run. So every six hours you will see the
diff stats and those it will actually diff the uh, uh, the current build compared to
the build that six hours previously.

The second way that you can get these diff things to work is that you can, this is
not entirely true, but to simplify, you can trigger, you're going to have an outside
system trigger a build on, uh, based on some consistent event. So for example, you
could, um, one of the things that you can do is you can actually tell Blackfire to
create a build by sending a webhook. In fact, you can see that's what the trigger is
of these. When you do that, if you pass some special information that will actually
link those builds together and it will have this diff in reality that happens in two
main situations. If you set up your deploy system to create a web, a build every time
you deploy, then you can actually see the diff between your deploys. The other thing
is if you set up get hub integration so that there is a build, every time you create
a pull request, it will actually show you the diff between your builds for that same
pull request. That's confusing.

The point is if you wanted to see this different action, we could just wait 12 hours
for the two periodic builds to go and you'd actually see those diffs. Or we can tell
Symfony cloud to create a build every time we deploy. We can just deploy two times
right now and check out that dif. So that's what we're going to do. I'm going to go
back to the Blackfire Symfony cloud documentation and down here I'm going to set
under builds. I'm going to select my environments and it has a Symfony integration
ad, but this basically is, is this configure Symfony cloud that every time we deploy
it's just should send a web hook to Blackfire that says create a bill. And it's going
to do that in such a way that those bills are linked together so that the diff
happens.

All right, so let's spin over here. Then I will paste that we were in port hall
events, so I'll hit enter all state, so I'll enter and for environments, this is
actually a Symfony cloud environment. That's what that term means. I'm going to type
master. I basically am only going to have this happen on the Blackfire master, the
Symfony cloud master environment, and you'll see why in a second. And that's it. All
right, check it out. Now let's run Symfony redeploy. Before I do, I just want to go
over here and let's just refresh this page and see that we currently have five bills.

Symfony Rita, boy dashes, bypass checks. There should be a pretty quick operation and
let's go see, refresh the page and yes, we have a bill. It's still running. Number
six, look at trigger Symfony cloud and it passes. So then on its own is cool. We can
trigger a build every single time that we deploy, but now let's go over here and
redeploy again. I'll go back here and click back on builds. All right, now let's wait
for that redeploy to finish. And now refresh this page. Yes, there is my bill, number
seven for my redeploy and it's already finished and actually go back. You know what,
that didn't do what I wanted it to do. Instead of doing a simple redeploy, I want to
do a meaningless change to my bat black Friday Jamo file so I can make a real commit
here. There's also a way to do this with allow empty on the commit. Now I'm going to
do a real synchrony deploy here with dash dash bypass checks.

Now I want to move over and refresh. That's surprising and there's an eighth bill
from that deploy and when we click into it, check this out. We actually, for each
profile there is a show because what it's doing is that it's actually comparing this
to the latest successful bill. They'll actually click that. It's comparing this to
build number seven, the previous build for the previous commit. So instantly I can
look at the comparison and of course we didn't make a significant change, so we would
expect to see anything. I will check the login page comparison. Yup. And you can
actually see there's actually quite a significant difference here. Yeah, let's not
even do that. We can actually open the comparison if we wanted to. And the really
important thing is that when we look at those constraints, check this out, we now
have pages are not suddenly much slower because it's actually running that diff
compared to before. And actually I want to check this out, open up this profile cause
it's even cooler

and it is do our assertion, we have our two assertions here that are specific to our
actual scenario and then we have our two global surgeons here, one of which is using
the uh, the diff but also once again in this diff view, the recommendations get more
interesting. It actually has some built in recommendations using the diff. So it
actually has a built in recommendation that says that the difference between the
count should be less than two. So you get some free kind of dip stuff in there as
well. This is the dip stuff is probably my favorite way to do time based metrics
because it just measures a big ugly things that might accidentally happen. So next,
what about our staging server? Should it be profiles, staging server. Um, so we can
catch performance problems before we go go to production. The answer is yes, and
we're going to do that with a second environment and be able to do some cool stuff.
