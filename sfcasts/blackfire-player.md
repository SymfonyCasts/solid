# Blackfire Player

Pretend for a few minutes that the Blackfire profiler that we've been learning
*so* much about... doesn't exist... at all. Why? Because we're *now* going to talk
about something that has the word "Blackfire" in it... but has absolutely
*nothing* to do with the Blackfire profiler. At least, not yet.

## Hello Blackfire Player

Google for "Blackfire player". The Blackfire Player is an open source library
that makes it *really* easy to write a few lines of code that will then be
executed to *crawl* a site: clicking on links, filling out forms, and doing
things with the result. It's basically a simple language for surfing the web
and a tool that's able to *read* that language and... actually do it!

To install it, copy the `curl` command, find your terminal, and paste. If you're
on Windows, you can just download the `blackfire-player.phar` file from that
URL and put it into your project.

Now go back and copy the other two commands.

```terminal-silent
chmod +x blackfire-player.phar
mv blackfire-player.phar /usr/local/bin/blackfire-player
```

Paste and... that's it! For Windows users, skip this step. Let's see if it works.
Run:

```terminal
blackfire-player
```

Nice!

***TIP
For Windows, run `php blackfire-player.php` from inside your project.
***

So here's the idea: we create a file that contains one or more *scenarios*.
Inside each scenario, we write code that says: go visit this URL, expect a 200
status code, then click on this link, and so on. It can get fancier, but that's
the gist of it.

## Creating our First Scenario & .bkf File

Let's create a our first Blackfire player file at the root of the project, though
it could live anywhere. Call it, how about, `scenario.bkf`. That's *pure* creativity.

At the top, I'll put a `name` - though it's not very important - then
`endpoint` set to our server's URL. So `https://localhost:8000`. You
can override this when you *execute* this file by passing a `--endpoint` option.

Notice that this *kind* of looks like YAML, but it's *not*: there is no `:`
between the key and value. This is a custom Blackfire player language, which
is friendly, but takes some getting used to.

At the bottom, add our first scenario - call it "Basic Visit". Inside, let's do
two things: first, `visit url("/")`. We can *also* give this page a name - it
helps debugging.

And second... once we're on the homepage, let's "click" this "Log In" link. Do
that with `click link()` and then use that exact text: `Log In`. Give this page a
name too.

## Executing blackfire-player

That's enough to start. We *should* be able to use the `blackfire-player` tool
to... actually *do* this stuff!. Let's try it:

```terminal
blackfire-player run scenario.bkf
```

And... it fails: curl error 60. If you Google'd this, you find out that this is
an SSL problem - it's caused because or Symfony dev server uses a, sort of,
self-signed certificate that blackfire-player doesn't like. The simplest solution,
which is ok since we're just testing locally - is to pass `--ssl-no-verify`

```terminal-silent
blackfire-player run scenario.bkf --ssl-no-verify
```

And... hey! It worked! Scenarios 1, steps 2. It *truly* made a request to
the homepage then clicked on that link! By the way, the requests aren't using a
*real* browser. And so, any JavaScript code on your page *won't* run.
That *might* change in the future - but I'm not sure.

Anyways, to see more fun output, use the `-v` flag:

```terminal-silent
blackfire-player run scenario.bkf --ssl-no-verify -v
```

Very cool! Blackfire player *is* now making two real HTTP requests to our site...
but it's not *doing* anything with that data. Next, let's add some *tests* to
our scenario - like expecting that the status code is 200 and checking for elements
in the DOM.
