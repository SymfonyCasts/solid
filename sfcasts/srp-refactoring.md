# Srp Refactoring

Coming soon...

We've identified that user manager register handles two things that might change for
different reasons. These are its two responsibilities creating and sending an email
or a welcome email registration email, and setting up the data for a user and saving
it to the database. We're not going to follow one of SRPs pieces of advice, separate
those things that change for different reasons. The first thing I want to do is
rename register to create, or you could even, uh, name it, something like safe. The
point is I want to make its intention clear. Its purpose is to set all the required
data on the user object and save it to the database. All right, click on register. Go
to refactor rename,

Call this create.

When I hit enter over in registration controller. If you're keeping track that
renamed the usage there automatically,

Right?

Good. Now let's move the email related logic into a new class in the service
directory though. It doesn't matter where I'm going to create a new PHP class called
how about confirmation email sender. This class will meet need to services the router
so it can generate the link and mailer. Let's add a polo function

With those two arguments,

Their interface, mailer prouder interface router. I had escape and go to alt enter
and go to initialize properties. And I'll initialize both those properties. Don't
need this extra PHP dock up here anymore. Now we can create a public function on here
called how about sin with a user object argument

That will return void

For the inside of this. We're going to go steal all of the email related logic from
user manager. So the confirmation link in confirmation email I'll delete those
things.

When I, Peter

Storm asked me if I want to import a couple of use statements, I definitely do. And
in the last piece I'm going to steal is the actual sending of the email, which we
will put at the bottom.

Okay.

Very nice. Let's celebrate. Celebrate by cleaning things up and user manager. We can
now remove the last two arguments, router and mailer. I'll remove those properties as
well. And then we can remove a couple of you statements up on top. Perfect. Now let's
see who should be responsible for creating and setting the confirmation token on the
user? I'm not exactly sure, but I have a useful trick when in doubt invert the
question who should not be responsible for creating the token, that's a bit easier.
It probably doesn't make sense that the service that sends emails sends the email
should be responsible for generating that token and saving user information to the
database.

Yes, this service does deal with the confirmation link, but it really is very
different, has a very different concern than saving things to the database. So if we
discard a confirmation email sender from our options, then there's only one logical
place left user manager create. And it makes sense. This method sets up new user
objects with all the data they need and then save it saves them. You could also
isolate this confirmation token creation logic into a third class. There's no right
or wrong answer, which is what makes this stuff so tricky. But over optimizing, by
splitting things into too many pieces is also something you do not want to do. We'll
talk more about that in the next chapter. Anyways, now that we've split all of our
coat into two places over in registration controller, we need to call both methods.
All the wire. I knew argument up here for a confirmation email sender. I'll call it
confirmation email center and then down here, right after we call user manager,
create we'll call confirmation email sender arrow, send in pass. If the user object
done our original feature, sending a confirmation email is now implemented in a more
SRP friendly way. By the way,

If you don't like needing to call

Two methods, whenever you're registering a new user, no problem. You can extract
these two calls into a new class called maybe user registration handler it's one
responsibility would be to orchestrate all tasks related to registering a user. It
wouldn't actually do that. Do that work as the details of persisting the user and
sending a confirmation email are separate responsibilities, but it is okay to put
those tasks into a single service. Anyways, now we can work on the new feature that
our team asked us the ability to re send a confirmation email. You'll instantly feel
how easy this is now that our code is SRP friendly. If you download the course code
from this page, you should have a tutorial director down here with a recent
confirmation controller file inside. I'm going to copy. Copy that, go up to the
controller directory and paste. This comes with the boiler plate needed to have an
end point that you could post to

That

Would rescind your confirmation email, but the actual sending a confirmation mail is
still a to do True. Remove that to do. We just need to auto wire, the confirmation
email sender, and then say confirmation, email sender->send

User

It's that easy. I won't bother testing this, but it should work ship it most
importantly, thanks to our new organization of having confirmation email sender and
user manager.

Okay,

Mark, if for example, a marketing person did want to tweak the subject on our welcome
email. We can make that change without messing around near code. That saves things to
the database or hash as passwords. But I have more to say about SRP, like the risks
of over optimizing for SRP, which violates a concept called cohesion. And I also
think that thanks to inspiration from Dan North, there's a much easier way to think
about SRP. I'll tell you what it is next.

