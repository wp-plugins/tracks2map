=== Tracks2Map ===
Tags: gpx, map, track, tracks, gpxviewer, gps, googlemap, maps, google maps, gps, gpx, biking, cycling, google, gps file, gps route, gps track, gpx file, hiking
Contributors: dimizu
Donate link: http://www.diewanderer.it/tracks2map/
Requires at least: 3.0
Tested up to: 3.3.2
Stable tag: 1.0

Stellt alle in deinem Blog genutzen GPX Dateien übersichtlich auf einer Google Map Karte dar. 


== Installation ==
DE:
- Im Wordpress Ordern wp-content/uploads muss ein Unterordner mit dem Namen cachegpx erstellt werden. Dieser Ordner muss vom Plugin beschreibbar sein. Am besten die Attribute 777 setzten.
- Nach der Aktivierung des Plugins, kann in einer belieben Seite oder einem beliebigen Artikel der Shortcode [tracks2map] eingefügt werden, sodass genau dort die Google Map angezeigt wird.
- Dabei können folgende Parameter genutzt werden: [tracks2map width=600 height=650 zoom=8]. Die Standardvorgaben sind 600, 650 unnd 8.
- Über die Optionen Seite des Plugins (Track2Map) kann Folgendes eingestellt werden:
1. Die Toleranz, wie stark die Tracks reduziert werden
2. Der Post Query String
3. Die Zuordnung der Icons im icons Ordner mit Wordpress Kategorien. Dabei muss für jedes Icon die tag_ID einer Wordpress Kategorie angegeben werden. Wenn das Plugin keine entsprechende Zuordnung findet, dann wird das Icon 0.png verwendet. 

EN:
- Create in the Wordpress uploads folder wp-content/uploads a new folder with the name: cachegpx. This folder must be writable. 
- After activating the plugin, add the shortcode [tracks2map] wherever you want the map to show.
- Some options can be defined within the shortcode attributes, ex: [tracks2map width=600 height=650 zoom=8] , if not defined: defaults to those values (600,650,8).
- Over the options page of the plugin (Track2Map) can set the following:
1. The tolerance for reduce the tracks
2. The Post QueryString
3. The assignment of the icons in the icons folder to WordPress categories. It must be specified for each icon the tag_id of the WordPress category. If the plugin finds no appropriate mapping, then is used the icon 0.png.




== Description ==
DE:
= Technisches =
Das Plugin sucht bei Aktivierung alle GPX Dateien, die mit einem Artikel verknüpft sind, speichert sie im Ordner "wp-content/uploads/cachegpx",  reduziert die Punkte der Tracks und speichert auch die reduzierten GPX Dateien im JSON Format im Ordner "wp-content/uploads/cachegpx". Dieser Prozess kann auch manuell über die Optionsseite des Plugins mit Update Cache (sucht nach neuen verknüpften GPX Dateien) bzw. Rebuild Cache (löscht zuerst Cache und sucht dann nach verknüpften GPS Dateien) ausgeführt werden. Zusätzlich wird dieser Prozess beim Publizieren eines Post automatisch ausgeführt, sodass er nicht bei jedem Post manuell gemacht werden muss. Eine manuelle Ausführung ist also nur selten, z.B. wenn GPX Dateien gelöscht werden oder der Cache aus einem anderen Grund manuell aktualisiert oder neu erstellt werden soll, vonnöten.

Beispielseite: http://www.diewanderer.it/wanderungen-suedtirol-karte/

= Technical Notes: =
- The plugin does its 'search and cache' process as soon as it is activated, and you can later activate the process manually through the plugin options page.
- The plugin hooks into the publishing process of posts, so it caches new attachement additions.
- The plugin depends on WP workflow, not file searching, providing a more flexible way to control what shows up and what not, plus minimizes the I/O impact of searching files each time WP loads.
- The plugin caches each GPX file into two JSON-formatted files, one with full points ( in case you need it later for anything ), and another for the reduced points, which is used to display the map. I choose JSON to directly embed the files as Javascript objects in the page without additional processing.

Example page: http://www.diewanderer.it/wanderungen-suedtirol-karte/

== Screenshots ==
DE:
Ergebnis

EN:
result
