# Refactoring for SRP

We've identified that `UserManager::register()` handles two things that might change
for different reasons. These are its two responsibilities: one, creating and sending
a confirmation email and two, setting up the data for a user and saving it to the
database.

We're now going to follow the advice of SRP and "separate those things that change
for different reasons".

## Clarifying The Responsibility of UserManager

The first thing I want to do is rename `register()` to `create()`... or you could
use `save()`... or even rename the entire class itself. The point is: I want to make
its responsibility more clear: to set all the required data on the user object
and save it to the database.

Right click on `register()`, go to Refactor->Rename and call this `create()`.
When I hit enter, over in `RegistrationController`, PhpStorm renamed the method
there too.

## Creating the ConfirmationEmailSender Class

Next, let's move the email-related logic into a new class in the `Service/`
directory... though, it doesn't matter where this lives. Create a new PHP class
called, how about, `ConfirmationEmailSender`. This class will need two services:
the router so it can generate the link and mailer. Add a public function
`__construct()` with those two arguments: `MailerInterface $mailer`,
and `RouterInterface $router`. Hit Alt + Enter and go to "Initialize properties"
to create both of those properties and set them. We don't need this extra PHPDoc
up here.

Now we can create a public function called, how about, `send()`, with a `User` object
argument that will return `void`.

For the inside of this, let's go steal all of the email-related logic from
`UserManager`. So... copy the `$confirmationLink` and `$confirmationEmail` parts...
delete those... and paste. Yes PhpStorm: I *definitely* want you to import
the `use` statements for me.

The last line we need to steal is the `$mailer->send()` line. Paste that into the
new class.

Very nice! Let's celebrate by cleaning things up in `UserManager`: we can
remove the last two arguments of the constructor - `$router` and `$mailer` - their
properties... and even some `use` statements on top.

## Who Should Generate the Confirmation Token?

Done! Now... let's see... who should be responsible for creating and setting the
confirmation token on the User? I'm... not exactly sure. But let's *invert* that
question: who should *not* be responsible for creating the token?

That's a bit easier: it *probably* doesn't make sense for the service whose only
responsibility is creating an email... to *also* be responsible for generating this
cryptographically-secure token and saving it to the database. Yes, this service
*does* deal with the confirmation link... but it feels like that logic would change
for very different reasons than the email itself.

So if we discard `ConfirmationEmailSender` from our options, then there's only one
logical place left `UserManager::create()`. And... it makes sense: this method sets
up new `User` objects with *all* the data they need and then saves them. You
*could* also choose to isolate the confirmation token creation logic into a *third*
class... there's no right or wrong answer, which is what makes this stuff so darn
tricky! But over optimizing, by splitting things into *too* many pieces, is also
something that we do *not* want to do. We'll talk more about that in the next chapter.

*Anyways*, now that we've split all of our code into two places, over in
`RegistrationController`, we need to call both methods. Autowire a new argument
into the method: `ConfirmationEmailSender $confirmationEmailSender`. Then, below,
right after we call `$userManager->create()`, say `$confirmationEmailSender->send()`
and pass the `$user` object.

Done! Our original feature - sending a confirmation email - is now implemented in
a more SRP-friendly way.

## Creating a "Takes Care of Everything" Service?

By the way, if you *don't* like that you need to call two methods whenever you're
registering a new user... I kind of agree! And it's no problem: you could extract
these two calls into a *new* class... maybe called `UserRegistrationHandler`.

It's *one* responsibility would be to "orchestrate" all the tasks related to
registering a user. This is just *one* responsibility - not many - because it's
not actually *doing* any of the real work. So, for example, if we needed to make
a change to the confirmation email... or change how users are persisted to the
database... neither of those would require us to need to modify this new class.
The new class would only change if we added some new "step" to user registration -
like sending an API call to our newsletter service.

## Enjoying SRP: Adding the Resend Feature

*Anyways*, now that we've refactored to be SRP-compliant, we get to enjoy our
hard work by *finally* adding the new feature that our team asked for: the ability
to resend a confirmation email.

If you downloaded the course code from this page, you should have a `tutorial/`
directory with a `ResendConfirmationController` file inside. Copy this, go up to
the `Controller/` directory... and paste. This comes with the boilerplate needed
for an endpoint that a user could POST to in order to resend their confirmation
email.

But... the actual *sending* of that confirmation email is still a "TODO". Remove
that comment, autowire the `ConfirmationEmailSender` service... and then say
`$confirmationEmailSender->send($user)`.

It's that easy! I won't bother testing this... but I will repeat the words that
every developer loves to say: "it should work".

The important thing is that, thanks to our new organization, if, for example, a
marketing person *did* want to tweak the subject on our welcome email, we can
make that change without messing around near code that saves things to the database
or hashes passwords.

But... I have *more* that I want to say about SRP... like the risks of over-optimizing,
which violates a concept called cohesion. I also think that, thanks to inspiration
from Dan North, there's an easier way to think about SRP. I'll explain all of that
next.
