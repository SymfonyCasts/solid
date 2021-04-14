# Srp Defined

Coming soon...

Solid starts with the single responsibility principle or SRP, as RP says, a module
should have only one reason to change. Um, okay. That doesn't really mean anything
useful. At least to me, that sounded like a bunch of wishy washy, technical speak

In somewhat simpler terms. SRP says a function or class should be responsible for
only one task or should have only one responsibility, but what is a responsibility?
Exactly. And why is this rule helpful on a high level? What SRP is really trying to
say is gathered together the things that change for the same reason and separate
things that change for different reasons. We'll talk more about this, but keep it in
mind. And what problem is SRP trying to help us solve? In theory, if we organize our
code into units that will change for the same reason, then when we get a new feature
or change request, we will only need to modify it one class instead of making 10
changes to 10 different files and trying not to break anything.

Okay. And enough defining stuff. Let's jump into an example, how your browser click
sign up. As you can see, our app does have a registration form, open 
`src/Controller/RegistrationController` to see the code behind this. Most of the logic
for saving the user is in this `UserManager::register()` function. So I'll click into
that. That's actually a `src/Manager/UserManager.php`, and this method
hashes the user's password, and then saves the user to the database. Now we have a
change request. The product manager of Sasquatch sightings would like us to send a
confirmation email after registration to confirm the user's email address, to
understand SRP let's implement this the wrong way first, at least wrong. According to
SRP side note, we're going to build a simple email confirmation system by hand. If
you have this need in a real project, check out `symfonycasts/verify-email-bundle`.
Anyways, the easiest way I can see to add this feature just to add the logic right
inside `UserManager::register()` because then we will only have to touch with one file
and it will make sure that we always send the confirmation email to the new users.
Since we always already call this `register()` method at the bottom of this function.

I'm gonna start by cop pasting in a private function called `createToken()`. You can
copy this from the code block on this page. This generates a random string that we
will include in the confirmation link up in register. Let's generate a new token
`$token = $this->createToken()`

Then set it on the user `$user->setConfirmationToken($token)`. Uh, before I started
recording, if you look in the user dot PHP file, I already created a `$confirmationToken`
property on here that saves to the database. So now when we are new user
registers, they will have a random confirmation token set onto their user record row
back in `RegistrationController`. If you scroll down a little bit, I've also already
built a confirmation action to confirm their account or user just needs to hit this
pre-made route

Where

The token in the URL matches the confirmation token that we've set onto their user
record. So back in `UserManager`, we have two jobs left. First. We need to generate an
absolute URL to the confirm action that contains the token. And second, we need to
send a user an email with that URL inside. Let's generate the URL first up in the
`__constructor()` autowire `RouterInterface $router` Then I'll hold
Alt + Enter and go to "Initialize properties" to create that property and set it

Now, January that you were all below, let's say `$confirmationLink = $this->router->generate()`
And then the name of our route, which over here, if you look that is
called `check_confirmation_link` is that, and for the second argument, we'll need to
pass a `token` set to `$user->getConfirmationToken()`. And then since we need this
to generate an absolute URL, we need to pass a third argument, which is going to be
`UrlGeneratorInterface::ABSOLUTE_URL`, because this will be included in an email to
send the email we need mailer. So go back up and add one more argument here, 
`MailerInterface $mailer`. I use the same trick of Alt + Enter to go to "Initialized properties",
to create that property and set it

Beautiful below. I'm going to paste

Some email generation code. And the only thing I need to do is re type the L on
`TemplatedEmail` and hit tab. So that as the `use` statement for me,

Very simply, this will send it to that user from this email address, here's the
subject and this template already exists. So down here and `templates/emails/`, there's
a `registration_confirmation.html.twig`, and you see we're passing any `confirmationLink`
variable. That `confirmationLink` variable is being rendered inside of this
email. So I've done some of the work for us, but to get this, to get this started the
last thing we need to do all the way at the bottom. So after we know that the user
has saved successfully, we'll say `$this->mailer->send($confirmationEmail)`

all right, we did it. We can even test this thing out, back at the registration page.

Let's register as a new user, any password it enter and

Awesome. It looks like it worked. Now,

The project is set up to not actually send any emails, but we can see what that
imaginary email would have looked like by going down to the web debug toolbar,
clicking any of these links to go to the profile profile hitting last 10, and then
clicking to look, go into the post request for the signup form. Now on the left here,
there is an email section and you can see that we sent one email and we can even look
at what the HTML look like for it. So the email did send, and if we kind of steal our
confirmation link right there, I'll pop this and do another tab. And our email is
confirmed. Okay. Mission accomplished. And all of our code is centralized into one S
one method, but we did just violate SRP. Y our `UserManager` class now has too many
responsibilities, but what is a responsibility? Exactly. And what are the
responsibilities that this class has. And also what's the problem with violating SRP.
Anyways, let's talk about all of this in detail next, including refactoring our code
to follow SRP more closely.

