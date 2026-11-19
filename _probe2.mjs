import http from "http";
process.on("uncaughtException",e=>console.log("UNCAUGHT",e.message,e.code));
await import("./server.js");
await new Promise(r=>setTimeout(r,1200));
const req=http.request({host:"127.0.0.1",port:8080,path:"/notify",method:"POST"},r=>{let b="";r.on("data",c=>b+=c);r.on("end",()=>{console.log("server.js SELF-CURL:",b);process.exit(0);});});
req.on("error",e=>console.log("server.js SELF-CURL ERR",e.code));
req.end('{"message":"x"}');
