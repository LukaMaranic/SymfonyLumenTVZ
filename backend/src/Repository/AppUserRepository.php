<?php

namespace App\Repository;

use App\Entity\AppUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method AppUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method AppUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method AppUser[]    findAll()
 * @method AppUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppUserRepository extends ServiceEntityRepository
{

    private $appGroupRepository;
    private $manager;

    public function __construct
    (
        ManagerRegistry $registry,
        EntityManagerInterface $manager
    )
    {
        parent::__construct($registry, AppUser::class);
        $this->manager = $manager;
    }
    public function findOneByEmail($email): ?AppUser
    {
        try {
            return $this->createQueryBuilder('a')
                ->andWhere('a.email = :val')
                ->setParameter('val', $email)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
        }
    }
    public function findOneByEmailAndPassword($email,$password): ?AppUser
    {
        try {
            return $this->createQueryBuilder('a')
                ->where('a.email = :email')
                ->andWhere('a.password = :password')
                ->setParameter('email', $email)
                ->setParameter('password', sha1($password))
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
        }
    }
        public function saveUser($username, $isEmployer, $email, $password, $phoneNumber,$address)
    {
        $user = new AppUser();
        $date = date_create();
        $user
            ->setUsername($username)
            ->setIsEmployer($isEmployer)
            ->setEmail($email)
            ->setPassword(sha1($password))
            ->setPhoneNumber($phoneNumber)
            ->setAddress($address)
            ->setDateCreated($date)
            ->setDateModified($date)
            ->setProfileImage("../userImages/defaultAvatarImg.jpeg");

       // $group->addAppUser($user);
        $this->manager->persist($user);
        $this->manager->flush();
    }

    public function updateUser(AppUser $appUser, $data)
    {
        $isUpdated = false;
        if (array_key_exists("username", $data)){
            $appUser->setUsername($data['username']);
            $isUpdated = true;
        }
        if (array_key_exists("isEmployer", $data)){
            $appUser->setIsEmployer($data['isEmployer']);
            $isUpdated = true;
        }
        if (array_key_exists("email", $data)){
            $appUser->setEmail($data['email']);
            $isUpdated = true;
        }
        if (array_key_exists("phoneNumber", $data)){
            $appUser->setPhoneNumber($data['phoneNumber']);
            $isUpdated = true;
        }
        if (array_key_exists("password", $data)){
            $appUser->setPassword($data['password']);
            $isUpdated = true;
        }
        if (array_key_exists("profileImage", $data)){
            $exploded = explode(',', $data['profileImage'], 2);
            $encoded = $exploded[1];
            $decoded = base64_decode($encoded);
            $img = imagecreatefromstring($decoded);
            $file = '../userImages/'.$appUser->getId().".png";
            $appUser->setProfileImage($file);
            imagepng($img, $file);
            imagedestroy($img);
            $isUpdated = true;
        }
        $this->manager->flush();
        return $isUpdated;
    }

    public function deleteUser(AppUser $appUser)
    {
        $this->manager->remove($appUser);
        $this->manager->flush();
    }
}
