import { GoogleGenerativeAI } from "@google/generative-ai";
import { manageMessagesCache } from "./pluginHelper.js";



async function geminiAiPlugin(context) {
  const { command, from, plugin, device: fullDevice } = context;
  const device = fullDevice?.[0]?.body;
  const message = command;
  if (!message) return { handled: false };
  try {
    const genAi = new GoogleGenerativeAI(plugin.main_data);
    const model = genAi.getGenerativeModel({ model: "gemini-1.5-flash" });

    // Ambil prompt & dataset dari extra_data

    let dataset = "";
    try {
      const extra =
        typeof plugin.extra_data === "string"
          ? JSON.parse(plugin.extra_data)
          : plugin.extra_data;

      dataset = extra.dataset || "";
    } catch (err) {
      console.warn("Invalid extra_data format");
    }

    // Bangun history dengan prompt + dataset di awal
    const systemContext = `\n\n here is the data ,you can learn from this dataset :${dataset}`;
    const rawHistory = await manageMessagesCache(device, from, "user", message);

    // Konversi ke format Gemini dengan role valid
    const formattedHistory = [
      { role: "user", parts: [{ text: systemContext }] },
      ...rawHistory.map((msg) => {
        const geminiRole = msg.role === "assistant" ? "model" : msg.role;
        return {
          role: geminiRole,
          parts: [{ text: msg.content }],
        };
      }),
    ];

    const chat = model.startChat({
      history: formattedHistory,
      generationConfig: { maxOutputTokens: 100 },
    });

    const result = await chat.sendMessage(message);
    const text = result.response.text();

    await manageMessagesCache(device, from, "model", text, true);

    return {
      handled: true,
      reply: { text },
    };
  } catch (error) {
    console.error("[Gemini Plugin Error]", error.message);
    return { handled: false };
  }
}

export default geminiAiPlugin;
