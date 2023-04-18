<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $baseCost = null;

    #[ORM\Column]
    private ?float $languageCost = null;

    #[ORM\Column]
    private ?float $channelCost = null;

    #[ORM\Column]
    private ?int $rate = null;
    public string $baseCostTime;
    public string $languageCostTime;
    public string $channelCostTime;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getBaseCost(): ?float
    {
        return $this->baseCost;
    }
    public function getBaseCostAsTime(): ?string
    {
        return $this->baseCostTime;
    }

    public function setBaseCost(float $baseCost): self
    {
        $this->baseCost = $baseCost;

        return $this;
    }
    public function setBaseCostAsTime(string $time): self
    {
        $this->baseCostTime = $this->decimalToTime($time);
        return $this;
    }

    public function getLanguageCost(): ?float
    {
        return $this->languageCost;
    }
    public function getLanguageCostAsTime(): ?string
    {
        return $this->languageCostTime;
    }

    public function setLanguageCost(float $languageCost): self
    {
        $this->languageCost = $languageCost;

        return $this;
    }
    public function setLanguageCostAsTime(string $time): self
    {
        $this->languageCostTime = $this->decimalToTime($time);
        return $this;
    }

    public function getChannelCost(): ?float
    {
        return $this->channelCost;
    }
    public function getChannelCostAsTime(): ?string
    {
        return $this->channelCostTime;
    }

    public function setChannelCost(float $channelCost): self
    {
        $this->channelCost = $channelCost;

        return $this;
    }
    public function setChannelCostAsTime(string $time): self
    {
        $this->channelCostTime = $this->decimalToTime($time);
        return $this;
    }

    public function getRate(): ?int
    {
        return $this->rate;
    }

    public function setRate(int $rate): self
    {
        $this->rate = $rate;

        return $this;
    }
    private function decimalToTime($decimal): string{
        return gmdate('H:i', floor($decimal * 3600));
    }
}
