# Solid

Coming soon...

Hey friends, welcome to our long awaited tutorial on the principles of SOLID, single
responsibility principle, open closed principle, Liska principle, integration,
segregation principle, and my personal favorite. The donut interface principle also
probably actually known as the pendency inversion principle. First, I want to thank
my coauthor Diego for helping me put finally put together this tutorial. So why did
it take us so long to get this done? The short answer is I kind of don't like the
solid principles. Okay. Let me rephrase that. The solid principles are tough to
understand. Okay.

In my humble opinion, they're not always good advice. It depends on your situation.
If you want to know a bit more was recently echoed by a blog post written by Dan
North

Who was actually the person who introduced behavior driven development. For those of
you, B hat users, his blog post, he goes on to challenge a lot of the conclusions of
SOLID. Anyways, this tutorial is not going to be yet another tutorial where we read
the definition of each SOLID principle and slowly get lost board and finally fall
asleep. Nope. We're going to dive into each learn what they really mean. Like with
human words, code some are real examples and discuss why and when following these
principles does not make sense. So strap in for a wild ride. Since we are going to be
doing some real coding, let's get the project rocking, do me a solid by downloading
the course code from this page and unzipping it after do you'll find a start
directory with the same code you see here, this fancy `README.md` file

Has all the details about how to get the project up and running. The last step will
be to find a terminal move into the project. And I wanted to use the Symfony finery
to start a web server by running 

```terminal
symfony serve -d
```

once this finishes I'll copy that URL And spit back over to my browser and say hello to
Sasquatch Sightings, Our latest effort to find the infamous Bigfoot. What this code actually
does is not too important. It talks to a database and lists some things. It's
basically going to be a playground for us to look at the solid principles. So next,
let's start with the first one single responsibility principle.

