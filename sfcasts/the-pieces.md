# Blackfire Install: Agent, Probe, Chrome Extension

So let's get Blackfire installed on our local computer. Head over to
`https://blackfire.io` and log in or register for a new account. As you can see,
I've been busy using Blackfire already.

## Agent & Probe: How it all Works

Click the Docs link on top... then installation on the left. Before we jump in
and install everything, I want you to understand just a *little* bit about how this
all works: understanding this helped me a *bunch*. If you want to skip this
and head to the next video you *can*... just prepare to miss out on some cool
diagrams!

Click the "main components of Blackfire" link and scroll down to find... woh! A
diagram that shows you *exactly* how Blackfire works.

## The Probe: PHP Extension that Collects Data

How about... we look at a simplified version. There are 3 things we need to
install. The first is called the "probe", which is really just a PHP extension.
You'll install this wherever your code is running - like on your local machine,
and later on production. The probe's job is simple, but huge! It's responsible for
collecting *all* of the information: all the function calls, how long each took,
which function called which other function, how much memory did something take,
network requests... you get the idea. By the way, the process of "collecting all
the data" is sometimes called instrumentation... which I *only* mention so that
if you see this fancy word... it hopefully won't confuse you... it confused me.

## The Probe: Collector and Sender

The *second* thing we will need to install is called the "agent". This is a
service - or "daemon" - that runs on your computer - or on your production machine.
It... just sits there and waits. When the PHP extension - the probe - finishes
collecting all the data, it sends that data to the agent. The agent does some
processing on it - like removing unimportant information and anonymizing things -
then ultimately sends that data to the Blackfire server. It's... the middleman.

So basically, the probe and agent work together to collect the info and send it
to Blackfire.

## The Browser Extension: Profiling Activator

The *last* piece you'll need to install is a browser extension. Remember: the
probe is *not* profiling *every* single request. Normally, when a request comes
in, it yawns... and does nothing. The browser extension's job is to *activate*
profiling. It basically says:

> Hey probe! Wake up! I'm going to make a request and I *actually* want you to
> do your thing - collect all the data and sent it to the agent. Cool? Text me
> when it's done.

And... that's it! This bottleneck-fighting superhero trio is our ticket to
performance glory. Next, let's get them installed.
