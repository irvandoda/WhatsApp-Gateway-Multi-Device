import axios from "axios";

export const sendWebhook = async ({
  device,
  command,
  media,
  from,
  name,
  url,
  participant,
  ppUrl,
}) => {
  try {
    const data = {
      device,
      message: command,
      media,
      from,
      name,
      participant,
      ppUrl,
    };

    console.log("forward to", url);

    const res = await axios.post(url, data, {
      headers: { "Content-Type": "application/json" },
      maxContentLength: 100 * 5024 * 1024,
      maxBodyLength: 100 * 1024 * 1024,
    });

    return res.data;
  } catch (error) {
    console.log("Webhook error:", error);
    return false;
  }
};
