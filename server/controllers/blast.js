const { dbQuery } = require('../database'),
  { formatReceipt } = require('../lib/helper'),
  wa = require('../whatsapp')

const campaignQueues = new Map()

const scheduleCampaignExecution = (campaignId, task) => {
  const previous = campaignQueues.get(campaignId) ?? Promise.resolve()
  const hasRunningJob = campaignQueues.has(campaignId)

  const nextJob = previous
    .catch(() => {})
    .then(task)
    .catch(error => {
      console.error(`Failed to process campaign ${campaignId}:`, error)
    })
    .finally(() => {
      if (campaignQueues.get(campaignId) === nextJob) {
        campaignQueues.delete(campaignId)
      }
    })

  campaignQueues.set(campaignId, nextJob)

  return !hasRunningJob
}

const updateStatus = async (campaignId, receiver, status) => {
  await dbQuery(
    "UPDATE blasts SET status = '" +
      status +
      "' WHERE receiver = '" +
      receiver +
      "' AND campaign_id = '" +
      campaignId +
      "'"
  )
}

const checkBlast = async (campaignId, receiver) => {
  const result = await dbQuery(
    "SELECT status FROM blasts WHERE receiver = '" +
      receiver +
      "' AND campaign_id = '" +
      campaignId +
      "'"
  )
  return result.length > 0 && result[0].status === 'pending'
}

const sendBlastMessage = async (req, res) => {
  let parsedData

  try {
    parsedData = JSON.parse(req.body.data)
  } catch (error) {
    return res.status(400).send({ status: false, message: 'Invalid payload' })
  }

  const { campaign_id: campaignId, data: messageData } = parsedData || {}

  if (!campaignId) {
    return res.status(400).send({ status: false, message: 'Missing campaign identifier' })
  }

  if (!Array.isArray(messageData) || messageData.length === 0) {
    return res.send({ status: 'in_progress', queued: false, processed: 0 })
  }

  const processCampaign = async () => {
    const delay = ms => new Promise(resolve => setTimeout(resolve, ms))

    const parsedMinDelay = Number(parsedData.delay)
    const minDelay = Number.isFinite(parsedMinDelay) ? Math.max(0, parsedMinDelay) : 0
    const parsedMaxDelay = Number(parsedData.delay_max)
    const maxDelay = Number.isFinite(parsedMaxDelay) ? Math.max(minDelay, parsedMaxDelay) : minDelay

    for (let index = 0; index < messageData.length; index++) {
      const item = messageData[index]

      if (!item) {
        continue
      }

      const shouldDelay = maxDelay > 0 || minDelay > 0
      if (shouldDelay) {
        const delayRange = maxDelay > minDelay ? maxDelay - minDelay + 1 : 1
        const delaySec = maxDelay > minDelay
          ? Math.floor(Math.random() * delayRange) + minDelay
          : minDelay
        if (delaySec > 0) {
          await delay(delaySec * 1000)
        }
      }

      if (!parsedData.sender || !item.receiver || !item.message) {
        continue
      }

      const blastStillPending = await checkBlast(campaignId, item.receiver)
      if (!blastStillPending) {
        continue
      }

      try {
        const exists = await wa.isExist(parsedData.sender, formatReceipt(item.receiver))
        if (!exists) {
          await updateStatus(campaignId, item.receiver, 'failed')
          continue
        }
      } catch {
        await updateStatus(campaignId, item.receiver, 'failed')
        continue
      }

      try {
        let sendResult

        if (parsedData.type === 'media') {
          const mediaMessage = JSON.parse(item.message)
          if (mediaMessage.caption && mediaMessage.caption.trim() !== '') {
            if (mediaMessage.footer && mediaMessage.footer.trim() !== '') {
              mediaMessage.caption = `${mediaMessage.caption}\n\n> _${mediaMessage.footer}_`
              delete mediaMessage.footer
            }
          } else if (mediaMessage.footer && mediaMessage.footer.trim() !== '') {
            mediaMessage.caption = `> _${mediaMessage.footer}_`
            delete mediaMessage.footer
          }
          sendResult = await wa.sendMedia(
            parsedData.sender,
            item.receiver,
            mediaMessage.type,
            mediaMessage.url,
            mediaMessage.caption,
            0,
            mediaMessage.viewonce,
            mediaMessage.filename
          )
        } else if (parsedData.type === 'sticker') {
          const stickerMessage = JSON.parse(item.message)
          sendResult = await wa.sendSticker(
            parsedData.sender,
            item.receiver,
            stickerMessage.type,
            stickerMessage.url,
            stickerMessage.filename
          )
        } else if (parsedData.type === 'button') {
          const buttonData = JSON.parse(item.message)
          const buttons = buttonData.buttons.map(buttonRawData => {
            const raw = buttonRawData.buttonText?.displayText || {}
            return {
              type: raw.type || 'reply',
              displayText: raw.displayText,
              id: buttonRawData.buttonId,
              phoneNumber: raw.phoneNumber,
              url: raw.url,
              copyCode: raw.copyCode
            }
          })
          sendResult = await wa.sendButtonMessage(
            parsedData.sender,
            item.receiver,
            buttons,
            buttonData.caption || buttonData.text || '',
            buttonData.footer,
            buttonData.image?.url
          )
        } else {
          const msg = JSON.parse(item.message)
          if (msg.text && msg.footer && msg.text.trim() !== '') {
            msg.text = wa.randomizeText(`${msg.text}\n\n> _${msg.footer}_`)
            delete msg.footer
          }
          sendResult = await wa.sendMessage(
            parsedData.sender,
            item.receiver,
            msg
          )
        }

        const status = sendResult ? 'success' : 'failed'
        await updateStatus(campaignId, item.receiver, status)
      } catch (sendError) {
        if (sendError?.message?.includes('503')) {
          await delay(5000)
          index--
        } else {
          await updateStatus(campaignId, item.receiver, 'failed')
        }
      }
    }
  }

  const startedImmediately = scheduleCampaignExecution(campaignId, processCampaign)

  res.send({ status: 'in_progress', queued: !startedImmediately })
}

module.exports = { sendBlastMessage }