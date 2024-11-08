<?php
class DatabaseBackup {
    private $host;
    private $username;
    private $password;
    private $database;
    private $backupPath;
    private $conn;

    public function __construct($host, $username, $password, $database, $backupPath = 'backups/') {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
        $this->backupPath = $backupPath;
    }

    private function connect() {
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
            $this->conn->set_charset("utf8");
        } catch (Exception $e) {
            throw new Exception("Connection failed: " . $e->getMessage());
        }
    }

    public function createBackup() {
        try {
            $this->connect();
            
            // Create backup directory if it doesn't exist
            if (!is_dir($this->backupPath)) {
                mkdir($this->backupPath, 0755, true);
            }
    
            // Generate backup filename with timestamp
            $backupFile = $this->backupPath . $this->database . '_' . date('Y-m-d_H-i-s') . '.sql';
            
            // Open file handle
            $handle = fopen($backupFile, 'w+');
            if (!$handle) {
                throw new Exception("Unable to create backup file");
            }
    
            // Write header
            fwrite($handle, "-- Database Backup for {$this->database}\n");
            fwrite($handle, "-- Generated: " . date('Y-m-d H:i:s') . "\n\n");
            fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");
            fwrite($handle, "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n");
            fwrite($handle, "SET time_zone = '+00:00';\n\n");
    
            // Get all tables (except views, only base table)
            $tables = [];
            $result = $this->conn->query("SHOW FULL TABLES WHERE Table_Type = 'BASE TABLE'");
            while ($row = $result->fetch_array()) {
                $tables[] = $row[0];
            }
    
            // Process each table
            foreach ($tables as $table) {
                fwrite($handle, "\n-- Table structure and indexes for table `$table`\n\n");
    
                // Get create table syntax (this includes index definitions)
                $result = $this->conn->query("SHOW CREATE TABLE `$table`");
                $row = $result->fetch_array();
                fwrite($handle, "DROP TABLE IF EXISTS `$table`;\n");
                fwrite($handle, $row[1] . ";\n\n");
    
                // Get and document all indexes separately for reference
                fwrite($handle, "-- Indexes for table `$table`\n");
                $indexes = $this->conn->query("SHOW INDEX FROM `$table`");
                $currentIndex = '';
                $indexColumns = [];
                
                while ($index = $indexes->fetch_assoc()) {
                    if ($currentIndex != $index['Key_name']) {
                        if (!empty($currentIndex)) {
                            $indexType = '';
                            if ($currentIndex == 'PRIMARY') {
                                $indexType = '-- Primary key';
                            } elseif ($index['Non_unique'] == 0) {
                                $indexType = '-- Unique index';
                            } else {
                                $indexType = '-- Index';
                            }
                            fwrite($handle, "$indexType on (" . implode(', ', $indexColumns) . ")\n");
                        }
                        $currentIndex = $index['Key_name'];
                        $indexColumns = [];
                    }
                    $indexColumns[] = $index['Column_name'] . 
                                    ($index['Sub_part'] ? '(' . $index['Sub_part'] . ')' : '') .
                                    ($index['Collation'] == 'D' ? ' DESC' : '');
                }
                
                // Write the last index
                if (!empty($currentIndex)) {
                    $indexType = '';
                    if ($currentIndex == 'PRIMARY') {
                        $indexType = '-- Primary key';
                    } else {
                        $indexType = '-- Index';
                    }
                    fwrite($handle, "$indexType on (" . implode(', ', $indexColumns) . ")\n");
                }
                
                fwrite($handle, "\n-- Dumping data for table `$table`\n");
    
                // Get table data
                $result = $this->conn->query("SELECT * FROM `$table`");
                $numColumns = $result->field_count;
    
                // Get column types for proper escaping
                $columnTypes = [];
                $fieldsResult = $this->conn->query("SHOW COLUMNS FROM `$table`");
                while ($field = $fieldsResult->fetch_assoc()) {
                    $columnTypes[] = $field['Type'];
                }
    
                while ($row = $result->fetch_array(MYSQLI_NUM)) {
                    $values = [];
                    for ($i = 0; $i < $numColumns; $i++) {
                        if ($row[$i] === null) {
                            $values[] = 'NULL';
                        } else {
                            // Handle different data types
                            if (strpos($columnTypes[$i], 'int') !== false || 
                                strpos($columnTypes[$i], 'float') !== false || 
                                strpos($columnTypes[$i], 'double') !== false) {
                                $values[] = $row[$i];
                            } else {
                                $values[] = "'" . $this->conn->real_escape_string($row[$i]) . "'";
                            }
                        }
                    }
                    fwrite($handle, "INSERT INTO `$table` VALUES(" . implode(',', $values) . ");\n");
                }
                fwrite($handle, "\n");
            }
    
            // Write footer
            fwrite($handle, "\nSET FOREIGN_KEY_CHECKS=1;\n");
            
            // Close file handle
            fclose($handle);
    
            // Get file size for the response
            $fileSize = filesize($backupFile);
            $formattedSize = $this->formatFileSize($fileSize);
    
            $this->conn->close();
            return [
                'success' => true,
                'message' => 'Backup created successfully',
                'file' => $backupFile,
                'size' => $formattedSize
            ];
            
        } catch (Exception $e) {
            if ($this->conn) {
                $this->conn->close();
            }
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    private function formatFileSize($bytes) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function dropDatabase() {
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password);
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }

            // Drop the database
            $dropQuery = "DROP DATABASE IF EXISTS `" . $this->conn->real_escape_string($this->database) . "`";
            if (!$this->conn->query($dropQuery)) {
                throw new Exception("Failed to drop database: " . $this->conn->error);
            }
            
            return true;
        } catch (Exception $e) {
            throw new Exception("Error droping database: " . $e->getMessage());
        }
    }

    public function downloadBackup($filePath) {
        if (file_exists($filePath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/sql');
            header('Content-Disposition: attachment; filename=' . basename($filePath));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));
            readfile($filePath);
            
            // Drop database to for a fresh start - intramurals this a.y.
            $this->dropDatabase();
            exit;
        }
        return false;
    }
}

// Handle backup request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Database configuration
    $config = [
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'database' => 'ilps'
    ];

    $backup = new DatabaseBackup(
        $config['host'],
        $config['username'],
        $config['password'],
        $config['database']
    );

    if (isset($_POST['action']) && $_POST['action'] === 'backup') {
        $result = $backup->createBackup();
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

    if (isset($_POST['action']) && $_POST['action'] === 'download' && isset($_POST['file'])) {
        $backup->downloadBackup($_POST['file']);
        exit;
    }
}
?>