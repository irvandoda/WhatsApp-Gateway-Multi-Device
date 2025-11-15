"use strict";

import pino from "pino";

const logger = pino({
  transport: {
    target: "pino-pretty",
    options: {
      translateTime: "SYS:standard",
      ignore: "hostname,pid",
      singleLine: false,
      colorize: true,
      levelFirst: true,
      append: true, // file dibuka dengan flag 'a'
    },
  },
  // level: process.env.NODE_ENV === 'production' ? 'info' : 'debug'
  level: "info",
});

export default logger;
