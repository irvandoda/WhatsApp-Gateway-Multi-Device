import Anthropic from "@anthropic-ai/sdk";
import { manageMessagesCache } from "./pluginHelper.js";

async function claudeAiPlugin(context) {
  const { command, from, plugin, device: fullDevice } = context;
  const device = fullDevice?.[0]?.body;
  const message = command;
  if (!message) return { handled: false };

  try {
    const anthropic = new Anthropic({
      apiKey: plugin.main_data,
    });

    // Ambil dataset dari extra_data
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

    // Ambil history pesan
    const history = await manageMessagesCache(
      device,
      from,
      "user",
      message,
      false
    );

    // Format pesan untuk Claude API
    const messages = history.map((msg) => ({
      role: msg.role === "assistant" ? "assistant" : "user",
      content: msg.content,
    }));

    // Tambahkan sistem prompt jika ada dataset
    const systemPrompt = dataset
      ? `Berikut adalah data yang bisa kamu pelajari:\n\n${dataset}`
      : undefined;

    const response = await anthropic.messages.create({
      model: "claude-3-haiku-20240307", // atau claude-3-sonnet-20240229
      max_tokens: 150,
      system: systemPrompt,
      messages: messages,
    });

    const text = response.content[0].text;

    await manageMessagesCache(device, from, "assistant", text, false);

    return {
      handled: true,
      reply: { text },
    };
  } catch (error) {
    console.error("[Claude Plugin Error]", error.message);
    return { handled: false };
  }
}

export default claudeAiPlugin;
