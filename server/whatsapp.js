import { Boom } from "@hapi/boom";
import makeWASocket, {
  fetchLatestBaileysVersion,
  useMultiFileAuthState,
  makeCacheableSignalKeyStore,
  DisconnectReason,
} from "@whiskeysockets/baileys";

import { getDevice } from "./database/model.js";
import QRCode from "qrcode";
import fs from "fs";

import { sock, qrcode, pairingCode, intervalStore } from "./wa/store.js";
import { setStatus } from "./database/index.js";
import { IncomingMessage } from "./controllers/incomingMessage.js";
import { getSavedPhoneNumber } from "./lib/helper.js";

import MAIN_LOGGER from "./lib/pino.js";
import NodeCache from "node-cache";
import { release } from "os";

const logger = MAIN_LOGGER.child({});
const msgRetryCounterCache = new NodeCache();

const connectToWhatsApp = async (token, io = null, viaOtp = false) => {
  if (typeof qrcode[token] !== "undefined" && !viaOtp) {
    io?.emit("qrcode", {
      token,
      data: qrcode[token],
      message: "please scan with your Whatsapp Accountt",
    });

    return {
      status: false,
      sock: sock[token],
      qrcode: qrcode[token],
      message: "please scan",
    };
  }
  if (typeof pairingCode[token] !== "undefined" && viaOtp) {
    io?.emit("code", {
      token,
      data: pairingCode[token],
      message:
        "Go to whatsapp -> link device -> link with phone number, and pairing with this code.",
    });
    return {
      status: false,
      code: pairingCode[token],
      message: "pairing with that code",
    };
  }
  try {
    let number = sock[token].user.id.split(":");
    number = number[0] + "@s.whatsapp.net";
    const ppUrl = await getPpUrl(token, number);
    io?.emit("connection-open", {
      token,
      user: sock[token].user,
      ppUrl,
    });
    delete qrcode[token];
    delete pairingCode[token];
    return { status: true, message: "Already connected" };
  } catch (error) {
    io?.emit("message", {
      token,
      message: `Connecting.. (1)..`,
    });
  }
  //

  const { version, isLatest } = await fetchLatestBaileysVersion();
  console.log(
    "You re using whatsapp gateway M Pedia v8.x.x - Contact admin if any trouble : 6292298859671"
  );
  console.log(`using WA v${version.join(".")}, isLatest: ${isLatest}`);
  // check or create credentials
  const { state, saveCreds } = await useMultiFileAuthState(
    `./credentials/${token}`
  );

  sock[token] = makeWASocket({
    version: version,
    browser: ["Windows", "Chrome", release()],
    logger,
    printQRInTerminal: !viaOtp,

    auth: {
      creds: state.creds,
      keys: makeCacheableSignalKeyStore(state.keys, logger),
    },

    msgRetryCounterCache,
    generateHighQualityLinkPreview: true,
  });

  if (viaOtp && "me" in state.creds === false && !state.creds.registered) {
    const phoneNumber = await getSavedPhoneNumber(token);

    try {
      const code = await sock[token].requestPairingCode(phoneNumber);
      pairingCode[token] = code?.match(/.{1,4}/g)?.join("-") || code;
      console.log("pairing code", code);
    } catch (error) {
      io?.emit("message", {
        token,
        message: "Time out, please refresh page",
      });
    }
    io?.emit("code", {
      token,
      data: pairingCode[token],
      message:
        "Go to whatsapp -> link device -> link with phone number, and pairing with this code.",
    });
  }

  sock[token].ev.process(async (events) => {
    if (events["connection.update"]) {
      const update = events["connection.update"];
      const { connection, lastDisconnect, qr } = update;
      console.log("connection", update);

      if (connection === "close") {
        const ErrorMessage = lastDisconnect.error?.output?.payload?.message;
        const ErrorType = lastDisconnect.error?.output?.payload?.error;

        if (
          (lastDisconnect?.error instanceof Boom)?.output?.statusCode !==
          DisconnectReason.loggedOut
        ) {
          delete qrcode[token];
          io?.emit("message", { token: token, message: "Connecting.." });
          // when refs qr attemts end
          if (ErrorMessage == "QR refs attempts ended") {
            sock[token].ws.close();
            delete qrcode[token];
            delete pairingCode[token];
            delete sock[token];
            //   clearConnection(token);
            io?.emit("message", {
              token,
              message: "Request QR ended. reload web to scan again",
            });
            return;
          }
          // ahwtsapp disconnect but still have session folder,should be delete
          if (
            ErrorType === "Unauthorized" ||
            ErrorType === "Method Not Allowed"
          ) {
            setStatus(token, "Disconnect");
            clearConnection(token);
            connectToWhatsApp(token, io);
          }
          if (ErrorMessage === "Stream Errored (restart required)") {
            connectToWhatsApp(token, io);
          }

          if (ErrorMessage === "Connection was lost") {
            delete sock[token];
          }
        } else {
          setStatus(token, "Disconnect");
          console.log("Connection closed. You are logged out.");
          io?.emit("message", {
            token,
            message: "Connection closed. You are logged out.",
          });
          clearConnection(token);
          connectToWhatsApp(token, io);
        }
      }

      if (qr) {
        // SEND TO YOUR CLIENT SIDE
        console.log("new qr", token);
        QRCode.toDataURL(qr, function (err, url) {
          if (err) console.log(err);
          qrcode[token] = url;
          connectToWhatsApp(token, io, viaOtp);
        });
      }
      if (connection === "open") {
        setStatus(token, "Connected");
        delete qrcode[token];
        delete pairingCode[token];
        let number = sock[token].user.id.split(":");
        number = number[0] + "@s.whatsapp.net";
        const ppUrl = await getPpUrl(token, number);

        io?.emit("connection-open", {
          token,
          user: sock[token].user,
          ppUrl,
        });
        delete qrcode[token];
        delete pairingCode[token];
      }
    }

    if (events["creds.update"]) {
      const creds = events["creds.update"];
      saveCreds(creds);
    }

    if (events["messages.upsert"]) {
      const messages = events["messages.upsert"];
     
    
      const reply = await IncomingMessage(messages, sock[token]);
    }
  });

  sock[token].ev?.on("call", async (node) => {
    const getDeviceWa = await getDevice(sock[token].user.id.split(":")[0]);
    const reject_call = getDeviceWa[0].reject_call;

    if (reject_call === 1) {
      const { from, id, status } = node[0];
      if (status == "offer") {
        const sendresult = {
          tag: "call",
          attrs: {
            from: sock[token].user.id,
            to: from,
            id: sock[token].generateMessageTag(),
          },
          content: [
            {
              tag: "reject",
              attrs: {
                "call-id": id,
                "call-creator": from,
                count: "0",
              },
              content: undefined,
            },
          ],
        };
        await sock[token].query(sendresult);
      }
    }
  });

  return {
    sock: sock[token],
    qrcode: qrcode[token],
  };
};
//
async function connectWaBeforeSend(token) {
  let status = undefined;
  let connect;
  connect = await connectToWhatsApp(token);

  await connect.sock.ev.on("connection.update", (con) => {
    const { connection, qr } = con;
    if (connection === "open") {
      status = true;
    }
    if (qr) {
      status = false;
    }
  });
  let counter = 0;
  while (typeof status === "undefined") {
    counter++;
    if (counter > 4) {
      break;
    }
    await new Promise((resolve) => setTimeout(resolve, 1000));
  }

  return status;
}

//set available
const sendAvailable = async (body) => {
  const getDeviceAll = await getDevice(body);

  let sendAvailableResult;
  try {
    if (getDeviceAll[0].set_available == 1) {
      sendAvailableResult = await sock[body].sendPresenceUpdate("available");
    } else {
      sendAvailableResult = await sock[body].sendPresenceUpdate("unavailable");
    }
    return sendAvailableResult;
  } catch (error) {
    return false;
  }
};

// fetch group

async function fetchGroups(token) {
  // check is exists token
  try {
    let getGroups = await sock[token].groupFetchAllParticipating();
    let groups = Object.entries(getGroups)
      .slice(0)
      .map((entry) => {
    
        return entry[1];
      });

    return groups;
  } catch (error) {
    return false;
  }
}

// if exist
async function isExist(token, number) {
  try {
    if (typeof sock[token] === "undefined") {
      const status = await connectWaBeforeSend(token);
      if (!status) {
        return false;
      }
    }
    if (number.includes("@g.us")) {
      return true;
    } else {
      const [result] = await sock[token].onWhatsApp("+" + number);
      return number.length > 11 ? result : true;
    }
  } catch (error) {
    return false;
  }
}

// ppUrl
async function getPpUrl(token, number, highrest) {
  let ppUrl;
  try {
    ppUrl = await sock[token].profilePictureUrl(number);
    return ppUrl;
  } catch (error) {
    return "https://upload.wikimedia.org/wikipedia/commons/thumb/6/6b/WhatsApp.svg/1200px-WhatsApp.svg.png";
  }
}

// close connection
async function deleteCredentials(token, io = null) {
  if (io !== null) {
    io.emit("message", { token: token, message: "Logout Progres.." });
  }
  try {
    if (typeof sock[token] === "undefined") {
      const status = await connectWaBeforeSend(token);
      if (status) {
        sock[token].logout();
        delete sock[token];
      }
    } else {
      sock[token].logout();
      delete sock[token];
    }
    delete qrcode[token];
    clearInterval(intervalStore[token]);
    setStatus(token, "Disconnect");

    if (io != null) {
      io.emit("Unauthorized", token);
      io.emit("message", {
        token: token,
        message: "Connection closed. You are logged out.",
      });
    }
    if (fs.existsSync(`./credentials/${token}`)) {
      fs.rmSync(
        `./credentials/${token}`,
        { recursive: true, force: true },
        (err) => {
          if (err) console.log(err);
        }
      );
    }

    return {
      status: true,
      message: "Deleting session and credential",
    };
  } catch (error) {
    console.log(error);
    return {
      status: true,
      message: "Nothing deleted",
    };
  }
}

function clearConnection(token) {
  clearInterval(intervalStore[token]);

  delete sock[token];
  delete qrcode[token];
  setStatus(token, "Disconnect");
  if (fs.existsSync(`./credentials/${token}`)) {
    fs.rmSync(
      `./credentials/${token}`,
      { recursive: true, force: true },
      (err) => {
        if (err) console.log(err);
      }
    );
    console.log(`credentials/${token} is deleted`);
  }
}

async function initialize(req, res) {
  const { token } = req.body;
  if (token) {
    const fs = require("fs");
    const path = `./credentials/${token}`;
    if (fs.existsSync(path)) {
      sock[token] = undefined;
      const status = await connectWaBeforeSend(token);
      if (status) {
        return res
          .status(200)
          .json({ status: true, message: `${token} connection restored` });
      } else {
        //   setStatus(token, "Disconnect");
        return res
          .status(200)
          .json({ status: false, message: `${token} connection failed` });
      }
    }
    return res.send({
      status: false,
      message: `${token} Connection failed,please scan first`,
    });
  }
  return res.send({ status: false, message: "Wrong Parameterss" });
}

// delay send message

export * from "./wa/sender.js";

export {
  connectToWhatsApp,
  isExist,
  getPpUrl,
  fetchGroups,
  deleteCredentials,
  initialize,
  connectWaBeforeSend,
  sendAvailable,
};
