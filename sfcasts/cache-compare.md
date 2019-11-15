# Using a Caching Layer & Proving its Worth

Whenever we make something more performant, we often *also* make our code
more complex. So, was the property-caching trick we just used worth
it? Maybe... but I'm going to revert it.

Remove the property caching logic and just return
`$this->calculateUserActivityText($user)`. And... we don't need the `$userStatuses`
property anymore.

We *could* stop here and say: this spot is not worth optimizing. Or, we can try a
different solution - like using a *real* caching layer. After all, this label
probably won't change very often... and it's probably not *critical* that the
label changes at the *exact* moment a user adds enough comments to get to the
next level. Caching could be an easy win.

## Adding Caching

Back in `AppExtension`, autowire Symfony's cache object by adding an argument
type-hinted with `CacheInterface` - the one from `Symfony\Contracts\Cache`. I'll
press Alt+Enter and select  "Initialize fields" to make PhpStorm create a new
property with this name and set it in the constructor.

Down in the method, let's first create a cache key that's specific to each user.
How about: `$key = sprintf('user_activity_text_'.`and then `$user->getId()`.
Wow, I *just* realized that my `sprintf` here is totally pointless.

Then, `return $this->cache->get()` and pass this `$key`. If that item exists in
the cache, it will return immediately. *Otherwise*, it will execute this callback
function, pass us a `CacheItemInterface` object and *our* job will be to return
the value that *should* be stored in cache.

Hmm... I need the `$user` object inside here. Add `use` then `$user` to bring it
into scope. Then return `$this->calculateUserActivityText($user)`. I think it's
probably safe to cache this value for one hour: that's long enough, but not *so*
long that we need to worry about adding a system to manually *invalidate* the cache.
Set the expiration with `$item->expiresAfter(3600)`.

So... does this help? Of course it will! More importantly, because we decided we
don't need to worry about adding more complexity to *invalidate* the cache,
it's probably a big win! But let's find out for sure.

Move over and refresh. Boo - 500 error. We're in the `prod` environment... and I
forgot to rebuild the cache:

```terminal
php bin/console cache:clear
```

And:

```terminal
php bin/console cache:warmup
```

## Profiling with Cache

Refresh again. And... profile! I'll name this one: `[Recording] Show page real cache`.
Open up the call graph: http://bit.ly/sf-bf-real-caching.

This time things look *way* better. But let's not trust it: go compare the *original*
profile - before we even did property caching - to this new one:
http://bit.ly/sf-bf-compare-real-cache.

Wow. The changes are significant... and there's basically no downside to
the changes we made. Even our memory went down! You can also compare this to the
property caching method:
http://bit.ly/sf-bf-compare-prop-real-caching. Yea... it's way better

And really, this is *no* surprise: *fully* caching things will... of course be
faster! The *question* is how *much* faster? And if adding caching means that
you *also* need to add a cache invalidation system, is that performance boost
worth it? Since we don't need to worry about invalidation in this case, it was
*totally* worth it.

Next: let's find & solve a classic N+1 query problem. The final solution might
not be what you traditionally expect.
