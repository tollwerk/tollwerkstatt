# tollwerkstatt

Test :-)

### Alte tollwerkstatt-Seite

* Findet sich unter *public/_ALT*. Der Aufruf über http://tollwerkstatt.tollwerk.de/_ALT ist möglich.

### SASS / CSS 

Mit sass kann wie gewohnt gearbeitet werden. Zunächst ins Wurzelverzeichnis des Projektes wechseln: 
``` 
cd /www/accounts/tollwerkstatt
```
Dann den grunt mit folgendem Befehl staren:
```
grunt watch
```

Die sass-Dateien liegen in folgenden Ordnern. Im "below"-Verzeichnis dürfen natürlich weitere .scss-Dateien angelegt und importiert werden.

  * /resources/sass/below
  * /resources/sass/common
  
### Javascript
  
Javascript wird aktuell **nicht** wie in anderen Projekten per grunt kompiliert, sondern komplett händisch erstellt und eingebunden. Aktuell passiert alle eigene Javascript-Programmierung hier:

* /public/js/tollwerkstatt.js

Sollten weitere Javascript-Dateien benötigt werden, müssen diese von Hand in nach */public/js* kopiert und anschließend in */resources/views/Layouts/Layout.html* eingebunden werden.

### Bilder 

Werden in folgendne Ordner kopiert und können von dort aus ins HTML eingebunden werden:

* /public/img/
  
### HTML bearbeiten

#### Inhalte eintragen
Wir arbeiten hier ohne ein CMS wie TYPO3 und tragen die Inhalte alle händisch ins HTML ein. Da wir nur einen Single-Pager haben, kann alles in folgende Datei geschrieben werden:

* /resources/views/Templates/Index.html

Partials können auch normal genutzt werden und finden sich hier:
 
* /resources/views/Partials/

Wenn neue Javascript-Dateien oder externe, nicht von unserem sass generierte .css-Dateien eingebunden werden müssen, passiert das in der Layout-Datei im Ordner:
 
 * /resources/views/Layouts/

#### ViewHelper

Ohne TYPO3 im Hintergrund stehen nur grundlegende ViewHelper zur Verfügung. `<f:format.raw>`, `<f:for>` etc. gibt es. Nicht zur Verfügung steht z.B. `<f:debug>`. Um die verfügbaren ViewHelper zu sehen kann man einfach mal ins Verzeichnis */vendor/typo3fluid/src/ViewHelpers* schauen. Man kann die normale TYPO3-Doku (also nach dem ViewHelper googeln) nehmen, wenn man etwas nachschauen muss.

@kai: 
https://github.com/TYPO3/Fluid
Unsere ViewHelper können mit dem Namensraum "tw" verwendet werden, z.B. `<tw:format.date>...</tw:format.date>`

#### Ausklappbare Bereiche

Hierfür wird das selbst geschriebene, ursprünglich für Fischer Automobile entwickelte,  **COL_collapsible**-Plugin verwendet. 
Ausklappbarer Inhalt kann mit Hilfe von drei CSS-Klassen gesteuert werden. Diese können jedem beliebigen HTML-Element, also 
nicht nur einem `<div>`, gegeben werden. Beispiel:

```html
<div class="COL_collapsible">
    <h2 class="COL_title">Überschrift. Bei Klick wird der Inhalt auf-/zugeklappt.</h1>
    <div class="COL_content">
        Alle Inhalte hier drin werden auf-/zugeklappt.
    </div>
</div>
```

Die Reihenfolge von `COL_title` und `COL_content` ist egal, solange sie sich **innerhalb** von `COL_collapsible` befinden.
Wahrscheinlich müssen die beiden Elemente nicht einmal auf der gleichen Ebene liegen, das ist aber nicht erprobt. Noch ein Beispiel:

```html
<article class="COL_collapsible">
    
    <h1>Überschrift</h1>
    <p>Ein Absatz, der immer zu sehen ist.</p>
    <p>Noch ein Absatz, der immer zu sehen ist.</p>
    
    <div class="COL_content">
        <p>Wird angezeigt/versteckt.</p>
        <p>Wird angezeigt/versteckt.</p>
        <p>Wird angezeigt/versteckt.</p>
    </div>
    
    <!-- Hier wird kurzerhand ein Bild zum "Titel", der den Inhalt aus-/einklappt -->
    <img src="Pfeil.svg" class="COL_title"/>
    
</article>
```

### Kontaktformular

Bei Änderungen spielt sich alles in folgenden Dateien ab:
* **Template für die Webseite:** /resources/views/Partials/Contact/Form.html
* **Template für die Admin-Email:** /resources/views/Templates/Contact/EmailAdmin.html
* **Ausertung / Versand:** /app/Http/Controllers/IndexController.php -> sendContactEmail()

Möchte man neue Felder hinzufügen, muss das zunächst nur in den beiden HTML-Dateien geschehen.

Die PHP-Datei muss man bearbeiten, wenn die neuen Felder auch validiert (Pflichtfeld, max./min. Werte etc.) werden sollen. Einfach mal in die `sendContactEmail()`-Funktion schauen, ist alles selbsterklärend. 