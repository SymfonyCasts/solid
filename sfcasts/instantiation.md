# Spotting Heavy Object Instantiation

I want to show a... more *subtle* performance problem. To even *see* it, we need
to go back to the `prod` environment:

[[[ code('698fc11371') ]]]

Make sure to run `cache:clear`:

```terminal-silent
php bin/console cache:clear
```

`cache:warmup`:

```terminal-silent
php bin/console cache:warmup
```

And also:

```terminal
composer dump-autoload --optimize
```

Let's create a fresh profile of the homepage. I'll call this one:
`[Recording] Homepage prod`. Click to view the timeline: http://bit.ly/sf-bf-instantiation

Overall, this request is pretty fast. Click into the "Memory" dimension. The
biggest call is `Composer\Autoload\includeFile`: that's *literally* Composer
including files that we need... not a lot of memory optimization we can do about
that.

But, if we look closer, the memory dimension reveals something else. See this
"Container" thing - the 2nd item on the function list? This is related to Symfony's
container, which is responsible for *instantiating* all of our objects. This
specific function is interesting: it's highlighting a *section* of a file that lives
in our cache directory. If you looked in that file, you would see that this part
of the code  is responsible for *including* some of the main files that our app
needs. It's basically another version of the top node: it's code that includes
files for classes we're using.

## Seeing Object Instantiation

Ok, so the first few aren't really *that* interesting. Things get much more
intriguing down on the 4th function call: some
`Container{BlahBlah}/getDoctrine_Orm_DefaultEntityManagerService.php` call.
What is this? Well, the details of how this is organized are specific to Symfony:
but this is evidence of something that *every* app does: this is showing the
amount of resources used to *instantiate* Doctrine's EntityManager object.
I know, we don't often think about how much time or how much memory it takes to
*instantiate* an object, but it *can* sometimes be a problem. The next function
call is for the instantiation of Doctrine's Connection service.

Go down a little bit... I'm looking for something specific... here it is:
`getLoginFormAuthenticatorService()`. This is responsible for instantiating a
`LoginFormAuthenticator` object in our app. It's not a particularly problematic
function though: it's 10th on the list... only takes 2.56 milliseconds and uses about
500 kilobytes.

## Checking the Instantiation of LoginFormAuthenticator

Let's check out the class: `src/Security/LoginFormAuthenticator.php`:

[[[ code('fd3bf2ea51') ]]]

As its name suggests, this is responsible for authenticating the user when they
submit the login form.

But, there's something special about this class. Due to the way the Symfony
security system works, Symfony instantiates this object on *every* request. It does
that so it can then call `supports()` to figure out if this service should be
"activated" on this request or not:

[[[ code('412f2b425a') ]]]

For this class, it *only* needs to its work when the URL is `/login` and this
is a `POST` request. In *every* other situation, `supports()` returns false
and *no* other methods are called on this class.

So let's think about this. Instantiating this class takes about 3 milliseconds
and 500 kilobytes... which is not a *ton*... but since *all* it needs to do for
*most* requests is check the current URL... then exit... that *is* kind of heavy.

## Why Instantiation is Slow?

The question is: *why* does it take so many resources to instantiate? Well, 500
kilobytes is not a *ton*, but this *is* - according to Blackfire - one of the
*most* expensive objects that is created on this request. Why?

Check out the constructor:

[[[ code('2e55155e0b') ]]]

In order to instantiate this class, Symfony needs to make sure the `EntityManager`
is instantiated... and the `UrlGenerator`.. and the `CsrfTokenManager`... and the
`UserPasswordEncoder`. If any of *these* services have their *own* dependencies,
even *more* objects may need to be instantiated. In rare situations, creating
a service can be a *huge* performance problem.

In the case of the `EntityManager` and the `UrlGenerator`... those are pretty
core objects that would *probably* be needed and thus instantiated by *something*
on this request anyways. But `CsrfTokenManager` and `UserPasswordEncoder` are
*not* normally needed. In other words, we're forcing Symfony to instantiate both
of those services on *every* request... even though we *only* need them when the
user is submitting the login form.

This is a *classic* situation where you have an object that is instantiated on
every request... but only needs to do *real* work in rare cases. Certain event
subscribers - like our `AgreeToTermsSubscriber` - Symfony security voters & Twig
extensions are other examples from Symfony. These services might be quick to
instantiate... so no problem! But they *also* might be expensive.

So... how *could* we make it quicker to instantiate `LoginFormAuthenticator`?
In Symfony, with a service subscriber.
