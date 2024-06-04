<?php
namespace App\Controller;

use App\Manager\ApiManager;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends AbstractController
{
    protected $manager;
    public function __construct(ApiManager $manager)
    {
        $this->manager = $manager;
    }
    #[Rest\Post('/api/tags')]
    public function addTag(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        //dd($data);
        return new JsonResponse($this->manager->addTag($data));
    }

    #[Rest\Get('/api/tags')]
    public function listTag()
    {
        return new JsonResponse($this->manager->listTag());
    }

    #[Rest\Post('/api/tags/{id}')]
    public function editeTag(Request $request, $id)
    {
        $data = json_decode($request->getContent(), true);
        $data['id'] = $id;
        return new JsonResponse($this->manager->updateTag($data));
    }

    #[Rest\Delete('/api/tags/{id}')]
    public function removeTag($id)
    {
        return new JsonResponse($this->manager->removeTag($id));
    }

    #[Rest\Post('/api/news')]
    public function addNews(Request $request)
    {
        $data = $request->request->all();
        $data['image'] = $request->files->get('image');
        $data['video'] = $request->files->get('video');

        return new JsonResponse($this->manager->createNews($data));
    }

    #[Rest\Get('/api/news')]
    public function listNews()
    {
        return new JsonResponse($this->manager->listNews());
    }

    #[Rest\Post('/api/logiciels')]
    public function addLogiciel(Request $request)
    {
        $data = $request->request->all();
        $data['packageFile'] = $request->files->get('packageFile');

        return new JsonResponse($this->manager->addLogiciel($data));
    }

    #[Rest\Get('/api/logiciels')]
    public function listLogiciels()
    {
        return new JsonResponse($this->manager->listLogiciels());
    }

    #[Rest\Post('/api/logiciels/{id}')]
    public function activeLogiciels($id, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        return new JsonResponse($this->manager->activeLogiciel($data, $id));
    }

    #[Rest\Post('/api/users')]
    public function addUser(Request $request)
    {
        $data = $request->request->all();
        $data['photo'] = $request->files->get('photo');

        return new JsonResponse($this->manager->createUser($data));
    }

    #[Rest\Get('/api/users')]
    public function listUser()
    {

        return new JsonResponse($this->manager->getUsers());
    }

    #[Rest\Get('/download/windows')]
    public function getWind()
    {

        return new JsonResponse($this->manager->getFilePackage('windows'));
    }

    #[Rest\Get('/download/android')]
    public function getAnd()
    {

        return new JsonResponse($this->manager->getFilePackage('android'));
    }

    #[Rest\Get('/api/infos')]
    public function getInfo()
    {

        $user = $this->getUser();
            return new JsonResponse($this->manager->getInfo($user));
    }
}
