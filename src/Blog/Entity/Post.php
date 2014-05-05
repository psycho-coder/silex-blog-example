<?php

namespace Blog\Entity;

class Post
{
    /**
     * Post's identifier
     * @var int
     */
    protected $id;

    /**
     * Post's title
     * @var string
     */
    protected $title;

    /**
     * Post's content
     * @var string
     */
    protected $content;

    /**
     * Post's slug
     * @var string
     */
    protected $slug;

    /**
     * Post's creation date
     * @var DateTime
     */
    protected $created_at;

    /**
     * Post's meta title
     * @var string
     */
    protected $meta_title;

    /**
     * Post's page meta keywords
     * @var string
     */
    protected $meta_keywords;

    /**
     * Post's page meta description
     * @var string
     */
    protected $meta_description;

    /**
     * Gets the Post's identifier.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Sets the Post's identifier.
     *
     * @param int $id the id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets the Post's title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * Sets the Post's title.
     *
     * @param string $title the title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Gets the Post's content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
    
    /**
     * Sets the Post's content.
     *
     * @param string $content the content
     *
     * @return self
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Gets the Post's slug.
     *
     * @return string
     */    
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Sets the Post's slug.
     *
     * @param string $slug 
     *
     * @return self
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * Gets the Post's creation date.
     *
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }
    
    /**
     * Sets the Post's creation date.
     *
     * @param DateTime|null $created_at Post's date
     *
     * @return self
     */
    public function setCreatedAt($created_at)
    {
        if ( !is_null($created_at) && !$created_at instanceof \DateTime ) {
            throw new \InvalidArgumentException('Method setCreatedAt() only accepts DateTime or NULL argument');
        }

        $this->created_at = $created_at;

        return $this;
    }

    /**
     * Gets the Post's meta title.
     *
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->meta_title;
    }
    
    /**
     * Sets the Post's meta title.
     *
     * @param string $meta_title Meta title for post's page
     *
     * @return self
     */
    public function setMetaTitle($meta_title)
    {
        $this->meta_title = $meta_title;

        return $this;
    }

    /**
     * Gets the Post's page meta keywords.
     *
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->meta_keywords;
    }
    
    /**
     * Sets the Post's page meta keywords.
     *
     * @param string $meta_keywords Meta keywords for post's page
     *
     * @return self
     */
    public function setMetaKeywords($meta_keywords)
    {
        $this->meta_keywords = $meta_keywords;

        return $this;
    }

    /**
     * Gets the Post's page meta description.
     *
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->meta_description;
    }
    
    /**
     * Sets the Post's page meta description.
     *
     * @param string $meta_description Meta description for post's page
     *
     * @return self
     */
    public function setMetaDescription($meta_description)
    {
        $this->meta_description = $meta_description;

        return $this;
    }
}