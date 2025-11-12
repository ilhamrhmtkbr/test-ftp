<?php

namespace ilhamrhmtkbr\App\Config;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Email
{
    private static PHPMailer $email;

    public function __construct()
    {
        // Inisialisasi PHPMailer
        self::$email = new PHPMailer(true);
    }

    public static function sendEmail($to, $subject, $body)
    {
        require_once __DIR__ . '/../../config/email.php';
        $config = getEmailConfig();
        try {
            // Konfigurasi Server SMTP
            self::$email->isSMTP();
            self::$email->Host = 'smtp.gmail.com';
            self::$email->SMTPAuth = true;
            self::$email->Username = $config['email']['username'];
            self::$email->Password = $config['email']['password'];
            self::$email->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            self::$email->Port = 587;

            // Pengirim Email
            self::$email->setFrom($config['email']['username'], 'IOGM Talent Hub');
            self::$email->addAddress($to);

            // Konten Email
            self::$email->isHTML(true);
            self::$email->Subject = $subject;
            self::$email->Body    = $body;
            self::$email->AltBody = strip_tags($body);

            // Mengirim email
            self::$email->send();
            echo 'Email berhasil dikirim.';
        } catch (Exception $e) {
            echo "Email gagal dikirim. Error: " . self::$email->ErrorInfo;
        }
    }
}
