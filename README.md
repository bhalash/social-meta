# Social Mata
[social-meta.php](social-meta.php) is a self-contained library for a WordPress theme that generates [Facebook](https://developers.facebook.com/docs/sharing/opengraph)-appropriate [Open Graph](http://ogp.me/) tags and [Twitter Card](https://dev.twitter.com/cards/overview) tags appropriate for a personal site. 

I wrote this because there are what seems like a hundred different WordPress Open Graph/Facebook/social/sharing plugins, but none of which did exactly what I wanted. The either had features locked behind paywall, nagging donations or outdated functionality. 

## Usage
Social Meta requires my separate [Article Images](https://github.com/bhalash/article-images) library, to extract the correct image and its dimensions. One you have that loaded (Social Meta will do this itself), all you need to do is add the file to WordPress:

`include('/path/to/social-meta.php')`

That's it! 

## Support
Your mileage will vary; while this library is suitable for my site, it's compatibility with yours is unknowable. Caveat emptor! Pull requests and forks are welcome. If you have a simple support question, email <mark@bhalash.com>. I'm happy to take on paid commissions for more advanced requests, or paid commissions for other work, period. :) 

## Copyright and License
All code is Copyright (c) 2015 [http://www.bhalash.com/](Mark Grealish). All of the code in the project is licensed under the GPL v3 or later, except as may be otherwise noted.

> This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public > License, version 3, as published by the Free Software Foundation.
> 
> This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
> 
> You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA

A copy of the license is included in the root of the plugin’s directory. The file is named LICENSE.