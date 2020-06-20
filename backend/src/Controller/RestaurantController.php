<?php

namespace App\Controller;
header("Access-Control-Allow-Origin: *");

use App\Repository\AppUserRepository;
use App\Repository\RestaurantRepository;
use App\Repository\RestaurantTableRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;

class RestaurantController extends AbstractController
{
    private $restaurantRepository;
    private $appUserRepository;
    private $tableRepository;
    public function __construct(RestaurantRepository $restaurant, AppUserRepository $appUserRepository,RestaurantTableRepository $tableRepository)
    {

        $this->restaurantRepository = $restaurant;
        $this->appUserRepository = $appUserRepository;
        $this->tableRepository = $tableRepository;
    }
    /**
     * Create restaurant
     *
     * This API is used for creating a new restaurant
     *
     * @SWG\Response(
     *     response=200,
     *     description="Restaurant sucessfully added",
     * )
     *@SWG\Parameter(
     *     name="restaurantName",
     *     in="formData",
     *     type="string",
     *     description="Restaurant's name"
     * )
     *@SWG\Parameter(
     *     name="restaurantImage",
     *     in="formData",
     *     type="string",
     *     description="Restaurant's profile image"
     * )
     *
     * @SWG\Tag(name="Restaurant")
     * @Route("/restaurant/add", name="create_restaurant", methods={"POST"})
     */
    public function createRestaurant(Request $request):
    JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $restaurantName = $data['restaurantName'];
        $restaurantImage = $data['restaurantImage'];
        $restaurantName = htmlspecialchars(strip_tags($restaurantName));
        $restaurantImage = htmlspecialchars(strip_tags($restaurantImage));
        if (empty($restaurantName)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }
        $this->restaurantRepository->saveRestaurant($restaurantName, $restaurantImage);
        return new JsonResponse(['response' => 'Restaurant added!'], Response::HTTP_CREATED);
    }

    /**
     *
     *Get all restaurants
     *
     * This API returns all restaurant in JSON
     *
     *@SWG\Response(
     *response=200,
     *description="Users successfully returned",
     * )
     * @SWG\Tag(name="Restaurant")
     * @Route("/restaurant/all", name="get_all_rataurants", methods={"GET"})
     */
    public function getAllRestaurants(): JsonResponse
    {
        $restaurants = $this->restaurantRepository->findAll();
        foreach ($restaurants as $restaurant) {
            $userData = [];
            $users = $restaurant->getUsersInRestaurant();
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
                'id' => $restaurant->getId(),
                'restaurantName' => $restaurant->getRestaurantName(),
                'usersInRestaurant' => $userData,
                'tableType' => $restaurant->getUsersInRestaurant()
            ];
        }
        return new JsonResponse(['restaurants' => $data], Response::HTTP_OK);
    }

    /**
     * Find specific restaurant
     *
     * This API returns only one restaurant in JSON
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns data of one restaurant",
     * )
     * @SWG\Response(
     *     response=400,
     *     description="No such restaurant in DB",
     * )
     * @SWG\Tag(name="Restaurant")
     * @Route("/restaurant/{id}", name="get_one_restaurant", methods={"GET"})
     */
    public function getOneRestaurant($id): JsonResponse
    {
        $restaurant = $this->restaurantRepository->findOneBy(['id' => $id]);

        if ($restaurant==null){
            return new JsonResponse(['status' => "restaurant not found"], Response::HTTP_BAD_REQUEST);
        }
        $userData = [];
        $users = $restaurant->getUsersInRestaurant();
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
            'id' => $restaurant->getId(),
            'restaurantName' => $restaurant->getRestaurantName(),
            'usersInRestaurant' => $userData
        ];
        return new JsonResponse(['restaurants' => $data], Response::HTTP_OK);
    }
    /**
     * Update restaurant
     *
     * This API updates specific restaurant
     * @SWG\Response(
     *     response=200,
     *     description="restaurant sucefully updated",
     * )
     * @SWG\Tag(name="Restaurant")
     * @Route("/restaurant/update/{id}", name="update_restaurant", methods={"PUT"})
     */
    public function updateRestaurant($id, Request $request): JsonResponse
    {
        $restaurant = $this->restaurantRepository->findOneBy(['id' => $id]);
        $data = json_decode($request->getContent(), true);
        $this->restaurantRepository->updateRestaurant($restaurant, $data);
        return new JsonResponse(['status' => 'restaurant updated!']);
    }
    /**
     * Delete restaurant
     *
     * This API deletes specific restaurant
     * @SWG\Response(
     *     response=400,
     *     description="No such restaurant in DB or there are still some users in other groups",
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Restaurant sucefully deleted",
     * )
     * @SWG\Tag(name="Restaurant")
     * @Route("restaurant/delete/{id}", name="delete_restaurant", methods={"DELETE"})
     */
    public function deleteRestaurant($id): JsonResponse
    {
        $restaurant = $this->restaurantRepository->findOneBy(['id' => $id]);
        if (empty($restaurant)) {
            return new JsonResponse(['response' => "restaurant not found"], Response::HTTP_BAD_REQUEST);
        }
        $users = $restaurant->getUsersInRestaurant();
        if (count($users) != 0) {
            return new JsonResponse(['status' => "restaurant can not be deleted because some users are still in a group"], Response::HTTP_BAD_REQUEST);
        } else {
            $this->restaurantRepository->deleteRestaurant($restaurant);
            return new JsonResponse(['status' => 'restaurant deleted'], Response::HTTP_OK);
        }
    }
}
