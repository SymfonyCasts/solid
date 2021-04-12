# SOLID: The Good, The Bad & The Real World

Hey friends! Welcome to our *long* awaited tutorial on the principles of SOLID: single
responsibility principle, open closed principle, Liskov substitution principle,
interface segregation principle and, my personal favorite: the donut in face principle.
Probably... actually known as the dependency inversion principle.

I want to thank my coauthor Diego for helping me *finally* put this tutorial together.
And I'm *super* sorry if you've been waiting for this!

## SOLID Principles: I don't Love Them

So... why *did* it take us so long to get this tutorial done? The short answer is:
I.... kind of don't like the SOLID principles. Okay, let me rephrase that. The
SOLID principles are tough to understand. And, in my most humble opinion, they're
not always good advice! It depends on the situation. For example, you should write
code for your application *differently* than you would write code that's meant to
be open sourced and *shared*.

If you want to know a bit more about why SOLID might *not* always be correct,
you can read a recent blog post written by Dan North called
[CUPID – THE BACK STORY](https://dannorth.net/2021/03/16/cupid-the-back-story/).
Dan North is known for being the person who first made behavior-driven development
famous. You may have heard of him if you're a Behat user.

*Anyways*, this tutorial is *not* going to be yet another tutorial where we read
the definition of each SOLID principle in a monotone voice... and slowly get lost,
bored and finally fall asleep. Nope. We're going to dive into each principle, learn
what they *really* mean - using normal human words - code some real examples and
discuss why and when following these principles makes sense and does *not* make
sense. But even when the SOLID principles should *not* be followed, they have a
lot to teach us. So strap in for a wild ride.

## Project Setup

Since we're going to be doing some *real* coding, let's get the project set up and
rocking. Do me a solid by downloading the course code from this page and unzipping
it. After you do, you'll find a `start/` directory with the same code you see here.
This fancy `README.md` file has all the details about how to get the project up and
running. The last step will be to find a terminal, move into the project and start
a local web server. I'll use the Symfony binary for this:

```terminal
symfony serve -d
```

Once this finishes, copy that URL, spin back over to your browser, paste and... say
hello to "Sasquatch Sightings"! Our latest effort to find the infamous Bigfoot. What
this code actually does is... not too important. It talks to a database, lists some
big foot sightings and has some calculations. It will be our playground for diving
into the SOLID principles.

So next, let's start with the first: the single responsibility principle!
