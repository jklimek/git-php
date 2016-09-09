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
     * @return array
     */
    public function listAction()
    {
        $status = $this->get("git_php.service.githandler")->showStatus();
        $filesLists = $this->get("git_php.service.githandler")->listFiles();
        $activeBranch = $this->get("git_php.service.githandler")->getActiveBranch();
//        $mergeRequests =

        return [
            "status"       => $status,
            "filesLists"   => $filesLists,
            "activeBranch" => $activeBranch,
//            "mergeRequests" => $mergeRequests
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

        return ["fileDataArray" => $fileDataArray];
    }


    /**
     * @Route("/commit")
     * @Template()
     * @param Request $request
     * @return array
     */
    public function commitAction(Request $request)
    {
        $commitMessage = $request->get("commitMessageBody");

        try {
            $commitOutput = $this->get("git_php.service.githandler")->commit($commitMessage);
            $dataArray = [
                "status"       => "OK",
                "commitOutput" => $commitOutput
            ];
        } catch (Exception $e) {
            $dataArray = [
                "status" => "ERROR",
                "error"  => $e->getMessage()
            ];
        }

        return ["dataArray" => $dataArray];
    }


    /**
     * @Route("/mergeRequest")
     * @Template()
     * @param Request $request
     * @return array
     */
    public function mergeRequestAction(Request $request)
    {
        $mergeRequestRepository = $this->getDoctrine()->getRepository('GitPhpBundle:MergeRequest');

        try {
            $commitHash = $this->get("git_php.service.githandler")->getLastCommitHash();
            $sourceBranchName = $request->get("sourceBranchName");
            $destinationBranchName = $request->get("destinationBranchName");

            // Create merge request entity
            $mergeRequestRepository->createMergeRequest($sourceBranchName, $commitHash, $destinationBranchName);

            $dataArray = [
                "status" => "OK",
                "commitHash" => $commitHash,
                "sourceBranchName" => $sourceBranchName,
                "destinationBranchName" => $destinationBranchName
            ];
        } catch (Exception $e) {
            $dataArray = [
                "status" => "ERROR",
                "error"  => $e->getMessage()
            ];
        }

        return ["dataArray" => $dataArray];
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
            $fileDataArray = [
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

        return new JsonResponse($fileDataArray);
    }

    /**
     * @Route("/ajax/getBranches")
     * @return array
     */
    public function getBranchesAjaxAction()
    {
        $branches = $this->get("git_php.service.githandler")->getBranches();
        try {
            $fileDataArray = [
                "status"   => "OK",
                "branches" => $branches,
            ];
        } catch (Exception $e) {
            return new JsonResponse([
                "status"   => "ERROR",
                "error"    => $e->getMessage()
            ]);
        }

        return new JsonResponse($fileDataArray);
    }

    /**
     * @Route("/ajax/getActiveBranch")
     * @return array
     */
    public function getActiveBranchAjaxAction()
    {

        try {
            $fileDataArray = [
                "status"   => "OK",
                "branch" => $this->get("git_php.service.githandler")->getActiveBranch(),
            ];
        } catch (Exception $e) {
            return new JsonResponse([
                "status"   => "ERROR",
                "error"    => $e->getMessage()
            ]);
        }

        return new JsonResponse($fileDataArray);
    }

}
