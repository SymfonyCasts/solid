# The Blackfire CLI Tool for AJAX Requests

We know that the probe - that's the Blackfire PHP extension - doesn't
run on every single request: it only runs when it detects that our browser extension
is *telling* it to run.

There's actually a *second* way that you can tell the probe to do its work. It's
with a *super* handy command-line tool.

## Installing the Blackfire CLI Tool

Go back to the Blackfire site, click on their docs... and once again find the
[installation page](https://blackfire.io/docs/up-and-running/installation).
When we went through this earlier, we purposely skipped one step: installing
the Blackfire CLI tool. Actually, Blackfire recently updated this page... and I
like the newer version a lot better. In both versions of the docs - the new one
and the old one you see here - if you followed the commands to install the "agent"
then you've already *also* installed the CLI tool. Nice!

To make sure, find your terminal and try running:

```terminal
blackfire version
```

## Blackfire CLI Confiug: Client ID & Token

Got it! Before using this, we *do* need to add a little bit of configuration
by running a `blackfire config` command. On the old version of the docs, I'll
copy the "client ID": I'll need that in a second. On the newer version of the
docs, you'll be able to copy a `blackfire config` command that already includes
the client id and client token. For me, I'll run

```terminal
blackfire config
```

If your version of the command has the `--client-id` and `--client-token` options
already, you're done! If not, like me, paste in the Client Id... then also copy and
paste in the token.

The client id and token work... almost like a username and password to your Blackfire
account. When we use the browser extension, we're logged into Blackfire in the
browser. When we click profile, the Blackfire API is able to give the extension
some credentials that it passes to the probe to prove that we're allowed to profile
this page.

When you use the Blackfire command line tool to profile something... the
client id and client token are used to talk to the Blackfire API and get those
*same* credentials that it then passes to the probe to prove we're authorized to
profile. They basically identify & prove which *user* we are on Blackfire.

## Profiling AJAX Requests

The Blackfire CLI tool has two superpowers. The first is that you can run
`blackfire curl` and then pass a URL to *any* page on your site that you want
to create a profile for. Now... that *might* seem *totally* worthless. After all...
if we want to profile a page... isn't it easier just to *go* to that page in
our browser and use the extension to profile it?

Yep! Unless... you *can't* easily "go" to that page - like if you want to profile
an AJAX request or an API endpoint. Check this out: I'll open up the dev tools, 
go to the "Network" section and refresh. Notice I'm already filtered to XHR 
requests - so the `/api/github-organization` AJAX request pops up. Want to easily 
profile *just* that request? Right click on it and select "Copy as cURL".

Now head *back* to your terminal and paste. Cool, right? It creates a *full*
curl command that you can use to make that same request... *including* any session
cookies, which means this request will be authenticated as the same user you're
logged in as in the browser. We can use this with Blackfire: say `blackfire`
then paste!

Try it! It's profiling and using the same process as the browser: making 10 requests
and profiling each one. This is my favorite way to profile AJAX requests. When
it finishes, it gives us the URL to the call graph and some basic stats below.
Go open that profile: http://bit.ly/sf-bf-curl!

It works! Use that to easily profile *any* AJAX requests you want to.

So what is the *second* superpower of the CLI tool? It's actually its *main*
superpower: the ability to profile command-line scripts. Let's do that next.
