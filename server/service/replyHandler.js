import { ulid } from "ulid";
import { formatButtonMsg, Button } from "../dto/button.js";
import { formatListMsg, Section } from "../dto/list.js";
import { prepareMediaMessage } from "../lib/helper.js";
export const handleMediaReply = async (reply, sock, msg, quoted) => {
  const ownerJid = sock.user.id.replace(/:\d+/, "");

  if (reply.type === "audio") {
    return sock.sendMessage(msg.key.remoteJid, {
      audio: { url: reply.url },
      ptt: true,
      mimetype: "audio/mpeg",
    });
  }

  const generate = await prepareMediaMessage(sock, {
    caption: reply.caption || "",
    fileName: reply.filename,
    media: reply.url,
    mediatype: ["video", "image"].includes(reply.type)
      ? reply.type
      : "document",
  });

  const message = { ...generate.message };

  return sock.sendMessage(
    msg.key.remoteJid,
    {
      forward: {
        key: { remoteJid: ownerJid, fromMe: true },
        message,
      },
    },
    { quoted: quoted ? msg : null }
  );
};

export const handleButtonReply = async (reply, sock, msg) => {
  const btns = reply.buttons.map((btn) => new Button(btn));
  const message = formatButtonMsg(
    btns,
    reply.footer,
    reply.text || reply.caption,
    sock,
    reply.image?.url
  );
  const msgId = ulid(Date.now());
  return sock.relayMessage(msg.key.remoteJid, message, { messageId: msgId });
};

export const handleListReply = async (reply, sock, msg) => {
  const sections = reply.sections.map((sect) => new Section(sect));
  const message = formatListMsg(
    sections,
    reply.footer || "..",
    reply.text || reply.caption,
    sock,
    reply.image?.url
  );
  const msgId = ulid(Date.now());
  return sock.relayMessage(msg.key.remoteJid, message, { messageId: msgId });
};

export const handleTextReply = async (reply, sock, msg, quoted) => {
  console.log("otw send");
  return sock.sendMessage(msg.key.remoteJid, reply, {
    quoted: quoted ? msg : null,
  });
};

export const getPpUrlFromSock = async (sock, msg) => {
  try {
    const id =
      msg.key.participant && msg.key.participant.trim() !== ""
        ? msg.key.participant
        : msg.key.remoteJid;

    return await sock.profilePictureUrl(id);
  } catch (error) {
    console.log("Failed to get PP:", error);
    return "https://upload.wikimedia.org/wikipedia/commons/thumb/6/6b/WhatsApp.svg/1200px-WhatsApp.svg.png";
  }
};
