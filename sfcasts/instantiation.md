# Spotting Heavy Object Instantiation

I want to show a... more *subtle* performance problem. To even *see* it, we need
to go back to the `prod` environment. Make sure to run `cache:clear`:

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
`[Recording] Homepage prod`. Click to view the call graph: http://bit.ly/sf-bf-instantiation

Overall, this request is pretty fast. Click into the "Memory" dimension. The
biggest call is `Composer\Autoload\includeFile`... that's *literally* Composer
including files that we need... not a lot of memory optimization we can do about
loading classes we need.

But, if we look closer, the memory dimension reveals something else. See this
"Container" thing - the 2nd item on the function list? This is related to Symfony's
container, which what's responsible for *instantiating* all of our objects. This
specific node is interesting: it's highlighting a *section* of a file that lives
in our cache directory. If you looked in that file, you would see that this section
is responsible for including some of the main files that our app needs. It's
basically another version of the top node: it's code that includes files we need.

## Seeing Object Instantiation

Things *really* get interesting down on the 4th function call: some
`Container{BlahBlah}/getDoctrine_Orm_DefaultEntityManagerService.php` call.
What is this? Well, the details of how this is organized is specific to Symfony:
but this is evidence of something that *every* app does: this is showing the
amount of resources used to *instantiate* Doctrine's EntityManager object.
I know, we don't often think about how long or how much memory it takes to
*instantiate* an object, but it *can* sometimes be a problem. The next function
call is for the instantiation of Doctrine's Connection service.

Go down a little bit... I'm looking for something specific... here it is:
`getLoginFormAuthenticatorService`. This is responsible for instantiating some
`LoginFormAuthenticator` object. It's not a particularly problematic function
though: it's 10th on the list... only takes 2.56 milliseconds and uses about
500 kilobytes.

## Checking the Instantiation of LoginFormAuthenticator

Let's check out the class: `src/Security/LoginFormAuthenticator.php`. As its
name suggests, this is responsible for authenticating the user when they submit
the login form.

But, there's something special about this class. Due to the way the Symfony
security system works, Symfony instantiates this object on *every* request. It does
that so it can then call `supports()` to figure out of this service should be
"activated" on this request or not. For this class, it *only* needs to its work
when the URL is `/login` and this is a `POST` request. In *every* other situation,
`supports()` returns false and *no* other methods are called on this class.

So let's think about this. Instantiating this class takes about 3 milliseconds
and 500 kilobytes... which is not a *ton*... but since *all* it needs to do for
*most* requests is check the current URL... that *is* kind of heavy.

The question is: *why* does it take so many resources to instantiate? Well, 500
kilobytes is not a *ton*, but this *is* - according to Blackfire - one of the
most expensive objects that is instantiated on this request. Why?

Check out the constructor. In order to instantiate this class, Symfony needs
to make sure the `EntityManager` is instantiated... and the `UrlGenerator`.. and
the `CsrfTokenManager` and `UserPasswordEncoder`. And, if any of *those* services
have dependencies, even *more* objects may need to be instantiated. In some rare
casts, creating a service can be expensive.

In the case of the `EntityManager` and the `UrlGenerator`... those are pretty
core objects that would *probably* be instantiated by *something* on this request
anyways. But `CsrfTokenManager` and `UserPasswordEncoder` probably are *not*
normally needed. In other words, we're forcing Symfony to instantiate both of those
services on *every* request... even though we *only* need them when the user
is submitting the login form.

This is a *classic* situation when you have an object that is instantiated on
every request but only needs to do *real* work on rare cases. Certain event
subscribers - like our `AgreeToTermsSubscriber` - and Symfony security voters
are other examples. These services might be quick to instantiate... so no problem!
Or they might be more expensive.

So... how *could* we make it quicker to instantiate `LoginFormAuthenticator`?
In Symfony, with a service subscriber.
