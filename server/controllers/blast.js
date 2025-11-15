import { dbQuery } from "../database/index.js";
import { formatReceipt } from "../lib/helper.js";
import * as wa from "../whatsapp.js";


let inProgress = [];

const updateStatus = async (campaignId, receiver, status) => {
  await dbQuery(
    `UPDATE blasts SET status = '${status}' WHERE receiver = '${receiver}' AND campaign_id = '${campaignId}'`
  );
};
const updateStatusById = async (campaignId, id, status) => {
  await dbQuery(
    `UPDATE blasts SET status = '${status}', updated_at = NULL WHERE id = '${id}' AND campaign_id = '${campaignId}'`
  );
};

const checkBlastById = async (campaignId, id) => {
  const checkBlast = await dbQuery(
    `SELECT status FROM blasts WHERE id = '${id}' AND campaign_id = '${campaignId}'`
  );
  return checkBlast.length > 0 && checkBlast[0].status === "pending";
};
const sendBlastMessage = async (req, res) => {
  const data = JSON.parse(req.body.data);
  const dataBlast = data.data;
  const campaignId = data.campaign_id;

  const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

  if (inProgress[campaignId]) {
    console.log(
      `still any progress in campaign id ${campaignId}, request canceled. `
    );
    return res.send({ status: "in_progress" });
  }

  inProgress[campaignId] = true;
  console.log(`progress campaign ID : ${campaignId} started`);

  // Send the "in_progress" status immediately
  res.send({ status: "in_progress" });

  const send = async () => {
    for (let i in dataBlast) {
      const delay = data.delay;
      await sleep(delay * 1000);

      if (data.sender && dataBlast[i].receiver && dataBlast[i].message) {
        const isValid = await checkBlastById(campaignId, dataBlast[i].id);
        if (isValid) {
          try {
            const check = await wa.isExist(
              data.sender,
              formatReceipt(dataBlast[i].receiver)
            );
            if (!check) {
              await updateStatusById(campaignId, dataBlast[i].id, "failed");
              continue;
            }
          } catch (error) {
            console.error("Error in wa.isExist: ", error);
            await updateStatusById(campaignId, dataBlast[i].id, "failed");
            continue;
          }

          // start send blast
          console.log(
            `sending to ${dataBlast[i].receiver} id ${dataBlast[i].id}`
          );
          try {
            let sendingTextMessage;
            // MEDIA MESSAGE
            if (data.type === "media") {
              const fileDetail = JSON.parse(dataBlast[i].message);
              sendingTextMessage = await wa.sendMedia(
                data.sender,
                dataBlast[i].receiver,
                fileDetail.type,
                fileDetail.url,
                fileDetail.caption,
                0,
                fileDetail.filename,
                0
              );
              // BUTTON MESSAGE
            } else if (data.type === "button") {
              const msg = JSON.parse(dataBlast[i].message);

              sendingTextMessage = await wa.sendButtonMessage(
                data.sender,
                dataBlast[i].receiver,
                msg.buttons,
                msg.text ?? msg.caption,
                msg.footer,
                msg?.image?.url
              );
            } else {
              //TEST MSG
              sendingTextMessage = await wa.sendMessage(
                data.sender,
                dataBlast[i].receiver,
                dataBlast[i].message,
                0
              );
            }

            const status = sendingTextMessage ? "success" : "failed";
            await updateStatusById(campaignId, dataBlast[i].id, status);
          } catch (error) {
            console.log("anyerror", error);
            if (error.message.includes("503")) {
              console.log(
                "Server is busy, waiting for 5 seconds before retrying..."
              );
              await sleep(5000); // Wait for 5 seconds
              i--; // Decrement the counter to retry the current message
            } else {
              await updateStatusById(campaignId, dataBlast[i].id, "failed");
            }
          }
        } else {
          console.log("no pending, not send!");
        }
      } else {
        console.log("wrong data, progress canceled!");
      }
    }

    delete inProgress[campaignId];
  };

  send().catch((error) => {
    console.error(`Error in send operation: ${error}`);
    delete inProgress[campaignId];
  });
};

export  {
  sendBlastMessage,
};
