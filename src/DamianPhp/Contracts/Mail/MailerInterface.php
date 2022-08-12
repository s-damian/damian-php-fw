<?php

namespace DamianPhp\Contracts\Mail;

/**
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
Interface MailerInterface
{
    public function __construct();
    
    /**
     * Expéditeur du mail.
     *
     * @param array|string $from - (array numéroté) - $from[0] : email, $from[1] : prénom et nom.
     */
    public function setFrom(array|string $from): self;

    /**
     * OPTIONAL
     * Eventuellement préciser à qui répondre.
     */
    public function setReplyTo(string $replyTo): self;

    /**
     * Destinataire (receveur) du mail.
     */
    public function setTo(string $emailTo): self;

    /**
     * Sujet du mail.
     */
    public function setSubject(string $subject): self;

    /**
     * OPTIONAL
     * Pour éventuellement laisser la possibilitée de joindre un pièce joint.
     */
    public function attach(?string $attach): self;

    /**
     * Corp qui contient le message au format 'text/html'.
     */
    public function setBody(string $path, array $data = []): self;

    /**
     * OPTIONAL
     * Pour éventuellement ajouter un cord de message au format 'text/plain'.
     */
    public function addBodyText(string $path,  array $data = []): self;

    /**
     * Essayer d'envoyer le mail.
     *
     * @return bool - True si le mail est bien parti.
     */
    public function send(): bool;
}
