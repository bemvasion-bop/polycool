/**
 * STRICT OFFLINE SYNC ENGINE
 * Blocks real saving in offline mode
 */

// In-memory queue from LocalStorage
let offlineQueue = JSON.parse(localStorage.getItem("offlineQueue") || "[]");

// Popup UI message
function popup(msg, color = "#c62828") {
    const div = document.createElement("div");
    div.style.position = "fixed";
    div.style.top = "12px";
    div.style.right = "12px";
    div.style.background = color;
    div.style.color = "white";
    div.style.padding = "10px 16px";
    div.style.borderRadius = "8px";
    div.style.fontSize = "14px";
    div.style.zIndex = 99999;
    div.innerText = msg;
    document.body.appendChild(div);
    setTimeout(() => div.remove(), 2500);
}

// Save to offline queue only
function saveOffline(action, data) {
    const id = "temp_" + Date.now();

    offlineQueue.push({
        id,
        action,
        data,
        timestamp: new Date().toISOString(),
    });

    localStorage.setItem("offlineQueue", JSON.stringify(offlineQueue));
    popup("Saved offline. Will sync when you're online.", "#1565c0");
}

/**
 * 🔄 Main Sync Processor
 * Identifies the correct API endpoint per action
 */
async function processQueueItem(item) {
    let url = null;
    let method = "POST";

    switch (item.action) {
        case "create_client":
            url = "/clients";
            break;

        // Future actions:
        // case "create_payment": url = "/payments"; break;
        // case "create_expense": url = "/expenses"; break;
        // etc...
    }

    if (!url) {
        console.log("Unknown action: ", item.action);
        return false;
    }

    try {
        const res = await fetch(url, {
            method,
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(item.data),
        });

        if (!res.ok) throw new Error("Bad response");

        return true;
    } catch (err) {
        console.error("Sync failed:", err);
        return false;
    }
}

/**
 * 🚀 Sync Handler
 */
async function syncOfflineQueue() {
    if (offlineQueue.length === 0) return;

    console.log("🔄 Attempting to sync offline items...");

    let newQueue = [];

    for (const item of offlineQueue) {
        const success = await processQueueItem(item);
        if (!success) newQueue.push(item);
    }

    offlineQueue = newQueue;

    if (offlineQueue.length === 0) {
        localStorage.removeItem("offlineQueue");
        popup("✔ All offline data synced!", "#2e7d32");
    } else {
        localStorage.setItem("offlineQueue", JSON.stringify(offlineQueue));
        popup("⚠ Some items failed to sync. Will retry.", "#ff9800");
    }
}

/**
 * 👂 Auto Sync Triggers
 */
window.addEventListener("online", syncOfflineQueue);
window.addEventListener("load", () => {
    if (navigator.onLine) syncOfflineQueue();
});

// Expose functions
window.saveOffline = saveOffline;
window.syncOfflineQueue = syncOfflineQueue;
