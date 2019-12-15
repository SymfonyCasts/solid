# Installing the Agent, Probe & Chrome Extension

So... let's get these pieces installed! Back on the install page, the setup details
will vary based on your operating system. Fortunately, Blackfire has details for
pretty much all situations. I'm on a Mac and will use Homebrew to get everything
working.

I'll copy the `brew tap` command, move to my terminal, open a new tab and paste:

```terminal-silent
brew tap blackfireio/homebrew-blackfire
```

## Installing the Agent

That gives me access to the Blackfire packages. *Now*, install the *agent* - that's
the "daemon" that runs in the background - with:

```terminal
brew install blackfire-agent
```

Perfect! It says I need to "register" my agent. And... the browser instructions
confirm that! I'll copy that command, clear the screen and paste:

```terminal-silent
sudo blackfire-agent --register
```

This is going to ask us for our "Server Id" and "Server Token". These are... basically
an internal "username and password" that the agent will use to tell the Blackfire
servers which *account* the profiles should be attached to. Copy the Server Id,
paste, copy the Server Token, paste and... we're good!

Finally, remember how the "agent" is a service that runs in the background?
We just *installed* the agent, but it's not running yet. Back in the docs, the
next two commands set up the agent as a "service" in Brew, so that it will *always*
be running. Copy the first, paste.

```terminal-silent
ln -sfv /usr/local/opt/blackfire-agent/*.plist ~/Library/LaunchAgents/
```

Then spin back over again, copy the `launchctl load` command... and paste that.

```terminal-silent
launchctl load -w ~/Library/LaunchAgents/homebrew.mxcl.blackfire-agent.plist
```

Cool! If everything worked, the Blackfire agent is now running in the background.
You wont really ever see it or care that it's there... but it is... waiting for
data.

## Installing the Probe

Back on the install docs, the next piece we need is the PHP extension - the probe.
Skip this CLI tool for now - we won't need it until later.

To install the PHP extension, we'll once again use `brew`. But... hopefully you're
*not* still using PHP 5.6. Let me head over to my terminal and see what version
I'm running:

```terminal
php --version
```

7.3.6. Brilliant! So I'll run:

```terminal
brew install blackfire-php73
```

Notice that the extension doesn't need *any* authentication info - like a server
Id or token. It's beautifully dumb: its job is to profile data, send it to the
agent, and let *it* worry about authentication with the Blackfire servers.

We *do*, however, as it says, need to restart our web server. For us, that means
going to the other terminal tab, hitting Control + C, and then running

```terminal
symfony serve
```

Is the Blackfire extension working? I don't know! Because we're using Symfony, an
easy way to check is to hover over the web debug toolbar and click the
"View phpinfo()" link. Let's see... yep! The Blackfire PHP extension is here.

***TIP
If you have XDebug installed, disable it for the best results.
***

## Installing the Browser Extension

At this point, our *server* is set up and ready to profile! Victory! The only
thing we need *now* is a way to tell the probe when to *activate*. That's the
job of the browser extension.

Go almost *all* the way back to the top of the install page where they talk about
the different pieces. I'm using Chrome, so I'll click the
[Google Chrome](https://blackfire.io/docs/integrations/browsers/chrome)
extension link. I don't have it installed yet, so let's fix that: Add to Chrome.

There it is! If you refresh the docs... yep! It sees the extension.

## Profiling our First Page

Hey! We're ready to profile! Ahhhh! Where should we start? Let's... just click
to view details about any Big Foot sighting. All of this data comes from some data
fixtures that we used to pre-populate the database while setting up the project.
It uses a bunch of random data up here... and each sighting has a bunch of random
comments.

When we loaded this page a second ago, the PHP extension - the probe - did nothing.
To activate it, click the browser extension.

Moment of truth! When we click profile, the plugin will send a request to this
page with a special header that tells the probe to activate and start profiling.
Click "Profile"!

There it goes! It goes from 0 to 100% as it actually makes *10* requests and
averages their data. We can also give this "profile" a name to keep our account
organized: I'll say `[Recording] Show page initial` and hit enter.

## Troubleshooting Failure

If you got to 100%, congrats! If you got an error... wah wah. This is *the* most
common place for something to go wrong... and the error will almost always be
the same: Probe not found. This might mean that you forgot to install the PHP
extension, or that the PHP extension was installed on a different PHP binary...
or that the agent isn't running... or that the agent *is* running but you
misconfigured the server id and token. They have great docs to help with this.

But we had success! Click the "View Call Graph" button to go to a URL on their
site. Hello beautiful Blackfire profile. Wow.

Next, let's start diving into this *mountain* of information and see how we can use
it to find hidden sasquatch... I mean, hidden performance bugs.
