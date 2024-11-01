#WordPre.cio.us
* Author: Chris Heisel
* E-mail: del2wp@heisel.org
* Status: Beta
* Version: 1.0
* Dependencies: [MagpieRSS](http://magpierss.sourceforge.net/)

##INSTALL
1. Untar/gzip the file in a Web accessible place
2. Rename del2wp.config.php.example to del2wp.config.php and edit it:
    1. Fill in the URL of the del.icio.us RSS feed you'd like to use. You could use your main feed, or the feed of one your tags. This allows you more control over what bookmarks are imported.
    2. Fill in the ID number of the WordPress category you'd like your bookmarks filed to.
    3. Fill in the ID number of the author you'd like the bookmarks attributed to.
	4. Unless you live in GMT, change the offset from False to your offet from GMT.
    5. You may change the comment and ping status to 'open' if you'd like either of those available.
    6. Put in the path, relative to the location of the script, where wp-config.php lives.
    7. Put in the path, relative to the location of the script, where Magpie's rss_fetch.inc lives.
    8. Save the file.
3. Hit http://www.yourdomain.com/path-to-script/del2wp.php
   The script will output the title of any bookmarks added to your blog. (It's silent on updates and when no updates or inserts are made.)
4. To make your imports automatic, add something along the lines of this to your crontab:
0 */2 * * *    curl -s http://www.yourdomain.com/path-to-script/del2wp.php

You'll probably want to play with your WP theme to give your blogmarks a special or different presentation.

##LICENSE
This software is licensed under the GPL, available at:
http://www.gnu.org/licenses/gpl.txt

The code is made available without any warranty, guarantee, uptime promise, or anything of the sort. You munge your data and didn't back up, please don't come calling.