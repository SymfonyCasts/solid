# Fixing N+1 With a Join?

Now is that something we care about? I don't know, maybe. Um, but that's the thing.
Profiling, like you might not need it, you fix everything and you might need to wait
until you get to production before you actually see if it's really a problem.
Especially because fixing these things sometimes has added complexity,

but there is, but there's kind of a proper solution to this N plus one problem. That's
N plus one problem. Uh, when you're querying for all the sightings and then each row
has to query for its own comments to get those, what you're supposed to do is instead
query for all the articles into a join over to the con's table so that you can get
all of the data at once. So let's try that. So let's go over here and the entire
controller behind this is in `Controller/MainController`. And here's the `homepage()`.
If you kind of follow the logic inside of this function, we're down here. You want,
let's not do that.

Let's do that. Okay, I'm gonna move over and uh, actually opened up
`BigfootSightingRepository` and got a `findLatestQueryBuilder()`. This is the function if you did some
digging, that is actually making, creating the query that is returning these results
over here. And you can see it's a pretty simple query. It just queries from the, this
table a orders by `createdAt` , uh, sets a max results. But that's it. It's just a
normal query selecting only from this table. So let's add a `leftJoin()` on
`big_foot_sightin.comments` alias that the `comments`, and then we will say,
`addSelect(comments)`. So that says is it says, I do want to, um, I do want, I want to
S I'm gonna do that. Join, actually want to select that data over there so we
shouldn't need to, but just to be safe, let's clear our cache and warm

```terminal-silent
php bin/console cache:clear
```

```terminal-silent
php bin/console cache:warmup
```

up and we'll go over here, refresh the page. Let's create another black file profile.

We'll give that a quick name. And if you got called RAF and Oh Whoa, this looks
weird, it actually looks worse. Let's compare it to be sure close. The other one. Uh,
let's go from the `EXTRA_LAZY` to the new one. And wow, you can see this is much, much
worse. CPU is way up net iOS web. It's up in every single category, especially
network. The amount of data that went over the network and you forgot the queries.
Look, we do have 25 fewer queries, but we have 7.6 more milliseconds. So the problem
is that, sure we got rid of 25 queries, but we're now selecting all of the common
data just to get the count. So we made, we made less queries, but that query is so
much bigger in returns, so much more data that you can see how much more stuff we're
sending over the network and it's slowing everything else down.

So it's a classic thing. You know, like the normal thing you might think of as a
solution to this N plus one problem might may or not actually be the right solution.
So I'm actually go over and remove that joint stuff over here, which means that we're
going to kind of go back to this extra lazy solution and this profile now may happy
with this. And I'm okay with the 27 day base requests. Maybe it's up to you. You can
go in and see if you want to cache those further. But it's page is pretty fast. So
I'd probably just push this to production and let production tell me if they are
actually actually any real problems with this. A number of queries.
