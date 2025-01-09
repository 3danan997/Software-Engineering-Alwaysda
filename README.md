# AlwaysDa, Dein digitales Adressbuch

Mit AlwaysDa erhalten Sie ein digitales Adressbuch, das Ihnen das Leben erleichtert. AlwaysDa ist eine innovative Web-Anwendung, die Ihnen dabei hilft, Ihre sozialen Kontakte aufrechtzuerhalten, ohne dass Sie sich darum kümmern müssen. 

Es erinnert Sie in regelmäßigen Abständen daran, Ihre Kontakte anzurufen, ihnen zu schreiben oder sie zu treffen, damit Sie Ihr soziales Umfeld nie vernachlässigen. 

Es spart Ihnen Zeit und Aufwand, sich daran zu erinnern, Ihre Kontakte zu pflegen. Sie können Ihre Kontakte nach Kategorien sortieren, sodass Sie immer den Überblick behalten, es ist einfach zu bedienen und ermöglicht es Ihnen, Ihre Kontakte jederzeit und überall zu verwalten. 

Probieren Sie es jetzt aus und erleben Sie den Unterschied! AlwaysDa ist der Weg, wie Sie Ihre Kontakte organisieren und pflegen sollten.

## Voraussetzungen
* XAMPP oder ein gleichwertiger lokaler Server
* Laravel
* NodeJS
* Composer
* Virtual domain | localhost

## XAMPP VirtualHost

`<VirtualHost mydomain.local>
    DocumentRoot "ordner/laravel/public"
    ServerName mydomain.local
    <Directory "ordner/laravel/public">
        Allowoverride All
        Require all granted
    </Directory>
</VirtualHost>`

## Windows hosts 

* System32/drivers/etc/hosts => `127.0.0.1       mydomain.local`


## Setup
* Laden Sie die Projektdateien auf Ihren Computer herunter
* Erstellen Sie eine virtuelle Domain mit der Endung .local, z. alwaysda.local
* Verweisen Sie Ihren lokalen Server auf den /public Pfad des Projekts
* Verwenden Sie das bereitgestellte .env Beispiel und ändern Sie die Domäne, die Datenbankverbindung und die Firebase-Konfiguration
* Löschen Sie public/js und public/css und public/content/avatars
* Führen Sie die folgenden Befehle aus

Dadurch werden die composer packages installiert und ein Autoload für Laravel validiert
`composer install`
`composer dump-autoload`

Dadurch werden die node packages installiert
`npm install`

Dadurch wird ein neuer Schlüssel für Ihr Laravel Projekt generiert
`php artisan key:generate`

Dadurch wird die Konfiguration des Projekts gelöscht und erneut validiert
`php artisan optimize:clear`

Dadurch wird der Seeder ausgeführt, um die grundlegenden Verbindungen zu erstellen, die für unsere Ressourcen erforderlich sind
`php artisan migrate`

* Jetzt sollten Sie bereit sein, das Projekt zu testen. Stellen Sie sicher, dass sich das Projekt im Debug-Modus befindet und der boolesche Wert auf „true“ gesetzt ist
* Führen Sie den folgenden Befehl aus, um die Dateien zu kompilieren
  `npm run dev`

* Sie sind jetzt bereit. Greifen Sie mit der URL, die Sie in Ihrer .env Datei festgelegt haben, auf das Projekt zu

### PWA

PWA ermöglicht es dem Benutzer, das Projekt als App auf seinem Android-Gerät zu „installieren“. Es wird eine Verknüpfung erstellt.

* Ändern Sie public/manifest.json entsprechend Ihren Anforderungen

### Firebase

Firebase wird für unsere mobile Push-Benachrichtigungsfunktion benötigt.

* Erstellen Sie ein Firebase-Projekt
  *Ändern Sie die Konfiguration in der .env Datei und public/firebase-messaging-sw.js

### Scheduler

Um es dem lokalen Server zu erleichtern, können Sie Cron Jobs für die Warteschlange und den Scheduler verwenden. Der Cron Job würde jede Minute ausgeführt:

`* * * * * cd path/to/project && php artisan schedule:run 1>> /dev/null 2>&1`
`* * * * * cd path/to/project && php artisan queue:work 1>> /dev/null 2>&1`


## Entwickler

Für Entwickler, die auf das Projekt zugreifen und die Struktur anzeigen möchten, sehen Sie sich bitte unten an, um zu verstehen, wie wir die App erstellen.

### API

Wir haben Mikroendpunkte verwendet, um die Komponenten zu trennen. Dies bedeutet, dass Sie möglicherweise mehrere Anfragen auf derselben Seite sehen. Es könnte ein Problem für Benutzer mit langsamer Internetverbindung sein, aber dies wäre äußerst selten und die Anfragen sind auch gering.

Dieser Ansatz ist wichtig, um dem Benutzer etwas zu zeigen, ohne lange auf die Aktualisierung der gesamten Seite warten zu müssen.

### Struktur

Wir haben die SPA-Struktur verwendet. Sie werden eine einheitliche `.blade`-Datei finden, die das `<app>`-Tag in ihre Ansicht lädt. Dies funktioniert zusammen mit `web.php`, um einen Controller aufzurufen, der die `.blade`-Datei aufruft.

### Backend

#### Helper/Helper.php

Globale Hilfsklasse mit statischen Funktionen, die von verschiedenen, unterschiedlichen Quellen unseres Projekts aufgerufen würden. Dies beinhaltet die Logik zum Generieren von Erinnerungen.

#### Console/Kernel.php

Dies enthält unsere Planungslogik. Es durchläuft im Grunde die `reminders`, die von `Helper.php` generiert werden, und validiert, ob sie bereits ausgegeben wurden. Dieser Scheduler wird oben als `cron`-Job bezeichnet.

#### Http/Controllers

In /Auth finden Sie einen einzigen Controller, der die Anforderungen der Benutzer der Plattform verarbeitet, wie z. B.: Anmelden, Erstellen, Token anfordern und Token validieren, um das Passwort zurückzusetzen.

In den /Utils finden Sie drei Controller für die Verwaltung von Erinnerungen, Kontakten und Statistiken. Die Controller von Erinnerungen und Statistiken sind hauptsächlich GET-Controller, während der Kontakte-Controller ein CRUD-Controller ist.

#### Models

Wir haben einfache Modelle mit ORM-Beziehungen erstellt, wie z. B. belongsToMany usw. Sie müssen nichts ändern, es ist alles gut kommentiert und erklärt.

#### Jobs

Dies hat unseren E-Mail-Job. Grundsätzlich gibt es zwei Arten von E-Mails: `reminder` und `password token`. Wir verwenden ein `HTML`-Template, das unter `public/content/emails` zu finden ist


### Front-End

Um das beste Erlebnis zu bieten, verwenden wir eine Mischung aus Vue, Vuetify und Vuex.

#### app.js

Die brain-Datei unserer Vue. Es führt die Importe durch und erstellt Definitionen unserer Komponenten.

#### js/utils

Diese enthält die Vuex-, Router- und generischen Helfer, wie die helper.js und die API-Helfer.

#### js/design/views

Wir verwenden einen MVC-ähnlichen Ansatz. Wir haben views und Komponenten. Dieser Ordner enthält die Haupt-views unseres Projekts, wie die Haupt-views der Anmeldeseite, die Haupt-views des Dashboards und andere Seiten.

Einige API-Aufrufe werden an die zu verarbeitenden `views` gesendet, sodass `components` das Rendern des Ergebnisses übernehmen und den tatsächlichen Kontext aus zentrierten views kommen lassen.

#### js/desing/components

Mini-views sind in Komponenten aufgeteilt. Darüber hinaus verwenden wir Vuetify, um reaktionsschnelle Helfer bereitzustellen, sodass wir bestimmte Komponenten in bestimmten Bildschirmen anzeigen.