<?php
/**
 * Created by PhpStorm.
 * User: klemens
 * Date: 08/09/16
 * Time: 19:55
 */

namespace GitPhpBundle\DependencyInjection;

use Exception;

class FileHandler
{
    public function __construct()
    {

    }

    /**
     * Method creates file in the filesystem based on $fileDataArray
     *
     * @param string[] $fileDataArray Array containing filePath and fileBody
     * @return string status output
     * @throws Exception
     */
    public function createFile($fileDataArray)
    {
        // Create actual file in the filesystem
        try {
            if (!file_exists($fileDataArray["filePath"])) {
                $this->writeToFile($fileDataArray);
            } else {
                throw new Exception("File already exists!");
            }


            $status = "OK";
        } catch (Exception $e) {
            //TODO handle exception
            throw new Exception($e->getMessage(), $e->getCode());
        }

        return $status;
    }

    /**
     * Method creates (or opens if file exists) file and writes body to this file
     *
     * @param string[] $fileDataArray Array containing filePath and new fileBody
     */
    private function writeToFile($fileDataArray)
    {
        $fileHandler = fopen($fileDataArray["filePath"], "w");
        fwrite($fileHandler, $fileDataArray["fileBody"]);
        fclose($fileHandler);
    }

    /**
     * Method for editing file with new body
     *
     * @param string[] $fileDataArray Array containing filePath and new fileBody
     * @throws Exception
     */
    public function editFile($fileDataArray)
    {
        if (file_exists($fileDataArray["filePath"])) {
            $this->writeToFile($fileDataArray);
        } else {
            throw new Exception("File doesn't exist!");
        }
    }

    /**
     * Method for getting file body for given path
     *
     * @param string[] $filePath File path
     * @return string $fileBody File body string
     * @throws Exception
     */
    public function getFileBody($filePath)
    {
        try {
            if (file_exists($filePath)) {
                $fileBody = file_get_contents($filePath);

                return $fileBody;
            } else {
                throw new Exception("File doesn't exist!");
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }
}