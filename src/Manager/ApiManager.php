<?php

namespace App\Manager;


use App\Entity\Logiciel;
use App\Entity\News;
use App\Entity\Paragraphe;
use App\Entity\Tag;
use App\Entity\User;
use App\Services\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ApiManager extends BaseManager
{
    protected $fileUploader;
    protected $passwordHasher;
    public function __construct(EntityManagerInterface $em, FileUploader $fileUploader, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct($em);
        $this->fileUploader = $fileUploader;
        $this->passwordHasher = $passwordHasher;
    }

    public function addTag($data)
    {
        //dd($data["tag"]);
        //dd($data);
        if(!isset($data['tag']) || $data['tag'] == ''){
            return array($this->CODE_KEY => 500, $this->STATUS_KEY => false, $this->MESSAGE_KEY => "Veuillez renseigne le nom du tag!");
        }
        $libTag = $data["tag"];

        if($this->em->getRepository(Tag::class)->findOneBy(['libelle' => $libTag])){
            return array($this->CODE_KEY => 500, $this->STATUS_KEY => false, $this->MESSAGE_KEY => "Ce tag existe déjà!");
        }

        $tag = new Tag();
        $tag->setLibelle($libTag);
        $this->em->persist($tag);
        $this->em->flush();

        return array($this->CODE_KEY => 201, $this->STATUS_KEY => true, $this->MESSAGE_KEY => "Tag créé avec succès!");
    }

    public function updateTag($data)
    {
        $tag = $this->em->getRepository(Tag::class)->find($data['id']);
        if(!$tag){
            return array($this->CODE_KEY => 500, $this->STATUS_KEY => false, $this->MESSAGE_KEY => "Ce tag n'existe pas!");
        }

        if(!isset($data['tag']) || $data['tag'] == ''){
            return array($this->CODE_KEY => 500, $this->STATUS_KEY => false, $this->MESSAGE_KEY => "Veuillez renseigne le nom du tag!");
        }

        $tag->setLibelle($data['tag']);
        $this->em->flush();

        return array($this->CODE_KEY => 201, $this->STATUS_KEY => true, $this->MESSAGE_KEY => "Tag modifié avec succès!");
    }

    public function listTag()
    {
        $tags = $this->em->getRepository(Tag::class)->findTags();
        return array($this->CODE_KEY => 200, $this->STATUS_KEY => true, $this->DATA_KEY => $tags);
    }

    public function removeTag($id)
    {
        $tag = $this->em->getRepository(Tag::class)->find($id);
        if(!$tag){
            return array($this->CODE_KEY => 500, $this->STATUS_KEY => false, $this->MESSAGE_KEY => "Ce tag n'existe pas!");
        }

        $this->em->remove($tag);
        $this->em->flush();

        return array($this->CODE_KEY => 201, $this->STATUS_KEY => true, $this->MESSAGE_KEY => "Tag supprimé avec succès!");
    }

    public function createNews($data)
    {
        if(!isset($data['titre']) || $data['titre'] == ''){
            return array($this->CODE_KEY => 500, $this->STATUS_KEY => false, $this->MESSAGE_KEY => "Veuillez définir un titre !");
        }

        if(!isset($data['p']) || count($data['p']) == 0){
            return array($this->CODE_KEY => 500, $this->STATUS_KEY => false, $this->MESSAGE_KEY => "Veuillez définir un paragraphe !");
        }

        $news = new News();
        $news->setTitre($data['titre'])->setImage($data['image'] ? $this->fileUploader->upload($data['image']) : null)
            ->setVideo($data['video'] ? $this->fileUploader->upload($data['video']) : null)->setDate(new \DateTime());
        /*foreach ($data['tag'] as $t)
        {
            $tag = $this->em->getRepository(Tag::class)->find((int)$t);
            if($tag){
                $news->addTag($tag);
            }
        }*/

        foreach ($data['p'] as $p)
        {
            $paragraphe =  new Paragraphe();
            $paragraphe->setContent($p)->setNews($news);
            $this->em->persist($paragraphe);
        }

        $this->em->persist($news);
        $this->em->flush();

        return array($this->CODE_KEY => 201, $this->STATUS_KEY => true, $this->MESSAGE_KEY => "Actualité créée avec succès!");

    }

    public function listNews()
    {
        $news = $this->em->getRepository(News::class)->findNews();
        $data = [];
        /*$tags = $this->em->getRepository(Tag::class)->findAll();
        foreach ($tags as $t)
        {
            dd($t->contain());
        }*/
        foreach ($news as $n)
        {
            $n['paragraphes'] = $this->em->getRepository(Paragraphe::class)->findParagraphes($n['id']);
            //dd($this->em->getRepository(News::class)->find((int)$n['id'])->getTag());
           // $n['tags'] = $tags;
            $n['image'] = $n['image'] ? $this->fileUploader->getUrl($n['image']) : null;
            $n['video'] = $n['video'] ? $this->fileUploader->getUrl($n['video']) : null;
            $data[] = $n;
        }

        return array($this->CODE_KEY => 200, $this->STATUS_KEY => true, $this->DATA_KEY => $data);

    }

    public function addLogiciel($data)
    {
        if(!isset($data['name']) || $data['name'] == ''){
            return array($this->CODE_KEY => 500, $this->STATUS_KEY => false, $this->MESSAGE_KEY => "Veuillez renseigner le nom du logiciel !");
        }

        if(!isset($data['version']) || $data['version'] == ''){
            return array($this->CODE_KEY => 500, $this->STATUS_KEY => false, $this->MESSAGE_KEY => "Veuillez renseigner la version du logiciel !");
        }

        if(!isset($data['type']) || $data['type'] == ''){
            return array($this->CODE_KEY => 500, $this->STATUS_KEY => false, $this->MESSAGE_KEY => "Veuillez renseigner le type du logiciel !");
        }

        if(!isset($data['packageFile']) || $data['packageFile'] == ''){
            return array($this->CODE_KEY => 500, $this->STATUS_KEY => false, $this->MESSAGE_KEY => "Veuillez uploader le package du logiciel !");
        }

        $logiciel = new Logiciel();
        $logiciel->setName($data['name'])->setVersion($data['version'])->setType($data['type'])->setTaille($data['taille'] ?? '')
            ->setDate(new \DateTime())->setIsActive(true)->setPackage($this->fileUploader->upload($data['packageFile']));

        $this->desactiveLogiciel($data['type']);
        $this->em->persist($logiciel);
        $this->em->flush();

        return array($this->CODE_KEY => 201, $this->STATUS_KEY => true, $this->MESSAGE_KEY => "La version ".$data['version']." est ajoutée avec succès!");

    }

    public function desactiveLogiciel($type)
    {
        $logiciels = $this->em->getRepository(Logiciel::class)->findBy(['type' => $type]);
        foreach ($logiciels as $logiciel){
            $logiciel->setIsActive(false);
        }
    }

    public function listLogiciels()
    {
        $logiciels = $this->em->getRepository(Logiciel::class)->findLogiciels();

        return array($this->CODE_KEY => 200, $this->STATUS_KEY => true, $this->DATA_KEY => $logiciels);
    }

    public function activeLogiciel($data, $id)
    {
        $this->desactiveLogiciel($data['type']);
        $logiciel = $this->em->getRepository(Logiciel::class)->find($id);
        $logiciel->setIsActive(true);
        $this->em->persist($logiciel);
        $this->em->flush();

        return array($this->CODE_KEY => 201, $this->STATUS_KEY => true, $this->MESSAGE_KEY => "Package activé avec succès!");
    }

    public function createUser($data)
    {
        if(!isset($data['email']) || $data['email'] == ''){
            return array($this->CODE_KEY => 500, $this->STATUS_KEY => false, $this->MESSAGE_KEY => "Veuillez renseigner l'email!");
        }

        if(!isset($data['prenom']) || $data['prenom'] == ''){
            return array($this->CODE_KEY => 500, $this->STATUS_KEY => false, $this->MESSAGE_KEY => "Veuillez renseigner le prénom!");
        }

        if(!isset($data['nom']) || $data['nom'] == ''){
            return array($this->CODE_KEY => 500, $this->STATUS_KEY => false, $this->MESSAGE_KEY => "Veuillez renseigner le nom!");
        }

        if(!isset($data['password']) || $data['password'] == ''){
            return array($this->CODE_KEY => 500, $this->STATUS_KEY => false, $this->MESSAGE_KEY => "Veuillez renseigner le mot de passe!");
        }

        $userExists = $this->em->getRepository(User::class)->findOneBy(['email' => $data['email']]);

        if($userExists){
            return array($this->CODE_KEY => 500, $this->STATUS_KEY => false, $this->MESSAGE_KEY => "Cet email est déjà utilisé!");
        }

        $user = new User();
        $user->setEmail($data['email'])->setPrenom($data['prenom'])->setNom($data['nom'])
            ->setPassword($this->passwordHasher->hashPassword($user, $data['password']))
            ->setPhoto($data['photo'] ? $this->fileUploader->upload($data['photo']) : 'emptyUser.webp')->setRoles(['ROLE_ADMIN']);

        $this->em->persist($user);
        $this->em->flush();

        return array($this->CODE_KEY => 201, $this->STATUS_KEY => true, $this->MESSAGE_KEY => "Compte créé avec succès!");

    }

    public function getUsers()
    {
        $users = $this->em->getRepository(User::class)->findAdmins();
        $data = [];
        foreach ($users as $user){
            $user['photo'] = $this->fileUploader->getUrl($user['photo']);
            $data[] = $user;
        }

        return array($this->CODE_KEY => 200, $this->STATUS_KEY => true, $this->DATA_KEY => $data);

    }

    public function getFilePackage ($type)
    {
        $package = $this->em->getRepository(Logiciel::class)->findOneBy(['isActive' => true, 'type' => $type]);
        $link = $this->fileUploader->getUrl($package->getPackage());
        return array($this->CODE_KEY => 200, $this->STATUS_KEY => true, $this->DATA_KEY => $link);
    }

    public function getInfo ($user)
    {
        $data = [
            "id" => $user->getId(),
            "email" => $user->getEmail(),
            "prenom" => $user->getPrenom(),
            "nom" => $user->getNom(),
            "photo" =>  $this->fileUploader->getUrl($user->getPhoto()),
        ];
        return array($this->CODE_KEY => 200, $this->STATUS_KEY => true, $this->DATA_KEY => $data);
    }

}
