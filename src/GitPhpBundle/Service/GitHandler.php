<?php

/**
 * Created by PhpStorm.
 * User: klemens
 * Date: 06/09/16
 * Time: 21:19
 */

namespace GitPhpBundle\Service;

use Coyl\Git\Git;
use Exception;

class GitHandler
{
    private $repo;
    private $fileHandlerService;

    /**
     * GitHandler constructor.
     *
     * @param string $repositoryPath
     * @param FileHandler $fileHandlerService
     */
    public function __construct($repositoryPath, $fileHandlerService)
    {
        $this->repo = Git::open($repositoryPath);
        $this->fileHandlerService = $fileHandlerService;
    }

    /**
     * Method returns active branch name
     *
     * @return string Active branch name
     */
    public function getActiveBranch()
    {
        return $this->repo->getActiveBranch();
    }

    /**
     * Generate array with files list
     * repo files, modified files, new files and deleted files respectively
     *
     * @return array Array containing files lists
     */
    public function listFiles()
    {
        $filesListString = $this->repo->run('ls-files');
        $filesListArray = [];
        $filesListExplodedString = explode("\n", $filesListString);
        // Generate multidimensional array with keys for each dir
        foreach ($filesListExplodedString as $filePathString) {
            if ($filePathString != "") {
                // Make array from each path dir
                $filePathArray = explode("/", $filePathString);
                $fileCount = count($filePathArray);
                $tmpFileListArray = &$filesListArray;
                for ($i = 1; $i < $fileCount; $i++) {
                    // Create empty array if previous index does not exist but should
                    if (!isset($tmpFileListArray[$filePathArray[$i - 1]])) {
                        $tmpFileListArray[$filePathArray[$i - 1]] = [];
                    }
                    // Update result array with key
                    $tmpFileListArray = &$tmpFileListArray[$filePathArray[$i - 1]];
                }
                // Add actual file
                $tmpFileListArray[] = $filePathArray[$i - 1];
            }
        }

        $newFilesListArray = preg_split("#\n#", trim($this->repo->run('diff --cached --name-only --diff-filter=A')),
            null, PREG_SPLIT_NO_EMPTY);
        $modifiedFilesListArray = preg_split("#\n#", trim($this->repo->run('ls-files -m')), null, PREG_SPLIT_NO_EMPTY);
        $deletedFilesListArray = preg_split("#\n#", trim($this->repo->run('ls-files -d')), null, PREG_SPLIT_NO_EMPTY);


        return [
            "filesTree"     => $filesListArray,
            "modifiedFiles" => $modifiedFilesListArray,
            "newFiles"      => $newFilesListArray,
            "deletedFiles"  => $deletedFilesListArray
        ];
    }

    /**
     * Method returns git status info
     *
     * @return string Git status info string
     */
    public function showStatus()
    {
        $statusStringRaw = $this->repo->status();
        $statusString = preg_replace("/\n/", "<br/>", $statusStringRaw);

        return $statusString;
    }

    /**
     * Method adds creates file and adds it to the repo
     *
     * @param array $fileDataArray Array containing file path and its body
     * @return string git output
     * @throws \Exception
     */
    public function addFile($fileDataArray)
    {
        // Create file in the filesystem
        $this->fileHandlerService->createFile($fileDataArray);

        // Add file to git repo
        $out = $this->repo->add($fileDataArray["fileName"]);

        return $out;
    }

    /**
     * Performs git commit with given $message
     *
     * @param string $message Git commit message
     * @return string git output
     */
    public function commit($message)
    {
        return $this->repo->commit($message);
    }

    /**
     * Perform single file commit
     *
     * @param string $filePath Committed file path
     * @param string $message Commit message
     * @return string git output
     */
    public function commitFile($filePath, $message)
    {
        try {
            if (file_exists($filePath)) {
                return $this->repo->run("commit -m '$message' $filePath");
            } else {
                throw new Exception("File does not exists!");
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }

    }

}