# bash-glitcher
ChatGPT re-write of glitcherbot.

This code was written interactively with ChatGPT.

To run it, create a file called "urls.txt", and add a URL per line for each URL you wish to visit.

Then run

`glitcher.sh`

The results are stored in a sqlite database called `url_data.db`.

If you wish to view the results in a browser, there's a DDEV config (not chatgpt generated :) ) that you can use.  Simply install ddev and run "ddev start", then open the URL in your browser.
