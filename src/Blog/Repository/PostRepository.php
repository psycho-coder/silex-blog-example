<?php

namespace Blog\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\Common\Inflector\Inflector;

use Cocur\Slugify\SlugifyInterface;

use Blog\Entity\Post;

class PostRepository
{
    /**
     * Doctrine DBAL Connection
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;    


    /**
     * Slugificator
     * @var \Cocur\Slugify\SlugifyInterface
     */
    protected $slugify;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function setSlugify(SlugifyInterface $slugify)
    {
        $this->slugify = $slugify;
        return $this;
    }

    public function getSlugify()
    {
        return $this->slugify;
    }

    /**
     * Find post by identifier
     * @param  int   $id 
     * @return Post
     */
    public function find($id)
    {
        $data = $this->db->fetchAssoc(
            sprintf("SELECT * FROM `%s` WHERE `id` = ? LIMIT 1", $this->getTableName()),
            array($id)
        );

        return $data ? $this->buildFromArray($data) : null;
    }

    public function findAll()
    {
        $posts = array();

        $data = $this->db->fetchAll(sprintf("SELECT * FROM `%s`", $this->getTableName()));

        foreach ( $data as $postData ) {
            $posts[] = $this->buildFromArray($postData);
        }

        return $posts;
    }

    public function findBySlug($slug)
    {
        $data = $this->db->fetchAssoc(
            sprintf("SELECT * FROM `%s` WHERE `slug` = ? LIMIT 1", $this->getTableName()),
            array($slug)
        );

        return $data ? $this->buildFromArray($data) : null;        
    }

    public function save(Post $post)
    {
        // check if date wasn't set
        if ( !$post->getCreatedAt() ) {
            $post->setCreatedAt(new \DateTime(date('Y-m-d')));
        }

        // check if we need to generate slug from title
        if ( !$post->getSlug() ) {
            $post->setSlug($this->getSlugify()->slugify($post->getTitle()));
        }

        $data = array(
            'title'            => $post->getTitle(),
            'content'          => $post->getContent(),
            'slug'             => $post->getSlug(),
            'created_at'       => $post->getCreatedAt()->format('Y-m-d'),
            'meta_title'       => $post->getMetaTitle(),
            'meta_keywords'    => $post->getMetaKeywords(),
            'meta_description' => $post->getMetaDescription()
        );

        if ( $post->getId() ) {
            $this->db->update($this->getTableName(), $data, array('id' => $post->getId()));
        } else {
            $this->db->insert($this->getTableName(), $data);
            $post->setId($this->db->lastInsertId());
        }
    }

    /**
     * Delete post by identifier
     * @param  int $id Post's id
     * @return int Number of affected rows
     */
    public function delete($id)
    {
        return $this->db->delete($this->getTableName(), array('id' => $id));
    }

    public function getTableName()
    {
        return 'posts';
    }

    /**
     * Create Post object from array 
     * @param  array  $postData Associative array of values
     * @return Post
     */
    protected function buildFromArray(array $postData)
    {
        $post = new Post();

        foreach ( $postData as $property => $value )
        {
            $method = Inflector::camelize('set_'.$property);

            if ( $property == 'created_at' ) {
                $value = new \DateTime($value);
            }

            $post->$method($value);
        }

        return $post;
    }

    /**
     * Create QueryBuilder object for Pagination class
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function getPaginationQueryBuilder()
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder
            ->select('p.*')
            ->from($this->getTableName(), 'p')
            ->orderBy('p.created_at', 'DESC')
            ->addOrderBY('p.id', 'DESC');

        return $queryBuilder;
    }
}