# Database Tricks on SymfonyCloud

Coming soon...

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
