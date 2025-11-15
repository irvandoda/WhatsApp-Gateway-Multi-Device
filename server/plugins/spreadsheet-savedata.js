import { google } from "googleapis";
import NodeCache from "node-cache";

const sessionCache = new NodeCache({ stdTTL: 3600 });

function askNextQuestion(dataMap, answers) {
  for (const key in dataMap) {
    if (!answers[key]) {
      return { key, question: dataMap[key] };
    }
  }
  return null;
}

async function appendToSheet(
  googleKey,
  spreadsheetId,
  defaultHeaders,
  finalData
) {
  const creds = JSON.parse(googleKey);
  const auth = new google.auth.GoogleAuth({
    credentials: creds,
    scopes: ["https://www.googleapis.com/auth/spreadsheets"],
  });

  const client = await auth.getClient();
  const sheets = google.sheets({ version: "v4", auth: client });

  // Ambil header dari Sheet1 baris pertama
  const res = await sheets.spreadsheets.values.get({
    spreadsheetId,
    range: "Sheet1!1:1",
  });

  let sheetHeaders = res.data.values?.[0] || [];

  // Kalau belum ada header, pakai defaultHeaders lalu update sheet
  if (sheetHeaders.length === 0) {
    sheetHeaders = defaultHeaders;

    await sheets.spreadsheets.values.update({
      spreadsheetId,
      range: "Sheet1!A1",
      valueInputOption: "USER_ENTERED",
      requestBody: { values: [sheetHeaders] },
    });
  }

  // Susun data sesuai header
  const rowData = sheetHeaders.map((key) => finalData[key] ?? "");

  // Append ke sheet
  await sheets.spreadsheets.values.append({
    spreadsheetId,
    range: "Sheet1!A1",
    valueInputOption: "USER_ENTERED",
    insertDataOption: "INSERT_ROWS",
    requestBody: {
      values: [rowData],
    },
  });
}

// Main Bot
async function spreadSheetSaveData(context) {
  const { command, plugin, device, media, from, senderName } = context;
  const currentDevice = device[0].body;
  const sessionKey = `${currentDevice}:${from}`;
  const extra = JSON.parse(plugin.extra_data || "{}");

  const {
    googlekey,
    basickey = "time|from|name|media",
    data_map,
    commandstart: commandStart = plugin.main_data,
    finishmessage = "Data berhasil disimpan, terima kasih!",
  } = extra;

  const sheet_id = plugin.main_data;

  if (command === commandStart) {
    const firstKey = Object.keys(data_map)[0];
    sessionCache.set(sessionKey, {
      step: "asking",
      answers: {},
      lastKey: firstKey,
    });

    return {
      handled: true,
      reply: { text: data_map[firstKey] },
    };
  }

  const session = sessionCache.get(sessionKey);
  if (!session) return { handled: false };

  if (session.lastKey) {
    session.answers[session.lastKey] = command;
  }

  const next = askNextQuestion(data_map, session.answers);

  if (!next) {
    const basickeySplit = basickey.split("|");
    const finalData = {
      ...session.answers,
      [basickeySplit[0] || "time"]: new Date().toISOString(),
      [basickeySplit[1] || "from"]: from,
      [basickeySplit[2] || "name"]: senderName || "",
      [basickeySplit[3] || "media"]: "--" || "",
    };

    const defaultHeaders = [
      ...new Set([...basickey.split("|"), ...Object.keys(data_map)]),
    ];

    try {
      await appendToSheet(googlekey, sheet_id, defaultHeaders, finalData);
    } catch (err) {
      console.error("❌ Gagal simpan ke Google Sheet:", err.message);
    }

    sessionCache.del(sessionKey);

    return {
      handled: true,
      reply: { text: finishmessage },
    };
  }

  session.lastKey = next.key;
  sessionCache.set(sessionKey, session);

  return {
    handled: true,
    reply: { text: next.question },
  };
}

export default spreadSheetSaveData;
