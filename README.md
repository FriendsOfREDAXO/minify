Minify
======

Minifies HTML, combines/minfies CSS and JS files.

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/minify/assets/minify_01.png)


Dieses Addon erm�glicht das minimieren und b�ndeln von CSS und JS Dateien.
Dazu kann man unter dem Punkt 'Minify' beliebig viele Sets anlegen. Wichtig ist, dass der Name eines Sets pro Typ (CSS/JS) nur einmal vorkommen kann. In das Feld 'Assets' kommen zeilengetrennt die Pfade zu den einzelnen Dateien. Wenn eine Datei mit '.scss' endet, wird sie automatisch kompiliert. Die Pfade m�ssen Redaxo-Root relativ sein.
Anschliessend wird ein Snippet � la "REX_MINIFY[type=css set=default]" generiert, welches im Template an beliebiger Stelle platziert werden kann. Das Snippet ist jeweils in der Set-�bersicht zu finden und kann von da kopiert werden. Das Snippet wird im Frontend automatisch durch einen entsprechenden HTML-Tag ersetzt.
Zus�tzlich bringt dieses Addon einen Mediamanager-Typen 'tinify' mit, mit dem Bilder via TinyJPG/TinyPNG optimiert werden k�nnen.