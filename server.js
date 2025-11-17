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
 * Try to auto-detect SSL certificate and key based on APP_URL host.
 * Priority:
 * 1) Explicit env SSL_KEY_PATH + SSL_CERT_PATH
 * 2) Let's Encrypt path: /etc/letsencrypt/live/<host>/{privkey,fullchain}.pem
 * 3) Local LE-style files at project root: ./privkey.pem + ./fullchain.pem
 * 4) Local generic pems at project root: ./key.pem + ./cert.pem
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
  // Parse APP_URL host
  let appHost = null;
  const appUrl = process.env.APP_URL || "";
  try {
    const urlObj = new URL(appUrl.startsWith("http") ? appUrl : `https://${appUrl}`);
    appHost = urlObj.hostname;
  } catch (_) {
    // ignore malformed APP_URL
  }
  // 2) Let's Encrypt default path for the specific host
  if (appHost && appHost !== "localhost") {
    const leKey = `/etc/letsencrypt/live/${appHost}/privkey.pem`;
    const leCert = `/etc/letsencrypt/live/${appHost}/fullchain.pem`;
    candidates.push({
      key: leKey,
      cert: leCert,
      label: `LETSENCRYPT_LIVE_${appHost}`,
    });
  }
  // 3) Local Let's Encrypt style names placed at project root
  candidates.push({
    key: path.resolve(process.cwd(), "privkey.pem"),
    cert: path.resolve(process.cwd(), "fullchain.pem"),
    label: "LOCAL_LE_STYLE",
  });
  // 4) Local generated files (by SettingController::generateSslCertificate)
  candidates.push({
    key: path.resolve(process.cwd(), "key.pem"),
    cert: path.resolve(process.cwd(), "cert.pem"),
    label: "LOCAL_PEMS",
  });
  // Pick the first available pair
  for (const c of candidates) {
    try {
      if (fs.existsSync(c.key) && fs.existsSync(c.cert)) {
        console.log(`[server][ssl] Using certs from ${c.label}: key=${c.key} cert=${c.cert}`);
        return {
          options: {
            key: fs.readFileSync(c.key),
            cert: fs.readFileSync(c.cert),
          },
          source: c.label,
        };
      }
    } catch (e) {
      console.error(`[server][ssl] Failed reading ${c.label} certs:`, e?.message);
    }
  }
  console.warn("[server][ssl] No valid SSL certificate paths detected among candidates.");
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
const deriveCorsOrigins = () => {
  const origins = new Set();
  // 1) Explicit list
  if (process.env.CORS_ORIGIN) {
    process.env.CORS_ORIGIN.split(",").map((s) => s.trim()).forEach((o) => origins.add(o));
  }
  // 2) APP_URL
  const appUrl = process.env.APP_URL;
  if (appUrl) {
    try {
      const url = new URL(appUrl.startsWith("http") ? appUrl : `https://${appUrl}`);
      origins.add(`${url.protocol}//${url.host}`);
    } catch (_) {}
  }
  // 3) WA_URL_SERVER
  const waUrl = process.env.WA_URL_SERVER;
  if (waUrl) {
    try {
      const w = new URL(waUrl.startsWith("http") ? waUrl : `https://${waUrl}`);
      origins.add(`${w.protocol}//${w.host}`);
    } catch (_) {}
  }
  return origins.size > 0 ? Array.from(origins) : undefined;
};
const io = new Server(server, {
  pingInterval: 25000,
  pingTimeout: 10000,
  cors: deriveCorsOrigins()
    ? {
        origin: deriveCorsOrigins(),
        credentials: true,
      }
    : undefined,
});

const port = process.env.PORT_NODE || process.env.PORT || 3100;

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

// --- HTTP fallback endpoints for QR/status when Socket.IO fails on client ---
import { qrcode as qrStore, pairingCode as codeStore, sock as sockStore } from "./server/wa/store.js";
import { getPpUrl as getProfileUrl } from "./server/whatsapp.js";

// Kick-off connection (idempotent)
app.post("/ws/start/:token", async (req, res) => {
  try {
    const token = req.params.token;
    wa.connectToWhatsApp(token, req.io);
    return res.json({ ok: true });
  } catch (e) {
    return res.status(500).json({ ok: false, error: e?.message || "start_failed" });
  }
});

// Get latest QR or pairing code
app.get("/ws/qrcode/:token", async (req, res) => {
  const token = req.params.token;
  const data = qrStore[token] || codeStore[token];
  if (!data) {
    return res.status(404).json({ ok: false });
  }
  return res.json({ ok: true, token, data, message: "please scan" });
});

// Get connection status and profile if connected
app.get("/ws/status/:token", async (req, res) => {
  const token = req.params.token;
  try {
    const s = sockStore[token];
    if (s && s.user) {
      let number = s.user.id.split(":");
      number = number[0] + "@s.whatsapp.net";
      const ppUrl = await getProfileUrl(token, number);
      return res.json({ ok: true, connected: true, user: s.user, ppUrl });
    }
    return res.json({ ok: true, connected: false });
  } catch (e) {
    return res.status(500).json({ ok: false, error: e?.message || "status_failed" });
  }
});

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
