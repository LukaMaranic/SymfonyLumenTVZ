<?php

namespace App\Controller;
header("Access-Control-Allow-Origin:*");
header("Access-Control-Allow-Methods: HEAD, GET, POST, PUT, DELETE, OPTIONS");
use App\Repository\AppUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;

/**
 * Class AppUserController
 * @package App\Controller
 */
class AppUserController extends AbstractController
{
    /**
     * @var AppUserRepository
     */
    private $appUserRepository;
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * AppUserController constructor.
     * @param AppUserRepository $appUserRepository
     * @param EntityManagerInterface $manager
     */
    public function __construct(
        AppUserRepository $appUserRepository,
        EntityManagerInterface $manager
    )
    {
        $this->appUserRepository = $appUserRepository;
        $this->manager = $manager;
    }
    /**
     * Login user
     *
     * This API is used for users of Restorante Rezervante to login users
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns data of user who has logged in",
     * )
     * @SWG\Response(
     *     response=400,
     *     description="POST data is either in bad format or empty",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="No such user in DB",
     * )
     *@SWG\Parameter(
     *     name="email",
     *     in="formData",
     *     type="string",
     *     description="User email"
     * )
     *@SWG\Parameter(
     *     name="password",
     *     in="formData",
     *     type="string",
     *     description="User password"
     * )
     *
     * @SWG\Tag(name="User")
     * @Route("user/login", name="api_login", methods={"POST"})
     */
    public function login(Request $request)
    {
        try {
            $email = $request->request->get("email");
            $password = $request->request->get("password");
        } catch (\Exception $e) {
            return new JsonResponse(['status' => "Exception: ".$e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        $email=htmlspecialchars(strip_tags($email));
        $password=htmlspecialchars(strip_tags($password));

        if (empty($email) || empty($password)) {
            return new JsonResponse(['status' => "Expecting mandatory parameters!"], Response::HTTP_BAD_REQUEST);
        }

        $temp= $this->appUserRepository->findOneByEmailAndPassword($email,$password);
        if ($temp==null){
            return new JsonResponse(['status' => "User not found"], Response::HTTP_NOT_FOUND);
        }else{
            $path = $temp->getProfileImage();
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

            $assocArray = array(
                "id"=>$temp->getId(),
                "username"=>$temp->getUsername(),
                "isEmployer"=>$temp->getIsEmployer(),
                "address"=>$temp->getAddress(),
                "email"=>$temp->getEmail(),
                "phoneNumber"=>$temp->getPhoneNumber(),
                "profileImage"=>$base64
            );
            return new JsonResponse(['user' => $assocArray], Response::HTTP_OK);
        }
    }

    /**
     *Register user
     *
     * This API is used for users of Restorante Rezervante to register users
     *
     *@SWG\Response(
     *response=201,
     *description="User successfully added",
     * )
     *@SWG\Response(
     *response=400,
     *description="Either mail alredy exists or POST data is in wrong format",
     * )
     *@SWG\Parameter(
     *     name="username",
     *     in="formData",
     *     type="string",
     *     description="Username that is shown in app"
     * )
     *@SWG\Parameter(
     *     name="isEmployer",
     *     in="formData",
     *     type="boolean",
     *     description="Differentiate regular users from premium users"
     * )
     *@SWG\Parameter(
     *     name="email",
     *     in="formData",
     *     type="string",
     *     description="User email"
     * )
     *@SWG\Parameter(
     *     name="password",
     *     in="formData",
     *     type="string",
     *     description="User password"
     * )
     *@SWG\Parameter(
     *     name="phoneNumber",
     *     in="formData",
     *     type="string",
     *     description="User phone number"
     * )
     *@SWG\Parameter(
     *     name="address",
     *     in="formData",
     *     type="string",
     *     description="User address"
     * )
     * @SWG\Tag(name="User")
     * @Route("user/register", name="api_register", methods={"POST"})
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $username = $request->request->get("username");
            $isEmployer = $request->request->get("isEmployer");
            $email = $request->request->get("email");
            $password = $request->request->get("password");
            $phoneNumber = $request->request->get("phoneNumber");
            $address = $request->request->get("address");
        } catch (\Exception $e) {
            return new JsonResponse(['status' => "Exception: ".$e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        $username=htmlspecialchars(strip_tags($username));
        $isEmployer=htmlspecialchars(strip_tags($isEmployer));
        $email=htmlspecialchars(strip_tags($email));
        $password=htmlspecialchars(strip_tags($password));
        $phoneNumber=htmlspecialchars(strip_tags($phoneNumber));
        $address=htmlspecialchars(strip_tags($address));

        if (empty($username) || empty($isEmployer) || empty($email) || empty($password)|| empty($phoneNumber) || empty($address)) {
            return new JsonResponse(['status' => "Expecting mandatory parameters!"], Response::HTTP_BAD_REQUEST);
        }

        $temp= $this->appUserRepository->findOneByEmail($email);
        if ($temp!=null){
            return new JsonResponse(['status' => "Email is already in use"], Response::HTTP_BAD_REQUEST);
        }else{
            $this->appUserRepository->saveUser($username,$isEmployer,$email,$password,$phoneNumber,$address);
            return new JsonResponse(['status' => "User successfully added"], Response::HTTP_CREATED);
        }
    }

   /**
    *
    *Get all users
    *
    * This API returns all users in JSON
    *
    *@SWG\Response(
    *response=200,
    *description="Group successfully returned",
    * )
    * @SWG\Tag(name="User")
    * @Route("/user/all", name="get_all_users", methods={"GET"})
    */
    public function getAllUsers(): JsonResponse
    {
        $users = $this->appUserRepository->findAll();
        $groups = [];

        foreach ($users as $user) {
            $groupsOfUser= [];
            $groups=$user->getGroupId();

            foreach ($groups as $group) {
                 $groupsOfUser[] = [
                        'id' => $group->getId(),
                        'groupName' => $group->getGroupName()
                    ];  
            }

            $data[] = [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'isEmployer' => $user->getIsEmployer(),
                'email' => $user->getEmail(),
                'password' => $user->getPassword(),
                'phoneNumber'=> $user->getPhoneNumber(),
                'groupsOfUser'=> $groupsOfUser
            ];
        }
        return new JsonResponse(['users' => $data], Response::HTTP_OK);
    }

    /**
     * Find specific user
     *
     * This API returns only one user in JSON
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns data of one user",
     * )
     * @SWG\Response(
     *     response=400,
     *     description="No such user in DB",
     * )
     * @SWG\Tag(name="User")
     * @Route("user/get/{id}", name="get_one_user", methods={"GET"})
     */
    public function getOneUser($id): JsonResponse
    {
        $user = $this->appUserRepository->findOneBy(['id' => $id]);
        if (empty($user)) {
            return new JsonResponse(['status' => "user not found"], Response::HTTP_BAD_REQUEST);
        }
        $groups = $user->getGroupId();

        $groupsOfUser= [];
        $groups=$user->getGroupId();
        foreach ($groups as $group) {
             $groupsOfUser[] = [
            'id' => $group->getId(),
            'groupName' => $group->getGroupName()
        ];  
        }

        $data = [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'isEmployer' => $user->getIsEmployer(),
            'password' => $user->getPassword(),
            'groupsOfUser' =>$groupsOfUser
        ];

        return new JsonResponse(['user' => $data], Response::HTTP_OK);
    }

    /**
     * Update user
     *
     * This API updates specific user
     * @SWG\Response(
     *     response=400,
     *     description="No such user in DB or PUT data is in bad format",
     * )
     * @SWG\Response(
     *     response=200,
     *     description="User sucefully updated",
     * )
     * @SWG\Tag(name="User")
     * @Route("user/update/{id}", name="update_user", methods={"PUT"})
     */
    public function updateUser($id, Request $request): JsonResponse
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            return new JsonResponse(Response::HTTP_OK);
        }
        $user = $this->appUserRepository->find($id);

        if (empty($user)) {
            return new JsonResponse(['status' => "user not found"], Response::HTTP_BAD_REQUEST);
        }
        $data = json_decode($request->getContent(), true);

        if ($this->appUserRepository->updateUser($user, $data)){
            return new JsonResponse(['status' => 'user updated!'], Response::HTTP_OK);
        }
        return new JsonResponse(['status' => 'user not updated!'], Response::HTTP_BAD_REQUEST);

    }

    /**
     * Delete user
     *
     * This API deletes specific user
     * @SWG\Response(
     *     response=400,
     *     description="No such user in DB or user is in other groups",
     * )
     * @SWG\Response(
     *     response=200,
     *     description="User sucefully deleted",
     * )
     * @SWG\Tag(name="User")
     * @Route("user/delete/{id}", name="delete_user", methods={"DELETE"})
     */
    public function deleteUser($id): JsonResponse
    {
        $user = $this->appUserRepository->findOneBy(['id' => $id]);
        if (empty($user)) {
             return new JsonResponse(['status' => "user not found"], Response::HTTP_BAD_REQUEST);
        }
        $groups = $user->getGroupId();
        if (count($groups)>0){
            return new JsonResponse(['status' => "can not delete user who is in group"], Response::HTTP_BAD_REQUEST);
        }
        $this->appUserRepository->deleteUser($user);

        return new JsonResponse(['status' => 'user deleted']);
    }


  }
