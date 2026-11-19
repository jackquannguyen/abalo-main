import { WebSocketServer } from "ws";
import http from "http";

// =====================================================================
// WebSocket-Server fuer Abalo (Meilenstein 5)
//
// Dieser eine Server kann zwei Dinge:
//   1) WebSocket: Browser verbinden sich (ws://127.0.0.1:8080)
//   2) HTTP:      Laravel schickt per POST /notify eine Nachricht hierher,
//                 die dann ueber den WebSocket an die Browser verteilt wird.
//
// So funktioniert Aufgabe 13 und 14:
//   - Browser meldet sich an  -> sendet {type:"register", userId:"5"}
//   - Laravel ruft /notify auf -> wir leiten die Nachricht weiter:
//       * mit userId  -> NUR an diesen Nutzer (Aufgabe 13)
//       * ohne userId -> an ALLE (Broadcast, Aufgabe 14)
// =====================================================================

// Merkt sich, welcher Nutzer welche Verbindung(en) hat: userId -> Set<ws>
const usersById = new Map();

// ---------------------------------------------------------------------
// 1) HTTP-Server. Laravel ruft hier POST /notify auf.
// ---------------------------------------------------------------------
const server = http.createServer((req, res) => {
    if (req.method === "POST" && req.url === "/notify") {
        let body = "";
        req.on("data", (chunk) => {
            body += chunk;
            if (body.length > 65536) req.destroy();
        });
        req.on("end", () => {
            try {
                const data = JSON.parse(body);
                if (!data || typeof data !== 'object') throw new Error('Invalid JSON object');

                if (data.userId) {
                    // Aufgabe 13: nur an einen bestimmten Nutzer
                    sendToUser(String(data.userId), data);
                } else {
                    // Aufgabe 14: an alle Betrachter:innen
                    broadcast(data);
                }

                res.writeHead(200, { "Content-Type": "application/json" });
                res.end(JSON.stringify({ ok: true }));
            } catch (error) {
                res.writeHead(400, { "Content-Type": "application/json" });
                res.end(JSON.stringify({ ok: false, error: String(error) }));
            }
        });
        return;
    }

    res.writeHead(404);
    res.end();
});

// ---------------------------------------------------------------------
// 2) WebSocket-Server haengt am selben HTTP-Server (gleicher Port 8080).
// ---------------------------------------------------------------------
const wss = new WebSocketServer({ server });

wss.on("connection", function connection(ws) {
    console.log("Client connected");

    // Wie in Aufgabe 11: einfache Begruessung als Klartext.
    ws.send("Connected");

    ws.on("message", function message(raw) {
        const text = raw.toString();

        let data = null;
        try {
            data = JSON.parse(text);
        } catch (e) {
            data = null; // war kein JSON, also normaler Text
        }

        // Browser meldet seine User-ID an (fuer Aufgabe 13).
        if (data && data.type === "register" && data.userId) {
            ws.userId = String(data.userId);
            if (!usersById.has(ws.userId)) {
                usersById.set(ws.userId, new Set());
            }
            usersById.get(ws.userId).add(ws);
            console.log(`User ${ws.userId} registriert`);
            return;
        }

        // Offer notifications must be issued by Laravel after verifying ownership.
        // Browser messages remain available for the original text-chat exercises.
        if (data && (data.type === 'offer' || data.type === 'sold')) {
            console.warn('Rejected client-originated business notification');
            return;
        }

        // Sonst: normaler Text wird an alle verteilt (Aufgabe 11 & 12).
        console.log(`Received message: ${text}`);
        broadcast(text);
    });

    ws.on("close", function close() {
        // Verbindung aus der User-Liste entfernen.
        if (ws.userId && usersById.has(ws.userId)) {
            usersById.get(ws.userId).delete(ws);
            if (usersById.get(ws.userId).size === 0) {
                usersById.delete(ws.userId);
            }
        }
        console.log("Client disconnected");
    });
});

// Nachricht an ALLE verbundenen Browser.
function broadcast(payload) {
    const text = typeof payload === "string" ? payload : JSON.stringify(payload);
    wss.clients.forEach(function each(client) {
        if (client.readyState === 1 /* WebSocket.OPEN */) {
            client.send(text);
        }
    });
}

// Nachricht NUR an einen bestimmten Nutzer (alle seine offenen Tabs).
function sendToUser(userId, payload) {
    const text = JSON.stringify(payload);
    const sockets = usersById.get(String(userId));

    if (!sockets || sockets.size === 0) {
        console.log(`Kein verbundener Client fuer User ${userId}`);
        return;
    }

    sockets.forEach(function each(client) {
        if (client.readyState === 1 /* WebSocket.OPEN */) {
            client.send(text);
        }
    });
}

server.listen(8080, "127.0.0.1", () => {
    console.log("WebSocket + HTTP-Bridge laeuft auf Port 8080");
});
