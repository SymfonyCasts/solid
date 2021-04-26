# Dependency Inversion Principle

Coming soon...

We've made it to the fifth and final solid principle. The dependency inversion
principle, this puppy has a two part definition

Ready,

One high level modules. It should not depend on low level modules. Both should depend
on abstractions. For example, interfaces and two abstractions should not depend on
details, details or concrete implementations should depend on abstractions. Uh huh.
If that makes sense to you, you are awesome. And I'm really jealous of you.

How

Would I rephrase this? Uh, yikes. Um, how about this one classes should depend on
interfaces instead of concrete classes. And two, those interfaces should be designed
by the class that uses them, not by the class that will implement them. That's
probably still fuzzy, but don't sweat it. This requires a real example. Here's our
new problem. We've been starting to get so popular That some of our posts are getting
a lot of spam on them. And so we need a way to determine whether or not a comment is
spam based on some business logic that we've created. If you download the course code
from this page, then you should have a tutorial directory with a comment, spam
manager class inside, copy this, then go create a new directory and source call them

Comment

And paste this class inside This class, basically determines if a comment should be
flagged as spam by running a regular expression

On the comments, content, using a list of spam words. If the content contains two or
more of those words, then we consider this comment as spam. And we just, we throw an
exception. If you think about the single responsibility principle, you can argue that
this class already has two responsibilities. The low-level regular logic on the spam
words and a higher level business logic that decides that to spam words is too much.
What? Say that again. We do think that these are two different responsibilities. And
so we want to split this class into two pieces for the rejects logic and the service
directory. It doesn't really matter where though let's create a new class called
rejects bam word helper.

Let's go there. Luke moved the private spam words method over to this new place. I'll
delete that paste that here. And then we'll create a new public function called get
matched spam warrants where we will pass it a string of contents. And this will
return an array of the match. Spam works next, let's move the rejects logic itself
into this class. So I'm actually going to copy the entire contents of this method,
but leave them. Some of this needs to stay here. Well, paste this in here. And, and
what we actually want to do is we don't need comment or get content anymore. It's
just called content. And then we are actually going to the bad words on common. Zero
index is going to contain all the matches so we can do has just returned that
beautiful. Okay, now that we have this class all set up, we can inject it into our
comments, fair manager.

So I'll add a public function, honors core honors, welcome with rejects spam or
helper call the spam were helper. Then I'll go all to enter and go to initialize
properties to create that property and set it, then use it below. So now we're going
to have bad words on comment = this->spam word, helper, arrow, get matched spam
words. We'll pass that content from above and we don't need any of this logic, the
middle anymore. And now batteries on comment. And actually it'd be the bad word. So
we don't need to get the zero index anymore. We can just count that entire variable
and done

[inaudible].

At this point, we've separated the high level business logic of deciding how many
spam words should cause a comment to be marked as spam from the low level details of
actually matching and finding the spam words. The dependency and version principle
doesn't necessarily tell us whether or not we should split the original logic into
two classes like we just did. That's probably more a single responsibility principle
type of thing, but dip does teach us to think about our code in terms of high level
modules or classes like comment, spam manager, that deep pen on low level modules or
classes like reject spam word helper. And it gives us some concrete rules about how
this relationship should be treated next. Let's refactor this relationship, the
relationship between these two classes to be dependency in version principal
compliant. We'll see, in real terms exactly what changed each of the two parts of
this principle want us to make.

