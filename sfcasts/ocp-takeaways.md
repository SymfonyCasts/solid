# OCP: Takeaways

Coming soon...

The big thing that OCP wants you to take away from this conversation is this, try to
imagine the future change requests, your at may receive an architect, your code so
that you will be able to make these changes without modifying existing classes. We
showed one common pattern to do this by injecting an array or Iterable of services,
instead of hard coding, all the logic, right, right inside of the class, two other
design patterns, the strategy and template method patterns solve this same problem in
a slightly different way. But I don't really love OCP. And I've got three reasons why
first even uncle Bob, the father of the solid principles admits that OCP is a lie.
OCP promises that if you follow it correctly, you will never need to mess around with
your old coat. But a system can't be 100% OCP compliant and it shouldn't be our
siting score.

Class is not closed against all kinds of changes. What would happen if we decided
that a scoring factor needs to multiply the existing score, instead of just adding to
it, this unexpected change, would it require us to yup. Modify the code in this
sighting score class. Only if we had known we could have an anticipated and this
change and invented an abstraction to protect us from this new kind of change, but no
one can predict the future. We can only do our best and most of the time we'll be
wrong. And that leads me to the second reason. I don't like a OCP. It creates
unnecessary abstractions, which make your code harder to understand our siting score
class is now closed against new scoring factors, which means we can add new scoring
factors to our system without modifying this class. But at what cost, I can no longer
open up this class in quickly understand all of the factors that go into calculating
the believability score. Now I would need to do some digging to figure out how this,
which factors are injected in here. And then go look at each of the individual factor
classes.

If you have a really large team, being able to separate things into smaller pieces
might be a good thing. But for example, here at Symfony casts, with our brave team of
about four developers, we would probably not make this change and adds misdirection
to our code with a limited benefit. And that leads me to my final reason for not
really liking OCP. And this one comes from Dan North, Dan North's a blog post in his
blog post. He argues that the open closed principle comes from an era when it change
changes were expensive because of the need to compile a code. The fact that we hadn't
really worked out how to refactor code yet, and because version control was done with
CVS, which apparently added to a mentality of making additive changes instead of
modifying existing code. In other words, OCP is a dinosaur Dan's advice, which I
agree with is quite different than OCP. He says, if you need to code, if you need
code to do something, if you need code to do something else, change the code to make
it do something else.

Quoting Dan, he says, code is not an asset to be carefully shrink, wrapped, and
preserved, but a cost, a debt, all code is cost. So if I can take a big pile of
existing code and replace it, what they smaller, more specific costs than I'm winning
at code. I love that. So how do I personally navigate OCP in the real world? It's
pretty simple. If I'm building an open source library where the people who use my
code will literally not be able to change it. Like the siding score class. Then I do
follow a pattern like we used for citing score. This gives my users the ability to
change the behavior of this class without needing to modify the code itself. But if
I'm coding and a private application, I'm much more likely to keep all the code right
inside the class, but this is not an absolute rule separating the code makes it
easier to each piece, easier to unit test and larger teams will probably want to
split things up more quickly than small teams as with all the solid principles, do
your best to write simple code and don't overthink it next.

Let's turn to solid principle. Number three, the list of substitution principle.

