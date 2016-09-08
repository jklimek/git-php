<?php

namespace GitPhpBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class MainController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     *
     */
    public function indexAction()
    {
        $status = $this->get("git_php.service.handler")->showStatus();
        $filesLists = $this->get("git_php.service.handler")->listFiles();
        $activeBranch = $this->get("git_php.service.handler")->getActiveBranch();
        dump($filesLists);

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
        $fileDataArray = [
            "fileName" => $this->getParameter("repository_path") . "/" . $request->get("fileName"),
            "fileBody" => $request->get("fileBody")
        ];

//        $fileDataArray = [
//            "fileName" => $this->getParameter("repository_path") . "/" . "tests/test1.txt",
//            "fileBody" => "dupsko"
//        ];
        $status = $this->get("git_php.service.handler")->addFile($fileDataArray);

        return [
            "status" => $status
        ];
    }

}
