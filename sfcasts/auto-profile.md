# SDK: Automatically Create a Profile

Imagine you have a performance "problem" on production. No worries! Except...
the issue is only caused in some edge-case situation... and you're having a
hard time repeating the *exact* condition... which means that you can't create
a meaningful Blackfire profile by using the browser extension.

For example, imagine we want to profile the AJAX request that loads the GitHub
repository info... but we think that the performance problem only happens for
certain *types* of users - maybe users that have *many* comments. I'm just making
this up.

To do that, *instead* of triggering a new profile by clicking the browser
extension button - which maybe is hard because we can't seem to replicate the
correct situation - let's trigger a new profile automatically from *inside*
our code. We can do this using the PHP SDK.

Spin over, go back to `MainController` and scroll down to
`loadSightingsPartial()`... actually to the `gitHubOrganizationInfo()` method:

[[[ code('3a2d9ed726') ]]]

This is the controller that returns the content on the right side of the page.

Start by creating a fake variable `$shouldProfile = true`:

[[[ code('44e2fee779') ]]]

In a real app, you would replace this with logic to determine whether or not
this is one of those requests that you think might have a performance problem:
maybe you check to see if the user has *many* comments or something.

## Creating & Starting the Profile

Then, if `$shouldProfile`, it means that we *want* Blackfire to profile this request.
To do that, say `$blackfire = new Client()` - the one from `Blackfire`. This
is an object that helps communicate with the Blackfire servers. Next, *create*
a probe - basically create a new "profile" - with
`$probe = $blackfire->createProbe()`:

[[[ code('83848bc699') ]]]

Earlier, when we used `BlackfireProbe::getMainInstance()`, we were, kind of *asking*
for a "probe" if there *was* a profile happening. But this time, we're *creating*
a probe: creating a new profile and telling it to start "instrumenting" - collecting
data - right now.

In fact, the second argument to `createProbe()` is `$enabled=true`: whether or not
we want the probe to *immediately* start instrumentation or if we will enable
it later with `$probe->enable()`.

Now, *because* this will *create* a new profile, you need to make sure you do
this only *rarely* on production. Why? Because creating profiles is heavy and this
slow request will be *felt* by whichever user triggered it. So, choose your logic
for `$shouldProfile` carefully.

Anyways, let's try it! Move over and refresh your list of Blackfire profiles.
The most recent one is the "Only instrumenting some code" profile. Now refresh the
homepage. This triggers the AJAX call... but notice it's slower. And when we
refresh Blackfire... boom! We have a brand new profile! Open that up and...
let's give it a name: `[Recording] First automatic profile`:
http://bit.ly/f-bf-1st-auto-profile. I'm so proud.

## This only Profiles the Controller

You can now create *new* profiles from your code... *whenever* you want to. But...
there's a small problem: this only profiled a *tiny* part of our code. And,
that makes sense: when our PHP code started executing, the PHP extension didn't
yet know that we wanted to profile this request. And so, it couldn't start collecting
data until we told it to, which happened in the controller. To make matters *worse*,
as *soon* as PHP garbage collected the `$probe` variable... which happened once
the variable isn't used anymore... so at the end of the controller, internally,
the probe called `close()` on itself. That means that we just collected data
on *nothing* more than the code in our controller.

How can we fix that? By starting the probe *super* early and closing it manually
as late as we can. Let's do that next.
