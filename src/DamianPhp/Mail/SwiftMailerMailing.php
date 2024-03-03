<?php

declare(strict_types=1);

namespace DamianPhp\Mail;

use Swift_Mailer;
use Swift_Message;
use Swift_Attachment;
use Swift_SmtpTransport;
use DamianPhp\Support\Helper;
use Swift_SendmailTransport;
use Swift_Signers_DKIMSigner;

/**
 * Pour envoyer des mails avec SwiftMailer
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class SwiftMailerMailing implements MailingInterface
{
    /**
     * Expéditeur.
     */
    private string|array $from;

    /**
     * Eventuellement préciser à qui répondre.
     */
    private ?string $replyTo = null;

    /**
     * Destinataire.
     */
    private string $to;

    /**
     * Sujet du mail.
     */
    private string $subject;

    /**
     * Eventuellement laisser la possibilitée de joindre un pièce joint.
     */
    private ?string $attach = null;

    /**
     * Corp qui contient le message au format 'text/html'.
     */
    private string $body;

    /**
     * Eventuellement ajouter un corp de message au format 'text/plain'.
     */
    private ?string $bodyText = null;

    /**
     * Expéditeur du mail.
     *
     * @param string|array $from - (array numéroté) - $from[0] : email, $from[1] : prénom et nom.
     */
    public function setFrom(array|string $from): void
    {
        $this->from = $from;
    }

    /**
     * OPTIONAL
     * Eventuellement préciser à qui répondre.
     */
    public function setReplyTo(string $replyTo): self
    {
        $this->replyTo = $replyTo;

        return $this;
    }

    /**
     * Destinataire (receveur) du mail.
     */
    public function setTo(string $to): void
    {
        $this->to = $to;
    }

    /**
     * Sujet du mail.
     */
    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    /**
     * OPTIONAL
     * Pour éventuellement laisser la possibilitée de joindre un pièce joint.
     */
    public function attach(string $attach): void
    {
        $this->attach = $attach;
    }

    /**
     * Corp qui contient le message au format 'text/html'.
     */
    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    /**
     * OPTIONAL
     * Pour éventuellement ajouter un cord de message au format 'text/plain'.
     */
    public function addBodyText(string $bodyText): void
    {
        $this->bodyText = $bodyText;
    }

    /**
     * Essayer d'envoyer le mail.
     *
     * @return bool - True si le mail est bien parti.
     */
    public function send(): bool
    {
        // si array -> Email + Nom de l'expediteur
        // si pas array -> que Email de l'expediteur
        $from = is_array($this->from) ? [$this->from[0] => $this->from[1]] : $this->from;

        // pour autoriser plusieurs email destinataires (plusieurs receveurs)
        $to = explode(',', $this->to);

        // Create the Mailer using your created Transport
        $mailer = new Swift_Mailer($this->getTransport());

        // Create the message
        $message = new Swift_Message();
        $message->setSubject($this->subject)
            ->setFrom($from)
            ->setTo($to)
            ->setBody($this->body, 'text/html');

        if ($this->replyTo !== null) {
            $message->setReplyTo($this->replyTo);
        }

        if ($this->bodyText !== null) {
            $message->addPart($this->bodyText, 'text/plain');
        }

        if (Helper::config('mail')['dkim_private_key'] !== null && trim(Helper::config('mail')['dkim_private_key']) !== '') {
            if (file_exists(Helper::basePath(Helper::config('mail')['dkim_private_key']))) {
                $message->attachSigner($this->getSigner());
            }
        }

        if ($this->attach !== null) {
            if (file_exists($this->attach)) {
                $message->attach(Swift_Attachment::fromPath($this->attach));
            }
        }

        return ($mailer->send($message) !== 0);
    }

    /**
     * Create the Transport.
     *
     * @return Swift_SendmailTransport|Swift_SmtpTransport
     */
    private function getTransport()
    {
        if (Helper::config('mail')['driver'] === 'mail') {
            $transport = new Swift_SendmailTransport(Helper::config('mail')['sendmail']);
        } elseif (Helper::config('mail')['driver'] === 'smtp') {
            $transport = new Swift_SmtpTransport(
                Helper::config('mail')['host'],
                Helper::config('mail')['port'],
                Helper::config('mail')['encryption']
            );

            $transport->setUsername(Helper::config('mail')['username'])
                ->setPassword(Helper::config('mail')['password']);
        } else {
            $transport = new Swift_SendmailTransport(Helper::config('mail')['sendmail']);

            Helper::getExceptionOrLog('"driver" on mailer must be "mail" or "smtp"');
        }

        return $transport;
    }

    /**
     * Pour signer mail avec un clé DKIM.
     *
     * @return Swift_Signers_DKIMSigner
     */
    private function getSigner(): Swift_Signers_DKIMSigner
    {
        $privateKey = file_get_contents(Helper::basePath(Helper::config('mail')['dkim_private_key']));

        return new Swift_Signers_DKIMSigner(
            $privateKey,
            Helper::config('mail')['dkim_domain'],
            Helper::config('mail')['dkim_selector']
        );
    }
}
