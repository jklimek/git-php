<?php

/**
 * Created by PhpStorm.
 * User: klemens
 * Date: 06/09/16
 * Time: 21:19
 */

namespace GitPhpBundle\Service;

use Coyl\Git\Git;

class GitHandler
{
    private $repo;

    public function __construct($repositoryPath)
    {
        $this->repo = Git::open($repositoryPath);
    }

    public function getActiveBranch()
    {
        return $this->repo->getActiveBranch();
    }

    public function listFiles()
    {
        $filesListString = $this->repo->run('ls-tree -r HEAD --name-only');
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

    public function showStatus($html = false)
    {
        $statusStringRaw = $this->repo->status($html);
        $statusString = preg_replace("/\n/", "<br/>", $statusStringRaw);

        return $statusString;
    }

    public function addFile($fileDataArray)
    {
        // Create actual file in the filesystem
        $fileHandler = fopen($fileDataArray["fileName"], "w");
        fwrite($fileHandler, $fileDataArray["fileBody"]);
        fclose($fileHandler);

        // Add file to git repo
        $out = $this->repo->add($fileDataArray["fileName"]);

        return $out;
    }

    public function commit($message)
    {
        return $this->repo->commit($message);
    }

}