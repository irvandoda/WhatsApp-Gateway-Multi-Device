import mysql2 from "mysql2";
import dotenv from "dotenv";

dotenv.config();

// Create the connection pool. The pool-specific settings are the defaults
const db = mysql2.createPool({
  host: process.env.DB_HOST,
  user: process.env.DB_USERNAME,
  database: process.env.DB_DATABASE,
  password: process.env.DB_PASSWORD,
  port: process.env.DB_PORT || 3306,
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0,
});

const setStatus = (device, status) => {
  try {
    db.query(`UPDATE devices SET status = '${status}' WHERE body = ${device} `);
    return true;
  } catch (error) {
    return false;
  }
};

const updateLastActive = async (device) => {
  try {
    // Update last_active timestamp (column should exist, but handle gracefully if not)
    await dbQuery(`UPDATE devices SET last_active = NOW() WHERE body = '${device}'`);
    return true;
  } catch (error) {
    // If column doesn't exist, try to create it
    if (error?.sqlMessage?.includes('Unknown column') || error?.message?.includes('Unknown column')) {
      try {
        await dbQuery(`ALTER TABLE devices ADD COLUMN last_active TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP`);
        // Retry update after creating column
        await dbQuery(`UPDATE devices SET last_active = NOW() WHERE body = '${device}'`);
        return true;
      } catch (alterError) {
        console.log(`[updateLastActive] Failed to create column for ${device}:`, alterError?.message);
        return false;
      }
    }
    console.log(`[updateLastActive] Error for ${device}:`, error?.message);
    return false;
  }
};

function dbQuery(query) {
  return new Promise((data) => {
    db.query(query, (err, res) => {
      if (err) throw err;
      try {
        data(res);
      } catch (error) {
        data({});
        //throw error;
      }
    });
  });
}

export { setStatus, updateLastActive, dbQuery, db };

// EXPORT
