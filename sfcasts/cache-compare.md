# Using a Caching Layer & Proving its Worth

Coming soon...

we have to think about whenever we talk, whenever we make something more performance,
we also added like some complexity to our code. So is this one worth it? Prompt me.
Maybe I would probably profile a few more pages to make sure this actually has a
significant thing. But for me, I actually think that this might be over optimizing
too early. Sure, I'm saving 13% in this case, but it's only 10 milliseconds, so I'm
actually going to go over here and revert. This is going to take out my property
caching and return `$this->calculateUserActivityText($user)` take out my thing there.
This just is simpler code overall. So at this point we can either leave this and say,
look, it's not worth optimizing. That might be a proper solution or we might try
something different. So another thing that we can think of is, is actually just true
caching.

We can say, look, this little label here, it's not going to update that often. It's
what if we cache that per user for an hour? Let's see how that changes things. So
over inside of the service at the top, I'm going to auto wire in `CacheInterface`, the
one from Symfony contracts cache. I use a little Alt + Enter trick to "Initialize fields"
to create that property and set it. Now down here, I'll read a little bit of code
that's actually going to put this into Symfonys cache off to cache. Key with
`$key = sprintf('user_activity_text_'.`and then the `$user->getId()`. So a little a
unique cache per user. And the way that you use somebodies cache is you say we're
gonna return `$this->cache->get()` we pass it the `$key`. And if that exists in cache
it'll return it.

Otherwise it's going to call this function. Pass us a `CacheItemInterface`. A object
in our job inside of here is going to be to return the new value so that can be
cacheed. So I'm actually going to say `use` over here cause I need to get the `$user`
variable inside of here. We'll return `$this->calculateUserActivityText()`, pass it
the `$user`. I'm also going to control the TTL here a little bit. I'll say
`$item->expiresAfter(3600)` perfect. So does this help? I mean I'm sure it will, but is
this a more significant help? Let's find out. Let's go over here. Oh, of course, 500
eror. Cause I need to clear my cache

```terminal-silent
php bin/console cache:clear
```

```terminal-silent
php bin/console cache:warmup
```

area now spent over here and refresh. Awesome. Let's profile, I'll give that a name
as normal. Using a real cache view, the call collograph and yeah, this time it looks
way lower. Uh, but I'm not going to trust that. Let's actually go and let's compare
from the original one to this new one. And here you can see these are significant
changes minus 23 requests. Um, and basically there's no downside. Even our, even our
memory went down. Now if you want to, if you want to get even compare from the
property caching, uh, method to this, and you can see it's a, it's basically better
in every single category. So that's what I love about the comparison feature. Um,
it's kind of a, a lean way for performance, just a try things, but actually validate
that they work.
