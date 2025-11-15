import express from "express";
import path from "path";
import { fileURLToPath } from "url";
import * as cache from "./../lib/cache.js";
import * as controllers from "../controllers/index.js";
import { initialize } from "../whatsapp.js"
import { sendBlastMessage } from "../controllers/blast.js";
import {
  checkDestination,
  checkConnectionBeforeBlast,
} from "../lib/middleware.js";

const router = express.Router();

/**
 * Resolve __dirname untuk ESM
 */
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

/**
 * THIS IS MAIN ROUTER
 */
router.get("/", (req, res) => {
  res.sendFile(path.join(__dirname, "../../public/index.html"));
});

router.post("/backend-logout", controllers.deleteCredentials);
router.post("/backend-generate-qr", controllers.createInstance);
router.post("/backend-initialize", initialize);
router.post(
  "/backend-send-list",
  checkDestination,
  controllers.sendListMessage
);
router.post(
  "/backend-send-template",
  checkDestination,
  controllers.sendTemplateMessage
);
router.post(
  "/backend-send-button",
  checkDestination,
  controllers.sendButtonMessage
);
router.post("/backend-send-media", checkDestination, controllers.sendMedia);
router.post("/backend-send-text", checkDestination, controllers.sendText);
router.post("/backend-send-poll", checkDestination, controllers.sendPoll);
router.post("/backend-send-sticker", checkDestination, controllers.sendSticker);
router.post("/backend-send-available", controllers.sendAvailable);
router.post(
  "/backend-send-location",
  checkDestination,
  controllers.sendLocation
);
router.post("/backend-send-vcard", checkDestination, controllers.sendVcard);
router.post("/backend-getgroups", controllers.fetchGroups);
router.post("/backend-blast", checkConnectionBeforeBlast, sendBlastMessage);
router.post("/backend-logout-device", controllers.logoutDevice);
router.post("/backend-check-number", controllers.checkNumber);

router.post("/backend-clearCache", async (req, res) => {
  const { key, mode } = req.body;
  const allKeys = cache.myCache.keys();
  let keysToDelete = [];

  if (mode === "all") {
    keysToDelete = allKeys;
  } else if (mode === "prefix" && key) {
    keysToDelete = allKeys.filter((k) => k.startsWith(key));
  } else if (mode === "specific" && key) {
    keysToDelete = allKeys.filter((k) => k === key);
  } else {
    console.log("Invalid clear cache request. Mode or key missing/invalid.", {
      mode,
      key,
    });
  }

  if (keysToDelete.length > 0) {
    cache.myCache.del(keysToDelete);
  }

  console.log(`Deleted ${keysToDelete.length} cache keys.`);

  return res.json({
    status: "success",
    deleted: keysToDelete.length,
    deletedKeys: keysToDelete,
  });
});

export default router;
