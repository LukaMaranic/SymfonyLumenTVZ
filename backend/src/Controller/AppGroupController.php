<?php

namespace App\Controller;
header("Access-Control-Allow-Origin: *");
use App\Repository\AppUserRepository;
use App\Repository\AppGroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Swagger\Annotations as SWG;

class AppGroupController extends AbstractController
{
    private $appGroupRepository;
    private $appUserRepository;
    public function __construct(AppGroupRepository $appGroupRepository, AppUserRepository $appUserRepository)
    {
        $this->appGroupRepository = $appGroupRepository;
        $this->appUserRepository = $appUserRepository;
    }

    /**
     * Create group
     *
     * This API is used for creating a new group for users to be in
     *
     * @SWG\Response(
     *     response=200,
     *     description="Restaurant sucessfully added",
     * )
     *@SWG\Parameter(
     *     name="groupName",
     *     in="formData",
     *     type="string",
     *     description="Group's name"
     * )
     *@SWG\Parameter(
     *     name="groupImage",
     *     in="formData",
     *     type="string",
     *     description="Group's profile image"
     * )
     *
     * @SWG\Tag(name="Group")
     * @Route("/group/add", name="create_group", methods={"POST"})
     */
    public function createGroup(Request $request):
    JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $groupName = $data['groupName'];
        $groupImage = $data['groupImage'];
        $groupName = htmlspecialchars(strip_tags($groupName));
        $groupImage = htmlspecialchars(strip_tags($groupImage));
        if (empty($groupName)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }
        $this->appGroupRepository->saveGroup($groupName, $groupImage);
        return new JsonResponse(['group' => 'Group added!'], Response::HTTP_CREATED);
    }

    /**
     *
     *Get all groups
     *
     * This API returns all groups in JSON
     *
     *@SWG\Response(
     *response=200,
     *description="Users successfully returned",
     * )
     * @SWG\Tag(name="Group")
     * @Route("/group/all", name="get_all_groups", methods={"GET"})
     */
    public function getAllGroups(): JsonResponse
    {
        $groups = $this->appGroupRepository->findAll();
        $users = [];
        foreach ($groups as $group) {
            $userData = [];
            $users = $group->getUsersInGroup();
            foreach ($users as $user) {
                $userData[] = [
                    'id' => $user->getId(),
                    'username' => $user->getUsername(),
                    'isEmployer' => $user->getIsEmployer(),
                    'email' => $user->getEmail(),
                    'password' => $user->getPassword(),
                    'phoneNumber' => $user->getPhoneNumber()
                ];
            }
            $data[] = [
                'id' => $group->getId(),
                'groupName' => $group->getGroupName(),
                'usersInGroup' => $userData
            ];
        }
        return new JsonResponse(['groups' => $data], Response::HTTP_OK);
    }

    /**
     * Find specific group
     *
     * This API returns only one group in JSON
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns data of one group",
     * )
     * @SWG\Response(
     *     response=400,
     *     description="No such group in DB",
     * )
     * @SWG\Tag(name="Group")
     * @Route("/group/{id}", name="get_one_group", methods={"GET"})
     */
    public function getOneGroup($id): JsonResponse
    {
        $group = $this->appGroupRepository->findOneBy(['id' => $id]);
        if ($group==null){
            return new JsonResponse(['status' => "group not found"], Response::HTTP_BAD_REQUEST);
        }
        $userData = [];
        $users = $group->getUsersInGroup();
        foreach ($users as $user) {
            $userData[] = [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'isEmployer' => $user->getIsEmployer(),
                'email' => $user->getEmail(),
                'password' => $user->getPassword(),
                'phoneNumber' => $user->getPhoneNumber()
            ];
        }
        $data[] = [
            'id' => $group->getId(),
            'groupName' => $group->getGroupName(),
            'usersInGroup' => $userData
        ];
        return new JsonResponse(['group' => $data], Response::HTTP_OK);
    }

    /**
     * Update group
     *
     * This API updates specific group
     * @SWG\Response(
     *     response=400,
     *     description="No such user in DB or PUT data is in bad format",
     * )
     * @SWG\Response(
     *     response=200,
     *     description="User sucefully updated",
     * )
     * @SWG\Tag(name="Group")
     * @Route("/group/update/{id}", name="update_group", methods={"PUT"})
     */
    public function updateGroup($id, Request $request): JsonResponse
    {
        $group = $this->appGroupRepository->findOneBy(['id' => $id]);
        $data = json_decode($request->getContent(), true);
        $this->appGroupRepository->updateGroup($group, $data);
        return new JsonResponse(['status' => 'group updated!']);
    }

    /**
     * Delete group
     *
     * This API deletes specific group
     * @SWG\Response(
     *     response=400,
     *     description="No such group in DB or some users are still in this group",
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Group sucefully deleted",
     * )
     * @SWG\Tag(name="Group")
     * @Route("group/delete/{id}", name="delete_group", methods={"DELETE"})
     */
    public function deleteGroup($id): JsonResponse
    {
        $group = $this->appGroupRepository->findOneBy(['id' => $id]);
        if (empty($group)) {
            return new JsonResponse(['status' => "group not found"], Response::HTTP_BAD_REQUEST);
        }
        $users = $group->getUsersInGroup();
        if (count($users) != 0) {
            return new JsonResponse(['status' => "group can not be deleted because some users are still in group"], Response::HTTP_BAD_REQUEST);
        } else {
            $this->appGroupRepository->deleteGroup($group);
            return new JsonResponse(['status' => 'group deleted'], Response::HTTP_OK);
        }
    }
}

