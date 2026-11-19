import WebSocket from "ws";
import http from "http";
await import("./server.js");           // startet den WS+HTTP-Server in diesem Prozess
const wait=ms=>new Promise(r=>setTimeout(r,ms));
function post(body){return new Promise((res,rej)=>{const data=JSON.stringify(body);
 const req=http.request({host:"localhost",port:8080,path:"/notify",method:"POST",
 headers:{"Content-Type":"application/json","Content-Length":Buffer.byteLength(data)}},
 r=>{let b="";r.on("data",c=>b+=c);r.on("end",()=>res(b));});req.on("error",rej);req.write(data);req.end();});}
const got={u5:[],u2:[]};
function mk(tag,id){const ws=new WebSocket("ws://127.0.0.1:8080");
 ws.on("error",e=>console.log(tag,"wserr",e.code));
 ws.on("open",()=>ws.send(JSON.stringify({type:"register",userId:id})));
 ws.on("message",m=>{try{got[tag].push(JSON.parse(m.toString()));}catch{}});return ws;}
await wait(400);
const c5=mk("u5","5"), c2=mk("u2","2");
await wait(500);
const r1=await post({type:"sold",userId:5,articleName:"DeLorean DMC-12",message:"Großartig! Ihr Artikel DeLorean DMC-12 wurde erfolgreich verkauft!"});
const r2=await post({type:"offer",articleId:1,ownerId:5,articleName:"DeLorean DMC-12",message:"Der Artikel ..."});
await wait(300);
console.log("bridge resp:",r1,r2);
console.log("user5:",JSON.stringify(got.u5));
console.log("user2:",JSON.stringify(got.u2));
console.log("PASS A13 (sold nur User5):", got.u5.some(m=>m.type==="sold") && !got.u2.some(m=>m.type==="sold"));
console.log("PASS A14 (offer an beide):", got.u5.some(m=>m.type==="offer") && got.u2.some(m=>m.type==="offer"));
process.exit(0);
