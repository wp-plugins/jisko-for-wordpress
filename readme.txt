=== Jisko for Wordpress ===
Tags: jisko
Requires at least: 2.1
Tested up to: 2.5
Stable tag: trunk
Donate link: http://rick.jinlabs.com/donate/

Jisko for WordPress displays yours latest jisko notes in your WordPress blog.

== Description ==

Jisko for WordPress displays yours latest jisko notes in your WordPress blog.

**Features**

    *  Simply
    *  Customizable
    *  Widget support
    *  No options page (yes, its a feature)
    *  Uses Wordpress resources (no extra files needed)
    *  Detects URLs, e-mail address and @username replies

**Usage**

If you use WordPress widgets, just drag the widget into your sidebar and configure. If widgets are not your thing, use the following code to display your public Jisko messages:

`<?php jisko_messages("username"); ?>`

For more info (options, customization, etc.) visit [the plugin homepage](http://rick.jinlabs.com/code/jisko "Jisko for Wordpress").

**Customization**

The plug in provides the following CSS classes:

    * ul.jisko: the main ul (if list is activated)
    * li.jisko-item: the ul items (if list is activated)
    * p.jisko-message: each one of the paragraphs (if msgs > 1)
    * .jisko-timestamp: the timestamp span class
    * a.jisko-link: the jisko note link class
    * a.jisko-user: the @username reply link class

== Installation ==

Drop jisko-for-Wordpress folder (or even jisko.php) into /wp-content/plugins/ and activate the plug in the Wordpress admin area.

== Credits ==

[Ronald Heft](http://cavemonkey50.com/) - The plugin is highly based in his Pownce for Wordpress, so the major part of the credits goes to him.
[Michael Feichtinger](http://bohuco.net/blog) - For the multi-widget feature.

== Contact ==

Suggestion, fixes, rants, congratulations, gifts et al to rick[at]jinlabs.com