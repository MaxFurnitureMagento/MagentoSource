####################################################################

 jQuery AJAX-ZOOM 
 Oxid eSales plugin version 1.0 - 2010-10-18

 Copyright: Copyright (c) 2010-2013 Vadim Jacobi
 License Agreement: http://www.ajax-zoom.com/index.php?cid=download
 Version: 4.0.1
 Date: 2013-02-18
 URL: http://www.ajax-zoom.com
 Documentation: http://www.ajax-zoom.com/index.php?cid=docs

####################################################################

INSTALLATION:

1. EN: Unzip the contents keeping the file structure intact.

1. DE: Entpacken Sie den Inhalt der download Datei mit unveränderter Dateistruktur.


2. EN: Copy only the 'axZm' folder into your Oxid installation folder.

2. DE: Kopieren Sie nur den Ordner 'axZm' in das Verzeichnis Ihrer Oxid Installation.

3. EN: Move the folder 'axZm/mods/oxid/pic' into axZm folder (move up).

3. DE: Verschieben Sie den gesamten Ordner 'axZm/mods/oxid/pic' ebenfalls in das 'axZm' Verzeichnis.


4. EN: chmod all subfolders in 'axZm/pic/' to 775 or other (e.g. 777) so that PHP can write to them.

4. DE: Setzen Sie die Zugriffsrechte aller Unterordner im 'axZm/pic/' Verzeichnis auf 775 (chmod) oder andere, z.B. 777. Wichtig ist, dass PHP in diesen Ordnern schreiben kann.


5. EN: Make sure "ioncube" or "sourceguardian" loaders are installed on your server. 
For more into please check requirements at http://www.ajax-zoom.com/index.php?cid=docs

5. DE: Stellen Sie sicher, dass entweder "ioncube" oder "sourceguardian" Loader auf dem Server installiert sind. 
Mehr Informationen darüber finden Sie in der vollständigen Dokumentation unter http://www.ajax-zoom.com/index.php?cid=docs


6. EN: Backup your 'out/[your_template]/tpl/_header.tpl' file and open it in the editor.
 
6. DE: Sichern Sie die Datei 'out/[Ihr_Template]/tpl/_header.tpl' und öffnen diese in einem Editor.


7. EN: Insert the following line:
[{php}]include(getShopBasePath().'axZm/mods/oxid/oxid_head.php');[{/php}]
before: 
[{if $rsslinks}]
(somewhere in the head section of your page)

7. DE: Fügen Sie die folgende Zeile: 
[{php}]include(getShopBasePath().'axZm/mods/oxid/oxid_head.php');[{/php}]
vor: 
[{if $rsslinks}]
(also irgendwo im head Bereich des HTML Documents)


8. EN: In case jQuery core library is already installed open 'axZm/mods/oxid/xtc_head.php' in an Editor and remove the jQuery script tag (line 24).
Make sure that You insert the code from step 8 after existing jQuery script tag. 


8. DE: Im Falle, dass jQuery Bibliothek bereits im Head vorhanden ist, öffnen Sie die Datei 'axZm/mods/oxid/xtc_head.php' in einem Editor und entfernen Sie den jQuery Script Tag (Zeile 24). 
Stellen Sie dann aber sicher, dass der Code aus Schritt 8 nach dem existierenden jQuery Script Tag eingefügt ist. 


9. EN: Backup your 'out/[your_template]/tpl/details.tpl' file and open it in the editor.

9. DE: Sichern Sie die Datei 'out/[your_template]/tpl/details.tpl' und öffnen diese in einem Editor.



10. EN: Put instead of <div class="picture">....</div> this line of code: [{php}]include(getShopBasePath().'axZm/mods/oxid/oxid_media.php');[{/php}]

10. DE: Fügen Sie anstatt <div class="picture">....</div> diese Zeile ein: [{php}]include(getShopBasePath().'axZm/mods/oxid/oxid_media.php');[{/php}]


11. EN: In order to remove the standard gallery and zoom from detail view you can modify Your 'details.tpl' file. 
The link (a tag) with the id 'test_zoom' is not needed. Also the div tag with the css class name 'morepics' is also not needed. 
If $axZm['adjTemplate'] and $axZm['removeMorePics'] in file 'axZm/mods/oxid/oxid_media.php' are enabled, this is done by javasctipt after page load.

11. DE: Um die standard Galerie und Zoom aus dem Template zu entfernen können Sie die Datei 'details.tpl' entsprechend anpassen. 
Der Link (a tag) mit der id 'test_zoom' wird nicht mehr benötigt. Ebenso das div Ellement mit dem css class Namen 'morepics'. 
Wenn $axZm['adjTemplate'] und $axZm['removeMorePics'] in der Datei 'axZm/mods/oxid/oxid_media.php' aktiviert sind, 
werden diese Schritte mit Javascript erledigt, nachdem die Seite geladen ist. Langfristig sollten Sie aber das Template anpassen.


12. EN: Feel free to modify the 'axZm/mods/oxid/oxid_media.php' and 'axZm/mods/oxid/oxid_axZm.js' files to suit your needs.

12. DE: Sie können die Dateien 'axZm/mods/oxid/oxid_media.php' und 'axZm/mods/oxid/oxid_axZm.js' nach Ihren Bedürfnissen frei anpassen.


13. EN: Clear the Templatecache if needed.

13. DE: Templatecache wenn notwendig unter Stammdaten -> Grundeinstellungen -> Performance leeren. 


14. EN: You have installed the demo version of AJAX-ZOOM. 
To upgrade buy a commercial license at www.ajax-zoom.com/index.php?cid=download and update the 'axZm/zoomConfig.inc.php' file with the License Key.

14. DE: Sie haben die AJAX-ZOOM demo Version installiert. 
Nachdem Sie sich für kommerzielle Lizenz entschieden haben, können Sie hier eine Lizenz erwerben: www.ajax-zoom.com/index.php?cid=download
Der Lizenzschlüssel muss dann in die Datei axZm/zoomConfig.inc.php eingefügt werden.


Common Errors:

EN: In case You get an error stating, that images could not be found, 
please open /axZm/zoomConfig.inc.php and set this options manually. 
Replace:

$zoom['config']['installPath'] = $axZmH->installPath();

with:

$zoom['config']['installPath'] = '';

or if the path to you shop is '/shop', then set: 

$zoom['config']['installPath'] = '/shop';


DE: Sollten Sie einen Fehler erhalten, dass die Bilder nicht gefunden wurden, 
müßen höchstwahrscheinlich ein oder zwei Variablen manuell eingestellt werden. 
Öffnen Sie die Datei /axZm/zoomConfig.inc.php und setzen Sie zunächst anstatt 
der Zeile:

$zoom['config']['installPath'] = $axZmH->installPath();

diese Zeile ein:

$zoom['config']['installPath'] = '';

bzw. wenn der Shop im Unterverzeichnis, wie etwa '/shop' installiert ist, dann: 

$zoom['config']['installPath'] = '/shop';

NOTES:

1. EN: The 'axZm/zoomConfig.inc.php' contains over 250 options to configure AJAX-ZOOM. 
For Your Oxid eSales installation some options will be overridden in the same file. 
To see or adjust them find the line: elseif ($_GET['example'] == 'oxid') in 'axZm/zoomConfig.inc.php' 
From Ver. 2.1.6 these overrides have been moved to the file 'axZm/zoomConfigCustom.inc.php'

2. Run 'axZm/mods/oxid/oxid_cleanup.php' to remove all images produced by AJAX-ZOOM which are not needed any more. 
Edit the file (remove exit; at the beginning) to use it. 
You can rename it and set a cronjob running once a day (php q /[path_to_oxid]/axZm/mods/oxid/oxid_cleanup.php)

3. For a detailed explanation of all options, methods etc. see the documentation at http://www.ajax-zoom.com/index.php?cid=docs 

4. The lightbox implementation (jquery.fancybox Ver. 1.2.6) has been modified to support auto dimensions (jquery.fancybox-1.2.6.js line 158ff). 


