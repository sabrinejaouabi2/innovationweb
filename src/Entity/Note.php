<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Note
 *
 * @ORM\Table(name="note", indexes={@ORM\Index(name="note_voitures_ibfk_1", columns={"id_user"}), @ORM\Index(name="note_voitures_ibfk_2", columns={"ServId"})})
 * @ORM\Entity
 */
class Note
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_note_Service", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idNoteService;

    /**
     * @var float
     *
     * @ORM\Column(name="note", type="float", precision=10, scale=0, nullable=false)
     */
    private $note;

    /**
     * @var int
     *
     * @ORM\Column(name="id_user", type="integer", nullable=false)
     */
    private $idUser;

    /**
     * @var int
     *
     * @ORM\Column(name="ServId", type="integer", nullable=false)
     */
    private $servid;

    /**
     * @return int
     */
    public function getIdNoteService(): int
    {
        return $this->idNoteService;
    }

    /**
     * @param int $idNoteService
     */
    public function setIdNoteService(int $idNoteService): void
    {
        $this->idNoteService = $idNoteService;
    }

    /**
     * @return float
     */
    public function getNote(): float
    {
        return $this->note;
    }

    /**
     * @param float $note
     */
    public function setNote(float $note): void
    {
        $this->note = $note;
    }

    /**
     * @return int
     */
    public function getIdUser(): int
    {
        return $this->idUser;
    }

    /**
     * @param int $idUser
     */
    public function setIdUser(int $idUser): void
    {
        $this->idUser = $idUser;
    }

    /**
     * @return int
     */
    public function getServid(): int
    {
        return $this->servid;
    }

    /**
     * @param int $servid
     */
    public function setServid(int $servid): void
    {
        $this->servid = $servid;
    }


}
