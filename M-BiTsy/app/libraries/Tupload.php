<?php

class Tupload
{
    private $name;
    private $size;
    private $type;
    private $tmp_name;
    private $error;

    public function __construct($file)
    {
        if (isset($_FILES[$file])) {
            array_walk($_FILES[$file], function ($val, $key) {
                $this->$key = $val;
            });

            return;
        }

        $this->error = 4;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getName()
    {
        return mb_ereg_replace('[\\\\/:*?"<>|]', "", trim($this->name));
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getMime()
    {
        return shell_exec('file -b --mime-type ' . escapeshellarg($this->tmp_name));
    }

    public function getPath()
    {
        return $this->tmp_name;
    }

    public function getData()
    {
        try
        {
            $file = new SplFileObject($this->tmp_name, 'rb');

            $data = null;

            for ($file; $file->valid(); $file->next()) {
                $data .= $file->current();
            }

            return $data;
        } catch (RuntimeException $e) {

        }

        return null;
    }

    public function getExtension()
    {
        if (!($ext = pathinfo($this->name, PATHINFO_EXTENSION))) {
            return null;
        }

        return $ext;
    }

    public function remove()
    {
        return unlink($this->tmp_name);
    }

    public function move($path)
    {
        return move_uploaded_file($this->tmp_name, $path);
    }
    
}