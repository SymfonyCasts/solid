# Profile All Requests (Including Ajax)

When you open the browser extension to create a profile, it has a few options that
we've been ignoring so far.. and some of these are only available for certain
Blackfire plans.

## Debugging Mode

***TIP
Debugging mode is available via the Debugging add-on.
***

For example, "debugging mode" will tell Blackfire to *disable* pruning - that's
when it removes data for functions that don't take a lot of resources -  and also
disable anonymization - that's when it hides exact details used in SQL queries
and HTTP requests. Debugging mode is useful if something weird is going on and
you want to *fully* see what's happening inside a request.

## Distributed Profiling

***TIP
Distributed profiling is available to Premium plan users or higher.
***

Another superpower of Blackfire is called distributed profiling... which you either
won't care about... or it's the most awesome thing ever. Imagine you have a
micro-service architecture where when you refresh the page, it might make HTTP
requests to other microservices. If you have Blackfire installed on all of your
microservices, Blackfire will automatically create profiles for *every* request
made to every app. The final result is a profile with sub-profiles that show you
how the entire infrastructure is working together. It's... pretty incredible.

But, if you want to disable it and *only* profile this main app, you can do that
with this option.

## Disabling Aggregation

The last option is to "disable aggregation". That's a fancy way of telling Blackfire
that you want to make & profile just *one* request, instead of making 10 requests
and averaging the results.

## Profiling All Requests

But what I *really* want to look at is the "Profile all requests" link. Hit
"Record"... then refresh. Woh! Cool! It already made 2 requests! And if I scroll
down a little bit... there's a third request! Let's stop right here.

That jumps is to our dashboard. These *last* three profiles were just created:
one for the homepage and two others - these are both AJAX calls! Surprise! Without
even thinking about it, we discovered a few extra requests.

This first one - `/api/github-organization` - is what loads this GitHub repository
information on the right. This makes an API call for the most popular repositories
under the Symfonycasts organization... which is kind of silly... but it was a *great*
way to show how network requests look in Blackfire. We'll see that in a minute.

This other request - for `/_sightings` is an AJAX call that powers the forever
scroll on this page.

Basically... I like using "profile all requests" in 3 situations. One, to get
an idea of what's all happening on a page. Two, to profile AJAX requests... though
I'll show you another way to do that soon. And three, to profile form submits: fill
out the form, hit "Record", then submit.

## Checking out the Network Requests

But let's check out over here the, uh,
kind of get hub organization. Uh, one, as I mentioned, this goes and makes an Ajax
call, uh, an API call to the get hub API to load repository information about the
Symfony. A repositor on there. And this one is almost comical. You can see 438
milliseconds, uh, 82% of it is `curl_multi_select()`. In other words, 82% of it is the
actual time it's taking to make the API call pretty obvious. Um, now kind of fun
thing is if you look at the CPU time, which is only 74 milliseconds of that
`curl_multi_exec()` is still the biggest offender, but you can see it's a lot less obvious
what the critical path is here.

Whereas if you click on IO /wait, because this includes network time, it's comically
obvious. Now, one of the interesting things here is this is not the full call tree
like right? You can see it goes right from Handel, the beginning of the framework
being done all the way into the controller. Normally you see more layers than that.
And if you switch to the CPU, you can see all kinds of extra layers. This is
something that Blackfire does, which is really nice, and it's called pruning. It's
gonna prune the, it removes the node information that's less important. So the more
obvious your critical path is, the more stuff is going to be able to remove because
it's just noise, it's garbage.

So in this case, it's incredibly easy to see what the path is. Also, you can see the
a network calls themselves up here. So in here you can see actually there's two
network calls here and there's one, uh, that returns a 1.5 kilobytes and another one
that recurrence returns five, uh, kilobytes behind the scenes. You can say the
network time, the time here is not actually honest. It's because of the asynchronous
nature of the, uh, request I'm making. Um, but you can see that there's two API
calls. So how do we fix this? Do we cache? Do we somehow try to make only one AP call
API call both. We're actually gonna revisit and fix this problem later. For now, I
wanted you to be aware of the profile all as a way to see what's going on your app
and et cetera, et cetera, snacks. So we're actually gonna use the Blackfire command
line tool, which is the second and my preferred way to profile Ajax requests as well
as profile command line applications.