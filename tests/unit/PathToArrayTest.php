<?php


use GitPhpBundle\DependencyInjection\Helpers;

class PathToArrayTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var Helpers
     */
    protected $helpersService;

    protected function _before()
    {
        $this->helpersService = new Helpers();
    }

    public function testPathToArrayConversion()
    {
        $testDataset = ["dir/to/file.txt", "dir/to/another/file.txt"];
        $actualResult = ["dir" => ["to" => ["another" => [0 => "file.txt"], 0 => "file.txt"]]];

        $arrayToTest = $this->helpersService->pathToArray($testDataset);
        $this->assertEquals($actualResult, $arrayToTest);

        $testDataset = ["dir/to/file.txt", "dir/file.txt"];
        $actualResult = ["dir" => ["to" => [0 => "file.txt"], 0 => "file.txt"]];

        $arrayToTest = $this->helpersService->pathToArray($testDataset);
        $this->assertEquals($actualResult, $arrayToTest);

        $testDataset = ["dir/to/super/nested/secret/file.txt", "dir/to/super/nested/secret/second_file.txt"];
        $actualResult = [
            "dir" => [
                "to" => [
                    "super" => [
                        "nested" => [
                            "secret" => [
                                0 => "file.txt",
                                1 => "second_file.txt"
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $arrayToTest = $this->helpersService->pathToArray($testDataset);
        $this->assertEquals($actualResult, $arrayToTest);
    }
}