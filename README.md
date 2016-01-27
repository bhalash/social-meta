# Social Meta
social-meta is a self-contained library for WordPress that generates [Twitter Card][1] [Open Graph][2] header meta tag information.

## Usage
To initialize:

    include('/path/to/social-meta/social-meta.php');

    $my_social_meta_instance = new Social_Meta([
        'twitter' => '@username',
        'facebook' => 'username',
        'fallback_image' => [
            'url' => get_template_directory_uri() . '/path/to/image.jpg',
            'path' => get_template_directory() . '/path/to/image.jpg'
        ]
    ]);

That's it!

## article-images
Social Meta requires my [Article Images](https://github.com/bhalash/article-images) library to provide the required image for an article or page.

## Support
This library is suitable for my site, and its compatibility with yours is unknowable. Pull requests and forks are welcome. If you have a simple support question, email <mark@bhalash.com>.

## Copyright and License
All code is Copyright (c) 2015 [http://www.bhalash.com/][2]. All of the code in the project is licensed under the GPL v3 or later, except as may be otherwise noted.

> This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public > License, version 3, as published by the Free Software Foundation.
>
> This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
>
> You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA

A copy of the license is included in the root of the plugin’s directory. The file is named LICENSE.

[1]: https://dev.twitter.com/cards/overview
[2]: http://ogp.me
[3]: https://github.com/bhalash/article-images
[4]: https://www.bhalash.com
