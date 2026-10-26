# dashcam-route

A proof of concept (2017): reads the GPS subtitles (SRT) that a dashcam writes next to its videos and turns them into a
KML route for Google Maps or Google Earth, with a point per reading (time and speed) and the route as a line. PHP,
Composer. A historical archive, kept read-only.

Old scripts: they ran on the systems of their time and are not maintained.

## How it works

`web/test.php` reads `var/srt/test.srt` with `Subtitles::parseSrt()` (`src/WebServCo/Multimedia/`), takes the date, the
speed and the position from each subtitle, and writes `var/kml/route_output.kml` from `var/kml/route_template.kml`. The
paths are set in `src/WebServCo/Settings/Config.php`. `extra/gmaps/` holds two samples that I looked at while writing it:
a public bus-line route exported from Google My Maps and a LineString sample from Stack Overflow.

## History

| Date | Commit | |
|---|---|---|
| 2017-11-15 | 7cacccd | the route samples |
| 2017-12-26 | a1c05d1 | the tool |

## How it was built

From a copy of the project folder, turned into git in 2026, the dates kept. The last commit before this README sets LF
line endings and indents with spaces.

Left out of this public copy: my own trips (the dashcam subtitles, the routes made from them and my Google Maps routes)
and a file of another, private project. In their place the last commit has a short made-up sample: `var/srt/test.srt`
(ten readings on a straight line in Vienna) and the `var/kml/route_output.kml` that the tool makes from it.

## Third-party code

Bundled in `vendor/` as Composer installed it, each under its own licence: the captioning library
(`captioning/captioning`, MIT) and Composer's autoloader (MIT). The SRT parser in `Subtitles.php` comes from a Stack
Overflow answer (CC BY-SA), as its comment says.

## Licence

MIT (see `LICENSE`), added in 2026: the project named no licence. It covers my own code.
