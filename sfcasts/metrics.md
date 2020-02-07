# All about Metrics

Where did this metrics string come from - this `metrics.http.requests.count`?
There are two things I want to say about this. First, Blackfire stores *tons* of
raw data about your profile in little "categories" called metrics. More on that
soon. And second, inside the `assert()` call, you're using a special
"expression" language that's similar to JavaScript. It's technically Symfony's
ExpressionLanguage if you want to read more. Behind-the-scenes, `metrics`
is probably some object... and we're referencing an `http` property, then
a `requests`... property then a `count` property & then we're comparing that to 1.

## What Metrics are Available

Ok, cool. So... how the heck did I know to use this *exact* string to get the HTTP
call count? This goes back to the Blackfire timeline. On the profile, click the
timeline link.

When we talked about the timeline earlier, we talked about how, on the left side,
there are these "timeline" metrics. At *that* point, these were just a nice way
to add color to different sections of the timeline.

But *now* we understand that there is a *lot* more power behind this info: this
shows us *all* the pieces of data we can use in our tests... and in other places
that we'll talk about soon.

For example, there's a metric called `symfony.events.count` which equals seven.
You could use that in a metric if, for some reason, you wanted to assert that a
certain number of events were dispatched. If I needed to do an assertion about
the number of HTTP requests, I would probably search the metrics for http.
Apparently there are two... and if you looked closer, you'd find that `http.requests`
is *perfect*. Most of these metrics have data about multiple *dimensions*: we
can say `http.requests.count` to get the actual number or `http.requests.memory`
to get how much memory they used.

In the test system, we start with `metrics.` then use *anything* we find here.

## Fixing the Performance Bug

We now have a performance bug in our application that we've *proven* with
a test. And at this point, the actual way we *fix* that bug is not as important:
all we care about is that we can change some code and get this test to pass.

The logic for the API calls lives in `src/GitHub/GitHubApiHelper.php`: it has
two public function and each makes one API request.

How can we make this page only make *1* HTTP request? Well, if you looked closely..
Ah! Too close! Ahh. You'd find that you can get all the information you need
by *only* making this *second* HTTP request. The details aren't important - so let's
just jump in.

Add a new property called `$githubOrganizations` set to an empty array.
As we loop over the repositories for a specific organization, we will *store*
that organization's info. Add a new variable called `$publicRepoCount` set to 0:
the number of public repositories an organization has is one of the pieces of data
we need.

Then, inside the `foreach`: if `$repoData['private'] === false` - that's one of
the keys on `$repoData` - say `++$publicRepoCount`. So, as we're looping over
the repositories, we're counting how many are public.

Finally, at the bottom, if *not* `isset($this->githubOrganizations[$organization])`,
then `$this->githubOrganizations[$organization] = new GitHubOrganization()`. This
needs two arguments. The first is the organization name. We can probably use
the `$organization` argument... or you can use `$data[0]` - to get the first
repository - then `['owner']['login']`. For the second argument, pass
`$publicRepoCount`.

Now, *each* time we call this method, we *capture* the organization's information
and store it on this property. So if we call this method *first* and *then* the
other method... we could *cheat* and return the `GitHubOrganization` object that's
stored on the property. It's property caching!

Check it out: if `isset($this->githubOrganizations[$organization])` then return
that immediately without doing any work.

So... *are* we calling these two methods in the "correct" order to get this to
work? Check out the controller. Nope! Swap these two lines so the *first* call
will set up the caching for the second.

Phew! Let's see if that helps. It was a complicated fix... but thanks
to our test, we will know for *sure* if it worked. Go!

```terminal-silent
php bin/phpunit tests/Controller/MainControlerTest.php
```

They pass! This *proves* that we reduced the HTTP calls from two to one.

## Typos in Metrics

What I *love* about the metrics system is that there are *many* to choose from.
What I *don't* love is that you need to manually look up everything that's available.
*Fortunately*, if you make a typo - the error is great. Change `count` to `vount`
and re-run the test:

```terminal-silent
php bin/phpunit tests/Controller/MainControlerTest.php
```

> An error occurred when profiling the test

And when we follow the profile link... check out that error!

> The following assertions are not valid... Property "vount" does not exist,
> available ones are:

... and it lists all the properties. That's *super* friendly. Fix the typo.

## Organizing Blackfire Assertions into Separate Test Cases

The *one* downside to adding Blackfire assertions in your tests is that they *do*
slow things down because instrumentation happens and we need to wait for Blackfire
to create the profile.

Because of that, as a best practice, we usually like to isolate our performance
tests from our normal tests. Check it out: copy the test method name, paste it
below, and call it `testGetGitHubOrganizationBlackfireHttpRequests()`. And...
copy the contents of the original method and paste here. Now... we only need to
create the `$client`, create `$blackfireConfig` and, inside `assertBlackfire()`,
*just* make the request.

Back in the original method, we can simplify... in fact we can go *all* the way
back to the way it was before: create the client, make the request, assert something.

*Why* is this useful? Because *now* we can *skip* the Blackfire tests if we're
just trying to get something to work. How? Above the performance test, add
`@group blackfire`.

Thanks to that, we can add `--exclude-group=blackfire` to *avoid* the Blackfire
tests:

```terminal-silent
php bin/phpunit tests/Controller/MainControlerTest.php --exclude-group=blackfire
```

Yep! Just one test, two assertions. Another nice detail is to add
`@requires extension blackfire`. Now, if someone is *missing* the Blackfire
extension, instead of the tests exploding, they'll be marked as skipped.

## Don't do Time-Based Assertions

The *last* thing I want to mention about assertions is this: please, please *please*
avoid time-based assertions. They're the *easiest* to create - I know. It's
*super* tempting to want to create an assertion that the request should take
less than 500 milliseconds. If you *do* this, you will hate your tests.

Why? Because there's *way* too much variability in time: the request might run
fast enough on one machine, but *not* fast enough on another. Or your server
*might* just have a bad day... and suddenly your tests are failing. Relying on
time makes your tests fragile.

Next, we're going to talk *more* about metrics and assertions. We know that we
can add assertions to profiles that are created inside our tests.

But we an *also* add *global* assertions: tests that run *any* time you create
a profile for *any* page! If you want to make sure that a specific page - or
*any* page - doesn't make more than, I don't know, 10 database queries, you can
add an "assertion" for that and see a *big* failure if you break the rules.
