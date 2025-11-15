import * as wa from "./server/whatsapp.js";
import fs from "fs";
import * as dbs from "./server/database/index.js";
import dotenv from "dotenv";
dotenv.config();
import * as lib from "./server/lib/index.js";
global.log = lib.log;


/**
 * EXPRESS FOR ROUTING
 */
import express from "express";
const app = express();
import http from "http";
const server = http.createServer(app);

/**
 * SOCKET.IO
 */
import { Server } from "socket.io";
const io = new Server(server, {
  pingInterval: 25000,
  pingTimeout: 10000,
});

const port = process.env.PORT_NODE;

app.get("/", (req, res) => {
  return res.redirect("/home");
});
app.use((req, res, next) => {
  res.set("Cache-Control", "no-store");
  req.io = io;
  next();
});

import bodyParser from "body-parser";


app.use(
  bodyParser.urlencoded({
    extended: false,
    limit: "50mb",
    parameterLimit: 100000,
  })
);

app.use(bodyParser.json());
app.use(express.static("src/public"));
import router from "./server/router/index.js"

app.use(router);

io.on("connection", (socket) => {
  console.log("A user connected");

  socket.on("StartConnection", (data) => {
    wa.connectToWhatsApp(data, io);
  });

  socket.on("ConnectViaCode", (data) => {
    wa.connectToWhatsApp(data, io, true);
  });

  socket.on("LogoutDevice", (device) => {
    wa.deleteCredentials(device, io);
  });

  socket.on("disconnect", () => {
    console.log("A user disconnected");
  });
});

server.listen(port, () => {
  console.log(`Server running and listening on port: ${port}`);
});
