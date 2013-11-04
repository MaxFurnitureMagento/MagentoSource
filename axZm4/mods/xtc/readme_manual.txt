####################################################################

 jQuery AJAX-ZOOM 
 xt:Commerce plugin version 1.2 - 2011-02-13

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


2. EN: Copy only the 'axZm' folder into your xt:Commerce installation folder.

2. DE: Kopieren Sie nur den Ordner 'axZm' in das Verzeichnis Ihrer xt:Commerce Installation.


3. EN: Move the folder 'axZm/mods/xtc/pic' into axZm folder (move up).

3. DE: Verschieben Sie den gesamten Ordner 'axZm/mods/xtc/pic' ebenfalls in das 'axZm' Verzeichnis.


4. EN: chmod all subfolders in 'axZm/pic/' to 775 or other (e.g. 777) so that PHP can write to them.

4. DE: Setzen Sie die Zugriffsrechte aller Unterordner im 'axZm/pic/' Verzeichnis auf 775 (chmod) oder andere, z.B. 777. Wichtig ist, dass PHP in diesen Ordnern schreiben kann.


5. EN: Make sure "ioncube" or "sourceguardian" loaders are installed on your server. 
For more into please check requirements at http://www.ajax-zoom.com/index.php?cid=docs

5. DE: Stellen Sie sicher, dass entweder "ioncube" oder "sourceguardian" Loader auf dem Server installiert sind. 
Mehr Informationen darüber finden Sie in der vollständigen Dokumentation unter http://www.ajax-zoom.com/index.php?cid=docs


6. EN: Backup your 'includes/header.php' file and open it in the editor. 
Veyton: 'templates/[your_template]/javascript/js.php'
 
6. DE: Sichern Sie die Datei 'includes/header.php' und öffnen diese in einem Editor.
Veyton: 'templates/[Ihr_Template]/javascript/js.php'


7. EN: Insert the following line:

  
include(DIR_FS_DOCUMENT_ROOT.'axZm/mods/xtc/xtc_head.php'); 
before: 
require('templates/'.CURRENT_TEMPLATE.'/javascript/general.js.php');
(somewhere in the head section of your page)
Veyton: <?php include(_SRV_WEBROOT.'axZm/mods/xtc/xtc_head.php'); ?> 

7. DE: Fügen Sie die folgende Zeile: 

  
include(DIR_FS_DOCUMENT_ROOT.'axZm/mods/xtc/xtc_head.php'); 
vor: 
require('templates/'.CURRENT_TEMPLATE.'/javascript/general.js.php');
(also irgendwo im head Bereich des HTML Documents)
Veyton: <?php include(_SRV_WEBROOT.'axZm/mods/xtc/xtc_head.php'); ?>


8. EN: In case jQuery core library is already installed open 'axZm/mods/xtc/xtc_head.php' in an Editor and remove the jQuery script tag (line 24).
Make sure that You insert the code from step 8 after existing jQuery script tag. 
Should the jQuery be not in the head section xtcModified Ver. 1.04 jQuery was at the bottom of the body, fixed in 1.05) remove jQuery from there: 
open 'templates/[your_template]/javasctipt/general.js.php' and remove the line with the jquery.js

8. DE: Im Falle, dass jQuery Bibliothek bereits im Head vorhanden ist, öffnen Sie die Datei 'axZm/mods/xtc/xtc_head.php' in einem Editor und entfernen Sie den jQuery Script Tag (Zeile 24). 
Stellen Sie dann aber sicher, dass der Code aus Schritt 8 nach dem existierenden jQuery Script Tag eingefügt ist. 
Sollte die jQuery Biliothek nicht im Head Bereich vorhanden sein, dafür aber beispielsweise unten im Body, wie es bei xtcModified bis Ver. 1.04 der Fall war, 
öffnen Sie dann die Datei 'templates/[Ihr_Template]/javasctipt/general.js.php' und entfernen Sie jQuery von dort.


9. EN: Backup your 'templates/[your_template]/module/product_info/product_info_v1.html' file and open it in the editor. 
Veyton path: 'templates/[your_template]/xtCore/pages/product/product.html'

9. DE: Sichern Sie die Datei 'templates/[Ihr_Template]/module/product_info/product_info_v1.html' und öffnen diese in einem Editor.
Veyton Pfad: 'templates/[Ihr_Template]/xtCore/pages/product/product.html'



10. EN: Put between {if $PRODUCTS_IMAGE!=''} .... {/if} this line of code: {php}include(DIR_FS_DOCUMENT_ROOT.'/axZm/mods/xtc/xtc_media.php');{/php}
Veyton: Put between {if $products_image!=''} .... {/if} this line of code: {php}include(_SRV_WEBROOT.'/axZm/mods/xtc/xtc_media.php');{/php}

10. DE: Fügen Sie zwischen {if $PRODUCTS_IMAGE!=''} .... {/if} anstatt des vorhandenen Inhalts diese Zeile ein: {php}include(DIR_FS_DOCUMENT_ROOT.'/axZm/mods/xtc/xtc_media.php');{/php}
Veyton: Fügen Sie zwischen zwischen {if $products_image!=''} .... {/if} anstatt des vorhandenen Inhalts diese Zeile ein: {php}include(_SRV_WEBROOT.'/axZm/mods/xtc/xtc_media.php');{/php}


11. EN: Standard xt:Commerce: optionally delete everything between <!-- more images --> ... <!-- more images eof -->
xtcModified: optionally delete everything between {if $more_images|@count > 0} ... {/if}

11. DE: Standard xt:Commerce: optional löschen Sie alles zwischen <!-- more images --> ... <!-- more images eof -->
xtcModified: optional löschen Sie alles zwischen {if $more_images|@count > 0} ... {/if}


12. EN: Feel free to modify the 'axZm/mods/xtc/xtc_media.php' and 'axZm/mods/xtc/xtc_axZm.js' files to suit your needs.

12. DE: Sie können die Dateien 'axZm/mods/xtc/xtc_media.php' und 'axZm/mods/xtc/xtc_axZm.js' nach Ihren Bedürfnissen frei anpassen.


13. EN: In admin area choose Advanced Configuration -> Cache Options and hit 'Delete Templatecache' button.

13. DE: Im Administrator Bereich wählen Sie Erweiterte Konfiguration -> Cache Optionen und drücken Sie auf 'Templatecache leeren'.


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

______________________________________________________________________________________________

360 DEGREE SPINS:

HOW TO: to add 360 degree view simply upload your high resolution images of a spin over FTP 
into '/axZm/pic/zoom3D/[product id]' e.g. '/axZm/pic/zoom3D/123'.  
AJAX-ZOOM will look into this directory and trigger everything else instantly!
 
THINGS TO TAKE ACCOUNT OF: 
1. 	Every image must have an unique filename!!! 
	You could prefix each image of a spin with the product id to ensure the uniqueness.

2. 	When you upload 16, 24, 36, 48, 72 or even more high resolution images - this takes time. 
	To avoid incomplete generation of the animated preview gif and / or image tiles 
	you can upload the spin set into some other folder and move it 
	to '/axZm/pic/zoom3D/[product id]' after all images have been successfully uploaded. 
	Alternatively place an empty file named "upload.txt" into this folder and remove it after 
	the upload is complete. 


NOTES:

1. The 'axZm/zoomConfig.inc.php' contains over 250 options to configure AJAX-ZOOM. 
For Your xt:Commerce store installation some options will be overridden in the same file. 
To see or adjust them find the line: elseif ($_GET['example'] == 'xtc') in 'axZm/zoomConfig.inc.php' 
From Ver. 2.1.6 these overrides have been moved to the file 'axZm/zoomConfigCustom.inc.php'

2. Run 'axZm/mods/xtc/xtc_cleanup.php' to remove all images produced by AJAX-ZOOM which are not needed any more. 
Edit the file (remove exit; at the beginning) to use it. 
You can rename it and set a cronjob running once a day (php q /[path_to_xtc]/axZm/mods/xtc/xtc_cleanup.php)

3. For a detailed explanation of all options, methods etc. see the documentation at http://www.ajax-zoom.com/index.php?cid=docs 

4. The lightbox implementation (jquery.fancybox Ver. 1.2.6) has been modified to support auto dimensions (jquery.fancybox-1.2.6.js line 158ff). 


