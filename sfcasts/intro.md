# Performance, Profilers and APMs

Hey friends! Welcome to the *fastest*, most *performant* SymfonyCasts
tutorial of all time, on Blackfire. The end. What? We should say a bit more?

Uh, Blackfire is *all* about having fun while you
discover ways to make your site *absurdly* fast. We're going to see big graphs,
numbers, statistics, animated gifs, and watch all those numbers decrease as we hunt
down and eliminate performance bottlenecks. This stuff is just *fun*. And who
doesn't want a faster site?

But... ok... *just* "being fun" probably isn't a good enough reason to use Blackfire.
If you're trying to "sell" using a tool to your team... or management, the *real*
reason is profit. Performance is money. Heck, Google even has a page that will
[measure the speed of your site](https://www.thinkwithgoogle.com/feature/testmysite)
and tell you how much *revenue* you can gain by de creasing the rendering time of
your site by various amounts.

On the flip side, I'm sure you've heard the famous saying:

> Premature optimization is the root of all evil

I thought it was Nickelback. If that's true... doesn't a having a cool profiling
tool like Blackfire make you think *more* about prematurely
optimizing? Actually, it's the *opposite*: it let's us focus on creating
features and *then* noticing performance problems *if* there are any.

## Performance: Server + Network + Rendering

By the way, your site's performance is really three things put together. First,
the time it takes your server to build the page. Second, the time it
takes to transmit that data over the network. And third, the time it takes for
the browser to display stuff - the frontend. You should focus on *all* of these,
but the *main* parts are the server and frontend. Your browser has tools to
understand and optimize your frontend. Blackfire helps optimize your backend.

## Application Performance Monitoring (APM) Versus Blackfire

But it's not the *only* way to monitor performance on your server. The most
well-known way is by using an "application performance monitoring tool" - or APM...
which is an acronym I had to look up about 10 times before I could remember what it
meant! An APM is something that runs on your servers *all* the time, collecting
information about load times, slow queries, slow functions, errors and more.
The most famous one is probably NewRelic, though Blackfire is planning to release
their own sometime soon.

The *great* thing about an APM is that you can see data from *every* request on
your production servers. The *bad* part is that, because an APM is always running,
it needs to collect data *without* slowing down the page. If it tries to
collect too much, *it* would become the performance bottleneck!

Blackfire is a *profiler*. The *big* difference is that, instead of running on
*every* single request that our users make... and needing to stay very lightweight,
Blackfire only profiles a page when you *tell* it to. It then makes its *own*
request to the page and collects an *incredible* amount of *extremely* detailed
information. This process *totally* slows down that page load... which is fine,
because there's not a real user waiting for it to return.

The *point* is: use an APM *and* a profiler. The APM will give you a constant
stream of information from production. The profiler will give you the *deep*
information you need when debugging performance on specific pages.

## Project Setup

Ok, enough chat! Let's do this! To remove any bottlenecks and maximize your
learning performance, you should *totally* code along with me. Download the course
code from this page. When you unzip it, you'll find a `start/` directory with the
same code that you see here. Follow the `README.md` file for all the
setup details. This is a Symfony project - but that won't matter much: we'll
mostly focus on understanding and getting the most out of Blackfire.

The last setup step in the README will be to open a terminal, move into the project,
use the [Symfony binary](https://symfony.com/download) to start a local web
server by typing:

```terminal
symfony serve
```

Ok, let's see the site! Find your browser and head to `https://localhost:8000`.
Now you understand how important this project is. The world has been
looking for Big Foot, or "Sasquatch", for years. Thanks to the Big-Foot fanatic
community on our site - "Sasquatch Sightings" - we're closer than ever. In our
case, better performance doesn't mean more profit, it means, more big foot.

Do... I know where the performance problems are? Nope. No idea. Honestly, I was
too focused on getting this site to production to obsess over performance. And...
I feel great about that! We'll use Blackfire to find the bottlenecks - if any -
and Sasquash them!

Next, let's get Blackfire installed on my local machine and start profiling this
local website. And yes, you *can* use Blackfire on production - which is *awesome* -
and something we'll do later in the tutorial.
