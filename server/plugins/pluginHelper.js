import NodeCache from "node-cache"

const cache = new NodeCache();

// TTL Constants
const TTL = {
  CONTACTS: 60 * 60, // 1 h
  MESSAGES: 20 * 60, // 20 m
};

// ------------------- CONTACT -------------------

const loadContact = async (device) => {
  const contacts = cache.get(`contacts:${device}`);
  return contacts || [];
};

const saveContact = async (from, device) => {
  const contact = { from };
  let contacts = await loadContact(device);
  contacts.push(contact);
  cache.set(`contacts:${device}`, contacts, TTL.CONTACTS);
};

const checkContact = async (from, device) => {
  const contacts = await loadContact(device);
  return contacts.some((contact) => contact.from === from);
};

const removeContact = async (from, device) => {
  let contacts = await loadContact(device);
  const contactsNew = contacts.filter((contact) => contact.from !== from);
  cache.set(`contacts:${device}`, contactsNew, TTL.CONTACTS);
};

// ------------------- MESSAGE CACHE -------------------
const VALID_ROLES = ["user", "assistant", "system"];

const manageMessagesCache = async (device, number, role, content) => {
  const key = `messages:${device}:${number}`;
  const safeRole = VALID_ROLES.includes(role) ? role : "assistant";

  const msgs = cache.get(key) || [];
  const messages = [...msgs, { role: safeRole, content }];
  cache.set(key, messages, TTL.MESSAGES);

  return messages;
};

const deleteMessageCache = async (device, number) => {
  const key1 = `messages:${device}:${number}`;
  const key2 = `messages:${device}:${number}`;

  cache.del([key1, key2]);
};

// ------------------- EXPORT -------------------

export {
  loadContact,
  saveContact,
  checkContact,
  removeContact,
  manageMessagesCache,
  deleteMessageCache,
};
