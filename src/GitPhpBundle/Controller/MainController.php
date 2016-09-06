<?php

namespace GitPhpBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

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
        $repoFilesList = $this->get("git_php.service.handler")->listFiles();

        return [
            "status"    => $status,
            "filesList" => $repoFilesList
        ];
    }
}
