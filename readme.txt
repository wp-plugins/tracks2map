=== Tracks2Map ===

Tags: gpx, map, track, tracks, gpxviewer, gps, googlemap, maps, google maps, gps, gpx, biking, cycling, google, gps file, gps route, gps track, gpx file, hiking
Contributors: dimizu, giorgio25b
Donate link: http://#
Requires at least: 3.0
Tested up to: 4.2.2
Stable tag: 1.2

== Description ==

Tracks2Map outputs a Google Map in a blog page, post or CPT [via shortcode], all the GPX files uploded as Attachments in the WP Media Library.

Notes: 
- In the future version it might allow kml, gbd and other GPS file formats.
- The bubble functionality needs quite a bit of coding for working properly.
- If the gpx-xml structure is not perfect it generates errors that are easy to understand.

=== !!! Important !!! ===
=== Create in the Wordpress Uploads folder wp-content/uploads a new folder with the name: cachegpx. This folder must be writable. ===


- After activating the plugin, add the shortcode [tracks2map] wherever you want the map to show.
- Some options can be defined within the shortcode attributes, ex: [tracks2map width=600 height=650 zoom=8] , if not defined: defaults to those values (600,650,8).
- Over the options page of the plugin (Track2Map) can set the following:
1. The tolerance for reduce the tracks.
2. The Post Query String; this parameter still uses the old post_type query instead of the new WP_Query.
3. The assignment of the icons in the icons folder to WordPress categories. It must be specified for each icon the tag_id of the WordPress category. If the plugin finds no appropriate mapping, then is used the icon 0.png.


= Technical Notes: =

- The plugin does its 'search and cache' process as soon as it is activated; you can later activate the process manually through the plugin options page.
- The plugin hooks into the publishing process of posts, so it caches new attachement additions.
- The plugin depends on WP workflow, not file searching, providing a more flexible way to control what shows up and what doesn't, plus minimizes the I/O impact of searching files each time WP loads.
- The plugin caches each GPX file into two JSON-formatted files, one with full points ( in case you need it later for anything ), and another for the reduced points, which is used to display the map. I choose JSON to directly embed the files as Javascript objects in the page without additional processing.

Example page: http://www.youtrail.org/map

== Screenshots ==

Not available yet