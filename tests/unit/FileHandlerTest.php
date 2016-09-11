<?php


use GitPhpBundle\DependencyInjection\FileHandler;

class FileHandlerTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    private $application;
    private $repositoryPath;
    private $fileToTest;

    /**
     * @var FileHandler
     */
    protected $fileHandlerService;

    protected function _before()
    {
        $this->fileHandlerService = new FileHandler();

        // Create kernel
        $kernel = new AppKernel('test', true);
        $kernel->boot();
        $this->application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
        $this->application->setAutoExit(false);
        $this->repositoryPath = $this->application->getKernel()->getContainer()->getParameter("repository_path");

        $this->fileToTest = [
            "filePath" => $this->repositoryPath . "/" . "test_file_name.txt",
            "fileBody" => "test 123"
        ];
    }

    public function testFileCreation()
    {
        $this->fileHandlerService->createFile($this->fileToTest);
        $this->assertFileExists($this->repositoryPath . "/" . "test_file_name.txt");
        //Remove file
        unlink($this->repositoryPath . "/" . "test_file_name.txt");
    }

    public function testFileBody()
    {
        $this->fileHandlerService->createFile($this->fileToTest);
        $bodyToTest = $this->fileHandlerService->getFileBody($this->fileToTest["filePath"]);
        $actualResult = "test 123";
        $this->assertEquals($bodyToTest, $actualResult);
        //Remove file
        unlink($this->repositoryPath . "/" . "test_file_name.txt");
    }

    public function testEditFile()
    {
        $this->fileHandlerService->createFile($this->fileToTest);
        $editedFileToTest = $this->fileToTest;
        $editedFileToTest["fileBody"] = "test 321";
        $this->fileHandlerService->editFile($editedFileToTest);

        $bodyToTest = $this->fileHandlerService->getFileBody($this->fileToTest["filePath"]);
        $actualResult = "test 321";
        $this->assertEquals($bodyToTest, $actualResult);
        $this->assertFileExists($this->repositoryPath . "/" . "test_file_name.txt");
        //Remove file
        unlink($this->repositoryPath . "/" . "test_file_name.txt");
    }
}