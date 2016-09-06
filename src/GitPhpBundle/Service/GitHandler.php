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

    public function listFiles()
    {
        $filesListString = $this->repo->run('ls-tree -r HEAD --name-only');
        $filesListArray = [];
        $filesListExplodedString = explode("\n", $filesListString);
        // Generate multidimensional array with keys for each dir
        foreach ($filesListExplodedString as $filePathString) {
            if ($filePathString != "") {

                $filePathArray = explode("/", $filePathString);
                $fileCount = count($filePathArray);
                $tmpFileListArray = &$filesListArray;
                for ($i = 1; $i < $fileCount; $i++) {
                    // Create empty array if previous index does not exist but should
                    if (!isset($tmpFileListArray[$filePathArray[$i - 1]])) {
                        $tmpFileListArray[$filePathArray[$i - 1]] = [];
                    }
                    $tmpFileListArray = &$tmpFileListArray[$filePathArray[$i - 1]];
                }
                $tmpFileListArray[] = ["file" => $filePathArray[$i-1], "filePathString" => $filePathString];
            }
        }
        dump($filesListArray);
        return $filesListArray;
    }

    public function showStatus($html = false)
    {
        return $this->repo->status($html);
    }

}