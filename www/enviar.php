<?php

$message = '<html>
    <body>
        Nome: ' . $_POST['nome'] . '<br>
        Email: ' . $_POST['email'] . '<br>
        ' . $_POST['assunto'] . ': ' . nl2br($_POST['mensagem']) . '
    </body>
</html>';

$headers = 'From: webmaster@lithharbor.net' . "\r\n" .
        'Reply-To: webmaster@lithharbor.net' . "\r\n" .
        'Content-type: text/html' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();

mail('bob@wizley.com.br', 'Contato através do site', $message, $headers);

header('Location: contato.php?msg=' . base64_encode('E-mail enviado com sucesso!'));
