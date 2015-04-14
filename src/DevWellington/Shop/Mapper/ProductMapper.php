<?php

namespace DevWellington\Shop\Mapper;

use DevWellington\Shop\Entity\EntityInterface;
use Doctrine\ORM\EntityManager;

class ProductMapper implements MapperInterface
{
    /**
     * @var EntityInterface
     */
    private $productEntity;

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo, EntityManager $em)
    {
        $this->pdo = $pdo;
        $this->em = $em;
    }

    /**
     * @return EntityInterface
     */
    public function getEntity()
    {
        return $this->productEntity;
    }

    /**
     * @param EntityInterface $productEntity
     * @return $this
     */
    public function setEntity(EntityInterface $productEntity)
    {
        $this->productEntity = $productEntity;
        return $this;
    }

    /**
     * @return array|bool
     */
    public function fetchAll()
    {
        $sql = "SELECT * FROM product";

        $stmt = $this->pdo->prepare($sql);
        if($stmt->execute())
            return $stmt->fetchAll(\PDO::FETCH_OBJ);

        return false;
    }

    public function fetch($id)
    {
        $sql = "SELECT * FROM product WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        if($stmt->execute(array(':id' => $id)))
            return $stmt->fetch(\PDO::FETCH_OBJ);

        return false;
    }

    /**
     * @return bool|EntityInterface
     */
    public function insert()
    {
        $this->em->persist($this->productEntity);
        $this->em->flush();

        return $this->returnData();
    }

    /**
     * @return bool|EntityInterface
     */
    public function update()
    {
        $this->em->merge($this->productEntity);
        $this->em->flush();

        return $this->returnData();
    }

    /**
     * @return bool|EntityInterface
     */
    public function delete()
    {
        $products = $this->em->getReference(
            'DevWellington\Shop\Entity\ProductEntity',
            $this->productEntity->getId()
        );

        $this->em->remove($products);
        $this->em->flush();

        return $this->returnData();
    }

    /**
     * @return array
     */
    private function returnData()
    {
        $data['id'] =
            ($this->productEntity->getId() == '') ?
                $this->pdo->lastInsertId() :
                $this->productEntity->getId()
        ;
        $data['name'] = $this->productEntity->getName();
        $data['description'] = $this->productEntity->getDescription();
        $data['value'] = $this->productEntity->getValue();

        return $data;
    }
}