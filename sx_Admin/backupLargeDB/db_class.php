<?php

class ps_Export_DB
{
    var $max_rows;
    var $dir;
    var $backup_foder;
    var $db_Name;
    var $con;
    var $backup_path;
    var $output;
    var $name_Suffix;

    /**
     * Initializing constructor
     */
    public function __construct($dir = null, $backup_foder = null, $name_Suffix = '', $db_Name = null, $max_rows = null)
    {
        if (empty($dir)) $dir = '.';
        if (empty($db_Name)) $db_Name = sx_TABLE_SCHEMA;
        if (empty($backup_foder)) $backup_foder = $$db_Name;
        if (empty($max_rows)) $max_rows = 5000;
        $this->dir = $dir;
        $this->backup_foder = $backup_foder;
        $this->db_Name = $db_Name;
        $this->max_rows = $max_rows;
        $this->con  = dbconn();
        $this->backup_path = "{$this->dir}\\{$this->backup_foder}_" . date("Y_m_d_H_i") . $name_Suffix;
        $this->output = '';
    }

    /**
     * @return bool
     */
    public function backupTables($arrTables = null, $compress = false)
    {
        //Initializing database tables
        if (empty($arrTables)) {
            $tables = $this->get_tables();
        } else {
            $tables = $arrTables;
        }

        //Create database query
        $sql = "CREATE DATABASE IF NOT EXISTS `{$this->db_Name}`;\n\n";
        foreach ($tables as $table) {
            $this->print_to_user("Backing up table {$table}: ", null, 0);
            $sql .= "USE `{$this->db_Name}`;\n\n";
            $sql .= "SET foreign_key_checks = 0;\n\n";
            //Create table
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n\n";
            $create_sql = $this->con->query("SHOW CREATE TABLE `{$table}`;")->fetch(PDO::FETCH_NUM)[1];
            $sql .= "{$create_sql};\n\n";

            $table_rows = $this->con->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();
            $num_files = ceil($table_rows / $this->max_rows); // Calculate how many files we need to generate

            //Generate data
            $arrBytes = $this->insert_into($num_files, $table, $sql, $table_rows, $compress);
            $this->print_to_user("OK", $arrBytes);
            $sql = '';
        }
        return true;
    }

    /**
     * @return array
     */
    public function get_tables()
    {
        $tables = [];
        $result = $this->con->query('SHOW TABLES');
        //        while ($row = $result->fetch_row()) {
        while ($row = $result->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }
        return $tables;
    }

    /**
     * @param $num_files
     * @param $table
     * @param $sql
     */
    public function insert_into($num_files, $table, $sql, $table_rows, $compress)
    {
        if ($num_files == 0) {
            $this->save($sql, "{$table}.sql", $compress);
        } else {
            $left_rows = $table_rows;
            $arrBytes = array();
            for ($i = 1; $i <= $num_files; $i++) {
                $query = "SELECT * FROM `{$table}` LIMIT " . ($i * $this->max_rows - $this->max_rows) . ",{$this->max_rows}";
                $result = $this->con->query($query);
                $num_fields = $result->columnCount();

                if ($i > 1) $sql = '';
                $sql .= "INSERT INTO `{$table}` VALUES ";

                $row_count = 1;
                while ($row = $result->fetch(PDO::FETCH_NUM)) {
                    $sql .= '(';
                    for ($k = 0; $k < $num_fields; $k++) {
                        if (isset($row[$k])) {
                            $row[$k] = addslashes($row[$k]);
                            $row[$k] = str_replace("\n", "\\n", $row[$k]);
                            $row[$k] = str_replace("\r", "\\r", $row[$k]);
                            $row[$k] = str_replace("\f", "\\f", $row[$k]);
                            $row[$k] = str_replace("\t", "\\t", $row[$k]);
                            $row[$k] = str_replace("\v", "\\v", $row[$k]);
                            $row[$k] = str_replace("\a", "\\a", $row[$k]);
                            $row[$k] = str_replace("\b", "\\i", $row[$k]);
                            if ($row[$k] == 'true' || $row[$k] == 'false' || preg_match('/^-?[0-9]+$/', $row[$k]) || $row[$k] == 'NULL' || $row[$k] == 'null') {
                                $sql .= $row[$k];
                            } else {
                                $sql .= '"' . $row[$k] . '"';
                            }
                        } else {
                            $sql .= 'NULL';
                        }

                        if ($k < ($num_fields - 1)) {
                            $sql .= ',';
                        }
                    }
                    if ($row_count == $this->max_rows || $row_count == $left_rows) {
                        $sql .= ");\n";
                    } else {
                        $sql .= "),\n";
                    }
                    $row_count++;
                }
                $left_rows +=  - ($row_count - 1);

                if ($i > 1) {
                    $this->save($sql, "{$table}_{$i}.sql", $compress);
                    $arrBytes["{$table}_{$i}.sql"] = number_format((strlen($sql)/1024),2,',',' ') .' kb';
                } else {
                    $this->save($sql, "{$table}.sql", $compress);
                    $arrBytes["{$table}.sql"] = number_format((strlen($sql)/1024),0,',',' ') .' kb';
                }
                
                $sql = '';
            }
            return $arrBytes;
        }
    }

    public function save_NU($sql, $file, $compress)
    {
        if (!$sql) return false;
        try {
            if (!file_exists($this->backup_path)) {
                mkdir($this->backup_path, 0777, true);
            }
            if ($compress) {
                $gz_file = gzopen("{$this->backup_path}\\{$file}.gz", "a9");
                gzwrite($gz_file, $sql);
                gzclose($gz_file); 
            }else{
                file_put_contents("{$this->backup_path}/{$file}", $sql);
            }
        } catch (Exception $e) {
            print_r($e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * @param $sql
     * @param $file
     * @return bool
     */
    public function save($sql, $file, $compress)
    {
        if (!$sql) return false;
        try {
            if (!file_exists($this->backup_path)) {
                mkdir($this->backup_path, 0777, true);
            }
            file_put_contents("{$this->backup_path}\\{$file}", $sql);
            if ($compress) {
                $this->gzip("{$this->backup_path}\\{$file}");
            }
        } catch (Exception $e) {
            print_r($e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * @param $file
     * @param int $level
     * @return bool|string
     */
    public function gzip($file, $level = 9)
    {
        $method = "wb{$level}";
        $new_file = "{$file}.gz";
        if ($file_open = gzopen($new_file, $method)) {
            if ($file_in = fopen($file, 'rb')) {
                while (!feof($file_in)) {
                    gzwrite($file_open, fread($file_in, 1024 * 256));
                }
                fclose($file_in);
            } else {
                return false;
            }
            gzclose($file_open);
        }
        if (!unlink($file)) return false;

        return $new_file;
    }

    public function print_to_user($msg, $arr_bytes, $breaks = 1)
    {
        if (!$msg) return false;
        if ($msg != "OK") {
            $msg = date("Y-m-d H:i:s") . " - " . $msg;
        }

        $this->output .= $msg;
        if (!empty($arr_bytes)) {
            $this->output .= '<ul>';
            foreach ($arr_bytes as $key => $value) {
                if ($value > int_MaxPacket) {
                    $this->output .= '<li>'. $key .' : <span><b>'. $value .'</b></span></li>';
                }else{
                    $this->output .= '<li>'. $key .' : <b>'. $value .'</b></li>';
                }
            }
            $this->output .= '</ul>';
        }elseif ($breaks > 0){
            $this->output .= "<br/>";

        }
        /*
        if ($breaks > 0) {
            for ($i = 1; $i <= $breaks; $i++) {
                $this->output .= "<br/>";
            }
        }
        */
    }
}