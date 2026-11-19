import { WebSocketServer } from "ws";
import http from "http";
console.log("WebSocketServer is", typeof WebSocketServer);
const server = http.createServer((req,res)=>res.end("x"));
server.on("error", e=>console.log("SERVER ERR", e.code));
const wss = new WebSocketServer({ server });
console.log("wss created", typeof wss);
server.listen(8080, ()=>console.log("LISTENING"));
setTimeout(()=>process.exit(0), 1500);
