# Deploying to SymfonyCloud

Coming soon...

Transition point, everything we've talked about so far is about profiling locally.
But things get even cooler when we talk about profiling on production because real
data, having real data in your database often causes different performance problems
that you're not going to see locally. And because of the way Blackfire does, its
profiling, profiling has zero impact on your end users. So you can use Blackfire on
production without actually slowing down your site. And once you're in production, we
can also start talking about some more interesting things like having Blackfire
profile your site every few hours and notifying you of issues more on that later.

So to show how black is used on production, let's actually deploy our site to
production. You can of course use any host you want. I'm going to use Symfony cloud
because it's what we use in Symfony casts and it makes deployment dead simple. It
also has a free trial. So if you want to try it with me, you can do that without any
cost. So move over to your terminal. And I'm gonna start by running Symfony project
in knit. This is going to create a couple of, uh, files that, uh, simply cloud
configuration files. Uh, for us, the most important one being a new dot Symfony, that
cloud.yaml file. Let's go check that out. That's something cloud.yaml and it's using
PHP 7.1 we want to use feature 7.3. We changed that one spot and good down here it
gives us a big get add command and commit command to add all those new files and
commit.

So I'll copy that and paste it. Boom. And now we can deploy by saying Symfony deploy
because this is the first time it's going to ask us a few things. Do we want to
create a new Symfony cloud project? Every application has a project, we'll say yes
and then it's going to ask us. It's going to tell us that we get a free seven day
trial. But it does ask you for your credit card just to make sure people don't abuse
this. I already have my credit card in there so I'll hit zero, no VAT number and
we're good. Yes to confirm. Uh, when you deploy, you might also need to do a dash
dash bypass checks flag if your code has some security vulnerabilities, which
eventually might be the case in the code we're using in this project. Now, the cute
little Symfony cloud robot spinning up our project. Uh, once that finishes creating
the project in the background,

now that we've got the basic black bear config files, we're going to run Symfony
project create the first time. This is going to create a project on Blackfire side of
things. Every project will have a, every application will have a project Symfony
cloud project. You can ignore the big yellow warning here. That's because I have a
couple other Symfony projects in the background. I'll give this the [inaudible]. I'll
give this a nut, the name of our project Sasquatch sightings. I'll pick the
development level and then you can see up here it's actually going to give us a free
seven day trial, which is awesome. You do need to enter your credit card information
so that um, to avoid fraud so they can avoid abuse. Uh, I've already got my entered
in so I'll just get zero to select that and then I'll confirm from the background.
This is actually spinning up a new project and Symfony cloud getting all of our, uh,
you're getting all the infrastructure ready for us.

Perfect. Now we can deploy by running Symfony deploy. Now when you run this, you're
probably going to get a big air like I did, says the application has known security
issues and its dependencies. This is a cool feature is Symfony cloud where it
actually checks your dependencies and looks for it to see if you're using any
versions of security problems. Um, we are, I'm actually going to run desktop bypass
desk checks to get around this and a real project. You should actually upgrade these
since we're just in a dummy project. I'm going to skip that by running dash dash
bypass checks. It'll still give you that big message, but it's actually deploying in
the background. You'll see a bunch of code and it's taken care of everything for us.
It's taken care of, uh, running our database migrations, running composers.

The first time you deploy it might take a little bit of time, especially to download
all the dependencies it runs. Our Webpack Encore commands automatically creates an
SSL certificate for us. You will want to make sure that you're on your master branch
when you run this. You don't have to be, but it will make life easier and done. You
can actually see the URL here. I'm going to copy our new URL. It's got this funny
domain of course on production you can attach this to a real domain. We're just gonna
use this dummy domain for now. We'll spin it back over, pop that in our browser and
500 air. Okay, so let's see what's going on. Nice thing is we can spin back over here
and run Symfony logs and this will actually allow us to look at the production logs.
So let's actually look at our app, our VAR alarm prod.

That log right here app is the directory that the applications deployed to. So I'll
hit zero and of course an exception has been occurred, connection refused. So this is
actually a database connection problem. Not surprising cause we haven't even set up a
database yet. So let's go do that. I'm gonna go over here and just Google for Symfony
cloud my SQL and we just need to add a little bit of configuration to get this
working. Uh, we're gonna modify two files. The first one is this start Symfony
services.yaml. This is where you tell Symfony cloud that you need a database, maybe
elastic search, maybe Retis hospital. A common up here. That's, this is Maria DB. So
that's why the version of 10.2 you see here as Maria DB version. Now notice we've
used the key my database here. That can be anything that's going to become important
in the second change would we're going to make, which is in that Symfony cloud.yaml.

Inside of here we need to add eight relationship's key. This is what binds our
container to this database. Right now we don't have a relationships key yet, so let's
just add them, say relationships and then what we're going to put here is a database
set to the string. My database, colon, my SQL. That syntax is a little funny that my
here is referring to whatever we call here and because this is a my SQL type, that's
what we put over here. Now the really important thing here is we call this database.
The effect of that is that Symfony cloud will expose the connection parameters to
that database as database_URL, which is important because that's the environment
variable that we're actually using for our database. So we did want to call this
database because it makes all the configuration just work instantly. All right, so
let's go back over here. I'm to hit control C to get out of my logging. Well commit
with adding as a cloud database and then we'll say Symfony deploy again.

Oh of course I need to do my dash dash bypass checks. This time that deploys faster
is loading composer dependencies from cache and when it finishes, I'm going to be
even lazier this time and say Symfony open remote. That's a little command to open
the remote. You were out in my browser and this time we've got it. It works just like
that. We have a deployed site. There's not much data on it, which doesn't make it
very interesting. In a real site, you probably start using your site. And this would
naturally fill up with data. Um, to make it a little more interesting for us, let's
actually run our fixtures on production. Now this is a little bit tricky because the
fixtures bundle is something that is not, is a dev dependency. So it's not even
installed on production. So you can't actually run Symfony as S H to SSH onto a
Symfony cloud.

But if we did this, the doctrine fixtures bundle isn't actually installed here. So
instead we're gonna do as a cool tunneling feature. I'm gonna say Symfony tunnel
open. That's actually going to expose the, uh, uh, my production services and in this
case the database service locally so I can actually communicate with them, uh, via
local ports. Now I can override the database URL environment parameter by saying
database, you all = my SQL corn /lash root colon. Uh, because the production database
uses, um, root user with no password, it can do that because it's completely isolated
and nothing on the internet can talk to it. And now because of that tunnel, we can
say one to seven dot. zero.zero.one colon, 3000 /and then the date name of the
database is always Maine. Now we can say bin console, doctrine, fixtures,

load.

You're overriding the database, your environment for this command in. It's using the
tunnel at the top of the database. There's actually an easier way to do this type of
thing. Um, but we'll talk about that in some future Symfony cloud tutorial. Now,
because this is going over a remote connection, this can actually take a little bit
of time to run. So be patient. This might actually take several minutes to finish.
When it does, let's move over. And there we go. Our production site with at least
some data to make it interesting. So next, let's get Blackfire set up on our server,
which is both can be super simple, but also different than you might expect because
we're going to leverage it in super important feature. New feature inside of
Blackfire called environments. [inaudible].