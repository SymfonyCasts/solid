# SRP Takeaways

Coming soon...

We decided that the confirmation email functionality and user creation and
functionality we're likely to change for different reasons. And so we split these two
responsibilities into two separate classes. Now I have some questions. Should we
separate the password, hashing logic from the user persistence responsibility,
meaning move it to its own class. It should be treat the confirmation token creation
generation as its own responsibility and move it somewhere separate. If you look
quickly at SRP, it sounds like the rule is put every tiny piece of functionality into
its own class and method, but it is not this, this would make your code a disaster.
There's another concept called cohesion, which says, keep things together that are
related. At first, it seems like cohesion and SRP are opposites. I mean, SRP says
separate things and cohesion says keep things together. But on a closer look, there
are two ways of saying the same thing. Keep them only related things together. This
is the push and pull of SRP separate things that will change for different reasons,
but do not separate more looking at `UserManager`, we're already protected from
changes to the password hashing functionality, because it relies on a service that's
behind an interface, `UserPasswordEncoderInterface`, how that service works could
completely change and we wouldn't need to change any code in our class.

So the risk of that changing in some way that would cause us to need to change this
class is probably very low. What about the token generation logic? Well, do you think
it's really likely that you might change how your tokens are generated? This to me
feels like a weak thing to move elsewhere. It's already simple one line of code down
here, and two lines of code up here and it's unlikely change, especially for a reason
that's different than the other code in this class would change. Overall. My advice
is don't over anticipate potential future changes. Now at the beginning of this
tutorial,

I mentioned a blog post by a Dan North, the father of the behavior driven development
movement. He has something delightfully refreshing to say about the single
responsibility principle, instead of thinking about possible changes and organizing
things into responsibilities, which is tricky. He suggests something simpler, right?
Simple code using the high-risk stick that it fits in my head. I love this. And it's
just that simple. If a method or class has too many things in it, then the total
logic of that method won't fit in your head and it will be difficult to think about
and work with. So you should separate it into smaller pieces. On the other hand, if
you split the code for registering a user into 10 different classes, that's also
going to become complex to think about the overall goal is to create units of code
that fit in your head. If you do that, you'll find that you're probably creating
classes and methods that follow SRP pretty nicely without the stress of trying to
perfect it. Okay. It's time to dive into the next solid principle, the open closed
principle.
