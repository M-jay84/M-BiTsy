<?php

class Adminbackup
{

    public function __construct()
    {
        // Verify User/Staff
        Auth::user(_ADMINISTRATOR, 2);
    }

    // Backup Default Page
    public function index()
    {
        // Get Backups
        $Namebk = array();
        $Sizebk = array();
        $dir = opendir(BACUP . "/");
        while ($dir && ($file = readdir($dir)) !== false) {
            $ext = explode('.', $file);
            if ($ext[1] == "sql" || $ext[1] == "gz") {
                $size = round(filesize(BACUP . "/" . $file) / 1024, 2);
                $data1[] = ['name' => $ext[0], 'ext' => $ext[1], 'size' => $size];
            }
        }
        
        // Init Data
        $data = [
            'title' => Lang::T("Backups"),
            'data1' => $data1,
        ];

        // Load View
        View::render('backup/index', $data, 'admin');
    }

    // Backup Form Submit
    public function submit()
    {
        $DBH = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . "; charset=utf8", DB_USER, DB_PASS);
        //put table names you want backed up in this array or leave empty to do all
        $tables = array();
        $this->backup_tables($DBH, $tables);
    }

    // Delete Backup Form Submit
    public function delete()
    {
        $filename = $_GET["filename"];
        $delete_error = true;
        if (!unlink(BACUP . '/' . $filename)) {
            $delete_error = false;
        }
        if ($delete_error) {
            Redirect::autolink(URLROOT . '/adminbackup', "Selected Backup Files deleted");
        } else {
            Redirect::autolink(URLROOT . '/adminbackup', Lang::T("Error Deleting"));
        }
    }
    
    // Download Backup Page
    public function download()
    {
        // Check User Input
        $filename = Input::get("filename");
        // Check The File
        $fn = BACUP . "/$filename";
        // Download
        if (!file_exists($fn)) {
            Redirect::autolink($_SERVER['HTTP_REFERER'], "The file $filename does not exists");
        } else {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $filename);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($fn));
            ob_clean();
            flush();
            readfile($fn);
        }
    }
   
    // Backup Tables
    private function backup_tables($DBH, $tables)
    {
        $DBH->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_NATURAL);
        $BACKUP_PATH = BACUP . "/";
        $nowtimename = time();
        $handle = fopen($BACKUP_PATH . $nowtimename . '.sql', 'a+');
            
        // Array of all database field types which just take numbers
        $numtypes = array('tinyint', 'smallint', 'mediumint', 'int', 'bigint', 'float', 'double', 'decimal', 'real');
        // Get all of the tables
        if (empty($tables)) {
            $pstm1 = $DBH->query('SHOW TABLES');
            while ($row = $pstm1->fetch(PDO::FETCH_NUM)) {
                $tables[] = $row[0];
            }
        } else {
            $tables = is_array($tables) ? $tables : explode(',', $tables);
        }
        // Cycle through the table(s)
        foreach ($tables as $table) {
            $result = $DBH->query("SELECT * FROM `$table`");
            $num_fields = $result->columnCount();
            $num_rows = $result->rowCount();
            $return = "";
            
            // Table structure
            $pstm2 = $DBH->query("SHOW CREATE TABLE `$table`");
            $row2 = $pstm2->fetch(PDO::FETCH_NUM);
            $ifnotexists = str_replace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', $row2[1]);
            $return .= "\n\n" . $ifnotexists . ";\n\n";
            fwrite($handle, $return);
            $return = "";
            
            //insert values
            if ($num_rows) {
                $return = 'INSERT INTO `' . $table . '` (';
                $pstm3 = $DBH->query("SHOW COLUMNS FROM `$table`");
                $count = 0;
                $type = array();
                while ($rows = $pstm3->fetch(PDO::FETCH_NUM)) {
                    if (stripos($rows[1], '(')) {
                        $type[$table][] = stristr($rows[1], '(', true);
                    } else {
                        $type[$table][] = $rows[1];
                    }
                    $return .= "`" . $rows[0] . "`";
                    $count++;
                    if ($count < ($pstm3->rowCount())) {
                        $return .= ", ";
                    }
                }
                $return .= ")" . ' VALUES';
                fwrite($handle, $return);
                $return = "";
            }
            $count = 0;
            while ($row = $result->fetch(PDO::FETCH_NUM)) {
                $return = "\n\t(";
                for ($j = 0; $j < $num_fields; $j++) {
                    //$row[$j] = preg_replace("\n","\\n",$row[$j]);
                    if (isset($row[$j])) {
                        //if number, take away "". else leave as string
                        if ((in_array($type[$table][$j], $numtypes)) && (!empty($row[$j]))) {
                            $return .= $row[$j];
                        } else {
                            $return .= $DBH->quote($row[$j]);
                        }
                    } else {
                        $return .= 'NULL';
                    }
                    if ($j < ($num_fields - 1)) {
                        $return .= ',';
                    }
                }
                $count++;
                if ($count < ($result->rowCount())) {
                    $return .= "),";
                } else {
                    $return .= ");";
                }
                fwrite($handle, $return);
                $return = "";
            }
            $return = "\n\n-- ------------------------------------------------ \n\n";
            fwrite($handle, $return);
            $return = "";
        }

        //var_dump($result->errorInfo());
        fclose($handle);
        Redirect::autolink(URLROOT . '/adminbackup', Lang::T("Completed Please Check File"));
    }

}