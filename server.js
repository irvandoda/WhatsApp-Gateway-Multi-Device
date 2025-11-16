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
import https from "https";
import path from "path";

let server;
/**
 * Try to auto-detect SSL certificate and key.
 * Priority:
 * 1) Explicit env SSL_KEY_PATH + SSL_CERT_PATH
 * 2) Local files placed by generator: ./key.pem + ./cert.pem
 * 3) Let's Encrypt default path: /etc/letsencrypt/live/<host>/{privkey,fullchain}.pem (host from APP_URL)
 */
const resolveSslOptions = () => {
  const candidates = [];
  // 1) Explicit env paths
  if (process.env.SSL_KEY_PATH && process.env.SSL_CERT_PATH) {
    candidates.push({
      key: process.env.SSL_KEY_PATH,
      cert: process.env.SSL_CERT_PATH,
      label: "ENV_PATHS",
    });
  }
  // 2) Local generated files (by SettingController::generateSslCertificate)
  candidates.push({
    key: path.resolve(process.cwd(), "key.pem"),
    cert: path.resolve(process.cwd(), "cert.pem"),
    label: "LOCAL_PEMS",
  });
  // 3) Let's Encrypt default path
  const appUrl = process.env.APP_URL || "";
  try {
    const urlObj = new URL(appUrl.startsWith("http") ? appUrl : `https://${appUrl}`);
    const host = urlObj.hostname;
    if (host && host !== "localhost") {
      candidates.push({
        key: `/etc/letsencrypt/live/${host}/privkey.pem`,
        cert: `/etc/letsencrypt/live/${host}/fullchain.pem`,
        label: "LETSENCRYPT",
      });
    }
  } catch (_) {
    // ignore bad APP_URL
  }
  for (const c of candidates) {
    try {
      if (fs.existsSync(c.key) && fs.existsSync(c.cert)) {
        return {
          options: {
            key: fs.readFileSync(c.key),
            cert: fs.readFileSync(c.cert),
          },
          source: c.label,
        };
      }
    } catch (_) {}
  }
  return null;
};

const explicitHttps = process.env.SSL_ENABLED === "true";
let ssl = null;
if (explicitHttps) {
  // Respect explicit HTTPS request; prefer env paths, then auto-detect fallbacks
  if (process.env.SSL_KEY_PATH && process.env.SSL_CERT_PATH) {
    try {
      ssl = {
        options: {
          key: fs.readFileSync(process.env.SSL_KEY_PATH),
          cert: fs.readFileSync(process.env.SSL_CERT_PATH),
        },
        source: "ENV_PATHS",
      };
    } catch (e) {
      console.error("[server] Failed to read SSL from ENV_PATHS:", e?.message);
    }
  }
  if (!ssl) {
    ssl = resolveSslOptions();
  }
} else {
  // If not explicitly disabled (i.e., SSL_ENABLED !== 'false'), try auto
  if (process.env.SSL_ENABLED !== "false") {
    ssl = resolveSslOptions();
  }
}

if (ssl) {
  server = https.createServer(ssl.options, app);
  console.log(`[server] HTTPS enabled via ${ssl.source}`);
} else {
  server = http.createServer(app);
  console.log("[server] HTTP enabled (no SSL certs detected)");
}

/**
 * SOCKET.IO
 */
import { Server } from "socket.io";
const io = new Server(server, {
  pingInterval: 25000,
  pingTimeout: 10000,
  cors: process.env.CORS_ORIGIN
    ? {
        origin: process.env.CORS_ORIGIN.split(",").map((s) => s.trim()),
        credentials: true,
      }
    : undefined,
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
