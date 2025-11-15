import { dbQuery } from "./index.js";
import * as cache from "../lib/cache.js";


const myCache = cache.myCache;

// (5 h)
const CACHE_TTL = 5 * 60 * 60;

// Define cache key prefixes as constants
const CACHE_PREFIX = {
  EQUAL_COMMAND: "equalCommand:",
  CONTAIN_COMMAND: "containCommand:",
  WEBHOOK: "webhook:",
  DEVICE_ALL: "deviceAll:",
  PLUGINS: "plugins-",
};

async function getDeviceIdByBody(number) {
  const devices = await dbQuery(
    `SELECT id FROM devices WHERE body = '${number}' LIMIT 1`
  );
  if (devices.length === 0) return null;
  return devices[0].id;
}

const isExistsEqualCommand = async (command, number) => {
  const cacheKey = CACHE_PREFIX.EQUAL_COMMAND + command + ":" + number;
  if (myCache.has(cacheKey)) {
    return myCache.get(cacheKey);
  }

  const deviceId = await getDeviceIdByBody(number);
  if (!deviceId) return [];

  const data = await dbQuery(
    `SELECT * FROM autoreplies WHERE type_keyword = 'Equal' AND device_id = ${deviceId} AND status = 'Active'`
  );

  const matched = data.find((row) => {
    const keywords = row.keyword.split("|").map((k) => k.trim().toLowerCase());
    return keywords.includes(command.trim().toLowerCase());
  });

  if (!matched) return [];

  myCache.set(cacheKey, [matched], CACHE_TTL);
  return [matched];
};

const isExistsContainCommand = async (command, number) => {
  const cacheKey = CACHE_PREFIX.CONTAIN_COMMAND + command + ":" + number;
  if (myCache.has(cacheKey)) {
    return myCache.get(cacheKey);
  }

  const deviceId = await getDeviceIdByBody(number);
  if (!deviceId) return [];

  const data = await dbQuery(
    `SELECT * FROM autoreplies WHERE LOCATE(keyword, "${command}") > 0 AND type_keyword = 'Contain' AND device_id = ${deviceId} AND status = 'Active' LIMIT 1`
  );

  if (data.length === 0) return [];
  myCache.set(cacheKey, data, CACHE_TTL);
  return data;
};

const getUrlWebhook = async (number) => {
  const cacheKey = CACHE_PREFIX.WEBHOOK + number;
  if (myCache.has(cacheKey)) {
    return myCache.get(cacheKey);
  }

  let url = null;
  const data = await dbQuery(
    `SELECT webhook FROM devices WHERE body = '${number}' LIMIT 1`
  );
  if (data.length > 0) {
    url = data[0].webhook;
  }
  myCache.set(cacheKey, url, CACHE_TTL);
  return url;
};

const getDevice = async (deviceBody) => {
  const cacheKey = CACHE_PREFIX.DEVICE_ALL + deviceBody;
  if (myCache.has(cacheKey)) {
    return myCache.get(cacheKey);
  }

  const deviceResult = await dbQuery(
    `SELECT * FROM devices WHERE body = '${deviceBody}' LIMIT 1`
  );

  const deviceAll = deviceResult.length > 0 ? deviceResult : null;
  myCache.set(cacheKey, deviceAll, CACHE_TTL);
  return deviceAll;
};

const getActivePluginsByDevice = async (deviceId) => {
  const cacheKey = CACHE_PREFIX.PLUGINS + deviceId;
  if (myCache.has(cacheKey)) {
    return myCache.get(cacheKey);
  }

  const plugins = await dbQuery(
    `SELECT * FROM plugins WHERE device_id = ${deviceId} AND is_active = 1`
  );
  myCache.set(cacheKey, plugins, CACHE_TTL);
  return plugins;
};

export  {
  isExistsEqualCommand,
  isExistsContainCommand,
  getUrlWebhook,
  getDevice,
  getActivePluginsByDevice,
};
