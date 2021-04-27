# SRP: Responsibilities

We've just been informed that - gasp - from time to time, our confirmation email
doesn't reach our user's inbox! Ah! And so: we need to implement a resend feature.

## SRP: You Shouldn't Need to Change Unrelated Code

This should be easy, right? After all, we've encapsulated all of our logic for sending
a confirmation email into one method. But... hmm. To get this to work, we're probably
going to need to extract *part* of the `register()` method into a separate public
function so that we can *just* resend the email... without also creating a new token
and re-hashing the password.

[[[ code('6b25087b1f') ]]]

Isn't it kind of weird... or at least "not ideal"... that in order to add this
"email resend" feature, we're going to be messing with and rearranging code that
deals with hashing passwords and persisting user data? In a perfect world,
shouldn't I be able to create this "email resend" feature without going *anywhere*
near code that's unrelated to this functionality?

*This* is what SRP is trying to help us with. In that "perfect" SRP world, each time
a change is requested in our project, we would only need to touch code that directly
relates to that change: we wouldn't need to change - or even work near - unrelated
code. The fact that we're going to need to modify a method that *also* deals with
saving users and hashing passwords... in order to add a feature that has *nothing*
to do with that stuff... is a sign that `UserManager` violates SRP. Our `UserManager`
class has too many responsibilities.

## What *is* a "Responsibility"?

But what *are* the responsibilities of this class? I can think of 5 at least: generate
a confirmation link... which also includes creating the confirmation token,
create an email, hash a password, save the user and send an email.

But... hold on a second. And this is a very, very important - and confusing - point
about SRP. Defining responsibilities is *not* meant to mean:

> Think of all the different, tiny things that your class does.

Nope! A better way to say this might be:

> Think of all the different reasons that this class might change.

That's much harder... and it *completely* depends on your application and business.
To help with this, it's sometimes useful to think of what our class does on a higher
level. In my eyes, our register method does two basic things: (1) it prepares &
persists the user and (2) it sends an email.

Now let's see if we can think of a person in our "totally-not-fake" business that
might ask for a *change* to one of these two things.

For example, for the "high level job" of "preparing and persisting the user",
our database administrator might, in the future, want to change how users are stored...
or our CTO might want to start using a third party authentication provider instead
of storing users in a local database and managing their passwords. This type of change
would affect how we hash passwords *and* how we save users. In other words, two of
our original, so-called "responsibilities" - hashing the password and persisting
the user - will likely change for the same reason. And so, they are really part
of the *same*, *one* responsibility: "preparing and persisting the user".

The other "high level" thing the method does it send the confirmation email. That
will most likely need to change if a marketing person wants to tweak the subject
of an email to be more fun... or pass in some "featured product" variables to the
template to try to sell stuff. This means that 3 of the other original so-called "responsibilities" - generating the confirmation URL, creating the email and
sending the email - will all most likely change for the same reason. And so, for our
project, they would all be considered *one* responsibility: "sending the confirmation
email".

## Organizing Responsibilities is an Art... at Best

Is this perfect? *Definitely* not! You could *easily* argue that sending the email
would change for *another* reason. If someone decides we're going to start
sending emails using a *different* email provider service... we're already protected
from that change: that would just require some configuration tweaks in a *different*
file. But what if we think that it's likely that we might change how our email verification system works in the future? In that case, we would have a legitimate
reason to think that the generation of the confirmation token and link would change
for a *different* reason than our user persistence or email creation.

Identifying the most likely reasons that a function might need to change and then
grouping the functionality into those responsibilities is the hardest part of SRP.
Even *our* grouping looks imperfect. But honestly, it's good enough! My advice is
to do your best and don't over think it. We're also going to talk about *over*
optimization of SRP later... which can lead to a different problem.

It's also helpful to keep our original "human" definition for SRP in mind:

> Gather together the things that change for the same reason and separate those
> things that change for different reasons.

Next: now that we've identified the two responsibilities that `UserManager` currently
has, let's refactor our code to make it more SRP compliant.
