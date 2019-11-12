# Install

Coming soon...

All right, so let's get Blackfire installed on our local server and later when we
deploy to production. We're also going to start on production server so we have a
black fired IO and go ahead and make sure that you,

let's do this.

I don't want to buy fire.io and go ahead and log in or sign up. I'm going to go with
my Symfony connect account allow

and

perfect. You can see I already have some profiles from before and then I'm gonna go
up here to the documentation and we want to get down here to the installation to see
how things are working. Okay, so installation is fairly easy. There are a few pieces
and I really want you to understand what the pieces are behind the scenes. They have
a link off of it. It's the installation of the main components of Blackfire. It
actually gives you a big design here of like what's going on behind the scenes. I'm
going to give you, it's really fun. I'm going to give you a simpler version. There
are three things we need to install. The first thing is called the probe. This is a
PHP extension, so you're going to install it. If you want to profile things locally,
you will install this locally. If you want to profile things on production, you don't
install this on production.

The probe, the PHP extension, it's job is simple. It's what's responsible for
collecting all of the information about all the function calls, how long things took,
all the memory he collects. All of the raw data that we're going to see that by the
way is called instrumentation. That's a confusing word, but when you hear
instrumentation, it's talking about collecting all that data, so it's really the
workhorse of this entire process. The second thing we're gonna install is called the
agent. This is a little service that will run on your local computer or on production
at all times. It's just sits there and waits.

Okay.

When the probe finishes profiling, it sends all of that data to the agent and then
the agent does some processing on it. It prunes some data, it does some summarization
of it, and it ultimately sends it to Blackfire. It also anonymizes it.

So the probe and the agent worked together to collect the information incentive to
Blackfire. The last piece is the browser extension. The browser extension, as you'll
see is what's going to tell the probe that it should profile. Because remember we
don't want to profile on every single request that would slow down every single
request. So via browser extension we're going to say, Hey, on this next request I
want you to profile, I want the probe to collect all of that data. All right, so
let's go back here and actually get this stuff set up. Uh, the setup instructions are
going to depend on your operating system. Obviously, fortunately has details for
everything. I'm on a Mac. Um, and I am going to use a brew to install this. So I'm
gonna copy this brew tap command, move on to my terminal that's open a new terminal
tab, and I'll run through tap. That's going to give me access to a, some brew
channels from Blackfire. There we go. And then we're gonna install the agents. So
brew install Blackfire agents

[inaudible]

perfect. And then it says you need to register your agent. I'll move back over cause
that's exactly what the next command is. A pseudo Blackfire agent dash registered. So
I'll clear the screen and then run that.

Now here it's going to ask for your server ID and your client ID. And basically what
this is, is that the agent needs to know which account on Blackfire is your account
and needs to know where to send the data. So Blackfire gives you this server ID and
server token and this is basically a username and password, um, that is going to, um,
that the agent will use to tell Blackfire who you are and prove that that's your
account. So I'll move over there, I'll paste my server ID and then I'll copy the
token and paste those in. And you should keep these secret, otherwise people are
going to be able to profile to your account. Perfect. Now it says restart the
Blackfire agent in the background. So we're gonna use a brew to actually run this as
a service. So I'm a copy. The next command. This is basically setting up brew to be
run as a service in the background. Then spin back over again and then copy the
launch control load command here. Cool, so thanks to this, this Blackberry agent is
now running as a Damon in the background on my computer. You won't really ever see or
care that it's there, but it's sitting there. It's waiting for data.

Okay.

All right, so let's spend it back over. If you ever do have any configuration
changes, there's some details here on how to restart and the access to the lock. All
right, the next thing we need is the probe and actually if you look down here, it's
star. It talks about something called the black buyer's CLI tool. We don't need that
yet, so we are actually not going to do this step. We'll come back to it later.
What's not needed to get things set up. Our perfect. So installing the probe. This is
the PHP extension, so you can see here that they have little copy here for Bruin
style black fire dash PHP five six well hopefully you're not using a PHP 5.6 anymore.
Why didn't do is actually go over here and figure out what version of PHP I am
running. So I'm gonna say PHP dash dash in version 7.3 0.6 so I'm going to run it.
Brew install PHP Blackfire dash PHP seven three in this the extension

doesn't need any type of server ID or server token. It doesn't need any type of
authentication information. It's just a PHP extension. It's going to pass the data to
the agent and the agent already has a server ID and server tokens so we can send to
us.

We do, however, as it says need to restart our web service. On this case I'm using a
Symfony web server, so I'm going to go my other tab, hit control C and then run
Symfony's serve again. And with any luck we will now have a Blackfire extension
running. An easy way to figure that out in a browser is I can go over here and if I
can, because I'm using Symfony, I can go over here and restart and I can hit view PHP
info and search for Blackfire. Yes, and we are running Blackfire. Excellent.

[inaudible]

all right, close that up. All right, so our server is now set up. The last thing we
needed to do is we need a way to activate. We needed to tell, I would need a way to
activate the agent and that's done as I mentioned, via a browser extension. So I'm
going to go back to the installation and actually near the top they talk about the
different pieces of this. It talks about the browser extension currently available
for Firefox and Chrome. So I'll click the Chrome link here. You can see it to text
that I don't have it installed. Let's install the extension

add to Chrome. There it is. Awesome. So it's been added right here. Oh, X that out
and refresh this page. Yes, you can see now it's happy. It's installed. Cool. So
we're ready to go. We're going to see how this works. So let's see, just to start, we
can pick any page. Uh, I'm just going to click on one of my sightings here. You can
see it gives you information about the siding, like when it was, um, some, we have a
bunch of fake data here so you can see it looks a little silly on the down here and
people can comment on this, on disgust, this big foot siding. Now, the profile of
this page, as I mentioned, the agent, the probe, it's not profiling every single
request and we need to tell it to do that. The Blackfire extensions, the way we're
going to do that, you see, it knows that we're logging in as Ryan Weaver.

You might need to authenticate and by hitting profile here, it's going to send some
encrypted information. Uh, it's going to reload this page, but add some extra
information to the request that proves to the PHP extension that we are authenticated
to profile this. This is important because this is gonna mean that one. Once we get
Blackfriars set up from production only we can trigger profiles. Uh, not anybody can
pre trigger profiles for any website. All right, so let's profile. And there it goes.
You can see it actually goes from zero to a hundred percent behind the scenes. It's
actually taking 10 requests and averaging them together. I'm also going to give us a
name here, which is going to be really convenient. I'll say recording cause that's
what I'm doing. Show page and initial and hit enter. Now if you, this is where, uh,
if you have a setups issue, your uh, things are going to fail.

You're going to get an error called the probe, not to found, that's a very generic
air. They'll, if you see that air, they'll have links to the documentation on how to
fix it. Um, several things could have gone wrong. Um, and the problem is Blackfire
can't give you a more specific error because that might be leaking security
information. Um, so check that out and uh, and, and hopefully you can get it. All
right. So let's see what this got. Let's hit view call graph and check this out. It
takes us to the Blackfire website. Wow. With a huge treat. Tons of information about
that page. So next, let's dive into this. Learn how to use this and start fixing our
first performance issues.