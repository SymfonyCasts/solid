# Environments & Staging Machines

All right, so now I'm gonna go over to the installing thing. I'm going to select a
Symfony cloud and that will take us back over to the Symfony cloud documentation.
Okay, now there's two bits of configuration that we originally set up. We use this
bar set to tell Symfony cloud what our environment, a server credentials work. Then
we did this integration add in that so that every time we deployed in the master
branch it would create a bill. We're actually now going to go back to our
organization and I'm going to create a second environment. Let's call it Sasquatch
sightings, non master and for the environment end point. I'm going to use the
production environment end point, but as you'll see in a second, that's actually not
going to be needed. So I'll hit create environments and then I will take off my bill
notifications and hit save settings. And then I'll hit view environment. I'll go get
those credentials in the second. And the first thing I want to do is on our bills is
I'm going to stop the periodic bills. The periodic bills would make periodic builds
to my production. You were all, I don't want that. Instead, what we're gonna do is
every time we deploy to our staging environment, we're going to create a build when
we deploy. So not an automatic build every six hours, only when we deploy to these
other URLs, to these other staging things.

So now on a high level what we, now that we have these two different environments
here, we want the master environment to use the Blackfire server ID and token from
this environment and what every other deploy on Symfony cloud to use the Blackfire ID
and token from this environment. And we're going to do that with a little Symfony
cloud trick. So first thing I can say, Symfony bars. This will give me a list of the
variables that we defined earlier. So I'm actually say Symfony bar, delete Blackfire
server ID, somebody of our delete Blackfire server ID and wifi fire server token.
You'll see why I'm deleting these in a second. Now I'm going to go back over to the
installation page. I'm going to refresh so sees my new environment. Then I'm going to
go down to the non master one. That's the new one we just created and I'm going to
copy its settings and then come over here and paste those. Now at this moment what
this is going to mean is that the master is that the master, the production, the
master deploy,

every single deploy we do including the master deploy is going to go to our new non
master environment which is not exactly what we want but now I can switch over to the
production environments. I'm going to copy that command again and I'm going to
override this value but only for the production environments and the way I can do
this is by pacing that command but then passing a dash dash in B = master. It says I
want to set that variable on master only. Then dash dash N dash level, those two keys
there said say set this only on the master branch and that's going to override the
default one that we set for the project and then I'll say dash dash inheritable =
false, which says whenever we create a new environment off of master like we just did
a few minutes ago, it's not going to inherit this value. That's a long way of saying
that the master environment on Symfony cloud is now going to use the server ID and
server token for our Sasquatch sightings. Production in every other environment is
going to use this non master one.

We can see this if we were on Symfony VARs, dash dash and = master. Yup. Let's see,
six nine zero six nine zero and then somebody bars. That's just N = some feature. You
can see it uses the other value. So we are perfect. The last thing I want to do is go
back over here and down here. I'm gonna go back to non master and I want to do this
Symfony integration ad. This is going to be the command that makes it so that
whenever we deploy to a non master, whenever we do deploy, it creates a bill. It's
all clear. My screen pays that we want to report all events, all States, and for
environments in reality. What I want here is I want to send trigger a web hook to
this environment on every single branch except for master, but I don't think there's
a syntax to do that. So I'm just going to do it always. You'll see that you'll see
the effect of that in a minute. Okay, so let's actually redeploy both environments
here. I'm gonna say Symfony redeploy and since I'm on these some feature dash dash
bypass checks, and since I'm on the some feature branch, it's going to deploy to that
environment.

And then I'm going to add dash F N V = master to redeploy that one. I could also have
done get checkout master and then Symfony redeploy to redeploy the master branch.

Okay, done. Now let's go see what that did in reality. So first if we look at the
production environments, you can see that this just created a new bill and that is
totally expecting cause we just redeployed to the master branch. If we look at our
non master environment, this just created two bills, one for these, some feature and
another one from master, which we don't really need this one but it triggered it and
that's fine. So now every time we deployed to any non master branch, we're going to
have a log of it inside of this Blackfire environment. And the last step here is if
you look in the bottom get hub integration. If you're using GitHub and you've set up
the synchronization synchronization between get hub in Sydney cloud, don't forget to
configure the black fire get hub notification channel that fire that way Blackford
will post a status on your get a pull requests. It's basically means that we can use
this notification channel and it will automatically Pope run a post the status of the
build on your poor class. I want show that here, but that's something that you can
do.

The last little feature that I want to talk about now that we have these two
environments is that in reality, and this is true of Symfony cloud, sometimes the
your production infrastructure is a lot bigger and more powerful than your staging
infrastructure, which means that your builds on staging might run slower than your
production bills and that's a problem if you have things like this, a certain main
not wall time has less than a hundred milliseconds. That might be true in production,
but it might not, but on staging it might run slower and so maybe on staging you only
really care that it's faster than 200 milliseconds. If you want. Inside of here you
can use it variables, so I'll add some parentheses here and I'll say times and you
can say VAR and then inside of here I'm going to invent a new variable called speed
co efficient and then give us a second argument, which is one, what does this thing
is that when it does this assertion, it's doable. It should actually assert it
against a hundred milliseconds times whatever the value of this speed coefficient
variable is, which we haven't set yet and if that variable isn't available, then just
default to one.

So in the master environment on Blackfire, we won't set this variable, but I'll copy
this and over in my non master environment down here, I can set a variable, must set
this to two and it's safe. All right, let's swing back over. Adding speed coefficient
variable for a wall time cert. As a reminder, we're on our some feature branch, so
I'll say Symfony deploy bypass checks. All right. When that finishes, let's go over
here and because we just deployed the feature. Yup. Cool. We have a new build for
some feature and if we look inside of here, there's two cool things I want you to
notice. The first thing is that under the homepage you can see that the speed
coefficient, it kind of puts a little too here. It's saying that that = two, so
really it's me checking to make sure it's less than 200 milliseconds, which is really
cool.

The other thing I want you to notice is that if you go back to builds, we've now
built the some feature branch twice. So when you click on the second one, it actually
has the comparison to the latest successful bill. Now late a successful build is the
original on that branch. So when you're looking at the comparison on a branch, the
comparison is to that same branch, not to, you know, just the build right before,
which would be master. So we can do all of our diff stuff and look at the comparisons
and it's doing a really smart job of comparing them to things on that branch. Our
friends. That's it for the black bar tutorial. I hope you had as much fun as I did.
Uh, using Blackfire and a lightweight is a super fun way to find performance things.
But boy, if you dive in, you can really get a reach, a rich feature set with this
build system. I personally had been loving having Symfony casts, production, having
these graphs and watching my memory usage, uh, and tweaking things over time. And
we're just getting started with [inaudible],

our, um, [inaudible]

set up here and I'm excited to see where it goes. As always, if you have any
questions or we didn't explain something, we're here for you in the comments, so let
us know. All right. I wish you a very, very

fast day,

right friends. See you next time.
