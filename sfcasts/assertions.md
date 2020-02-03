# Assertions

Coming soon...

Okay.

Adding specific assertions to specific situations inside a test is really cool, but
you can also configure these assertions globally, like you can configure that every
single time you run a real Blackfire profile.

Okay.

It should run one or more of these assertions on that profile and tell you whether or
not they pass. It's actually very similar to something we've already seen. I'll click
into one of my profiles. There is a recommendation site on the a tab on the left and
Blackfire has a bunch of built in recommendations and what these actually are is
these are assertions. It's asserting bads. For example, the metrics Symfony Colonel
debug dot count is equal to zero, so we can add our own accustom metrics here and
they're going to show up under this assertions tab. Let's try it. The way you do it
as suggested there is you create a new file at the root of your project called dot
Blackfire that Yemen, a few different things can go into this file before. Right now
I want to focus on tests. Now, the trickiest thing about these assertions is trying
to figure out what a good assertion is. You can make these assertions run on only
specific pages.

The easiest things to do are time-based assertions like the page should be faster
than 300 milliseconds, but time-based assertions are super fragile, so we want to try
to avoid those when we can. So let's actually start with one that we've just done.
I'll say each TTP requests should be limited to one per page. We'll say that on every
page on our site, you shouldn't have more than one HTTP request. Under this run about
two keys, the person's going to be path, we'll say slash. Dot. Star. This says to run
this assertion whenever we profile any page. So you can actually make a assertions
down here that will only run on specific pages if you want. And then we say
assertions and we'll list all the assertions we want down here. Let's go and copy the
one from our test. I'll paste it here. And what we really want is we want less than
or equal to one

and that's it.

So check it out. Let's move over here and I will close my black fire tab, refresh
this page and profile Cod recording

additive first, yeah,

assertion. And then we will click to view the call graph and actually check it out.
You can honestly see this little checkbox there that is us passing our assertions. We
now on the bottom here, have a little check box, one green assertion, because our
page zero made less than or equal to one request. So this is a nice way to just add
some of your own basically recommendations. Uh, and they will run on every page. Um,
whenever you profile

[inaudible],

these will become even more interesting later when we talk about environments and
builds. But for now it's just a nice feature. All right, next let's, we're talking
about a tool in the Blackfire ecosystem called the Blackfire player. It's a little
command line utility that even independent of the Blackfire profiling system allows
you to write simple files and execute them as functional tests. It will be the key to
introducing us to something Blackfire called scenarios.
