importScripts(
    'https://storage.googleapis.com/workbox-cdn/releases/6.4.1/workbox-sw.js'
);
importScripts('https://www.gstatic.com/firebasejs/5.6.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/5.6.0/firebase-messaging.js');


// Taken from Firebase's documentation
// Background worker
try {
    if(firebase.messaging.isSupported()) {

        firebase.initializeApp({
            'messagingSenderId': '144659929800'
        });
        const messaging = firebase.messaging();

        messaging.setBackgroundMessageHandler(function(payload) {
            console.log('[firebase-messaging-sw.js] Received background message ', payload);
            // Customize notification here
            const notificationTitle = payload.title;
            const notificationOptions = {
                body: payload.body,
                icon: null
            };


            return self.registration.showNotification(notificationTitle,
                notificationOptions);
        });
    }
} catch (e) {

}

