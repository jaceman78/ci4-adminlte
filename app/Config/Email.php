<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    public string $fromEmail;
    public string $fromName;
    public string $recipients = '';

    /**
     * The "user agent"
     */
    public string $userAgent = 'CodeIgniter';

    /**
     * The mail sending protocol: mail, sendmail, smtp
     */
    public string $protocol;

    /**
     * The server path to Sendmail.
     */
    public string $mailPath = '/usr/sbin/sendmail';

    /**
     * SMTP Server Hostname
     */
    public string $SMTPHost;

    /**
     * SMTP Username
     */
    public string $SMTPUser;

    /**
     * SMTP Password
     */
    public string $SMTPPass;

    /**
     * SMTP Port
     */
    public int $SMTPPort;

    /**
     * SMTP Timeout (in seconds)
     */
    public int $SMTPTimeout;

    /**
     * Enable persistent SMTP connections
     */
    public bool $SMTPKeepAlive;

    /**
     * SMTP Encryption.
     *
     * @var string '', 'tls' or 'ssl'. 'tls' will issue a STARTTLS command
     *             to the server. 'ssl' means implicit SSL. Connection on port
     *             465 should set this to ''.
     */
    public string $SMTPCrypto;

    public bool $SMTPAuth;

    public function __construct()
    {
        parent::__construct();

        // Carregar configurações do .env
        $this->fromEmail      = getenv('email.fromEmail') ?: 'antonioneto@aejoaodebarros.pt';
        $this->fromName       = getenv('email.fromName') ?: 'António Neto - Escola Digital JB';
        $this->protocol       = getenv('email.protocol') ?: 'smtp';
        $this->SMTPHost       = getenv('email.SMTPHost') ?: 'smtp.gmail.com';
        $this->SMTPUser       = getenv('email.SMTPUser') ?: '';
        $this->SMTPPass       = getenv('email.SMTPPass') ?: '';
        $this->SMTPPort       = (int)(getenv('email.SMTPPort') ?: 587);
        $this->SMTPTimeout    = (int)(getenv('email.SMTPTimeout') ?: 10);
        $this->SMTPKeepAlive  = filter_var(getenv('email.SMTPKeepAlive'), FILTER_VALIDATE_BOOLEAN);
        $this->mailType       = getenv('email.mailType') ?: 'html';
        $this->charset        = getenv('email.charset') ?: 'UTF-8';
        $this->SMTPCrypto     = getenv('email.SMTPCrypto') ?: 'tls';
        $this->SMTPAuth       = filter_var(getenv('email.SMTPAuth'), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Enable word-wrap
     */
    public bool $wordWrap = true;

    /**
     * Character count to wrap at
     */
    public int $wrapChars = 76;

    /**
     * Type of mail, either 'text' or 'html'
     */
    public string $mailType = 'text';

    /**
     * Character set (utf-8, iso-8859-1, etc.)
     */
    public string $charset = 'UTF-8';

    /**
     * Whether to validate the email address
     */
    public bool $validate = false;

    /**
     * Email Priority. 1 = highest. 5 = lowest. 3 = normal
     */
    public int $priority = 3;

    /**
     * Newline character. (Use “\r\n” to comply with RFC 822)
     */
    public string $CRLF = "\r\n";

    /**
     * Newline character. (Use “\r\n” to comply with RFC 822)
     */
    public string $newline = "\r\n";

    /**
     * Enable BCC Batch Mode.
     */
    public bool $BCCBatchMode = false;

    /**
     * Number of emails in each BCC batch
     */
    public int $BCCBatchSize = 200;

    /**
     * Enable notify message from server
     */
    public bool $DSN = false;
}
