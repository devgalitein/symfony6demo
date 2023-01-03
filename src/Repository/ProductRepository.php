<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function save(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    
    public function isUniqueProductName($name = null,$product_id = null)
    {
        $query = $this->createQueryBuilder('product');
        
        $query->where('product.name = :name');
        $query->setParameter('name' ,trim($name));
        
        if($product_id != null) {
            $query->andWhere('product.id != :product_id');
            $query->setParameter('product_id',$product_id);
        }
        $results = $query->getQuery()->getOneOrNullResult();
        return $results;
    }
    
    // Get the total number of elements
    public function countResults()
    {
        return $this
            ->createQueryBuilder('product')
            ->select("count(product.id)")
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getRequiredDTData($start, $length, $orders, $search, $columns, $otherConditions)
    {
        // Create Main Query
        $query = $this->createQueryBuilder('product');
        
        // Create Count Query
        $countQuery = $this->createQueryBuilder('product');
        $countQuery->select('COUNT(product.id)');
        
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
                        $searchQuery = 'product.id LIKE :'.$keyword;
                        break;
                    }
                    case 'name':
                    {
                        $searchQuery = 'product.name LIKE :'.$keyword;
                        break;
                    }
                    case 'price':
                    {
                        $searchQuery = 'product.price LIKE :'.$keyword;
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
                        $orderColumn = 'product.id';
                        break;
                    }
                    case 'name':
                    {
                        $orderColumn = 'product.name';
                        break;
                    }
                    case 'price':
                    {
                        $orderColumn = 'product.price';
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

//    /**
//     * @return Product[] Returns an array of Product objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
