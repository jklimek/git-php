<?php

/**
 * Created by PhpStorm.
 * User: klemens
 * Date: 06/09/16
 * Time: 21:19
 */

namespace GitPhpBundle\DependencyInjection;

use Coyl\Git\Git;
use Coyl\Git\GitRepo;
use Exception;

class GitHandler
{
    private $repo;
    /**
     * @var FileHandler
     */
    private $fileHandlerService;
    /**
     * @var Helpers
     */
    private $helpersService;

    /**
     * GitHandler constructor.
     *
     * @param string $repositoryPath
     * @param FileHandler $fileHandlerService
     * @param Helpers $helpersService
     */
    public function __construct($repositoryPath, $fileHandlerService, $helpersService)
    {
        $this->repo = Git::open($repositoryPath);
        $this->fileHandlerService = $fileHandlerService;
        $this->helpersService = $helpersService;
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
     * Method returns active branch name
     *
     * @return string Active branch name
     */
    public function getLastCommitHash()
    {
        return $this->repo->run("rev-parse HEAD");
    }

    /**
     * Generate array with files list
     * repo files, modified files, new files and deleted files respectively
     *
     * @return array[] Array containing files lists
     */
    public function listFiles()
    {
        $filesListString = $this->repo->run('ls-files');
        $filesListExplodedString = explode("\n", $filesListString);

        $filesListArray = $this->helpersService->pathToArray($filesListExplodedString);

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
        try {// Create file in the filesystem
            $this->fileHandlerService->createFile($fileDataArray);

            // Add file to git repo
            $this->repo->add($fileDataArray["filePath"]);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

    }

    /**
     * Method removes safely file from repo
     *
     * @param array $filePath File path
     * @return string git output
     * @throws \Exception
     */
    public function removeFile($filePath)
    {
        try {
            // Remove file from git repo
            $this->repo->run("rm --cached $filePath");
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

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

    public function getBranches($keepAsterisk = true)
    {
        try {
            return $this->repo->branches(GitRepo::BRANCH_LIST_MODE_LOCAL, $keepAsterisk);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

}