# Timeline: Finding a Hidden Surprise

the way. All right, so I'm going to hit home, um, and scroll back up. And one of the
things I wanna look at is actually the `RequestEvent`. So this is the first event that
Symfony dispatches and anything that listens to this happens before your controller.
You can actually see that in here.

I'm going to zoom in by double clicking on this. Perfect. And you can see a couple of
cool things. You can actually see the `RouterListener`. This is the routing layer
happening. You can see the `Firewall`, this is where authentication takes place. And
you can even dive in here and see what's going on. You can see the entity repository.
It's actually eventually querying for our user object. So really cool stuff. But
check out this almost half of the request of is something called `AgreeToTermsSubscriber`
which is taking 30 milliseconds. Let me show you what that does. That
lives in `src/EventSubscriber/AgreeToTermSubscriber.php`. So the idea is that every
now and then maybe the terms of service update on your site. And so when a user comes
to your site, you need to ask them once again to agree to your terms of service.

I know it's very boring lawyer stuff, but this is something that sometimes this has
to happen. So you can see this goes and gets the authenticated user. If they're not
logged in, it just doesn't, it does nothing. But if they are logged in it then some
of this logic is fake here, but it then basically a renders a twig template with a
form that has the new information about the agree to terms and a checkbox to agree to
them and then goes down here and eventually renders that and actually sets that as a
response. So instead of running the controller, it would show you the agree to terms
box. But the key thing is that we store the last time the user agreed to the terms
and we only showed them if the terms have been updated since they last agreed to
them. So for 99.9% of the requests, this code most of this code and never needs to
run. So the fact that this is taking 30 milliseconds is way too big.

Also see this blue background back here, that is actually the memory footprint.
That's one of the coolest things. So for example, I can actually trace over this. You
see, this is about where the agreed to term subscriber happens. And you can see it's
taken 3.44 megabytes. And as I trace over by the finish, it's taking 4.46 about one
megabyte higher. So that's a lot of memory just for that one little uh, uh, class
that isn't even rendering the, the form on, uh, in my situation. And if you hover
over here, you can see the same thing about one megabyte of extra memory. So this is
cool. This is an invisible layer. The timeline helped us find this. It's not a huge
performance problem, but it's, but it's, but it's not ideal. And the mistake I made
here is very obvious. I'm checking to see if I actually need to render this form, but
I'm doing all the work to run to the form even before, even when I don't need it. So
simple fix in this case. Now that we have this, I'm just going to move that code all
the way to the top a little bit further down after my latest terms date. There we go.
That looks better. So now this should exit earlier and we shouldn't have this
problem. So let's actually try that. I'll refresh the page profile again because one
[Recording] Homepage authenticated fixed subscriber.

I want to finish this. I'll jump this time straight to view the timeline and I'll
double clip again on the uh, request event and you can see this time look, it doesn't
even show up here. You can see the `RouterListener`, the `Firewall` listener, and you
don't even see our `AgreeToTermSubscriber` here. It's not because it's not being
executed, but because Blackfire is pruning data that takes no time, that functions
now so fast that it's not a problem at all. Next, we'll talk about something else.
