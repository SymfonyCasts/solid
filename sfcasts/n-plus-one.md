# N Plus One

Coming soon...

At this point, I'm pretty happy with, uh, how our, uh, show pages is looking and
rendering. So let's profile something different. Let's go to the homepage and kind of
see how things look there. So I'm going to go back to just local host call a
thousand, walked into our homepage. You can see here, it's a list of all of the
sightings. Uh, we'll talk about this over here. This is just a, a, a list of Symfony
casts repository as we're pulling from GitHub. So let's refresh and profile. See what
happens. Give this a name, original homepage, and I bet 165 milliseconds. Let's view
the call graph.

All right, so this actually is very familiar. We have this classic biggest thing is
the `UnitOfWork::createEntity()`. Uh, we saw that on the show page and what it meant is
ultimately meant that we were querying for too many objects and doctrine and
hydrating, creating too many doctrine objects. Um, so as always, the question is why
can we, can we optimize this? So let's follow the hot path over here. Just like
before we rented their `MainController::homepage()`, we render a template. So the promise
actually once again coming from the template and it's coming from this
little `_sightings.html.twig` templates, and then `twig_length_filter`. Hm. And then find down here
it starts going to something called `loadOneToManyCollection`. So let's do a
little digging here in that sightings, that age, two minutes wig. So in 
`templates/main/_sightings.html.twig`

We also saw that it was referencing something called the `twig_length_filter`
. I searched this file for link. Ah, here we find something. So 
`sighting.comments|length` So if you look at the site, one of the things it does is it lists
the number of comments there are next to every single um, article. Now what the
length filter does in twig is it simply just counts. Whatever. This is over here. Now
for familiar with doctrine, this is done via relationship. So many call 
`sighting.comments` What that does is it actually queries for all of the comments for this
particular Bigfoot sighting. I'm actually going to open up `src/Entity/BigFootSighting.php`
 And you clicked down here. We're actually accessing this property here,
which is a `OneToMany` relationship over to `Comment`. So this is a long way of saying
[inaudible]. This is querying. We're creating for every single comment for every
single article just to get its count. This is the classic N plus one problem. We have
one query to load all the articles and then for every single row we have another
query to count the related, uh, uh, comments. And you can see this over in our
profile here. If you look at the SQL queries here, we have only one query. We have
one query from big book citing. This queer here is actually to page a nation. Uh,
then 25 queries from the comments table.

Okay, so we identified the problem. We are doing something inefficient. A, what's a
good solution for this? We'll end doctrine. One of the things that you can do is you
can actually tell it, Hey, if I reference a relationship, like I say, `sighting.comments` 
but the only way that I use it is by counting it via `|length` or
accounting and PHP. Then instead of querying for all the comments, Rose, just do a
quick query for the individual account. The way you do this is in big exciting above
comments. We can say something called `fetch="EXTRA_LAZY"`.

Alright, so let's go see if this helps. So I'll go over here, refresh this page, open
up Blackfire hit profile and give it a name while it's working and then view that
call graph and I'll close the one a second ago. Okay, was that better? Um, we'll
create entity is still a problem here. Let's just go do the comparison. So let's do
from, from this to this and now before we try that, don't forget to move over and run
`cache:clear` and `cache:warmup`. 

```terminal-silent
php bin/console cache:clear
```

```terminal-silent
php bin/console cache:warmup
```

So those changes actually take effect since we're
still on the production mode, it'll spin over here, refresh that page. And now let's
profile, I'll give us a name here or adding on page, extra lazy. Perfect. I'll close
the other one. Have you call graph and, okay. Is this better? Okay. We, the create
entity is not as much of a problem anymore.

Um, let's just go compare to be sure. So that's the best way to do it. Let's go from
the original homepage to our most recent one. And yes, you can see this is a huge win
in every single category. So was this a good change? Absolutely. This was an awesome
change. Um, but you'll notice how, you know, even though it made for faster SQL
queries, it still is the same number of requests. It just kind of got rid of these
big requests that kind of got all the data and replace them with a nice count query.
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