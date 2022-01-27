<?php

namespace App\Entity;

use App\Repository\LigneRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LigneRepository::class)
 */
class Ligne
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Prestation::class, inversedBy="lignes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $nomPresta;

    /**
     * @ORM\Column(type="float")
     */
    private $prixInd;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $remise;

    /**
     * @ORM\Column(type="float")
     */
    private $prixFinal;

    /**
     * @ORM\ManyToOne(targetEntity=Vente::class, inversedBy="lignes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $vente;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomPresta(): ?Prestation
    {
        return $this->nomPresta;
    }

    public function setNomPresta(?Prestation $nomPresta): self
    {
        $this->nomPresta = $nomPresta;

        return $this;
    }

    public function getPrixInd(): ?float
    {
        return $this->prixInd;
    }

    public function setPrixInd(float $prixInd): self
    {
        $this->prixInd = $prixInd;

        return $this;
    }

    public function getRemise(): ?int
    {
        return $this->remise;
    }

    public function setRemise(?int $remise): self
    {
        $this->remise = $remise;

        return $this;
    }

    public function getPrixFinal(): ?float
    {
        return $this->prixFinal;
    }

    public function setPrixFinal(float $prixFinal): self
    {
        $this->prixFinal = $prixFinal;

        return $this;
    }

    public function getVente(): ?Vente
    {
        return $this->vente;
    }

    public function setVente(?Vente $vente): self
    {
        $this->vente = $vente;

        return $this;
    }
}
