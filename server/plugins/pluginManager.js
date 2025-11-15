import { getActivePluginsByDevice } from "../database/model.js";
import stickerPlugin from "./botsticker.js";
import openaiPlugin from "./chatgpt.js";
import claudeAiPlugin from "./claudeai.js";
import geminiAiPlugin from "./geminiAi.js";
import {
  checkContact,
  saveContact,
  deleteMessageCache,
  removeContact,
} from "./pluginHelper.js";
import googleSheetSearchPlugin from "./spreadsheet.js";
import spreadSheetSaveData from "./spreadsheet-savedata.js";

const AI_PLUGIN_UUIDS = ["gemini", "chatgpt", "claude"];
const pluginMap = {
  spreadsheet: googleSheetSearchPlugin,
  gemini: geminiAiPlugin,
  chatgpt: openaiPlugin,
  sticker: stickerPlugin,
  claude: claudeAiPlugin,
  "spreadsheet-input": spreadSheetSaveData,
};
const runPlugins = async (context) => {
  try {
    const msg = context.msg;
    const plugins = await getActivePluginsByDevice(context.device?.[0]?.id);

    if (!plugins.length) return null;
    //only active plugin

    const activePluginsRaw = plugins.filter((p) => p.is_active === 1);
    const aiPlugins = activePluginsRaw.filter((p) =>
      AI_PLUGIN_UUIDS.includes(p.uuid)
    );
    const nonAiPlugins = activePluginsRaw.filter(
      (p) => !AI_PLUGIN_UUIDS.includes(p.uuid)
    );

    // random ai plugins if active more than 1
    let chosenAiPlugins = [];
    if (aiPlugins.length > 0) {
      const randomIndex = Math.floor(Math.random() * aiPlugins.length);
      chosenAiPlugins = [aiPlugins[randomIndex]];
    }

    // Gabungkan: plugin AI acak + plugin lainnya
    const activePlugins = [...nonAiPlugins, ...chosenAiPlugins];

    for (const pluginData of activePlugins) {
      const pluginFn = pluginMap[pluginData.uuid];

      if (!pluginFn) {
        console.log(`[Plugin Missing] UUID not found: ${pluginData.uuid}`);
        continue;
      }
      //check typebot,respon to all,gorup or personal
      const typeBot = pluginData?.typeBot;

      const isReplyNeeded =
        typeBot.toLowerCase() === "all" ||
        (typeBot.toLowerCase() === "group" &&
          msg.key.remoteJid.includes("@g.us")) ||
        (typeBot.toLowerCase() === "personal" &&
          !msg.key.remoteJid.includes("@g.us"));

      if (!isReplyNeeded) continue;

      // ⏩ AI preprocessing
      if (AI_PLUGIN_UUIDS.includes(pluginData.uuid)) {
        const preprocessResult = await preprocessAIPlugin(context, pluginData);
        if (preprocessResult === false) continue;
        if (typeof preprocessResult === "object" && preprocessResult.handled) {
          return preprocessResult;
        }
      }

      const enrichedContext = { 
        ...context,
        plugin: {
          uuid: pluginData.uuid,
          name: pluginData.name,
          main_data: pluginData.main_data,
          extra_data: pluginData.extra_data,
        },
      };

      try {
        const result = await pluginFn(enrichedContext);
        if (result && result.handled && result.reply) {
          return {
            ...result,
          };
        }
      } catch (err) {
        console.log(`[Plugin Error - ${pluginData.uuid}]`, err);
      }
    }
  } catch (err) {
    console.log("[runPlugins Error]", err);
  }

  return null;
};

const preprocessAIPlugin = async (context, pluginData) => {
  const { command, from, device } = context;

  const currentDevice = device?.[0]?.body;
  const extra =
    typeof pluginData.extra_data === "string"
      ? JSON.parse(pluginData.extra_data)
      : pluginData.extra_data;

  const commandStart = extra.command_start?.trim()?.toLowerCase();
  const commandStop = extra.command_stop?.trim()?.toLowerCase();
  const input = command.trim().toLowerCase();

  if (commandStart && commandStop) {
    const isRegistered = await checkContact(from, currentDevice);

    if (input === commandStart) {
      if (!isRegistered) await saveContact(from, currentDevice);
      return {
        handled: true,
        reply: {
          text: isRegistered
            ? "AI telah diaktifkan sebelumnya."
            : "✅ AI aktif. Selamat menggunakan!",
        },
      };
    }

    if (input === commandStop) {
      if (isRegistered) await removeContact(from, currentDevice);
      await deleteMessageCache(currentDevice, from);
      return {
        handled: true,
        reply: { text: "🛑 AI berhenti. Terima kasih!" },
      };
    }

    if (!isRegistered) return false;

    return true; //  proses AI
  }

  return true;
};

export { runPlugins };
