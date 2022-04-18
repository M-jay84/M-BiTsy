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
        require APPROOT . '/views/admin/admincp/header.php';
        Style::adminnavmenu();
        Style::begin("Backups");
        $Namebk = array();
        $Sizebk = array();
        // CHECK ALL SQL FILES INTO THE BACKUPS FOLDER AND CREATE AN LIST
        $dir = opendir(BACUP . "/");
        while ($dir && ($file = readdir($dir)) !== false) {
            $ext = explode('.', $file);
            if ($ext[1] == "sql") {
                if ($ext[2] != "gz") {
                    $Namebk[] = $ext[0];
                    $Sizebk[] = round(filesize(BACUP . "/" . $file) / 1024, 2);
                }
            }
        }

        // SORT THE LIST
        sort($Namebk);
        // OPEN TABLE
        echo ("<br/><table style='text-align:center;' width='100%'>");
        // TABLE HEADER
        echo ("<tr bgcolor='#3895D3'>"); // Start table row
        echo ("<th scope='colgroup'><b>Date</b></th>"); // Date
        echo ("<th scope='colgroup'><b>Time</b></th>"); // Time
        echo ("<th scope='colgroup'><b>Size</b></th>"); // Size
        echo ("<th scope='colgroup'><b>Hash</b></th>"); // Hash
        echo ("<th scope='colgroup'><b>Download</b></th>"); // Download
        echo ("<th></th>"); // Delete
        echo ("</tr>"); // End table row
        // TABLE ROWS
        for ($x = count($Namebk) - 1; $x >= 0; $x--) {
            $data = explode('_', $Namebk[$x]);

            //var_dump($data);

            echo ("<tr bgcolor='#CCCCCC'>"); // Start table row
            echo ("<td>" . $data[1] . "</td>"); // Date
            echo ("<td>" . $Sizebk[$x] . " KByte</td>"); // Size
            echo ("<td>" . $data[0] . "</td>"); // Hash
            echo ("<td><a href='" . URLROOT . "/backups/" . $Namebk[$x] . ".sql'>SQL</a> - <a href='" . URLROOT . "/backups/" . $Namebk[$x] . ".sql.gz'>GZ</a></td>"); // Download
            echo ("<td><a href='" . URLROOT . "/adminbackup/delete?filename=" . $Namebk[$x] . ".sql'><i class='fa fa-trash-o tticon-red' title='Delete'></i></a></td>"); // Delete
            echo ("</tr>"); // End table row
        }
        // CLOSE TABLE
        echo ("</table>");
        // CREATE BACKUP LINK
        echo ("<br><br><center><a href='" . URLROOT . "/adminbackup/submit'>Backup Database</a> (or create a CRON task on " . URLROOT . "/adminbackup/submit)</center>");
        Style::end();
        require APPROOT . '/views/admin/admincp/footer.php';
    }

    // Backup Form Submit
    public function submit()
    {
        $DBH = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . "; charset=utf8", DB_USER, DB_PASS);
        //put table names you want backed up in this array or leave empty to do all
        $tables = array();
        $this->backup_tables($DBH, $tables);
    }

    // Backup Tables
    private function backup_tables($DBH, $tables)
    {
        $DBH->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_NATURAL);
        //Script Variables
        $compression = false;
        $BACKUP_PATH = BACUP . "/";
        $nowtimename = time();
        //create/open files
        if ($compression) {
            $zp = gzopen($BACKUP_PATH . $nowtimename . '.sql.gz', "a9");
        } else {
            $handle = fopen($BACKUP_PATH . $nowtimename . '.sql', 'a+');
        }
        //array of all database field types which just take numbers
        $numtypes = array('tinyint', 'smallint', 'mediumint', 'int', 'bigint', 'float', 'double', 'decimal', 'real');
        //get all of the tables
        if (empty($tables)) {
            $pstm1 = $DBH->query('SHOW TABLES');
            while ($row = $pstm1->fetch(PDO::FETCH_NUM)) {
                $tables[] = $row[0];
            }
        } else {
            $tables = is_array($tables) ? $tables : explode(',', $tables);
        }
        //cycle through the table(s)
        foreach ($tables as $table) {
            $result = $DBH->query("SELECT * FROM $table");
            $num_fields = $result->columnCount();
            $num_rows = $result->rowCount();
            $return = "";
            //uncomment below if you want 'DROP TABLE IF EXISTS' displayed
            //$return.= 'DROP TABLE IF EXISTS `'.$table.'`;';
            //table structure
            $pstm2 = $DBH->query("SHOW CREATE TABLE $table");
            $row2 = $pstm2->fetch(PDO::FETCH_NUM);
            $ifnotexists = str_replace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', $row2[1]);
            $return .= "\n\n" . $ifnotexists . ";\n\n";
            if ($compression) {
                gzwrite($zp, $return);
            } else {
                fwrite($handle, $return);
            }
            $return = "";
            //insert values
            if ($num_rows) {
                $return = 'INSERT INTO `' . $table . '` (';
                $pstm3 = $DBH->query("SHOW COLUMNS FROM $table");
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
                if ($compression) {
                    gzwrite($zp, $return);
                } else {
                    fwrite($handle, $return);
                }
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
                if ($compression) {
                    gzwrite($zp, $return);
                } else {
                    fwrite($handle, $return);
                }
                $return = "";
            }
            $return = "\n\n-- ------------------------------------------------ \n\n";
            if ($compression) {
                gzwrite($zp, $return);
            } else {
                fwrite($handle, $return);
            }
            $return = "";
        }

        $error1 = $pstm2->errorInfo();
        $error2 = $pstm3->errorInfo();
        $error3 = $result->errorInfo();
        echo $error1[2];
        echo $error2[2];
        echo $error3[2];

        if ($compression) {
            gzclose($zp);
        } else {
            fclose($handle);
        }

        Redirect::autolink(URLROOT . '/adminbackup', Lang::T("Completed Please Check File"));
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

    /*
    public function submit1()
    {

        // CREATE THE RANDOM HASH
        $RandomString = chr(rand(97, 122)) . chr(rand(97, 122)) . chr(rand(97, 122)) . chr(rand(97, 122)) . chr(rand(97, 122)) . chr(rand(97, 122)) . chr(rand(97, 122)) . chr(rand(97, 122)) . chr(rand(97, 122)) . chr(rand(97, 122));
        $md5string = md5($RandomString);

        // COMPOSE THE FILENAME
        $curdate = str_replace(" ", "_",  TimeDate::utc_to_tz());
        $filename = BACUP . '/db-backup_' . $curdate . '_' . $md5string . '.sql';

        // COMPOSE THE HEADER OF THE SQL FILE
        $return = "//\n";
        $return .= "//  M-BiTsy\n";
        $return .= "//  Database BackUp\n";
        $return .= "//  " . date("y-m-d H:i:s") . "\n";
        $return .= "//\n\n";

        // LIST ALL TABLES ON THE DATABASE
        $tables = array();

        if (empty($tables)) {
            $result = DB::run('SHOW TABLES');
            while ($row = $result->fetch(PDO::FETCH_NUM)) {
                $tables[] = $row[0];
            }
        } else {
            $tables = is_array($tables) ? $tables : explode(',', $tables);
        }

        //var_dump($tables);die();

        // RETRIEVE THE TABLES
        foreach ($tables as $table) {
            $result = DB::run('SELECT * FROM ' . $table);
            $num_fields = $result->rowCount();
            $return .= 'DROP TABLE IF EXISTS ' . $table . ';';
            $row2 = DB::run('SHOW CREATE TABLE ' . $table)->fetch(PDO::FETCH_ASSOC);
            $return .= "\n\n" . $row2[1] . ";\n\n";
            for ($i = 0; $i < $num_fields; $i++) {
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    $return .= 'INSERT INTO ' . $table . ' VALUES(';

                    for ($j = 0; $j < $num_fields; $j++) {
                        $row[$j] = addslashes($row[$j]);
                        $row[$j] = preg_replace("/\n/", "/\\n/", $row[$j]);
                        if (isset($row[$j])) {
                            $return .= '"' . $row[$j] . '"';
                        } else {
                            $return .= '""';
                        }
                        if ($j < ($num_fields - 1)) {
                            $return .= ',';
                        }
                    }
                    $return .= ");\n";
                }
            }
            $return .= "\n\n\n";
        }

        // OLD FOPEN/FWRITE/FCLOSE METHOD
        //$handle = fopen($filename,'w+');
        //fwrite($handle,$return);
        //fclose($handle);

        // NEW METHOD TO STORE THE RESULT ON FILES
        $create_error = false;
        // if ( file_put_contents($filename, $return) >=  1) { $create_error = false; }
        // if ( file_put_contents($filename.'.gz', gzencode( $return,9)) >= 1 ) { $create_error = false; }
        var_dump($return);
        die();
        if ($create_error) {
            Redirect::autolink(URLROOT . "/adminbackup", "Has encountered a error during the backup.<br><br>");
        } else {
            Redirect::autolink(URLROOT . "/adminbackup", "BackUp Complete.<br><br>");
        }
    }
    */
}