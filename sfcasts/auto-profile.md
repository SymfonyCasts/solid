# SDK: Automatically Create a Profile

Imagine you have a performance "problem" on production that you want to profile.
No problem! Except... the problem is only caused in some edge-case situation...
and you're having a problem repeating the *exact* right conditions... and so
it's hard to create a profile with the browser extension.

For example, imagine we want to profile the AJAX request that loads the GitHub
repository info... but we think that the performance problem only happens for
certain *types* of users - maybe users that have *many* comments - I'm just making
this up.

To do this, *instead* of triggering a new profile by clicking the browser
extension button - which maybe is hard because we can't seem to replicate the
correct situation - we want to trigger a new profile automatically from *inside*
our code. We can do this using the PHP SDK.

Spin over, go back to `MainController` and scroll down to the
`loadSightingsPartial()`... actually to the `gitHubOrganizationInfo()` method.
This is the controller that returns the content on the right side of the page.

Start by creating a fake variable `$shouldProfile = true`. In a real app, you would
replace this with some logic to determine whether or not this is one of those
requests that you want to profile - maybe you check to see if the user has *many*
comments or something else.

## Creating & Starting the Profile

Then, if `$shouldProfile`, it means that we *want* Blackfire to profile this request.
To do that, say `$blackfire = new Client()` - the one from `\Blackfire`. This
is an object that helps communicate with the Blackfire servers. Now, *create*
a probe - basically create a new "profile" - with
`$probe = $blackfire->createProbe()`.

Earlier, when we used `BlackfireProbe::getMainInstance()`, we were, kind of *asking*
for a "probe" if there *was* a profile happening. But this time, we're *creating*
a probe: creating a new profile and telling it to start "instrumenting" - collecting
data - right now.

In fact, the second argument to `createProbe()` `$enabled=true`: whether or not
we want the probe to *immediately* start instrumentation or if we will enable
it later.

Now, *because* this will *create* a new profile, you need to make sure you do
this *sparingly* on production. Why? Because creating profiles is heavy and this
slower request will be felt by whatever user triggered it. So, choose your logic
for `$shouldProfile` carefully.

Anyways, let's try it! Move over and refresh your list of Blackfire profiles.
The most recent one is the "Only instrumenting some code" profile. Refresh the
homepage. This triggers the AJAX call... but notice it's slower. And when we
refresh Blackfire... boom! We have a brand new profile! Open that up and...
let's give it a name: `[Recording] First automatic profile`:
http://bit.ly/f-bf-1st-auto-profile.

## This only Profiles the Controller

You can now create *new* profiles from your code... *whenever* you want to. But...
there's a small problem: this only profiles a *small* part of your code. And,
that makes sense: when our PHP code starts executing, the PHP extension doesn't
yet know that we want to profile this request. And so, it can's start collecting
data until we tell it to, which happens in the controller. To make matters *worse*,
as *soon* as PHP garbage collects the `$probe` variables... which happens once
the variable isn't used anymore... which happens at the end of the controller,
internally, the probe calls `close()` on itself. That means that we just collected
data on *nothing* more than the code in our controller.

How can we fix that? By starting the probe *super* early and closing it manually
as late as we can. Let's do that next.
