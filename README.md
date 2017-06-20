Minify
======

Minifies HTML, combines/minfies CSS and JS files.

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/minify/assets/minify_01.png)


Dieses Addon ermöglicht das minimieren und bündeln von CSS und JS Dateien.

Dazu kann man unter dem Punkt 'Minify' beliebig viele Sets anlegen. Wichtig ist, dass der Name eines Sets pro Typ (CSS/JS) nur einmal vorkommen kann. In das Feld 'Assets' kommen zeilengetrennt die Pfade zu den einzelnen Dateien. Wenn eine Datei mit '.scss' endet, wird sie automatisch kompiliert. Die Pfade müssen Redaxo-Root relativ sein.

Anschliessend wird ein Snippet à la "REX_MINIFY[type=css set=default]" generiert, welches im Template an beliebiger Stelle platziert werden kann. Das Snippet ist jeweils in der Set-Übersicht zu finden und kann von da kopiert werden. Das Snippet wird im Frontend automatisch durch einen entsprechenden HTML-Tag ersetzt.