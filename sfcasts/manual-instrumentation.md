# Manual Instrumentation

Coming soon...

Profiling a page looks like this. Something tells the Blackfire PHP extension, Hey,
start profiling, which basically means that it starts collecting tons of info about
everything that's going on. This collection of information is called instrumentation.
I think that use that word to be extra confusing. So instrumentation means that the
PHP extension is collecting info. The second step is that eventually something tells
the PHP extension to stop profiling and send the data. The collection of data that
it's instruments that is known as a profile. The PHP extension sends the profile to
the agent, which aggregates it, prune some data in, ultimately sends it to Blackfire.
So what is the thing that tells the PHP extension to activate? As we know, the PHP
extension does not profile every request. So what is the thing that says, Hey, PSB
extension start profiling. We know that it's either the browser extension that
activates it or the black fire's command line utility, which we used earlier and
through things like Blackfire, PHP and then a command.

One of the byproducts of the way that the extension is activated is that M is that
the instrumentation is activated before even the first line of code is executed. That
means every single line of peach because you have it goes into the profile. This is
called an auto instrumentation. The idea that the instrument instrumentation
automatically starts, so this leads me to two interesting questions. First, could we
trigger a profile in a different way? Could we, for example, dynamically tell the PHP
extension to create a profile when some condition is met on our site? The second
question is, regardless of who triggers the profile, could we actually zoom in and
only profile some of our page like maybe we only want to profile the controller code
instead of profiling the entire requests code? Let's actually start with that second
thing. The idea of actually own actually profiling only part of our code and not all
of it. To help with this, we're going to install black fires, SDK, fire terminal and
run a composer require Blackfire /PHP dash SDK. This is a normal PHP library that is
going to help you interact directly with Blackfire.

When that finishes. Let's move over in find a source controller, main controller. So
here's the controller for our homepage. So let's pretend that when we profile the
homepage, we don't want to profile all of our code. We want to zoom into just what's
happening inside of our controller. So we can do that by saying, okay, prob =
Blackfire pro colon colon. Get main instance. Remember the PHP extension is called
the probe. So we're actually asking here is certainly PHP extension thing. Then we'll
say probe enable. And at the bottom I'll set the uh, rendered template to a response
and say, probe, arrow, disable, and then I want to return that response.

[inaudible]

okay, so what does this do exactly? The first thing I want you to notice is that if I
refresh a bunch of times and then go to blackfire.io I do not have any new profiles
in here, so adding this code does not create a new profile. It does not trigger the
PHP extension, the probe to do its work. What it does is it says that when we're
creating a profile, when something else triggers a profile, it's only going to
profile some of the code. So check this out. Let's add it. Yeah, profile here. I'll
call this one recording only instrument, some code, and let's view click to view the
call graph

and

awesome. So visa, zoom in here you can see there's actually less information than
normal. Basically it does show a couple things like main and handle raw, but
basically it jumps straight to the homepage.

Okay.

What's happening here is that the only code that it is instrumenting, the only code
that it's collecting information on is the code that's between the enable and the
disabled. This actually confused me the first time I saw what really happens behind
the scenes is that as soon as we use the browser extension to tell the probe to S to
do its job, the peep extension does start instrumenting immediately. But as soon as
it hits this probe, enable it basically forgets everything the probe enables says
start here. So if you've already collected some before this, get rid of that, get rid
of that auto instrumented, uh, information and start collecting information right
here. And then once it hits, probe disable, that's when it stops. And you can
actually use the probe enable and pro disable multiple times in your code if you want
to profile different parts. Uh, and the same thing, you can also option, they called
probe->close. You'll see this in their documentation that basically tells the PHP
extension to finish its work and send to the agent. You don't need to do that because
that automatically happens at the end of the script anyways. Uh, but it's up to you.

So yeah, it's an interesting feature. It may

be useful and only some edge cases, but it's a nice way of showing how you can
actually control which parts of your code, uh, get instrumented. Fun fact about this
is that we, when we started this, we installed the Blackfire PHP SDK library. We
actually haven't used that yet. This Blackfire pro class is not from the PHP SDK
library. This is a class that's available. As long as you have the Blackfire PHP
extension installs, we're actually interacting directly with the extension. In fact,
the only reason I installed the PHP SDK is because that, uh, it actually gives us
autocomplete for this library. So it's a kind of a trick. You see how this is coming
from Blackfire PHP sta. This is not actually the code that ran the code. This get
main instance. A method lives inside the PHP extension, but they add a little stub of
that, a class inside the PHP extension, what the PHP SDK, which is just a nice way
for us to get some auto complete on its methods, like enable and disable. So next,
let's actually use the PHP SDK to do something a little bit more interesting. This, I
want to automatically create a profile

under certain conditions, even if I didn't use the browser extension. This is a great
way to profile some conditional production that you might not be able to, uh,
manually replicate.