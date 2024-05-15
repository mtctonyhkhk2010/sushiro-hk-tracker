self.addEventListener("push", (event) => {

    const notification = event.data.json();
    // { "title":"Hi" , "body":"check this out" , "url":"/?message1"}
    event.waitUntil(self.registration.showNotification(notification.title , {
        body: notification.body ,
        icon: "apple-touch-icon.png" ,
        data: {
            notifURL: notification.url
        }
    }));
});

self.addEventListener("notificationclick",  (event) => {
    event.waitUntil(clients.matchAll({
        type: "window",
        includeUncontrolled: true
    }).then(function (clientList) {
        if (event.notification.data.notifURL) {
            let client = null;

            for (let i = 0; i < clientList.length; i++) {
                let item = clientList[i];

                if (item.url) {
                    client = item;
                    break;
                }
            }

            if (client && 'navigate' in client) {
                client.focus();
                event.notification.close();
                return client.navigate(event.notification.data.notifURL);
            }
            else {
                event.notification.close();
                // if client doesn't have navigate function, try to open a new browser window
                return clients.openWindow(event.notification.data.notifURL);
            }
        }
    }));

});
