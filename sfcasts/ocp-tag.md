# OCP: Autoconfiguration & tagged_iterator

Coming soon...

Of course, if you go over and I should try this, it's not going to quite work yet.
I'll hit submit right here. And, um, immediately we see an error can not resolve
arguments, citing score. Uh, the middle of the error is the most important part cannot
auto wire service citing score argument scoring factors of method construct is type
into the rate. You should configure its value explicitly. We haven't told Symfony
which objects we want to pass to our `SightingScorer`.

The easiest thing we we can do, the easiest solution is to configure this manually
and `config/services.yaml` So down here we will say `App\Service\SightingScorer`.

Then `arguments:`. And then we are going to specifically configure the `$scoringFactors`
argument. So I'll copy that. And then this is going to be set to an array. So below
this we'll use dashes and here we're going to inject each of those services. So at
`@App\Scoring\TitleFactor`, then I'm going to copy that, paste it and fix my
indentation. There. We have `DescriptionFactor` and we have `CoordinatesFactor` So
that should pass an array, which is what these dashes mean of these three services
for your fresh now, yes, the error has gone. It did kick us over to the log-in page.
Let's copy the email above and password hit, sign in and beautiful. The page loads.
Let's give it a try.

We'll fill in some details and hit upload. And before actually fill this in. Actually
I can go up here,

But in some key words in the description that will help with this. Okay.

And all right, something a and beautiful

Big foot believability score of 10.

Come on. Only a score of 10, four. Talk more about OCP on a technical Symfony level.
There is one other way to inject these services into, um, the score. In fact, your
services in deciding score, it's called a tagged iterator. It's pretty cool. And it's
really commonly done in the core of Symfony. First open up source `Kernel.php`. I
know we almost never even open up this file. Uh, I'm going to go to code generate or
command and in the Mac and go to override methods in override one called `build()`. Let
me see if I can find it. There it is.

This is a hook where we can do some extra processing on a container. The paramedic is
actually empty, but I'll leave the parent call now at the
`$container->registerForAutoconfiguration()` pass this `ScoringFactorInterface::class`
The name is Reiner face and then `->addTag('scoring.factor)` Thanks to this, any auto
configurable services, which is all of our services that implement `ScoringFactorInterface`
will automatically be tagged with `scoring.factor`, which is a name I
totally just made up this on its own. Won't make any real change, but now back in
`services.yaml` we can simplify. So for scoring the `$scoringFactors` argument, we're
going to set this to exclamation point. That's a special syntax in YAML, uh,
`!tagged_iterator scoring.factor`. This says please inject all services that are
tagged with `scoring.factor`. Pretty cool, right? The only gotcha is that we need
to change the type hint in scoring setting score to be an `iterable`. It's not actually
going to pass us a real Ray, but it's still going to be something that we can loop
over. So I'll also change the `iterable` Up here

Next, now that we understand the type of changes that OCP wants us to make, let's
talk about why we should care or not care about OCP and when we should and shouldn't
follow it.

Okay.

