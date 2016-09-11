<?php
/**
 * Created by PhpStorm.
 * User: klemens
 * Date: 12/09/16
 * Time: 00:43
 */

namespace GitPhpBundle\DependencyInjection;


class Helpers
{
    /**
     * Function generates multidimensional array from array of path strings
     * e.g. ["/dir/to/file.txt", "dir/to/another/file.txt"]
     *          ->
     *      ["dir" => ["to" => [0 => "file.txt", "another" => [0 => "file.txt"]]]]
     *
     * @param string[] $filesListExplodedString
     * @return array
     */
    public function pathToArray($filesListExplodedString)
    {
        $filesListArray = [];

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
        return $filesListArray;
    }
}