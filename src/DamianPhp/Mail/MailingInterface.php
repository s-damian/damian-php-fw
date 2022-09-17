<?php

namespace DamianPhp\Mail;

/**
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
interface MailingInterface
{
    /**
     * Expéditeur du mail.
     *
     * @param string|array $from - (array numéroté) - $from[0] : email, $from[1] : prénom et nom.
     */
    public function setFrom(array|string $from): void;

    /**
     * OPTIONAL
     * Eventuellement préciser à qui répondre.
     */
    public function setReplyTo(string $replyTo): self;

    /**
     * Destinataire (receveur) du mail.
     */
    public function setTo(string $emailTo): void;

    /**
     * Sujet du mail.
     */
    public function setSubject(string $subject): void;

    /**
     * OPTIONAL
     * Pour éventuellement laisser la possibilitée de joindre un pièce joint.
     */
    public function attach(string $attach): void;

    /**
     * Corp qui contient le message au format 'text/html'.
     */
    public function setBody(string $body): void;

    /**
     * OPTIONAL
     * Pour éventuellement ajouter un cord de message au format 'text/plain'.
     */
    public function addBodyText(string $bodyText): void;

    /**
     * Essayer d'envoyer le mail.
     *
     * @return bool - True si le mail est bien parti.
     */
    public function send(): bool;
}
