# Call Graph Comparison

Coming soon...

There are two different ways to optimize any function. Either you can optimize the
code inside that function or you can try to call the function less times. In our
case, we found that the problem function is `UnitOfWork::createEntity`. This is a
vendor function. This is not something in our code, so it's not something that we can
optimize and honestly it's probably already super optimized anyways, but we could try
to call it less times if we can understand what in our code is actually calling this.
So now we're going to turn over to the call graph on the right. So first I'm actually
gonna click on the little magnifying glass here next to `createEntity` and that is
going to zoom me in to the problem area. You know, zoom out just a little bit here.
Now the first thing to notice is that the call graph is basically just a visual
representation of the information that we could already get. On the left here you can
see that there's two collars here. Here are the two callers down here. Here's all the
things that cause here's all the function that it calls. So information is the same
as the left. It's just a lot easier to read.

now if we zoom way out here, we can see,
so you can see the some things highlighted in red and you can see there's actually
like a path that you follow all the way down to this dark red thing down here. This
is called the critical path. It's Blackfire highlighting to us the biggest problem
that we have inside of our application. And actually I'm going to hit home here. This
will reset the call graph, not to be centered around that call specifically. So we
lose some information about the call itself, but to give us a nice overall summary of
what's going on. So you can very clearly see that we have a critical path here. And a
critical thing to understand is why is that path in our application so slow?

So let's see. Well, let's actually just kind of look up here from the critical path
and figure out where does our code start. So I'm just going to scroll up here a
little bit and okay. Actually, as you can see here, uh, you can see our controller
being rendered. Our Symfony controller, our Symfony controller renders a template.
That's interesting. That means the problem is actually inside the template a, we're
inside block body and then Oh, it calls a tweak extension called `getUserActivityText()`
that calls something else called `CommentHelper::countRecentComments`. And that
is the last function here before it goes into doctrine. So the problem in our code is
around this text here. So let me show you a little bit about what's going on. Let's
look at this `main/sighting_show.html.twig` template. So I'm an open
`templates/main/sighting_show.html.twig`

Now if you look back over on the site itself, one of the things you'll see here is
that all of different comments here have a little label next to them that basically
says how active they are in the site. The way that we get that. If you look over in
the twig template is we loop over the comments and then for each comment we pipe it
into a filter car, `user_activity_text`. If you're not familiar with twig, that's no
problem. Basically this means this user activity text that's implemented inside of
`src/Twig/AppExtension.php`. Whenever we use this user activity text here, it
calls this get user activity texts down here. This counts all of the recent comments
for the user and then via our proprietary algorithm it says which how
active they are on the site. So if we go back to Blackfire, Blackfire was telling us
actually have the last partner code was actually that 
`CommentHelper::countRecentCommentsForUser()` 
this function call right here, so that is over in the
`Service/` directory `countRecentCommentsForUser()`, ah,

so this, if you're familiar with doctrine, you might immediately see the problem. If
you're not, don't worry, I'm going to hold command. Click this, `getComments()` thing.
Um, user has a one to many relationship with comments inside of the database. We can
see this inside of `User`. And basically what that means is that what this function
wants to, once they get all of the recent comments for user, the way it's doing them
is it's actually going in querying for every single comment for this user. And then
looping over those comments to find which ones are, um, uh, within the last three
months just to get the count. It's a massively, um, uh,

heavy function just to get the count. So it's seems obvious now that I'm looking at
it. But the nice thing is that, um, Blackfire just pointed this out, said, Hey, this
function is a problem, so let's fix this and see what happens. So I already inside of
my `src/Repository/` directory, if I open `CommentRepository`, I've already created a
function here that creates a direct SQL query, two count B comments for a user since
a certain date. So instead of loading all of the comments and looping over them,
we'll make a really quick count query to figure out how many counts that users had
since the date. So when `CommentHelper`, uh, this part's a little specific Symfony. I'm
going to public funtion `__construct()` and I'm going to auto wire the `CommentRepository`
 in here. Then I'll create a private `$commentRepository`. Method a property
me say `$this->commentRepository = $commentRepository`. The really important thing here
is, I don't need any of this logic here anymore. I'm just going to say return
`$this->commentRepository->countForUser()`.

And then this, we're going to pass this to `$user` optical [inaudible] for and then the
date that we want to count. So I'm gonna steal this `new \DateRimeImmutable('-3 months')`
months down there and then we can kill the rest of this code. So in essence what
we're doing here is we are calling the `UnitOfWork` function less times. If you look
back inside of here, we were calling this uniform thing 23 times, which eventually
down here meant that ah, many, many things are being called. So we're eliminating
this, uh, but making it faster. Well at least we think this will be faster. This is
the great thing about Blackfire. You just get to experiment and see if it actually is
faster. So now let's go over here and I'm just going to refresh this page. What's
going to do profile? So go over to by fire a profile, I'll give it some name again.
`[Recording] Show page after count query`

And once it finishes with the view, the call graph and let's see here. So this is 270
milliseconds total on the request. The last one was 415 so it's faster, right? We
win. Well it's probably, yeah, I agree. It does look faster. But one of the most
important things with performance is exactly know why is it faster and faster and
every category or did this slow it down in other categories and how, how big is that
impact? So one of my favorite tools at Blackfire is the ability to compare profiles.
So if you'd like this thing here to go back to your profile, you can see here my last
two profiles for the initial and after the comp queries. Now over here I can say, I
can hover and say from too, and this is going to take me to a performance comparison
of those two.

Everything that's faster is going to be in blue. Anything that's slower is going to
be in red. And you can see that absolutely like this is better in every single
category. So a very, very a big win here. It's low and every subcategory it's
technically higher and IO weight, but it was so small before. That's just a rounding
error. It's lower and CPU a down here you can see, uh, in the darkest blue, the
critical path this time is actually the method that gained the most. So you can click
the unit of work here and yeah, it actually went down by almost a hundred
milliseconds. You can see that even the memory plummeted and went down
1.3900000000000001 megabytes. So this is a huge, huge win. And even already, even
though at this big win, you can already see something interesting. Check this out
over here on the queries, the queries are less than before, but we have five more
queries and we had before click view details, you can see this. So we were eliminated
18 queries, we have 23 new ones. So is that a problem? Probably not, because overall
this was a great big change, but this is the kind of thing that you can start to see.
You can see the, um, well they're not a performance enhancement actually helped you
and many performance and backs actually hurt you in other areas. So let's keep going
and keep diving into this further and see what else we configure out from this page.