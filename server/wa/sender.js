import { formatReceipt, delayMsg, prepareMediaMessage } from "../lib/helper.js";
import { sock } from "./store.js";
import { Sticker, StickerTypes } from "wa-sticker-formatter";
import { Button, formatButtonMsg } from "../dto/button.js";
import { ulid } from "ulid";
import { Section, formatListMsg } from "../dto/list.js";

// text message
const sendText = async (token, number, text, delay = 0) => {
  try {
    await delayMsg(delay * 1000, sock[token], number);
    const sendingTextMessage = await sock[token].sendMessage(
      formatReceipt(number),
      { text: text }
    );

    return sendingTextMessage;
  } catch (error) {
    console.log(error);

    return false;
  }
};
const sendMessage = async (token, number, msg, delay = 0) => {
  try {
    await delayMsg(delay * 1000, sock[token], number);
    const sendingTextMessage = await sock[token].sendMessage(
      formatReceipt(number),
      JSON.parse(msg)
    );
    return sendingTextMessage;
  } catch (error) {
    return false;
  }
};

async function sendMedia(
  token,
  destination,
  type,
  url,
  caption,
  ptt,
  filename,
  delay = 0
) {
  const number = formatReceipt(destination);
  let ownerJid = sock[token].user.id.replace(/:\d+/, "");

  //for vn
  if (type == "audio") {
    return await sock[token].sendMessage(number, {
      audio: { url: url },
      ptt: true,
      mimetype: "audio/mpeg",
    });
  }
  // for send media ( document/video or image)
  const generate = await prepareMediaMessage(sock[token], {
    caption: caption ? caption : "",
    fileName: filename,
    media: url,
    mediatype: type !== "video" && type !== "image" ? "document" : type,
  });
  const message = { ...generate.message };

  await delayMsg(delay * 1000, sock[token], number);

  return await sock[token].sendMessage(number, {
    forward: {
      key: { remoteJid: ownerJid, fromMe: true },
      message: message,
    },
  });
}

// button message
async function sendButtonMessage(
  token,
  number,
  button,
  message,
  footer,
  image = null
) {
  /**
   * type is "url" or "local"
   * if you use local, you must upload into src/public/temp/[fileName]
   */
  let type = "url";
  const msg = message;
  try {
    const buttons = button.map((x, i) => {
      return new Button(x);
    });
    const message = await formatButtonMsg(
      buttons,
      footer,
      msg,
      sock[token],
      image
    );
    const msgId = ulid(Date.now());
    const sendMsg = await sock[token].relayMessage(
      formatReceipt(number),
      message,
      { messageId: msgId }
    );
    return sendMsg;
  } catch (error) {
    console.log(error);
    return false;
  }
}

async function sendTemplateMessage(token, number, button, text, footer, image) {
  try {
    if (image) {
      var buttonMessage = {
        caption: text,
        footer: footer,
        viewOnce: true,
        templateButtons: button,
        image: { url: image },
        viewOnce: true,
      };
    } else {
      var buttonMessage = {
        text: text,
        footer: footer,
        viewOnce: true,

        templateButtons: button,
      };
    }

    const sendMsg = await sock[token].sendMessage(
      formatReceipt(number),
      buttonMessage
    );
    return sendMsg;
  } catch (error) {
    console.log(error);
    return false;
  }
}

// list message
async function sendListMessage(
  token,
  number,
  list,
  text,
  footer,
  title,
  buttonText,
  image = null
) {
  try {
    const sections = list.map((sect) => new Section(sect));

    const listMsg = await formatListMsg(
      sections,
      footer,
      text,
      sock[token],
      image
    );

    const msgId = ulid(Date.now());
    const sendMsg = await sock[token].relayMessage(
      formatReceipt(number),
      listMsg,
      { messageId: msgId }
    );
    return sendMsg;
  } catch (error) {
    console.log(error);
    return false;
  }
}

async function sendPollMessage(token, number, name, options, countable) {
  try {
    const sendmsg = await sock[token].sendMessage(formatReceipt(number), {
      poll: {
        name: name,
        values: options,
        selectableCount: countable,
      },
    });

    return sendmsg;
  } catch (error) {
    console.log(error);
    return false;
  }
}

async function sendLocation(waToken, recipient, latitude, longitude) {
  try {
    await delayMsg(1000, sock[waToken], recipient);
    const sendLocationResult = await sock[waToken].sendMessage(
      formatReceipt(recipient),
      {
        location: { degreesLatitude: latitude, degreesLongitude: longitude },
      }
    );
    return sendLocationResult;
  } catch (error) {
    return false;
  }
}
async function sendVcard(waToken, recipient, name, phone) {
  try {
    const vcard =
      "BEGIN:VCARD\n" + // metadata of the contact card
      "VERSION:3.0\n" +
      "FN:" +
      name +
      "\n" + // full name
      "TEL;type=CELL;type=VOICE;waid=" +
      phone +
      ":+" +
      phone +
      "\n" + // WhatsApp ID + phone number
      "END:VCARD";
    delayMsg(1000, sock[waToken], recipient);
    const sendLocationResult = await sock[waToken].sendMessage(
      formatReceipt(recipient),
      {
        contacts: {
          displayName: name,
          contacts: [{ vcard }],
        },
      }
    );
    return sendLocationResult;
  } catch (error) {
    return false;
  }
}
async function sendSticker(
  waToken,
  recipient,
  mediaType,
  mediaPath,
  message,
  fileName
) {
  const formattedRecipient = formatReceipt(recipient);
  let userId = sock[waToken].user.id.replace(/:\d+/, "");
  const sticker = new Sticker(mediaPath, {
    pack: "",
    author: "",
    type: StickerTypes.FULL,
    quality: 50,
  });
  const buffer = await sticker.toBuffer();
  await sticker.toFile("sticker.webp");
  return await sock[waToken].sendMessage(
    formattedRecipient,
    await sticker.toMessage()
  );
}

export {
  sendText,
  sendMedia,
  sendButtonMessage,
  sendTemplateMessage,
  sendListMessage,
  sendPollMessage,
  sendMessage,
  sendLocation,
  sendVcard,
  sendSticker,
};
