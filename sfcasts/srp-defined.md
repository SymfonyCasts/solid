# Single-Responsibility Principle: What is it?

SOLID starts with the Single-Responsibility Principle or SRP. SRP says:

> A module should have only one reason to change.

Um, huh? This sounds... a *little* too "fluffy" to be *actually* useful.

Let's... try again with a... somewhat simpler definition:

> A function or class should be responsible for only one task... or should have
> only one "responsibility".

Better. But... what *is* a "responsibility" exactly? And why is this rule helpful?

## SRP: The Human Definition

On an *even* simpler level, what SRP is *really* trying to say is:

> Gather together the things that change for the same reason and separate
> things that change for different reasons.

We'll talk more about this definition later, but keep it in mind.

And what problem is SRP trying to help us solve? In theory, if we organize our
code into units that all change for the same reason, then when we get a new feature
or change request, we will only need to modify one class... instead of making 10
changes to 10 different files... and trying not to break things along the way.

## Sending a Confirmation Email

Enough defining stuff! Let's jump into an example. On your browser, click
"Sign Up". As you can see, our app has a registration form! Open
`src/Controller/RegistrationController.php` to see the code behind this. Most of
the logic for saving the user is in this `UserManager::register()` method. Hold
Cmd or Ctrl to jump into this: it lives at `src/Manager/UserManager.php`.

This method hashes the user's password... and then saves the user to the database.
Awesome!

But *now*... we've received a change request! The product manager of Sasquatch
Sightings - a suspiciously hairy person - would like us to send a confirmation email
after registration to verify the user's email address.

To understand SRP, let's implement this the *wrong* way first. Well "wrong" according
to SRP.

Side note: we're going to build a simple email confirmation system by hand. If you
have this need in a *real* project, check out
[symfonycasts/verify-email-bundle](https://github.com/symfonycasts/verify-email-bundle).

## Coding up the Confirmation Email System

Anyways, the easiest way I can see to add this feature is to add the logic right
inside `UserManager::register()`... because we will only have to touch one
file and it will guarantee that anything that calls this method will *definitely*
trigger the confirmation email.

At the bottom of this class, I'm going to start by pasting in a private function
called `createToken()`. You can copy this from the code block on this page. This
generates a random string that we will include in the confirmation link.

Up in register, generate a new token `$token = $this->createToken()`...
and then set it on the user: `$user->setConfirmationToken($token)`.

Before I started recording - if you look at the `User.php` file - I already created
a `$confirmationToken` property that saves to the database. So thanks to the new
code, when a user registers, they *will* now have a random confirmation token saved
onto their row in the database.

Back in `RegistrationController`... if you scroll down a bit, I've *also* already
built a confirmation action to confirm their email. A user just needs to go to
this pre-made route - where the `{token}` in the URL matches the `confirmationToken`
that we've set onto their `User` record - and... bam! They'll be verified!

So back in `UserManager`, we have two jobs left. First, we need to generate an
absolute URL to the `confirmAction` that contains their token. And second, we need
to send an email to the user with that URL inside.

Let's generate the URL first. Up in the constructor, autowire
`RouterInterface $router`. I'll hit Alt + Enter and go to "Initialize
properties" to create that property and set it.

Now, below, say `$confirmationLink = $this->router->generate()` and...
the name of our route... is `check_confirmation_link`. Use that. For the second
argument, pass `token` set to `$user->getConfirmationToken()`. And because this
URL will go into an email, it needs to be absolute. Pass a third argument to trigger
that: `UrlGeneratorInterface::ABSOLUTE_URL`.

Now, let's send the email! On top, add one more argument -
`MailerInterface $mailer` and use the same Alt + Enter, "Initialize properties",
trick to create that property and set it.

Beautiful! Below, I'll paste in some email generation code. I'll also re-type the
`l` on `TemplatedEmail` and hit tab so that PhpStorm adds the `use` statement
on top for me.

This creates an email to this user, from this address... and the template it
references already exists. You can see it in:
`templates/emails/registration_confirmation.html.twig`.

We're passing a `confirmationLink` variable... and that is rendered inside
the email.

Finally, all the way at the bottom of `register()`... so after we know that the
user has saved successfully, deliver the mail with:
`$this->mailer->send($confirmationEmail)`.

Alright! We did it! And we can even try this! Back at the registration page,
register as a new user... any password, hit enter and... awesome! It looks like
it worked!

Now, the project is not configured to *actually* deliver the email. But we
can see what that imaginary email *would* have looked like by going down to the web
debug toolbar, clicking any of these links to go to the profiler... hitting
"last 10"... then clicking to get into the profiler for the POST request that we
just made to the registration form.

On the left, click into the "Email" section. There's our email! You can even look
at its HTML. I'm going to steal the confirmation link... pop it into a new tab
and... our email is confirmed! Mission accomplished!

*And*, all of our code is centralized into one method. But... we *did* just violate
SRP: our `UserManager` class now has too many responsibilities! But what do I mean
by the word "responsibility"? And what *are* the responsibilities that this class
has? And what's the problem with violating SRP anyways? And does the influence
of gravity extend out forever?

Let's answers most of these questions next.
