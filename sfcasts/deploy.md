# Deploying to SymfonyCloud

Transition point! *Everything* we've talked about so far has included profiling
our *local* copy of the site. But things get even *cooler* when start to profile
our *production* site. Having *real* data often shows performance problems that
you just *can't* anticipate locally. And because of the way that Blackfire works,
we can create profiles on production *without* slowing down our servers and
affecting real users. *Plus*, once we're profiling on production, we can unlock
even *more* Blackfire features.

So... let's get this site deployed! You can use *any* hosting system you want,
but I'm going to deploy this with SymfonyCloud: it's what we use here on
SymfonyCasts and it makes deployment dead-simple for Symfony apps. It also has
a free trial if you want to code along with me.

## Initializing your SymfonyCloud Project

Find your terminal, make sure you're on your `master` branch - that's not required,
but will make life easier - and start by running:

```terminal
symfony project:init
```

This will create a few config files that will tell SymfonyCloud *everything* it
needs to know to deploy our site. The most important file is `.symfony.cloud.yaml`.
Ah, this says we will use PHP 7.1. Let's *upgrade* by changing that to 7.3.

Back at the terminal, copy the *big* git command: this will add all the new files
to git and commit them:

```terminal-silent
git add .symfony.cloud.yaml .symfony/services.yaml .symfony/routes.yaml php.ini
git commit -m "Add SymfonyCloud configuration"
```

Next, to *tell* SymfonyCloud that we want a new "server" on their system, run:

```terminal
symfony project:create
```

Every "site" in SymfonyCloud is known as a "project" and we only need to run
this command *once* to create one for this app. You can ignore the big yellow
warning - that's because I have a few other SymfonyCloud projects attached to my
account. Let's call the project "Sasquatch Sightings" - that's just a name to
help us identify it - and choose the "Development" plan.

The development plan includes a free 7 day trial... which is *awesome*. You *do*
need to enter your credit card info - to avoid people creating a bunch of free
servers and doing bad stuff - but it won't be charged unless you run
`symfony project:billing:accept` later to keep this project permanently.

I already have a credit card on file, so I'll use that one. Once we confirm,
this provisions our project in the background - I *assume* it's waking up
thousands of friendly robots who are carefully creating our new space in...
the "cloud". Hey! There's one now... dancing!

And... done!

## Deploying & Security Checks

Ready to do our first deploy? Just type:

```terminal
symfony app:prepare:deploy --branch=master --confirm --this-is-not-a-real-command
```

Kidding! Just run:

```terminal
symfony deploy
```

And... hello error! This is actually great. The deploy command automatically checks
your `composer.json` file to see if you're using any dependencies with known
security vulnerabilities. Some of my Symfony packages *do* have vulnerabilities...
and if this were a real app, I would upgrade those dependencies to fix that problem.
But... because this is a tutorial... I'm going to ignore this.

## Our First Deploy

Run the command again with a `--bypass-checks` flag:

```terminal-silent
symfony deploy --bypass-checks
```

We still see the big message... but it's deploying! This takes care of *many*
things automatically, like running `composer install`, executing database migrations.
This first deploy will be slow - especially to download all the Composer dependencies.
I'll fast-forward. It also handles setting up Webpack Encore... and even creates
a shiny new SSL certificate. Those are *busy* robots!

And... done! It dumped out a funny-looking URL. Copy that. In a *real* project,
you will attach your *real* domain to SymfonyCloud. But this "fake" domain will
work *beautifully* for us.

Spin back over and pop that URL into your browser to see... a beautiful 500 error!
Wah, wah. Actually, we're *super* close to a deployed site. Next, let's use a
special command to debug this error, add a database to SymfonyCloud, and load in
some dummy data over a "tunnel". *Lots* of good, nerdiness!
