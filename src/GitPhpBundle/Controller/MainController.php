<?php

namespace GitPhpBundle\Controller;

use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class MainController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     *
     */
    public function listAction()
    {
        $status = $this->get("git_php.service.githandler")->showStatus();
        $filesLists = $this->get("git_php.service.githandler")->listFiles();
        $activeBranch = $this->get("git_php.service.githandler")->getActiveBranch();

        return [
            "status"       => $status,
            "filesLists"   => $filesLists,
            "activeBranch" => $activeBranch
        ];
    }


    /**
     * @Route("/add")
     * @Template()
     * @param Request $request
     * @return array
     */
    public function addFileAction(Request $request)
    {
        $inputFileDataArray = [
            "filePath" => $this->getParameter("repository_path") . "/" . $request->get("fileName"),
            "fileBody" => $request->get("fileBody")
        ];

        try {
            $this->get("git_php.service.githandler")->addFile($inputFileDataArray);
            $fileDataArray = [
                "status"   => "OK",
                "filePath" => $inputFileDataArray["filePath"],
                "fileBody" => $this->get("git_php.service.filehandler")->getFileBody($inputFileDataArray["filePath"])
            ];
        } catch (Exception $e) {
            $fileDataArray = [
                "status"   => "ERROR",
                "filePath" => $inputFileDataArray["filePath"],
                "error"    => $e->getMessage()
            ];
        }

        return ["fileDataArray" => $fileDataArray];
    }

    /**
     * @Route("/edit")
     * @Template()
     * @param Request $request
     * @return array
     */
    public function editFileAction(Request $request)
    {
        $inputFileDataArray = [
            "filePath" => $this->getParameter("repository_path") . "/" . $request->get("fileName"),
            "fileBody" => $request->get("fileBody")
        ];

        try {
            $this->get("git_php.service.filehandler")->editFile($inputFileDataArray);
            $fileDataArray = [
                "status"   => "OK",
                "filePath" => $inputFileDataArray["filePath"],
                "fileBody" => $this->get("git_php.service.filehandler")->getFileBody($inputFileDataArray["filePath"])
            ];
        } catch (Exception $e) {
            $fileDataArray = [
                "status"   => "ERROR",
                "filePath" => $inputFileDataArray["filePath"],
                "error"    => $e->getMessage()
            ];
        }

        return ["fileDataArray" => $fileDataArray];
    }
    /**
     * @Route("/remove")
     * @Template()
     * @param Request $request
     * @return array
     */
    public function removeFileAction(Request $request)
    {
        $inputFileDataArray = [
            "filePath" => $this->getParameter("repository_path") . "/" . $request->get("fileName")
        ];

        dump($inputFileDataArray);

        try {
            $this->get("git_php.service.githandler")->removeFile($inputFileDataArray["filePath"]);
            $fileDataArray = [
                "status"   => "OK",
                "filePath" => $inputFileDataArray["filePath"]
            ];
        } catch (Exception $e) {
            $fileDataArray = [
                "status"   => "ERROR",
                "filePath" => $inputFileDataArray["filePath"],
                "error"    => $e->getMessage()
            ];
        }

        dump($fileDataArray);
        return ["fileDataArray" => $fileDataArray];
    }

    /**
     * @Route("/ajax/getFileBody")
     * @param Request $request
     * @return array
     */
    public function getFileBodyAjaxAction(Request $request)
    {
        $filePath = $this->getParameter("repository_path") . "/" . $request->get("filePath");
        try {
            $fileArray = [
                "status"   => "OK",
                "filePath" => $filePath,
                "fileBody" => $this->get("git_php.service.filehandler")->getFileBody($filePath)
            ];
        } catch (Exception $e) {
            return new JsonResponse([
                "status"   => "ERROR",
                "filePath" => $filePath,
                "error"    => $e->getMessage()
            ]);
        }

        return new JsonResponse($fileArray);
    }

}
