# Performance Tests

Let's profile the Github API endpoint again. I'll cheat and go directly to
`/api/github-organization`... and click to profile this. I'll call it:
`[Recording] GitHub Ajax HTTP requests` because we're going to look closer
at the HTTP requests that our app makes to the GitHub API.

Click to view the call graph: https://bit.ly/sf-bf-http-requests

Oh wow - this request was *super* slow - 1.83 seconds - a lot slower than we've
seen before. We can see that `curl_multi_select()` is the problem: this is our
code making requests to the GitHub API, which is *apparently* running a bit
slow at the moment.

## We have a Performance "Bug"

Lucky for us, that's *exactly* what I wanted to talk about! At the top, Blackfire
tells me that this page made *two* HTTP requests. And HTTP requests are *always*
expensive for performance.

If you studied the data from the two API endpoints that we're using, you would
discover that it's *possible* - by writing some clever code - to get *all* the
info our app needs with just *one* HTTP request.

What I'm saying is: our page is making one more HTTP request than it *truly*
needs to. If you think about it... that's kind of a performance "bug": we're
making 2 HTTP requests and we only need 1.

In an ideal world, when we find a bug, the process for fixing it looks like this.
First, write a test for the *expected* behavior. Second, run that test and watch
it fail. And third, fix the bug and make sure the test passes.

Whelp, when it comes to a *performance* bug... we can do the *exact* same thing!
We can write a functional test that *asserts* that this endpoint only makes *one*
HTTP request. It's... pretty awesome.

## Running the Functional Test

Find your editor and open `tests/Сontroller/MainControllerTest.php`. I already
set up a functional test that makes a request to `/api/github-organization`
and checks some basic data on the response. Let's makes sure this passes. Run
PHPUnit and point it directly at this class:

```terminal
php bin/phpunit tests/Controller/MainControlerTest.php
```

The first time you run this script, it will probably download PHPUnit in the
background. When it finishes... go tests go! All green.

## Adding a Performance Assertion

Here's the idea: in addition to asserting that this response contains JSON
with an `organization` key, I *also* want to assert that it only made one HTTP
request. To do that, first add a trait from the SDK: `use TestCaseTrait`. Next,
in the method, add `$blackfireConfig = new Configuration()` - the one from
`Blackfire\Profile`: the *same* `Configuration` class we used earlier when we
gave our custom-created profile a title. This time call `assert()` and pass it
a *very* special string: `metrics.http.requests.count == 1`.

I'll show you where that came from soon. Finally, below this, call
`$this->assertBlackfire()` and pass this `$blackfireConfig` and a callback function.

So... this confused me at first. When we call `$this->assertBlackfire()` it will
execute this callback. Inside, we will do whatever work we want - like making
the request. Finally, when the callback finishes, Blackfire will execute
this assertion against the code that we ran.

To get this to work, we need to `use ($client)`.

If this doesn't make sense yet... don't worry: we'll dive a bit deeper soon.
But right now... try it! Run the test again:

```terminal-silent
php bin/phpunit tests/Controller/MainControlerTest.php
```

And... it fails! Woo! Failed that `metrics.http.requests.count == 1`!

## Performance Tests Create Real Profiles

Behind the scenes, the Blackfire SDK created a *real* Blackfire profile for the
request! You can even copy the profile URL and go check it out! This takes us to
an "assertions" tab. We're making 2 requests instead of the expected one. We'll
talk a lot more about assertions soon.

Ok, but how did this *really* work? It's beautifully simple. When you run the test,
it *does* make a real Blackfire profile in the background. However, if you go to
your Blackfire homepage, you won't see it.

Why? Hold Command or Ctrl and click the `assertBlackfire()` method. I love it:
this method uses the SDK - *just* like we did! - to create a *real* profile. When
it does that, it *also* adds a `skip_timeline` option, which simply tells Blackfire
to hide this from our profile page... so it doesn't get cluttered up with all
these test profiles. You can *totally* override that if you wanted... via the
`Configuration` object.

In reality, the Blackfire PHPUnit integration is doing the *exact* same thing
that we just finished doing in our code: manually creating a new profile. This
is *really* nothing new... and I *love* that!

Except... for this metrics thing. Where did that string come from? And what else
can we do here? Let's dive into metrics next.
