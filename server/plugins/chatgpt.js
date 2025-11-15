import OpenAI from "openai"
import { manageMessagesCache } from "./pluginHelper.js"

async function openaiPlugin(context) {
  const { command, from, plugin, device: fullDevice } = context;
  const device = fullDevice?.[0]?.body;
  const message = command;
  if (!message) return { handled: false };

  try {
    const openai = new OpenAi({ apiKey: plugin.main_data });

    // Ambil dataset (prompt awal) dari extra_data
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

    const systemPrompt = dataset
      ? {
          role: "system",
          content: `Berikut adalah data yang bisa kamu pelajari:\n\n${dataset}`,
        }
      : null;

    const history = await manageMessagesCache(device, from, "user", message);
    const messages = systemPrompt ? [systemPrompt, ...history] : history;

    const completion = await openai.chat.completions.create({
      messages,
      model: "gpt-3.5-turbo",
      max_tokens: 150,
    });

    const text = completion.choices[0].message.content;

    await manageMessagesCache(device, from, "assistant", text, false);

    return {
      handled: true,
      reply: { text },
    };
  } catch (error) {
    console.error("[OpenAI Plugin Error]", error.message);
    return { handled: false };
  }
}

export default openaiPlugin;
