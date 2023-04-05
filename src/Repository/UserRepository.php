<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    
    public function isUniqueUsername($name = null,$user_id = null)
    {
        $query = $this->createQueryBuilder('user');
        
        $query->where('user.username = :username');
        $query->setParameter('username' ,trim($name));
        
        if($user_id != null) {
            $query->andWhere('user.id != :user_id');
            $query->setParameter('user_id',$user_id);
        }
        $results = $query->getQuery()->getOneOrNullResult();
        return $results;
    }
    
    public function isUniqueUserEmail($email = null,$user_id = null)
    {
        $query = $this->createQueryBuilder('user');
        
        $query->where('user.email = :email');
        $query->setParameter('email' ,trim($email));
        
        if($user_id != null) {
            $query->andWhere('user.id != :user_id');
            $query->setParameter('user_id',$user_id);
        }
        $results = $query->getQuery()->getOneOrNullResult();
        return $results;
    }
    
    public function countResults()
    {
        return $this
            ->createQueryBuilder('users')
            ->select("count(users.id)")
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getRequiredDTData($start, $length, $orders, $search, $columns, $otherConditions)
    {
        // Create Main Query
        $query = $this->createQueryBuilder('users');
        $query->leftJoin('users.city', 'city');
        $query->leftJoin('users.state', 'state');
        $query->leftJoin('users.country', 'country');
        // Create Count Query
        $countQuery = $this->createQueryBuilder('users');
        $countQuery->select('COUNT(users.id)');
        $countQuery->leftJoin('users.city', 'city');
        $countQuery->leftJoin('users.state', 'state');
        $countQuery->leftJoin('users.country', 'country');
        
        // Fields Search
        foreach ($columns as $key => $column)
        {
            if ($search['value'] != '')
            {
                // $searchItem is what we are looking for
                $searchItem = $search['value'];
                $searchQuery = null;
                $keyword = 'param_search_keyword';
                // $column['name'] is the name of the column as sent by the JS
                switch($column['name'])
                {
                    case 'id':
                    {
                        $searchQuery = 'users.id LIKE :'.$keyword;
                        break;
                    }
                    case 'username':
                    {
                        $searchQuery = 'users.username LIKE :'.$keyword;
                        break;
                    }
                    case 'email':
                    {
                        $searchQuery = 'users.email LIKE :'.$keyword;
                        break;
                    }
                    case 'city':
                    {
                        $searchQuery = 'city.name LIKE :'.$keyword;
                        break;
                    }
                    case 'state':
                    {
                        $searchQuery = 'state.name LIKE :'.$keyword;
                        break;
                    }
                    case 'country':
                    {
                        $searchQuery = 'country.name LIKE :'.$keyword;
                        break;
                    }
                }
        
                if ($searchQuery !== null)
                {
                    $query->orWhere($searchQuery)
                            ->setParameter($keyword, '%'.$searchItem.'%');

                    $countQuery->orWhere($searchQuery)
                            ->setParameter($keyword, '%'.$searchItem.'%');
                }
            }
        }
        
        // Limit
        $query->setFirstResult($start)->setMaxResults($length);
        
        // Order
        foreach ($orders as $key => $order)
        {
            // $order['name'] is the name of the order column as sent by the JS
            if ($order['name'] != '')
            {
                $orderColumn = null;
            
                switch($order['name'])
                {
                    case 'id':
                    {
                        $orderColumn = 'users.id';
                        break;
                    }
                    case 'username':
                    {
                        $orderColumn = 'users.username';
                        break;
                    }
                    case 'email':
                    {
                        $orderColumn = 'users.email';
                        break;
                    }
                    case 'city':
                    {
                        $orderColumn = 'city.name';
                        break;
                    }
                    case 'state':
                    {
                        $orderColumn = 'state.name';
                        break;
                    }
                    case 'country':
                    {
                        $orderColumn = 'country.name';
                        break;
                    }
                }
        
                if ($orderColumn !== null)
                {
                    $query->orderBy($orderColumn, $order['dir']);
                }
            }
        }
        
        // Execute
        $results = $query->getQuery()->getResult();
        $countResult = $countQuery->getQuery()->getSingleScalarResult();
        
        return array(
            "results" 		=> $results,
            "countResult"	=> $countResult
        );
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
