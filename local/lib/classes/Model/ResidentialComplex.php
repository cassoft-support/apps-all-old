<?php

namespace Cassoft\Model;

class ResidentialComplex
{
    private string $name;
    private string $link = '';
    private string $dateBegin = '';
    private string $dateEnd = '';
    private string $description = '';
    private string $avitoId = '';
    private string $cianId = '';
    private string $yandexId = '';
    private string $lat = '';
    private string $long = '';
    private int $city;
    private string $class = '';
    private string $type = '';

    public function __construct(string $name, int $city)
    {
        $this->name = $name;
        $this->city = $city;
    }

    /**
     * Get the value of link
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set the value of link
     *
     * @return  self
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get the value of dateBegin
     */
    public function getDateBegin()
    {
        return $this->dateBegin;
    }

    /**
     * Set the value of dateBegin
     *
     * @return  self
     */
    public function setDateBegin($dateBegin)
    {
        $this->dateBegin = $dateBegin;

        return $this;
    }

    /**
     * Get the value of dateEnd
     */
    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    /**
     * Set the value of dateEnd
     *
     * @return  self
     */
    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    /**
     * Get the value of description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @return  self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of avitoId
     */
    public function getAvitoId()
    {
        return $this->avitoId;
    }

    /**
     * Set the value of avitoId
     *
     * @return  self
     */
    public function setAvitoId($avitoId)
    {
        $this->avitoId = $avitoId;

        return $this;
    }

    /**
     * Get the value of cianId
     */
    public function getCianId()
    {
        return $this->cianId;
    }

    /**
     * Set the value of cianId
     *
     * @return  self
     */
    public function setCianId($cianId)
    {
        $this->cianId = $cianId;

        return $this;
    }

    /**
     * Get the value of yandexId
     */
    public function getYandexId()
    {
        return $this->yandexId;
    }

    /**
     * Set the value of yandexId
     *
     * @return  self
     */
    public function setYandexId($yandexId)
    {
        $this->yandexId = $yandexId;

        return $this;
    }

    /**
     * Get the value of lat
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set the value of lat
     *
     * @return  self
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get the value of long
     */
    public function getLong()
    {
        return $this->long;
    }

    /**
     * Set the value of long
     *
     * @return  self
     */
    public function setLong($long)
    {
        $this->long = $long;

        return $this;
    }

    /**
     * Get the value of class
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set the value of class
     *
     * @return  self
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Get the value of type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the value of type
     *
     * @return  self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the value of name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the value of city
     */
    public function getCity()
    {
        return $this->city;
    }
}