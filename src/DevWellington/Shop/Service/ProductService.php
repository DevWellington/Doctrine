<?php

namespace DevWellington\Shop\Service;

use DevWellington\Shop\Entity\ProductEntity;
use DevWellington\Shop\Mapper\MapperInterface;
use Doctrine\ORM\EntityManager;

class ProductService implements ServiceInterface
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return mixed
     */
    public function fetchAll()
    {
        $repo = $this->em->getRepository("DevWellington\Shop\Entity\ProductEntity");        
        return $repo->findAll();
    }

    public function fetch(int $id)
    {
        $repo = $this->em->getRepository("DevWellington\Shop\Entity\ProductEntity");
        return $repo->find($id);
    }

    /**
     * @param array $data
     * @return ProductEntity
     */
    public function insert(array $data)
    {
        $productEntity = new ProductEntity();

        // $productEntity->setId(7);
        $productEntity->setName($data['name']);
        $productEntity->setDescription($data['description']);
        $productEntity->setValue($data['value']);
        var_dump($productEntity);

        $this->em->persist($productEntity);
        $this->em->flush();

        return $productEntity;
    }

    /**
     * @param array $data
     * @return ProductEntity
     */
    public function update(array $data)
    {
        $productEntity = $this->em->getReference("DevWellington\Shop\Entity\ProductEntity", $data['id']);

        $productEntity->setName($data['name']);
        $productEntity->setDescription($data['description']);
        $productEntity->setValue($data['value']);

        $this->em->persist($productEntity);
        $this->em->flush();

        return $productEntity;
    }

    /**
     * @param $id
     * @return boolean
     */
    public function delete($id)
    {
        $productEntity = $this->em->getReference("DevWellington\Shop\Entity\ProductEntity", $id);

        $this->em->remove($productEntity);
        $this->em->flush();

        return $this->em->flush();
    }

    public function validate(array $data)
    {
        $productEntity = $this->em->getReference("DevWellington\Shop\Entity\ProductEntity", 1);

        $productEntity->setId(isset($data['id']) ? $data['id'] : null);
        $productEntity->setName($data['name']);
        $productEntity->setDescription($data['description']);
        $productEntity->setValue($data['value']);

        return $productEntity;
    }
} 