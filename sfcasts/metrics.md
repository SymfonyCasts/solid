# All about Metrics

Coming soon...

More importantly though, I want to talk about this metric stuff here. So two things I
want to know is by far has a great metric system and inside the metric what you're
using is an expression and what this basically is is a language that's very similar
to JavaScript. It's technically Symfony's expression language if you want to read
more about it. So metrics is probably an object and then we're calling an HTTP
property or request property account property and asserting that = one. The second
thing, probably even more important is how the heck did I know that this was the
exact string I need to use here to do this assertion? This goes back if you look at
our profile to the timeline. Remember when we checked on the timeline, we talked
about how on the left side there are these timeline metrics, which at that point were
just a nice fancy way to color different sections of the timeline.

But really the most useful part of these metrics is that these metrics just give you
access to low level information about what's going on. So for example, there's a
metric called `symfony.events.count` which = seven. And you could use that in a
metric if you for some reason wanted to assert that certain number of methods were
called. So if I were looking to do an assertion about HTD period HTTP requests, I
would go up the search here and to search for HTTP. And I say there's curl requests
and requests. If you're not sure which one to use, you can look at the difference
between them because this is going to show you the real data. So here you can see
that there's a metric called `http.requests` and then there's a bunch of metrics
about it. So I can say `http.requests.count` or `http.requests.memory`
or `.io`, any of these things on here. So that was the key. I started with `metrics.`
We can then reference any of the metrics in the system, which is super powerful.

Okay. So we now have proven that we have a performance bug in our application because
our tests fail so that we can fix this. And the fix for this is not really that
important because Hey, we don't care. This is a tutorial at Blackfire, but B, we have
a, um, if a test now, which is going to prove that once we have the fixed done that
it does work correctly. So the logic is H keep your questions in
`src/GitHub/GitHubApiHelper.php`, and we have two public functions in here that make the two API
requests. And basically we can get all the information from this second HTTP request.
So I'm not gonna fill you in with the exact details, but we're basically going to add
some caching via new  property called `$githubOrganizations`.

And then as we loop over the repositories for a specific organization, we can
actually store information about this organization because it's on that endpoint. So
what I'll do here is I can add a new variable here called `$publicRepoCount`. It's one
of the things that we put on our record for our organization. And then down here I
can say if `$repoData['privet'] === false`. That's one of the keys on
the repo data. Then we'll say `++$publicRepoCount`. So as we're looping over
the repositories, we're just counting how many public repositories there are. And
finally down here we'll say if not is set `$this->githubOrganizations[$organization]`
So this is an organization that we haven't seen yet on this
request we're going to say
`$this->githubOrganizations[$organization] = new GitHubOrganization()`

And this needs two pieces of information. The organization name, which we can
actually probably just use the `$organization` variable, but you can also get this off
of the data with `$data[0]` to get the first repository `['owner']['login']`. And then the
other thing we are recording as the public was the `$publicRepoCount`. So now every
time we call this method will actually capture this organization's information and
store it on this property. So if we call this method first and then this method, we
can actually do less work by looking that up, check this out. We can say if is set
`$this->githubOrganizations[$organization]` then we can just return
that immediately without doing any work. And in fact, if you looked in the
controller, we're already calling things in this order. The controller will just swap
the order of these things so that the repository is the one it's called first and
gets all that cache data set up. Phew. Okay. So let's say that helps. It was a bit of
a complicated fix, but thanks to our test we can know for sure

```terminal-silent
php bin/phpunit tests/Controller/MainControlerTest.php
```

and it does. So that proves that we have reduced the query count from two to one.

Okay. So thanks these metrics, there's a lots of things that we can assert and one of the
things I love about this system is, you know, if you, if even if you do typo one of
these, uh, variables, when you run it,

```terminal-silent
php bin/phpunit tests/Controller/MainControlerTest.php
```

you're going to get a really clear error.
Specifically, you're going to get an error about profiling and you're going to get a
link directly to go look at that profile. And that's going attain, going to contain
the information that you need. So you can see here the following assertions are not
valid `metrics.http.requests.vounts`, property `vount` does not exist. Available ones
are, and it tells you all the available things and even as documentation to more
about the assertion. So just really, really friendly how that was works.

So let's fix our typo. Now, the one downside to the Blackfire tests, they do slow your
test down because it needs to talk to Blackfire and have it create that profile. So
as a best practice, we usually like to isolate our performance tests from our normal
tests. So check this out. I'm going to copy the method name here, make a new one
called `testGetGitHubOrganizationBlackfireHttpRequests()`. And what we'll put
inside of here, I'll copy everything from the previous method, but really all we need
here is just, uh, creating the `$client`, creating `$blackfireConfig`. And then inside of
`assertBlackfire()`, we just make the request. Once this black callback finishes, it
will then run the asserts on it. Now up here in our original method, we can simplify
things a little bit. So I can, I'll copy all my code that's inside the callback and
just replace it with what we had before.

So this is a very straightforward test that actually test the endpoint and this is
testing just the performance now above the Blackfire one we can say `@group blackfire`
that's really powerful because now we can run just the Blackfire tests or
we could run our tests with a `--exclude-group=blackfire` and that would
Jen just our one test, not the other one. You can see just one test, two assertions
on that and while we're here, another nice thing to do is say
`@requires extension blackfire` So if for some reason somebody is running this on a system that
doesn't have Blackfire, it'll automatically Mark those tests as skipped a. My last
thing that I want to tell you, and we're going to talk more about this in the next
video, is that you really need to be careful with these assertions to try not to use
time based assertions.

One of the easiest things to do is create a test and say that the test should take
the request should take less than 500 milliseconds, but there's so much variability
with time. It depends on your machine ends on if the cache was warmed up, that those
tests tend to be very fragile and fail a lot. What you want to be looking for is more
specific things that will not just randomly change over time, like the number of HTTP
requests or the number of database requests or something else. So next, we're
actually going to talk about this metrics, these assertions system a bit more. You
can add these assertions and tests obviously, but you can also globally add these
assertions so that anytime you run a profile from any page, if that page makes too
many HTTP requests or it makes too many database requests, you're going to see a big
metric failure inside that profile.
