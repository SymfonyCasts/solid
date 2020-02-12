# Staging Servers on SymfonyCloud

For your site, you *hopefully* have a staging environment - or maybe *multiple*
staging environments where you can deploy new features and test them. What about
*those* machines? Should we *also* run Blackfire builds on them?

## Why Profile Staging Servers?

At first, that *might* not seem important. After all, if a staging machine is a
bit slow, who cares? But thanks to the *assertions* we've been writing, if we
executed our Blackfire scenario on a *staging* machine, we could identify
performance failures *before* deploying them to production. And if you have a
*really* cool setup, you can even have build results posted automatically to
your GitHub pull requests.

## Separating Staging from Production on Blackfire

Getting Blackfire set up on a staging server *seems* simple enough: just repeat
the Blackfire installation process... on a different server! But stop! I don't
want you to *quite* do that.

Why? I want your Blackfire production environment to *only* contains builds from
your *actual* production servers. I want this to be a *perfect* history and
representation of production *only*. If we suddenly start adding builds from a
staging server - which maybe has different hardware specs... or is running a buggy
new feature - some of those builds will fail... and we'll get extra noise in
our notifications.

Instead, I like to create a *second* Blackfire environment and send profiles
to *it*. If a have *multiple* staging servers, I make them *all* use this same
new environment.

## SymfonyCloud Environments

But... before we create that *second* Blackfire environment... I need you to - once
again - pretend like Blackfire doesn't exist at *all*... for a few minutes.

Because before we talk about how we *profile* a staging server, we need to *create*
a staging server and deploy to it. SymfonyCloud has an *incredible* way to do this.
*Unfortunately*, the feature in Symfony cloud that *does* this is called...
environments. And it has absolutely *nothing* to do with Blackfire environments.

Here's how it works: in addition to your `master` branch, which is your production
server, SymfonyCloud allows you to deploy different git branches and it will give
you a unique URL for each. Each branch deployment is called an "environment". If
you run:

```terminal
symfony envs
```

Yep! We currently have *one* environment: `master`. It's the "current" environment
because we're checked out to the `master` git branch locally.

Check it out: pretend that we're working on a new feature. And so, we want to
create a new local branch for this feature. Instead of doing that manually, run:

```terminal
symfony env:create some_feature
```

This does two things. First, it created a new local branch called `some_feature`.
That's no big deal - we could have done that by hand. Second, it *deploys* that
branch! It does this by creating a "clone" of the master environment: - even creating
a copy of the production database!

I'll fast-forward through the deploy. When it finishes, it gives us a URL to the
deployment. This is a *different* URL than on production: it's a totally separate,
isolated deployment. Let's open this the lazy way:

```terminal
symfony open:remote
```

Say hello to our *staging* server for the `some_feature` branch, which you can
see contains a *copy* of the production database. How cool is that?

## Configuring Blackfire on the Environments

Back on Blackfire, refresh to see the builds for the production environment. When
we deployed to that environment, it did *not* create a new build. We expected
that. When we added the integration to SymfonyCloud - we told it to trigger a
build on this Blackfire environment whenever we deploy the *master* branch only.
We did that because we *don't* want these staging servers to create builds here.

Next, let's create a *second* environment and configure our staging servers to
use *it*. But because staging servers don't always have the same hardware as your
production servers, some metrics might be quite different. To solve that, we'll
leverage environment *variables*.
