# Blackfire Environments

Now that our site is *deployed* - woo! - how can we get Blackfire working on it?
Well... we already know the answer. If you find the Blackfire Install page... it
makes it super easy: I want to install on "a server"... and let's pretend it uses
Ubuntu.

Getting Blackfire installed in your production server is as easy as running the
commands below to install the Blackfire PHP extension - the Probe - installing
the Agent and configuring the agent with our server id and token. Easy peasy!

## Hello: Environments

But.... *some* Blackfire account levels - offer a kick-butt feature called
*environments*. If you have access to Blackfire environment - or if you're able
to get a "plan" that offers environments, I highly recommend them.

An environment is basically an isolated Blackfire account. When you have an
environment, you send your *profiles* to that environment. The first advantage
is that you can *invite* multiple people to an environment, which means that
*anyone* can profile your production site and see other people's profiles. It
also has *other* superpowers that *really* make it shine.

## Understanding Organizations

So let's create an environment! Go back to https://blackfire.io and click on the
"Environments" tab. Actually, click on the "Organizations" tab... that's where
this all starts. Blackfire organizations are a bit like GitHub organizations.
With GitHub, you can subscribe to a "plan" directly on your *personal* account
*or* you can create an organization, have *it* subscribe & pay for some GitHub
plan, and then invite individual users to it. Blackfire organizations work
*exactly* like that. And if you want to use environments, you need to create
an organization and subscribe to a Blackfire plan *through* that organization.

This *did* confuse me a bit at first. Basically, unless you just want the
*lowest* Blackfire plan, you should probably *always* create an organization
and subscribe to Blackfire through *it*. It just has a few more features than
subscribing with your personal account.

## Creating an Environment

*Anyways*, I've already got an organization set up and subscribed to a plan.
Once you *have* an organization, you can click into it create a new environment.
You see that I already have one for SymfonyCasts.com production. Click to create
a new one. Let's call it: "Sasquatch Sightings Production".

For the "Environment Endpoint", it wants the URL to the site. Again, if this were
a *real* project, I would attach a *real* domain... but copy the weird domain
name, and paste. Select your timezone, sip some coffee, and...
"Create environment"!

On the second step, it asks us to provide URLs to test... and it starts with just
one: the homepage. We're going to talk more about this soon, so just leave it.
I'll also uncheck the build notifications - more on those later.

## Environment vs Personal Server Credentials

Hit "Save settings" and... we're done! It rewards us with a shiny new "Server Id"
and "Server Token".

This is *super* important. No matter *how* you install Blackfire on a server,
you eventually need to configure the "Server id" and "Server Token". This is
*basically* a username & password that tells the server which *account* on
Blackfire that any profile should be sent to.

When you register with Blackfire, it immediately created a "Server Id" and
"Server Token" connected with your personal account. We used that when we
installed Blackfire on our local machines. But now that we have an environment,
it has its *own* Server Id and token. The drop-down on the Install page is
allowing us to *choose* which credentials we want to see when using this page.

Locally, we should *still* use our *personal* credentials - it keeps things
cleaner. But on *production*, we should use the new *environment's* Server Id
and Token. The install page gives us all the commands we need *using* those
credentials.

Oh, and by the way: if you have a "free" personal account... but are
attached to an organization with a paid plan, any profiles created locally with
your *personal* Server Id and Token will *inherit* the features from that
organization. That lets us use our personal credentials locally and *still*
get all the Blackfire features we're paying for. One exception to that rule,
unfortunately, are any "Add-Ons" - we'll see some of those soon.

## Configuring Blackfire on SymfonyCloud

Ok, we can *now* go through the Blackfire setup instructions on production and
use the Server Id and Token from the new environment. I'll select "Symfony Cloud"
as my host... which takes me to a dedicated page on this topic.

Let's see... step one is, instead of installing Blackfire with something like
`apt-get`, we'll add a line to `.symfony.cloud.yaml`. I already have an
`extensions`... so just add one new line: `blackfire`. Boom! Blackfire is installed.
Add this file to git... and commit it:

```terminal-silent
git add .
git commit -m "adding blackfire extension"
```

The *other* step is to *configure* Blackfire. Once again, it has a drop-down
menu to select between my personal credentials and credentials for an enivornment.
Select our "Sasquatch production". Cool! This gives us an easy command to set
two SymfonyCloud *variables*. Copy that, move over, and paste:

```terminal
symfony var:set BLACKFIRE_SERVER_ID=XXXXXXX BLACKFIRE_SERVER_TOKEN=XXXXXX
```

And... we're good! To make both changes take affect, deploy!

```terminal
symfony deploy --bypass-checks
```

I'll fast-forward. Once this finishes... move over and refresh. Ok good: everything
still works. *Now*, moment of truth: open the Blackfire browser extension and
create a new profile. It's working! I'll call it:
`[Recording] First profile in production`.

Next, let's... *look* at this profiler! It will contain a *few* new things and
some other data that is now *more* relevant, now that we're on production.
