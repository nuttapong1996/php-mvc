// ตั้งชื่อ cache
const CACHE_NAME = "sqhs-empcenter-cache";

// ไฟล์สำหรับแสดงหน้า offline
const OFFLINE_URL = "views/offline.html";

// ไฟล์ที่ต้องการ cache
const contentToCache = ["views/offline.html", "assets/css/style.css"];

// ทำการเพิ่ม Event Listener "Push" ให้กับ Service Worker
self.addEventListener("push", (event) => {
  // แปลงข้อมูลให้เป็น json
  const notification = event.data.json();

  const options = {
    body: notification.body,
    icon: "assets/icons/152x152.png",
    badge: "assets/icons/96x96.png",
    data: { notifURL: notification.url },
  };

  // แสดงแจ้งเตือน
  event.waitUntil(
    self.registration.showNotification(notification.title, options)
  );
});

// เมื่อมีการคลิกที่แท็บแจ้งเตือนให้เปิด url
self.addEventListener("notificationclick", (event) => {
  event.waitUntil(clients.openWindow(event.notification.data.notifURL));
});

// ติดตั้ง Service Worker และทำการ cache ไฟล์
self.addEventListener("install", (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      contentToCache.forEach((content) => {
        cache
          .add(content)
          .catch((_) => console.error(`Error while caching "${content}"`));
      });
    })
  );
  console.log("Service Worker installed");
});

// จัดการ Fetch request
self.addEventListener("fetch", (event) => {
  if (event.request.mode === "navigate") {
    event.respondWith(
      fetch(event.request).catch(() => {
        return caches.open(CACHE_NAME).then((cache) => {
          return cache.match(OFFLINE_URL);
        });
      })
    );
  }
});

// ลบ Cache เก่าที่ไม่ได้ใช้
self.addEventListener("activate", (event) => {
  const cacheWhitelist = [CACHE_NAME];
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cacheName) => {
          if (!cacheWhitelist.includes(cacheName)) {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});
