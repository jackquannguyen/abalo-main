process.on("uncaughtException",e=>console.log("UNCAUGHT",e.message));
console.log("before import");
await import("./server.js");
console.log("after import");
await new Promise(r=>setTimeout(r,1000));
import("http").then(({default:http})=>{
  const req=http.request({host:"localhost",port:8080,path:"/notify",method:"POST"},r=>{
    let b="";r.on("data",c=>b+=c);r.on("end",()=>{console.log("SELF-CURL resp:",b);process.exit(0);});});
  req.on("error",e=>{console.log("SELF-CURL ERR",e.code);process.exit(1);});
  req.end('{"message":"x"}');
});
