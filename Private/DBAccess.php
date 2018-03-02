<?php

/**
 * Accesses a CSV file with DB access credentials for the application and
 * obtains a database connection. Provides connection methods for both
 * PDO and MySQLi.
 *
 * @author JAM
 */
class DBAccess {



    public static function getMysqliConnection() {
        try {
            $dbCreds = self::getDBCredentials();
            $db = new mysqli($dbCreds[1], $dbCreds[2], $dbCreds[3], $dbCreds[4], (int) $dbCreds[5]);
            return $db;
        } catch (Exception $e) {
            $error = $e->getMessage();
            return null;
        }
    }

    public static function closeConnection($db) {
        if ($db instanceof mysqli) {
            $db->close();
        }
        return null;
    }

    private static function getDBCredentials() {
        $dbAccessFilePath = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/login-system2/Private/access/access.csv';
        $dbAccessFile = fopen($dbAccessFilePath, 'rb');
        $dbAccessParams = fgetcsv($dbAccessFile, 0, ",");
        fclose($dbAccessFile);
        return $dbAccessParams;
    }

}
