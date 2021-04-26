# OCP: Takeaways

The big thing that OCP wants us to take away from this conversation is this: try to
imagine the future changes you are most likely to need to make, and architect,
your code so that you will be able to make those changes without modifying existing
classes.

## OCP Design Patterns

We showed one common pattern to do this: by injecting an array or - iterable - of
services instead of hardcoding all the logic right inside the class. There are
also other patterns that you can use to accomplish OCP, including the "strategy
pattern" - which is similar to what we did, but where you allow just *one* service
to be passed in to handle some work - and the template method pattern. All of
these are different flavors of the same thing: allowing functionality to be passed
*into* a class, instead of living *inside* the class.

## OCP is Never Fully Achievable

But the truth is, I don't love OCP. And I've got three reasons. First, even
Uncle Bob - the father of the SOLID principles - knows that OCP is a "lie". OCP
promises that, if you follow it correctly, you will *never* need to mess around
with your old code. But a system can't be 100% OCP-compliant. Our `SightingScorer`
class is "closed" against the change of "adding new scoring factors". But what
would happen if we suddenly needed a scoring factor to be able to *multiply* the
existing score by a number... instead of just adding *to* it. This unexpected change
would require us to, yup, modify the code in `SightingScorer`. If we had
anticipated this change, we could have added an abstraction to `SightingScorer`
to protect us from this new kind of change. But no one can perfectly predict the
future: we can do our best... but often, we'll be wrong.

## Unnecessary Abstractions add Complexity

Of course, just because a principle isn't perfect doesn't meant we should never
use it. But that leads me to the second reason that I don't love OCP: It creates
unnecessary abstractions... which make our code harder to understand.

`SightingScorer` is now closed against new scoring factors, which means we can add
new scoring factors to our system without modifying the class. But at what cost?
I can no longer open up this class and quickly understand how the believability
score is calculated. Now I need to dig around to figure out which factors are
injected... then go look at each individual factor class.

If you have a large team, being able to separate things into smaller pieces like
this becomes more desirable. But, for example here at SymfonyCasts - with our brave
team of about four - we would probably *not* make this change. It adds misdirection
to our code, with a limited benefit.

## Changing Code is... Ok!

And that leads me to my third and final reason for not loving OCP. And this one comes
from [Dan North's blog post](https://dannorth.net/2021/03/16/cupid-the-back-story/amp/).

He argues that the open-closed principle comes from an era when changes were
expensive because of the need to compile a code, the fact that we hadn't really
mastered the science of refactoring code yet, and because version control was
done with CVS, which according to him, added to a mentality of wanting to make
changes by adding *new* code, instead of modifying *existing* code.

In other words... OCP is a dinosaur! Dan's advice, which I agree with, is quite
different than OCP. He says:

> If you need code to do something else, change the code to make it do something else.

Quoting Dan, he says:

> Code is not an asset to be carefully shrink-wrapped, and preserved, but a cost,
> a debt. All code is cost. So if I can take a big pile of existing code and replace
> it with smaller, more specific costs, than I'm winning at code.

I love that.

So how do I *personally* navigate OCP in the real world? It's pretty simple. If I'm
building an open source library where the people who use my code will *literally*
not be able to modify it, then I *do* follow a pattern like we used in
`SightingScorer` whenever I identify a change that a user might need to make.
This gives my users the ability to *make* that change... without modifying the
code in the class... which would be impossible for them.

But if I'm coding in a private application, I'm *much* more likely to keep all the
code right inside the class. But this is *not* an absolute rule. Separating the code
makes it easier to unit test and can help us follow the advice from SRP: writing
code that "fits in your head". Larger teams will also probably want to split things
more readily than smaller teams. As with all the SOLID principles, do your best to
write simple code and... don't overthink it.

Next, let's turn to SOLID principle number three: the Liskov Substitution Principle.
