const {
  default: makeWASocket,
  downloadContentFromMessage,
  prepareWAMessageMedia,
  generateWAMessageFromContent,
} = require('@onexgen/baileys');
const mime = require('mime-types');
const fs = require('fs');
const { join } = require('path');
const { default: axios } = require('axios');
const { ulid } = require('ulid');

function formatReceipt(phoneNumber) {
  try {
    if (phoneNumber.endsWith('@g.us')) {
      return phoneNumber;
    }
	if (phoneNumber.endsWith('@newsletter')) {
      return phoneNumber;
    }
    let formattedNumber = phoneNumber.replace(/\D/g, '');
    if (formattedNumber.startsWith('08')) {
      formattedNumber = '62' + formattedNumber.substr(1);
    }
	if (formattedNumber.startsWith('00')) {
      formattedNumber = formattedNumber.substr(2);
    }
    if (!formattedNumber.endsWith('@c.us')) {
      formattedNumber += '@c.us';
    }
    return formattedNumber;
  } catch (error) {
    return phoneNumber;
  }
}

async function asyncForEach(array, callback) {
  for (let index = 0; index < array.length; index++) {
    await callback(array[index], index, array);
  }
}

async function removeForbiddenCharacters(inputString) {
  return inputString.replace(/[\x00-\x1F\x7F-\x9F'\\"]/g, '');
}

async function parseIncomingMessage(incomingMessage) {
  const messageType = Object.keys(incomingMessage.message || {})[0];
  let messageContent = '';

  if (messageType === 'conversation' && incomingMessage.message.conversation) {
    messageContent = incomingMessage.message.conversation;
  } else if (messageType == 'imageMessage' && incomingMessage.message.imageMessage.caption) {
    messageContent = incomingMessage.message.imageMessage.caption;
  } else if (messageType == 'videoMessage' && incomingMessage.message.videoMessage.caption) {
    messageContent = incomingMessage.message.videoMessage.caption;
  } else if (messageType == 'extendedTextMessage' && incomingMessage.message.extendedTextMessage.text) {
    messageContent = incomingMessage.message.extendedTextMessage.text;
  } else if (messageType == 'templateButtonReplyMessage' && incomingMessage.message.templateButtonReplyMessage.selectedDisplayText) {
    messageContent = incomingMessage.message.templateButtonReplyMessage?.selectedDisplayText;
  } else if (messageType == 'messageContextInfo' && incomingMessage.message.listResponseMessage?.title) {
    messageContent = incomingMessage.message.listResponseMessage.title;
  } else if (messageType == 'messageContextInfo') {
    messageContent = incomingMessage.message.buttonsResponseMessage?.selectedDisplayText;
  }

  const lowerCaseContent = messageContent?.toLowerCase();
  const sanitizedContent = await removeForbiddenCharacters(lowerCaseContent);
  const pushName = incomingMessage?.pushName || '';
  const senderNumber = incomingMessage.key.remoteJid.split('@')[0];

  let imageBuffer = null;
  if (messageType === 'imageMessage') {
    const imageStream = await downloadContentFromMessage(
      incomingMessage.message.imageMessage,
      'image'
    );
    let buffer = Buffer.from([]);
    for await (const chunk of imageStream) {
      buffer = Buffer.concat([buffer, chunk]);
    }
    imageBuffer = buffer.toString('base64');
  }

  return {
    command: sanitizedContent,
    bufferImage: imageBuffer,
    from: senderNumber,
  };
}

function getSavedPhoneNumber(token) {
  return new Promise((resolve, reject) => {
    const savedPhoneNumber = token;
    if (savedPhoneNumber) {
      setTimeout(() => {
        resolve(savedPhoneNumber);
      }, 2000);
    } else {
      reject(new Error('Nomor telepon tidak ditemukan.'));
    }
  });
}

const prepareMediaMessage = async (socket, mediaOptions) => {
  try {
    const preparedMedia = await prepareWAMessageMedia(
      { [mediaOptions.mediatype]: { url: mediaOptions.media } },
      { upload: socket.waUploadToServer }
    );
    const messageKey = mediaOptions.mediatype + 'Message';

    if (mediaOptions.mediatype === 'document' && !mediaOptions.fileName) {
      const fileNameRegex = /.*\/(.+?)\./;
      const fileNameMatch = fileNameRegex.exec(mediaOptions.media);
      mediaOptions.fileName = fileNameMatch[1];
    }

    let mimetype = mime.lookup(mediaOptions.media);
    if (!mimetype) {
      const response = await axios.head(mediaOptions.media);
      mimetype = response.headers['content-type'];
    }
    if (mediaOptions.media.includes('.cdr')) {
      mimetype = 'application/cdr';
    }

    preparedMedia[messageKey].caption = mediaOptions?.caption;
    preparedMedia[messageKey].mimetype = mimetype;
    preparedMedia[messageKey].fileName = mediaOptions.fileName;

    if (mediaOptions.mediatype === 'video') {
      preparedMedia[messageKey].jpegThumbnail = Uint8Array.from(
        fs.readFileSync(join(process.cwd(), 'public', 'images', 'video-cover.png'))
      );
      preparedMedia[messageKey].gifPlayback = false;
    }

    let userJid = socket.user.id.replace(/:\d+/, '');
    return await generateWAMessageFromContent(
      '',
      { [messageKey]: { ...preparedMedia[messageKey] } },
      { userJid: userJid }
    );
  } catch (prepareError) {
    console.log('error prepare', prepareError);
    return false;
  }
};

class Button {
  constructor(buttonData) {
    this.type = buttonData.type || 'reply'
    this.displayText = buttonData.displayText || ''
    this.id = buttonData.id
    this.url = buttonData.url
    this.copyCode = buttonData.copyCode
    this.phoneNumber = buttonData.phoneNumber
    this.type === 'reply' && !this.id && (this.id = ulid())
    this.mapType = new Map([
      ['reply', 'quick_reply'],
      ['copy', 'cta_copy'],
      ['url', 'cta_url'],
      ['call', 'cta_call'],
    ])
  }
  get ['typeButton']() {
    return this.mapType.get(this.type)
  }
  ['toJSONString']() {
    const stringify = (val) => JSON.stringify(val),
      typeMap = {
        call: () =>
          stringify({
            display_text: this.displayText,
            phone_number: this.phoneNumber,
          }),
        reply: () =>
          stringify({
            display_text: this.displayText,
            id: this.id,
          }),
        copy: () =>
          stringify({
            display_text: this.displayText,
            copy_code: this.copyCode,
          }),
        url: () =>
          stringify({
            display_text: this.displayText,
            url: this.url,
            merchant_url: this.url,
          }),
      }
    return typeMap[this.type]?.() || ''
  }
}

const formatButtonMsg = async (
  buttons,
  footerText,
  bodyText,
  sock,
  imageUrl = null
) => {
  const mediaPrepared = await (async () => {
    if (imageUrl) {
      return await prepareMediaMessage(sock, {
        mediatype: 'image',
        media: imageUrl,
      })
    }
  })()
  return {
    interactiveMessage: {
      carouselMessage: {
        cards: [
          {
            body: {
              text: (() => {
                return bodyText
              })(),
            },
            footer: { text: footerText ?? '..' },
            header: (() => {
              if (mediaPrepared?.message?.imageMessage) {
                return {
                  hasMediaAttachment: !!mediaPrepared.message.imageMessage,
                  imageMessage: mediaPrepared.message.imageMessage,
                }
              }
            })(),
            nativeFlowMessage: {
              buttons: buttons.map((btn) => {
                return {
                  name: btn.typeButton,
                  buttonParamsJson: btn.toJSONString(),
                }
              }),
              messageParamsJson: JSON.stringify({
                from: 'api',
                templateId: ulid(Date.now()),
              }),
            },
          },
        ],
        messageVersion: 1,
      },
    },
  }
}

module.exports = {
  formatReceipt,
  asyncForEach,
  removeForbiddenCharacters,
  parseIncomingMessage,
  getSavedPhoneNumber,
  prepareMediaMessage,
  Button,
  formatButtonMsg,
};