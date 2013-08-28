<?php namespace m8rge;

/**
 * Class AlternativeMail
 *
 * Simple class for utf8 html/text emails with small attachments (they are loaded in memory)
 */
class AlternativeMail
{
    /**
     * @var array
     */
    protected $to = array();

    /**
     * @var array
     */
    protected $from;

    /**
     * @var string
     */
    protected $subject;

    /**
     * @var string
     */
    protected $htmlBody;

    /**
     * @var string
     */
    protected $textBody;

    /**
     * @var array
     */
    protected $attachments = array();

    function __construct()
    {
        mb_internal_encoding('UTF-8');
    }

    /**
     * @param string $email
     * @param string $name
     * @return $this
     */
    public function setFrom($email, $name = '')
    {
        $this->from = array(
            'email' => $email,
            'name' => $name
        );

        return $this;
    }

    /**
     * @param string $email
     * @param string $name
     * @return $this
     */
    public function addTo($email, $name = '')
    {
        $this->to[] = array(
            'email' => $email,
            'name' => $name
        );

        return $this;
    }

    /**
     * @param string $file
     * @param string $forceFileName
     * @param string $forceMimeType
     * @return $this
     */
    public function addAttachment($file, $forceFileName = '', $forceMimeType = '')
    {
        $this->attachments[] = array(
            'file' => $file,
            'fileName' => $forceFileName,
            'mimeType' => $forceMimeType,
        );

        return $this;
    }

    /**
     * @param string $html
     * @return $this
     */
    public function setHtmlBody($html)
    {
        $this->htmlBody = $html;

        return $this;
    }

    /**
     * @param string $text
     * @return $this
     */
    public function setTextBody($text)
    {
        $this->textBody = $text;

        return $this;
    }

    /**
     * @param string $text
     * @return $this
     */
    public function setSubject($text)
    {
        $this->subject = $text;

        return $this;
    }

    /**
     * @param string $boundary
     * @param string[] $headers
     * @param string $body
     * @return string
     */
    protected function compilePart($boundary, $headers, $body)
    {
        array_unshift($headers, '--' . $boundary);
        $headers = implode("\r\n", $headers);
        $body = $headers . "\r\n\r\n" . $body . "\r\n\r\n";

        return $body;
    }

    /**
     * @param string $part 'html' or 'text'
     * @return array
     */
    protected function getTextPartData($part)
    {
        $contentTypes = array(
            'text' => 'text/plain',
            'html' => 'text/html',
        );
        $headers[] = "Content-Type: {$contentTypes[$part]}; charset=UTF-8";
        $headers[] = 'Content-Transfer-Encoding: base64';
        $bodyVarName = $part . 'Body';
        /** @noinspection PhpUndefinedFieldInspection */
        $body = substr(chunk_split(base64_encode($this->$bodyVarName)), 0 ,-2);

        return array($headers, $body);
    }

    /**
     * @return array
     */
    protected function getMergedTextPartData()
    {
        $body = '';
        $boundary = md5(microtime());
        $headers = array();
        if (!empty($this->textBody) && !empty($this->htmlBody)) {
            $headers[] = 'Content-Type: multipart/alternative; boundary=' . $boundary;

            list($textHeaders, $textBody) = $this->getTextPartData('text');
            list($htmlHeaders, $htmlBody) = $this->getTextPartData('html');
            $body .= $this->compilePart($boundary, $textHeaders, $textBody);
            $body .= $this->compilePart($boundary, $htmlHeaders, $htmlBody);
            $body .= '--' . $boundary . '--';
        } elseif (!empty($this->textBody)) {
            list($headers, $body) = $this->getTextPartData('text');
        } elseif (!empty($this->htmlBody)) {
            list($headers, $body) = $this->getTextPartData('html');
        }

        return array($headers, $body);
    }

    /**
     * @return array
     */
    protected function getAttachmentPartsData()
    {
        $attachments = array();
        if (!empty($this->attachments)) {
            foreach ($this->attachments as $attachment) {
                $mimeType = $attachment['mimeType'];
                if (empty($mimeType)) {
                    $info = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = finfo_file($info, $attachment['file']);
                }
                $fileName = $attachment['fileName'];
                if (empty($fileName)) {
                    $fileName = pathinfo($attachment['file'], PATHINFO_BASENAME);
                }
                $fileName = mb_encode_mimeheader($fileName);
                $headers[] = "Content-Type: $mimeType; name=\"$fileName\"";
                $headers[] = "Content-Disposition: attachment; filename=\"$fileName\"";
                $headers[] = 'Content-Transfer-Encoding: base64';
                $body = substr(chunk_split(base64_encode(file_get_contents($attachment['file']))), 0, -2);
                $attachments[] = array($headers, $body);
            }
        }

        return $attachments;
    }

    /**
     * @return array
     */
    protected function getMailData()
    {
        list($textHeaders, $textBody) = $this->getMergedTextPartData();

        $body = '';
        $boundary = md5(microtime());
        if (!empty($this->attachments)) {
            $headers[] = 'Content-Type: multipart/mixed; boundary=' . $boundary;

            $body .= $this->compilePart($boundary, $textHeaders, $textBody);
            foreach ($this->getAttachmentPartsData() as $attachmentPartData) {
                list($attachmentHeaders, $attachmentBody) = $attachmentPartData;
                $body .= $this->compilePart($boundary, $attachmentHeaders, $attachmentBody);
            }
            $body .= '--' . $boundary . '--';
        } else {
            $headers = $textHeaders;
            $body = $textBody;
        }

        return array($headers, $body);
    }

    /**
     * @return bool
     */
    public function send()
    {
        list($additionalHeaders, $body) = $this->getMailData();

        $to = array_map(
            function ($value) {
                if (!empty($value['name'])) {
                    return mb_encode_mimeheader($value['name']) . " <{$value['email']}>";
                } else {
                    return $value['email'];
                }
            },
            $this->to
        );
        $to = implode(', ', $to);
        if (!empty($this->from['email'])) {
            if (!empty($this->from['name'])) {
                $from = mb_encode_mimeheader($this->from['name']) . " <{$this->from['email']}>";
            } else {
                $from = $this->from['email'];
            }
            $additionalHeaders[] = 'From: ' . $from;
        }

        $subject = mb_encode_mimeheader($this->subject, 'UTF-8');
        $additionalHeaders = implode("\r\n", $additionalHeaders);
        return mail($to, $subject, $body, $additionalHeaders);
    }
}
