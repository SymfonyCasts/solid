# Intro

Coming soon...

Hey friends, welcome to our tutorial about Blackfire about performance, making your
sites super fast. This stuff is just fun. We get to see numbers and see things get
faster than they were before across so many different metrics, so of course
performance is fun, but that's not a good enough reason to do it. When you're trying
to sell this up, the chain performance is also about profit. It's a well known fact
that the faster your pays loads, the more satisfied your users are going to be, the
more stuff they're going to buy. Google even has a site where you can calculate the
impact of making your site a little bit faster.

Of course, another famous thing about performance is that premature optimization is
the root of all evil, so instead of thinking about performance while we're coding,
we're going to use Blackfire to find the real performance problems after the facts
and no premature optimization, we are going to hit real targets. Now when we talk
about performance, there are three aspects of it. There is the amount of time that it
takes your server to build the page or the JSON. Then there is the transit time or
the network to the user. And finally there is the frontend time.

Blackfire is all about the servers side of that, which is maybe, which along with a
frontend is really the two most important things. Now when you talk about optimizing
performance of the frontend, you actually have two main tools for this. The probably
the most well known one is called an APM, an application performance monitoring tool.
This is something that runs on your production servers at all time and it monitors
performance, um, uh, queries, slow page loads and it kind of alert you when something
is going wrong. Um, the most famous one of these is probably new Relic,

but Blackfire itself will in the future be launching their own, uh, applicate
application performance monitoring tool. So I love Blackfire so I'm really excited
and I'm watching out for this. So the first way is with this surplus thing that runs
on your production server at all times, the, and those can give you great pieces of
information. The problem with an APM is that because it's running on production all
the time, it needs to, it can't be very heavy. It can't slow down the request if it
collects too much information to actually slow the site down itself. So the second
way to handle a performance, the second tool that we have is called a profiler. A
profile is something where you collect tons of data about every single call, how long
every part of a page load, every aspect of it took. And if you have a profile or
running for requests, it slows down your page immensely. But the amount of
information you get is incredible. Blackfire allows you to have that amount of power
without slowing down the experience for your users. And you'll see why in a few
minutes. But it's because it doesn't run all the time in your production servers or
locally. It only activate you activate the profiler when you want.

Alright? So to have the most fun with this stuff, as always, you should absolutely
download the code, a code along with me. Download the course code from this page.
When you in sip it, you'll find a `start/` directory with the same code that you see
here. You can follow the, read me that MD file down here with all the details and how
to get set up. This is a Symfony project. However, for the most part we're going to
be talking about is Blackfire and how to optimize Blackfire. So there will be a few
Symfony specific things, but for the most part, this is going to be a good for
anybody that wants to learn a Blackfire. The last step will be to open a terminal,
move into the project, and I'm gonna use the Symfony binary to start a local web
server by typing:

```terminal
symfony serve
```

That's going to start a web server on `localhost:8000` I'll spin back over,

go to `http://localhost:8000` to see our website that we need to optimize Sasquatch
sightings. Oh website, all about finding and locating where big foot is a, this site
is getting really, really popular. So it's time for us to look in and see how we can
make it faster. Now, I've written all the code for this, uh, application. Do I know
where the performance problems are? No, I have no idea. I've been focusing purely on
building the features and deploying them. I haven't been worrying or obsessing about
performance, and that's the way it should be because Blackfire is going to help me
find the actual problems instead of me thinking about what the potential problems
are. All right, so next, let's get Blackfire installed on my local machine and start
profiling this local website. Eventually, we are going to profile use Blackfire on a
production site, but for awhile we're just going to use it, uh, to profile this site
locally.