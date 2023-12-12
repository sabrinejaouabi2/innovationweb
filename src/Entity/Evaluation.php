<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Evaluation
 *
 * @ORM\Table(name="evaluation")
 * @ORM\Entity
 */
class Evaluation
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_eval", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idEval;

    /**
     * @var int
     *
     * @ORM\Column(name="nombreeval", type="integer", nullable=false)
     */
    private $nombreeval;

    public function getIdEval(): ?int
    {
        return $this->idEval;
    }

    public function getNombreeval(): ?int
    {
        return $this->nombreeval;
    }

    public function setNombreeval(int $nombreeval): self
    {
        $this->nombreeval = $nombreeval;

        return $this;
    }


}
