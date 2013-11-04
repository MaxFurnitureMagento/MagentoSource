####################################################################

 jQuery AJAX-ZOOM 
 Magento module version 1.3 - 2013-02-18

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


2. EN: Copy only the 'axZm' folder into your Magento store installation folder.

2. DE: Kopieren Sie nur den Ordner 'axZm' in das Verzeichnis Ihrer Magento Installation. 


3. EN: Move the folder 'axZm/mods/magento/pic' into axZm folder (move up)

3. DE: Verschieben Sie den gesamten Ordner 'axZm/mods/magento/pic' ebenfalls in das 'axZm' Verzeichnis. 


4. EN: chmod all subfolders in 'axZm/pic/' to 775 or other (e.g. 777) so that PHP can write to them.

4. DE: Setzen Sie die Zugriffsrechte aller Unterordner im 'axZm/pic/' Verzeichnis auf 775 (chmod) oder andere, z.B. 777. Wichtig ist, dass PHP in diesen Ordnern schreiben kann. 


5. EN: Make sure "ioncube" or "sourceguardian" loaders are installed on your server. 
For more into please check requirements at http://www.ajax-zoom.com/index.php?cid=docs 

5. DE: Stellen Sie sicher, dass entweder "ioncube" oder "sourceguardian" Loader auf dem Server installiert sind. 
Mehr Informationen darüber finden Sie in der vollständigen Dokumentation unter http://www.ajax-zoom.com/index.php?cid=docs 


6. EN: Backup your 'app/design/frontend/[your_interface]/[your_theme]/template/page/html/head.phtml' file and open it in the editor.
In case there is no such a file find it at 'app/design/frontend/base/default/template/page/html/head.phtml  
or at 'app/design/frontend/default/default/template/page/html/head.phtml'

6. DE: Sichern Sie die Datei 'app/design/frontend/[your_interface]/[your_theme]/template/page/html/head.phtml' und öffnen diese in einem Editor. 
Wenn diese Datei dort nicht vorhanden ist, kann sie möglicherweise unter 'app/design/frontend/base/default/template/page/html/head.phtml' 
oder 'app/design/frontend/default/default/template/page/html/head.phtml' gefunden werden. 


7. EN: Insert the following line:

  <?php require (BP . str_replace('/', DS, '/axZm/mods/magento/magento_head.php'));?> after <?php echo $this->getCssJsHtml() ?>

7. DE:Fügen Sie folgende Zeile: <?php require (BP . str_replace('/', DS, '/axZm/mods/magento/magento_head.php'));?> nach <?php echo $this->getCssJsHtml() ?>  


8. EN: In case jQuery is already installed open 'axZm/mods/magento/magento_head.php' and remove the jQuery script tag (line 24).

8. DE: Im Falle, dass die jQuery Core Bibliothek bereits vorhanden ist, öffnen Sie die Datei 'axZm/mods/magento/magento_head.php' und entfernen sie dan JavaScript Tag mit jQuery Core (line 24). 


9. EN: Backup your 'app/design/frontend/[your_interface]/[your_theme]/template/catalog/product/view/media.phtml' file.

9. DE: Sichern Sie die Datei 'app/design/frontend/[your_interface]/[your_theme]/template/catalog/product/view/media.phtml'. 


10. EN: Move the file 'axZm/mods/magento/media.phtml' into the 'app/design/frontend/[your_interface]/[your_theme]/template/catalog/product/view/' folder of your Magento store.

10. DE: Verschieben Sie dann die Datei 'axZm/mods/magento/media.phtml' in das Verzeichnis 'app/design/frontend/[your_interface]/[your_theme]/template/catalog/product/view/'. 


11. EN: Feel free to modify the media.phtml and 'axZm/mods/magento/magento_axZm.js' files to suit your needs.

11. DE: Es steht Ihnen frei die Dateien media.phtml und 'axZm/mods/magento/magento_axZm.js' beliebig anzupassen. 
Am Anfang der Datei media.phtml finden Sie einige Optionen, mit denen das Design an Ihr Layout anpassbar ist. 


12. EN: If template cache is activated do not forget to clear it in order to changes take effect. 
(To clear cache go to System -> Cache Management in the admin area.)
 
12. DE: Wenn Templatecache aktiviert ist, muss dieser geleert werden, damit die Änderungen im Frontend sichtbar sind. 
In Administratorbereich gehen Sie dazu auf System -> Cache Management und löschen den Cache. 


13. EN: You have istalled the demo version of AJAX-ZOOM. 
To upgrade buy a commercial license at www.ajax-zoom.com/index.php?cid=download and update the 'axZm/zoomConfig.inc.php' file with the License Key.

13. DE: Sie haben die AJAX-ZOOM demo Version installiert. Nachdem Sie sich für kommerzielle Lizenz entschieden haben, 
können Sie hier eine Lizenz erwerben: www.ajax-zoom.com/index.php?cid=download Der Lizenzschlüssel muss dann in die Datei 'axZm/zoomConfig.inc.php' eingefügt werden. 

Common Errors:

EN: 
1.	In case You get an error stating, that images could not be found, 
	please open /axZm/zoomConfig.inc.php and set this options manually. 
	Replace:
	
	$zoom['config']['installPath'] = $axZmH->installPath();
	
	with:
	
	$zoom['config']['installPath'] = '';
	
	or if the path to you shop is '/shop', then set: 
	
	$zoom['config']['installPath'] = '/shop';
	
2.	IMPORTANT: the most common reason why modules that contain jQuery plugins do not work together 
	is that jQuery core library is inserted more than once into the source. Consider this example:
	
	<script type="text/javascript" src="jqueryCore_1.js"></script>
	<script type="text/javascript" src="somePlugin_1.js"></script>
	
	<script type="text/javascript" src="jqueryCore_2.js"></script>
	<script type="text/javascript" src="somePlugin_2.js"></script>
	
	jqueryCore_2.js overwrites jqueryCore_1.js and somePlugin_1.js; as the result somePlugin_1.js is not existent. 
	When some arbitrary code is called on a function from somePlugin_1.js the results are javascript errors and none of the plugins might work properly.
	
	Please make sure, that jQuery Core Library is bint only once into the source code and all jQuery plugins are below it.
	
	In case jQuery core library is already installed, open 'axZm/mods/magento/magento_head.php' and remove the jQuery core script tag.
	
	In case it is not possible to insert AJAX-ZOOM Code below the jQuery core(s) inserted by other modules (or present in the template) but only before, 
	then you can make the following: do not remove jQuery core from 'axZm/mods/xtc/xtc_head.php'. 
	Find all other jQuery cores (not plugins), open these files and delete everything in them. 
	The script tags will be present in the source code but they will do no harm anymore. 
	This way the page is optimized in a sense, that the same library is not loaded twice or more times, 
	your modules (not just AJAX-ZOOM) work together and you have the control over the version of jQuery in your shop. 

DE: 
1.	Sollten Sie einen Fehler erhalten, dass die Bilder nicht gefunden wurden, 
	müßen höchstwahrscheinlich ein oder zwei Variablen manuell eingestellt werden. 
	Öffnen Sie die Datei /axZm/zoomConfig.inc.php und setzen Sie zunächst anstatt 
	der Zeile:
	
	$zoom['config']['installPath'] = $axZmH->installPath();
	
	diese Zeile ein:
	
	$zoom['config']['installPath'] = '';
	
	bzw. wenn der Shop im Unterverzeichnis, wie etwa '/shop' installiert ist, dann: 
	
	$zoom['config']['installPath'] = '/shop';
	
2. 	WICHTIG: die häufigste Ursache dafür, dass Module mit jQuery Plugins sich nicht miteinander vertragen 
	ist die automatische Eintragung von jQuery Core Bibliothek doppelt oder dreifach in eine Seite. Dann passiert nämlich folgendes:
	
	<script type="text/javascript" src="jqueryCore_1.js"></script>
	<script type="text/javascript" src="irgendEinPlugin_1.js"></script>
	
	<script type="text/javascript" src="jqueryCore_2.js"></script>
	<script type="text/javascript" src="irgendEinPlugin_2.js"></script>
	
	jqueryCore_2.js überschreibt jqueryCore_1.js und irgendEinPlugin_1.js, 
	so dass irgendEinPlugin_1.js überhaupt nicht mehr vorhanden ist. 
	Ruft nun ein anderer Code auf der Seite eine Funktion aus irgendEinPlugin_1.js, so wird vom Browser ein Fehler ausgegeben. 
	Eventuell funktioniert gar nichts mehr. 
	
	Stellen Sie sicher, dass jQuery Core auf der Seite nur ein Mal vorhanden ist und alle jQuery Plugins sich unter jQuery Core Bibliothek befinden! 
	
	Im Falle, dass die jQuery Core Bibliothek bereits vorhanden ist, öffnen Sie die Datei 'axZm/mods/magento/magento_head.php' 
	und entfernen sie dan JavaScript Tag mit jQuery Core.
	
	Wenn es nicht möglich ist den AJAX-ZOOM Code im Headbereich nach dem von anderen Modulen (oder dem Template) 
	eingefügten jQuery Core Biliothek(en) zu platzieren, sondern nur davor, dann können Sie folgendes machen: 
	Entfernen Sie nicht jQuery Core aus 'axZm/mods/xtc/xtc_head.php'. 
	Finden Sie alle anderen jQuery Core (nicht Plugin) Dateien im Quelltext der Ausgabeseite, 
	öffnen diese und machen sie unschädlich, indem einfach alles darin gelöscht wird. 
	Sie werden dann zwar im Quelltext angezeigt, macht aber nichts, weil sie nun leer sind. 
	Dadurch optimieren Sie die Ladezeiten, haben Kontrolle über die Version von jQuery Core und Ihre Module (nicht nur AJAX-ZOOM) vertragen sich wieder. 

______________________________________________________________________________________________

360 DEGREE SPINS:

HOW TO:
To add 360 degree view simply upload your high resolution images of a spin over FTP into '/axZm/pic/zoom3D/[product id]' e.g. '/axZm/pic/zoom3D/123'.

An other words create folder with product ID under '/axZm/pic/zoom3D/' 
and put all your images (frames of 360 spin) in it. AJAX-ZOOM will look into this directory and trigger everything else instantly!

THINGS TO TAKE ACCOUNT OF:

1. 	Every image must have an unique filename!!! You could prefix each image of a spin with the product id to ensure the uniqueness, e.g.
	/axZm/pic/zoom3D/123/123_spin001.jpg
	/axZm/pic/zoom3D/123/123_spin002.jpg
	/axZm/pic/zoom3D/123/123_spin003.jpg
	[...]
	/axZm/pic/zoom3D/123/123_spin036.jpg
	(number of images is not restricted)
	  
2. 	When you upload 16, 24, 36, 48, 72 or even more high resolution images - this takes time. 
	To avoid incomplete generation of the animated preview gif and / or image tiles 
	you can upload the spin set into some other folder and move it 
	to '/axZm/pic/zoom3D/[product id]' after all images have been successfully uploaded. 
	Alternatively place an empty file named "upload.txt" into this folder and remove it after 
	the upload is complete. 

NOTES:

1. The 'axZm/zoomConfig.inc.php' contains over 300 options to configure AJAX-ZOOM. 
For Your Magento store installation some options will be overridden in the same file. 
To see or adjust them find the line: elseif ($_GET['example'] == 'magento') in 'axZm/zoomConfig.inc.php' 
From Ver. 2.1.6 these overrides have been moved to the file 'axZm/zoomConfigCustom.inc.php'

2. Run 'axZm/mods/magento/magento_cleanup.php' to remove all images produced by AJAX-ZOOM which are not needed any more. 
Edit the file (remove exit; at the beginning) to use it. 
You can rename it and set a cronjob running once a day (php q /[path_to_magento]/axZm/mods/magento/magento_cleanup.php)

3. For a detailed explanation of all options, methods etc. see the documentation at http://www.ajax-zoom.com/index.php?cid=docs 

4. The lightbox implementation (jquery.fancybox Ver. 1.2.6) has been modified to support auto dimensions (jquery.fancybox-1.2.6.js line 158ff). 
The new Version of fancybox, currently 1.3.1 slows down Magento in IE (some JS issue?). 
The slightly modified old version 1.2.6 is sufficient for the purpose. 


