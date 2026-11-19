import { spawn } from "child_process";
import WebSocket from "ws";
import http from "http";

const ABALO = "/sessions/ecstatic-blissful-heisenberg/mnt/abalo";
const srv = spawn("node", ["server.js"], { cwd: ABALO });
srv.stdout.on("data", d => process.stdout.write("[server] " + d));
srv.stderr.on("data", d => process.stdout.write("[server-err] " + d));

const wait = ms => new Promise(r => setTimeout(r, ms));

function post(body) {
    return new Promise((res, rej) => {
        const data = JSON.stringify(body);
        const req = http.request({ host: "localhost", port: 8080, path: "/notify", method: "POST",
            headers: { "Content-Type": "application/json", "Content-Length": Buffer.byteLength(data) } },
            r => { let b = ""; r.on("data", c => b += c); r.on("end", () => res(b)); });
        req.on("error", rej); req.write(data); req.end();
    });
}

const got = { u5: [], u2: [] };
function mkClient(tag, userId) {
    const ws = new WebSocket("ws://127.0.0.1:8080");
    ws.on("open", () => ws.send(JSON.stringify({ type: "register", userId })));
    ws.on("message", m => {
        const s = m.toString();
        try { const j = JSON.parse(s); got[tag].push(j); } catch { /* "Connected" */ }
    });
    return ws;
}

await wait(600);
const c5 = mkClient("u5", "5");
const c2 = mkClient("u2", "2");
await wait(400);

// Aufgabe 13: targeted to user 5
await post({ type: "sold", userId: 5, articleName: "DeLorean DMC-12", message: "Großartig! Ihr Artikel DeLorean DMC-12 wurde erfolgreich verkauft!" });
await wait(300);

// Aufgabe 14: broadcast (no userId)
await post({ type: "offer", articleId: 1, ownerId: 5, articleName: "DeLorean DMC-12", message: "Der Artikel DeLorean DMC-12 wird nun günstiger angeboten! Greifen Sie schnell zu." });
await wait(300);

console.log("\n--- RESULT ---");
console.log("user5 received:", JSON.stringify(got.u5));
console.log("user2 received:", JSON.stringify(got.u2));

const soldOnlyTo5 = got.u5.some(m => m.type === "sold") && !got.u2.some(m => m.type === "sold");
const offerToBoth = got.u5.some(m => m.type === "offer") && got.u2.some(m => m.type === "offer");
console.log("PASS Aufgabe13 (sold nur an User5):", soldOnlyTo5);
console.log("PASS Aufgabe14 (offer an alle):", offerToBoth);

c5.close(); c2.close(); srv.kill();
process.exit(0);
