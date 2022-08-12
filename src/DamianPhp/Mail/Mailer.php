<?php

namespace DamianPhp\Mail;

use DamianPhp\Support\Helper;
use DamianPhp\Support\Facades\Response;
use DamianPhp\Contracts\Mail\MailerInterface;

/**
 * Classe client.
 * Pour envoyer des mails.
 * 
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class Mailer implements MailerInterface
{
    private MailingInterface $mailing;

    public function __construct()
    {
        $this->mailing = new SwiftMailerMailing();
    }

    /**
     * Expéditeur du mail.
     *
     * @param array|string $from - (array numéroté) - $from[0] : email, $from[1] : prénom et nom.
     */
    public function setFrom(array|string $from): self
    {
        $this->mailing->setFrom($from);

        return $this;
    }

    /**
     * OPTIONAL
     * Eventuellement préciser à qui répondre.
     */
    public function setReplyTo(string $replyTo): self
    {
        $this->mailing->setReplyTo($replyTo);

        return $this;
    }

    /**
     * Destinataire (receveur) du mail.
     */
    public function setTo(string $to): self
    {
        $this->mailing->setTo($to);

        return $this;
    }

    /**
     * Sujet du mail.
     */
    public function setSubject(string $subject): self
    {
        $this->mailing->setSubject($subject);

        return $this;
    }

    /**
     * OPTIONAL
     * Pour éventuellement laisser la possibilitée de joindre un pièce joint.
     */
    public function attach(?string $attach): self
    {
        if ($attach !== null) {
            $this->mailing->attach($attach);
        }

        return $this;
    }

    /**
     * Corp qui contient le message au format 'text/html'.
     */
    public function setBody(string $path, array $data = []): self
    {
        $body = Response::share(Helper::config('path')['emails'].'/'.$path, $data);

        $this->mailing->setBody($body);

        return $this;
    }

    /**
     * OPTIONAL
     * Pour éventuellement ajouter un cord de message au format 'text/plain'.
     */
    public function addBodyText(string $path,  array $data = []): self
    {
        $bodyText = Response::share(Helper::config('path')['emails'].'/'.$path, $data);

        $this->mailing->addBodyText($bodyText);

        return $this;
    }

    /**
     * Essayer d'envoyer le mail.
     *
     * @return bool - True si le mail est bien parti.
     */
    public function send(): bool
    {
        return $this->mailing->send();
    }
}
