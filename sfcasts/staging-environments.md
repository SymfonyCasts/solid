# Staging Environment Builds

We now have *two* versions of our site deployed: our production deploy and a,
sort of, "staging" deploy of a pretend feature we're working on. Blackfire is
*all* set up on the *production* server, but not on the staging server. Let's
fix that!

Back on the install page, select "SymfonyCloud" as our host to get to its docs.
To set up Blackfire on production, we did 3 things. One, added the extension. Two,
ran this `var:set` command to configure our Blackfire Server id and token. And
three, ran `integration:add` so that every deploy to `master` would trigger a
Blackfire build in our environment.

*Technically*, on the staging server, the Blackfire extension is already enabled
*and* it's set up to use the Server Id and token from our *production* Blackfire
environment. But, as we talked about in the last chapter, I don't want to mix
my production builds with builds from staging servers.

## Creating a new Blackfire Environment

Instead, go back to our Blackfire organization and create a *second* environment.
Let's call it "Sasquatch Sightings Non-master". For the endpoint, use the
production environment URL. But don't worry, that URL won't *actually* be used.
You'll see.

Hit "Create environment"... then remove the build notifications and save. View
the new environment - I'll get the credentials in a minute. Now, *stop* the
periodic builds. Why? Well in our setup, at any point, we may have zero or
*many* different "staging" servers. There's not just *one* server to build... so
if we did a periodic build... which "staging" server would it use? It just doesn't
make sense in our case. What *does* make sense is to *trigger* a new build each
time we *deploy* to a staging server.

## Different Server Id and Token on Staging

Ok, let's think about this: we now have *two* Blackfire environments. We want the
*production* server to use the Blackfire server id and token for the production
environment... and we want every *other* deploy to use the Blackfire id and token
from the new "Non-master" environment.

*How* you do that depends on how you deploy. For us, we can use a SymfonyCloud
config trick. First, list which variables we have set with:

```terminal
symfony vars
```

We have the two that were set by the `var:set` command we ran earlier. Delete
*both* of them:

```terminal
symfony var:delete BLACKFIRE_SERVER_ID BLACKFIRE_SERVER_TOKEN
```

We're going to *re-add* these in a minute... but with some different options. Now,
go back to the installation page... and refresh... so this shows our new environment.
For the `var:set` command, select the `Non-master` environment. Copy the command,
move over and paste:

```terminal-silent
symfony var:set BLACKFIRE_SERVER_ID=XXXXXXX BLACKFIRE_SERVER_TOKEN=XXXXXX
```

If we stopped now, it would mean that *every* server would send its profiles to
the new Non-Master environment... which is not exactly what we want. But here's
the trick: on the install page, change to the "Production" Blackfire environment,
and copy *its* command. We're going to *override* these variables, but *just* on
the SymfonyCloud `master` environment.

Paste the command, then add `--env=master --env-level` so that the variables are
used as "overrides" for *only* that environment. Finish with `--inheritable=false`
so that when we create *new* SymfonyCloud environments, they don't inherit these
variables from `master`: we want them to use the *original* values.

```terminal-silent
symfony var:set BLACKFIRE_SERVER_ID=XXXXXXX BLACKFIRE_SERVER_TOKEN=XXXXXX \
		--env=master --env-level --inheritable=false
```

This is a *long* way of saying that the `master` environment on SymfonyCloud will
now use the server id and token for the "Sasquatch Sightings Production" Blackfire
environment. And every *other* deploy will use the credentials for the
"Non-Master" environment. To be sure, run:

```terminal
symfony vars --env=master
```

Yep! 6900 is the server id for Production. Now try:

```terminal
symfony vars --env=some_feature
```

Perfect: that uses the *other* Server id and token. We're good!

## Staging: Builds on Deploy

The *last* thing I want to do is run this `integration:add` command again. We
ran this *earlier* to tell SymfonyCloud that it should notify our "Production"
Blackfire environment whenever we deploy to `master`. Now copy the "Non-Master"
environment command... and run it:

```terminal-silent
symfony integration:add --type=webhook --url='https://USER:PASS@blackfire.io/api/v2/builds/env/aaaabbee-abcd-abcd-abcd-c49b32bb8f17/symfonycloud'
```

Say yes to all events, all states *and* all environments. Actually, what we
*really* want to say is: create a build on the "Non-Master" environment every
time any branch *except* for master is deployed... but I don't think that's possible.

Phew! Let's redeploy both SymfonyCloud environments to see all of this in action:

```terminal
symfony redeploy --bypass-checks
```

Because we're currently checked out to the `some_feature` branch, this *deploys*
*that* branch. When it finishes, run the same command but with `--env=master` to
redeploy production:

```terminal-silent
symfony redeploy --bypass-checks --env=master
```

We also could have *switched* to that branch - `git checkout master` - and *then*
ran `symfony redeploy`. That's the more traditional way.

Done! Let's go see what that did! First check out the Blackfire production
environment. Yes! The redeploy to `master` created *one* new build. Perfect.
Now check out the Non-master environment. Oh, this has *two* new builds: one
for the `some_feature` deploy and another for the `master` deploy. We don't
*really* want or care about that second one... but it's fine. What we *do* care
about is that *now*, every time we deploy to a non-production server, we get a
new build here.

If you use GitHub or Gitlab, you can take this one step further by doing 2 things.
First, SymfonyCloud has a feature where it can automatically deploy the code
you have on a pull request. And because that would trigger a new build, *second*,
you can configure Blackfire to *notify* GitHub or Gitlab of your build results
so that they show up *on* the pull request itself. Pretty awesome.

I *love* our setup. But there's one more environment feature that we haven't
checked out yet: the ability to set *variables* that you use in your scenarios.
Let's check that out next.
