# Manually Profile (Instrument) Part of your Code

Profiling a page looks like this.

## Profiling: What happens Behind the Scenes

First, something tells the Blackfire PHP extension - the "Probe":

> Hey! Start profiling!

Which basically means that it starts collecting *tons* of data. The process
of collecting data is called *instrumentation*... because when a concept is
*too* simple, sometimes we tech people like to invent confusing words.
*Instrumentation* means that the PHP extension is collecting data.

The second step is that - eventually - something tells the PHP extension to *stop*
"instrumentation" and to send the data. The collection of data is known as a
"profile". The PHP extension sends the profile to the agent, which aggregates it,
prune some stuff and ultimately sends it to the Blackfire server.

So: what is the "thing" that tells the PHP extension to activate? We know that
the PHP extension doesn't profile *every* request... so what is it that says:

> Hey PHP extension "probe" thing: start profiling!

The answer - *so* far - is: the browser extension: it sends special information
that tells the probe to do its thing. Or, if you use the `blackfire` command line
utility, which we did earlier to profile a command, then *it* is what tells the
PHP extension to activate.

In either situation, the extension is activated *before* even the *first* line
of code is executed. That means that *every* single line of PHP code is
"instrumented": our final profile contains *everything*. This is called
auto-instrumentation: instrumentation starts automatically.

This naturally leads to three interesting questions.

First, who *is* baby Yoda? I mean, is he... like, related to Yoda? Or just the
same species?

The second question is: could we trigger, or *create* a Blackfire profile in a
*different* way? Could we, for example, dynamically tell the PHP extension to create
a profile from *inside* our code under some specific condition?

And third, *regardless* of who *triggers* the profile, could we "zoom in" and
only collect profiling data for *part* of our code? Like, could we create a profile
that only collects data about the code from our controller instead of the
entire request?

Let's actually start with that last question: profiling a *specific* part of
our code, instead of the whole thing. To be *fully* honest, I don't know if this
part has a *ton* of practical use-cases, but it *will* give you an even better
idea of how Blackfire works behind the scenes.

## Installing the Blackfire SDK

To help with this crazy experiment, we're going to install Blackfire's PHP SDK.
Find your terminal, dial up your modem to the Internet, and run:

```terminal
composer require blackfire/php-sdk
```

This is a normal PHP library that helps interact directly with Blackfire from
*inside* your code. You'll see how.

When it finishes, move over and open `src/Controller/MainController.php`. Ok:
this is the controller for our homepage. Let's pretend that when we profile this
page, we don't want to collect data about *all* of our code. Nope, we want
to, sort of, "zoom in" and see *only* what's happening inside the controller.

## Manually Instrumenting Code

We can do that by saying `$probe = \BlackfireProbe::getMainInstance()`. Remember:
the PHP extension is called the "probe"... that's important if you want this to
make sense. Then call `$probe->enable()`. At the bottom, I'll set the rendered
template to a `$response` variable, add `$probe->disable()` and finish with
`return $response`.

Okay, so... what the heck does this do? The first thing I want you to notice is
that if I refresh the homepage a bunch of times... and then go to
https://blackfire.io, I do *not* have any new profiles. Adding this code does
not "trigger" a new profile to be created: it does *not* tell the PHP extension -
the "probe" - that it should to do its work.

Instead, *if* a profile is currently being created, this tells the probe *when*
to start collecting data. Hmm, this isn't going to *quite* make sense until we
see it in action. Trigger a new profile on the homepage. I'll call this one:
`[Recording] Only instrument some code`.

Click to view the call graph: https://bit.ly/sf-bf-partial-profile.

*Fascinating*. This contains *less* information than normal. It has a few things
on top - `main()` and `handleRaw()`... but basically it jumps straight to the
`homepage()` method.

## How Disabling Auto-Instrumentation Works

What's happening here is that the *only* code that the probe "instrumented", the
*only* code that it collected information on, is the code between the `enable()`
and `disable()` calls.

This... completely confused me the first time I saw it. What *really* happens is
this: as soon as we use the browser extension to tell the probe to do its job,
the PHP extension starts instrumenting - so, collection data - *immediately*.
Initially, it *is* collecting data about *every* line of PHP code.

But as *soon* as it sees `$probe->enable()`, it basically *forgets* about all
the data collected so far. The `$probe->enable()` call says:

> Hey! Start instrumenting *here*. If you've already collected some data before
> thanks to auto-instrumentation, get rid of it.

This effectively *disables* auto-instrumentation: we're now *controlling* which
code is instrumented instead of it happening automatically. Once the code hits
`$probe->disable()` instrumentation stops.

You can actually use `$probe->enable()` and `$probe->disable()` multiple times
in your code if you want to profile different pieces: `$probe->enable()` only
forgets data it's already collected the *first* time you call it.

Oh, and you can *also* optionally call `$probe->close()` - you'll see this in
their documentation. That tells the PHP extension that you're *definitely* done
profiling and it can send the data to the agent. But, it's not *strictly* required,
because it'll be sent automatically when the script ends anyways.

So... this feature is *maybe* useful... but it's *definitely* a nice intro into
taking more control of the profiling process.

## We haven't used the SDK Yet

And.. fun fact! We installed the `blackfire/php-sdk` library... but we haven't
*actually* used it yet! This `\BlackfireProbe` class is *not* from the `php-sdk`
library: it's from the Blackfire PHP *extension*. As long as you have the
extension installed, that class will exist. We're interacting *directly* with
the extension.

So... why did we install the SDK if we didn't need it? Because... it gave us
auto-complete on that class. And you all know that I freakin' *love* auto-complete.

The SDK has a, sort of, "stub" version of this class. This is *not* the code that
was *actually* executed when we called those methods... but having this at least
shows us what methods and arguments exist.

Next, let's actually *use* the PHP SDK to do something a bit more interesting.
I want to *create* a profile automatically in my code *without* needing to use
the browser extension. This *does* have real-world use-cases.
