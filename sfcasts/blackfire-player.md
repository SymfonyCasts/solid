# Blackfire Player

Coming soon...

Pretend for a few minutes that the Blackfire profiler doesn't exist at all. I'll
close my profile tab entirely. Just pretend like, because we're going to talk about
something that has the word Blackfire in it, but has nothing to do with the Blackfire
profiling, at least not yet. Google forum Blackfire player.

No. Yeah. Regardless, you need it.

The Blackfire player isn't open source library. That makes it really easy to write a
little bit of code that can then crawl different websites, click links, fill out
forms, and then do things with the results. It's a little tool in language

for them

that commands a browser.

Was there any [inaudible]

and other than being made by the BioFire people right now, it has nothing to do with
the profiler. So let's get it installed. I'll copy this curl command, spin a merge my
terminal paste.

You're right.

Then go back and copy those other two commands.

Okay.

From windows. Uh, I will need to look into what you need to do and then if everything
worked correctly, we should be able to run Blackfire player. Perfect. All right, so
here's the basic idea. You create a file that looks like this where you set up things
called scenarios and then you can write code that actually says go visit this URL and
expect the status code is 200 that's a very, very simple example. I can do a lot
fancier things than that. So let's create a our first Blackfire player file at the
root of my project though is could live anywhere. I'm going to create a scenario dot
B K F file, Blackfire

[inaudible].

At the top, I'll put a name. This is not really very important. Various scenarios for
the site and then an end point configuration which you're going to point at whatever
our local you were out is, which is local host colon 8,000 I put a comment above
this,

the override the end point with the dash dash end point option when we run the
command. That's useful if you are running this from a different server that uses a
different endpoint. Now notice this is not, this kind of looks like Yammel but it's
not Yammel. We don't have colon between these values. This is a custom language used
by the Blackfire player. So it does take a little bit of getting used to. Now down
here what we're going to do is define some scenarios. We'll find one scenario, so
we'll scenario. Then we'll give it a name, basic visit, end of the scenario. Let's do
two things. Let's visit the URL of the homepage. And when we do this, I can actually
give this a name. This will just make debugging easier cause that'll tell us that
we're visiting the homepage. Then once we're there, we're going to click a link and
if we go back to our site, you can see that we have a link up here whose text is a
log in. We can use the assistant to click that link, check it out, we can say click
link and then use that exact text log in down here. I'll give this another page,
we'll call it log in page

and let's just start there. So the idea is that we can run the black power player and
it will actually do this stuff in the background. So let's try it. It's been over and
let's run Blackfire player run and then scenario that BKF and it fails curl air 60 if
you Google this, you find this as an SSL problem. This is because the Symfony web
server you creates a nice a a self signed a certificate for us, but the Blackfire
player doesn't like that. The simplest solution since we're just doing things locally
is to pass dash dash SSL dash no dash verify and Hey, it worked. Okay. Scenarios one,
steps two. Okay. That means it actually was able to go to the home page and then
click that link for more info and get past this with dash V and we see a little bit
more verbosity and he and get request in both of those things. So that's pretty sweet
to make this better. We can add a test to this. So we can say expect.

Yeah.

And then inside of here we can give it an expression. So one of the things we can say
is status_code = = 200 and I'll copy that and put it in both places. And we can have
as many expect lines as we want. So now I want you to run the test. Yep. It's still
passes. So two things I want to say about this. The first thing is that just like
with art metrics expression here, once you get inside of the expecting, this is an X
in expression language, it's actually the Symfony expression language. So this, it's
a JavaScript like language. Um, yeah. The second thing I want to say is if you search
this page for

if you search this phage or status code and keep searching. There we go. Eventually
you find information about these expectations. There are a bunch of functions built
in and get the current URL headers. Um, you can use a CSS. We'll see. I'll show you
that in a second to actually look at the CSS structure of your page and a number of
things internally. If you're familiar with it, you're actually working with Symfonys
Dom crawler object. And you'll see an example of that in a second. So this has really
good examples of some more complex things that you can do. Um, inside of there. We're
not going to become experts in Blackfire player. I just want to get you a nice
introduction into how it works.

I remember it. [inaudible]

so let's look at what a failing example is. I'm gonna use this CSS function which
allows us to use a CSS selector. And we want to do here is let's look at this table
and assert that this table, uh, has 500 rows, which it definitely does not have 500
rows. So what we can do here is we can look for, there we go. Let me scroll down a
little bit. We look for this T body that has this jazz sightings list class and then
count the tr elements. So basically we'll go to the homepage and then I'll say expect
and then CSS. And instead of hero pass at the CSS selector, which is going to be T
bodied dot JS sightings list space T, R and this would turn a Dom color object, which
has a count method on it is greater than 500 all right, so let's see what happens.
That should not pass. And when we run, it doesn't in the error is really nice. I can
see that it didn't fail 500 because the count was actually equal to 25 so let's go
back now and just change that to 10 this is actually dynamic data, so we don't really
know how many rows are going to be in there. So we'll say there should probably be at
least 10 at any time.

Now when I run that it passes. Another thing I really like is the errors on this. So
if I do a typo, like a type count to just out and rerun the tests, you get unable to
call method, I don't have object Symfony component Dom, crawler, crawler. So that's a
nice hint that you're working with this object. So you can go look up on the internet
to see what methods that they actually has. And then you can use the expression
language to dive in deeper. So let's change that back to count. So as you've seen,
this has nothing to do with the Blackfire profiler. This is just a fun little tool
for being able to visit pages, click on links, fill out forms and do assertions about
uh, about your, your site. But this idea of creating these scenarios is about to
become very important in Blackfire. It actually, there is a one little integration
with um, the black fire player and Blackfire itself. And that is that you can add
performance assertions here. So when you as expect, these are actually like test
assertions, but you can also say assert.

And then here you can do one of the types of things that you do inside of your
Blackfire tests. So here we can say something like, uh, metrics that SQL got queries
that count is less than or equal to 30. Cause maybe we just say, Hey, the homepage
should always have less than 30 queries. Now if we ran this right now, it would still
pass. But if you started playing with this value, like you said, less than one and
rerun it again, you would notice that it's still passing even though this page is
making more than one query. And the reason is that this, this special functionality
is not going to work until we use something in Blackfire called environments, which
is a super awesome, powerful topic. And that we are going to start talking about
next. So leave this here for now, but it's not going to work, but let's get this
working. Next, we're going to deploy this site to production and started talking
about how we profile things on production, and that's going to include introducing us
to this very important Blackfire environments system.
