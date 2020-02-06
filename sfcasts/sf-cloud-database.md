# Database Tricks on SymfonyCloud

We just deployed to SymfonyCloud!!! Well, I mean, we *did*... but it doesn't...
ya know... *work* yet. Because this is the *production* 500 error, we can't see
the real problem.

No worries! Head back to your terminal. The `symfony` command has an easy way to
check the production logs. It is...

```terminal
symfony logs
```

This prints a list of *all* the logs. The `app/` directory is where our application
is deployed to - so the first item is our app's `var/log/prod.log` file. You can
also check out the raw access log... or everything. Hit 0 to "tail" the `prod.log`
file. And... there it is:

> Ab exception has occurred... Connection refused.

## Adding a Database to SymfonyCloud

I recognize this: it's a database error.... which... hmm... makes sense: we haven't
told SymfonyCloud that we *need* a database! Let's go do that!

Google for "SymfonyCloud MySQL" to find... oh! A page that talks about *exactly*
that. Ok, we're going to need to add a little bit of config to 2 files. The first
is `.symfony/services.yaml`. This is where you tell SymfonyCloud about all the
"services" you need - like a database service, or ElasticSearch or Redis or
RabbitMQ.

Copy the config for `.symfony/services.yaml`... then open that file and paste
this in. The database is actually MariaDB, which is why the version here is 10.2:
MariaDB version 10.2.

Notice that we've used the key `mydatabase`. That can be *anything* you want: we'll
*reference* this string from the *other* config file we need to change:
`.symfony.cloud.yaml`.

Inside *that* file, we need a `relationships` key: this is what *binds* the
web container to that database service. Let's see... we don't have a
`relationships` key yet, so let's add it: `relationships` and, below, add our
*first* relationship with a special string: `database` set to `mydatabase:mysql`.

This syntax... is a little funny. The `mydatabase` part is referring to whatever
key we used in `services.yaml` - and then we say `:mysql` because that service is
a `mysql` type.

The *really* important thing is that we called this relationship `database`. Thanks
to this `SymfonyCloud` will expose an environment variable called `DATABASE_URL`.
It's literally `DATABASE_URL` and not `PIZZA_URL` because we called the relationship
`database` instead of `pizza`... which would have been less descriptive, but more
delicious.

This is important because `DATABASE_URL` happens to be the environment variable
that our app will use to connect to the database. In other words, our app will
*instantly* know how to connect to the new database.

Back at the terminal, hit "Ctrl+C" to exit from logging. Let's add the two changes
and commit them:

```terminal
git add .
git commit -m "adding SfCloud database"
```

Now, deploy!

```terminal
symfony deploy
```

Oh, duh - run with the `--bypass-checks` flag:

```terminal-silent
symfony deploy --bypass-checks
```

The deploy will still take some time - it has a lot of work to do - but it'll
be faster this time. When it finishes... it dumps the same URL as before - that
URL won't change. But to be even *lazier* than last time, let's tell the command
to open this URL in my browser *for* me:

```terminal
symfony open:remote
```

## Tunneling to the Database

And... we have a deployed site! Woo! The database is empty... but if this were
a real app, it would start to be populated by *real* users entering their *real*
Bigfoot sightings.

But... to make this a bit more interesting for *us*, let's load the fixture data
one time on production.

This is a little bit tricky because the fixture system  - which comes from
DoctrineFixturesBundle - is a composer "dev" dependency... which means that
it's not even *downloaded* on production - that's good for performance. If it
*were*, we could run:

```terminal
symfony ssh
```

to ssh into our container, and then run the command to load the fixtures. But...
that won't work.

No problem! We can do something cooler. Exit out of ssh, and run:

```terminal
symfony tunnel:open
```

I *love* this feature. Normally, the remote database isn't accessible by *anything*
other than our container - you can't connect to it from anywhere else on the
Internet. It's totally firewalled. But *suddenly*, we can connect to the production
database locally on port 30000. We can *use* that to run the fixtures command
locally - but send the data up to *that* database. Do it by running:

```terminal
DATABASE_URL=mysql://root:@127.0.0.1:30000/main php bin/console doctrine:fixtures:load
```

Ok, let's break this down. First, there is actually a *much* easier way to do all
of this... but I'll save that for some future SymfonyCloud tutorial. Basically,
we're running the `doctrine:fixtures:load` command but sending it a *different*
`DATABASE_URL`: one that points at our production database. When you open a
tunnel, you can access the database with `root` user, no password - and the
database is called main.

The only problem is that this command... takes *forever* to run. I'm not sure
exactly why - but go grab some coffee and come back in a few minutes.

When it finishes... yes! Go refresh the page! Ha! We have a production site with
at least *enough* data to make profiling it interesting.

Next, let's do that! Let's configure Blackfire on production! That's easy right?
Just repeat the Blackfire install process on a different server... right? Yep!
Wait, no! Bah! To explain, we need to talk about a *wonderful* concept in Blackfire
called "environments".
