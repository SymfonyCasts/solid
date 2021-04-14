# Srp Responsibilities

Coming soon...

We just been informed that from time to time, our emails don't reach our user's
inbox. So we need to implement a rescind feature. This should be easy. Right, right.
After all, you've encapsulated all of our logic for sending a confirmation email into
one method, but Hm. Get this to work. We're probably going to need to extract part of
this method into another public function so that we can just resend the email without
also creating a new user and setting a new confirmation token. And isn't it kind of
weird that to add this new, recent feature, we're going to need to mess with logic.
We're gonna need to work right next to logic that's related to in hashing passwords
and saving users functionality that has nothing to do with confirmation emails.

This is what SRP is trying to help us with in a perfect SRP world. Each time a change
is requested in your project, you would only need to touch code that directly relates
to that change. You wouldn't need to change or even work next to co unrelated code.
The fact that we're going to need to kind of mess a work near this code for saving
users and hashing passwords, this unrelated code in order to add this recent
functionality is assigned that our user manager violates SRP. Our user manager class
has too many responsibilities, but what are the responsibilities of this class? I can
think of six generate a confirmation link, which actually also includes generating
the confirmation token. We create an email, we hash a password. We save the user and
we send the email, but hold on a second. And this is a very, very important and
confusing point about SRP defining responsibilities is not supposed to be think of
all the different, small things that your class does. Nope. A better way to say this
might be, think of all the different reasons that this class might change. That's
much harder and it completely depends on your application and business to help with
this. It's sometimes useful to think of what our class does at a higher level and on
a high level, our register method does two things. It creates users, grace, and saves
users, and sends an email.

Now thinking about these two things, let's see if we can detect a person in our
totally not fake business. That would be, that might be interested in asking for a
change to those. For example, first four creates and saves a user. Our database
administrator might want to change how users are stored to not store in doctrine, or
our CTO might want to start using a third party authentication provider instead of
storing users in a local database and managing their passwords. This type of change
would affect how we hash passwords and how we save users. So two of our original
responsibilities will likely change for the same reason. And so they all, they both
really fit under these same one creates and saves a user responsibility. Second for
sends an email that will probably change. If a marketing person wants an email that's
more fun or contains more upselling. That means that generating the confirmation URL,
creating the email and sending the email are all really part of one responsibility.

Is this perfect? Definitely. No. You could argue that sending the email would change
for a different reason. Like if you want to switch your email systems, then the, then
the email template, which would change if a marketing person wants to make the logo
bigger, identifying the most likely reasons that a function might be required to
change and then grouping the functionality into those responsibilities is the hardest
part of SRP. My advice is to do your best and don't overthink it. We'll also talk
about over-optimization later and keep in mind our original human definition for SRP
gathered together. The things that change for the same reason, separate those things
that change for different reasons. So next, now that we've identified the two
responsibilities that user manager currently has, let's refactor our code to make it
more SRP compliant.

Okay.

