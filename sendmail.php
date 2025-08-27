<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ime = htmlspecialchars($_POST['ime']);
    $email = htmlspecialchars($_POST['email']);
    $izdelki = isset($_POST['izdelki']) ? implode(", ", $_POST['izdelki']) : "Ni izbranih izdelkov";

    $sporocilo = "Novo povpraševanje:\n\n";
    $sporocilo .= "Ime: $ime\n";
    $sporocilo .= "Email: $email\n\n";
    $sporocilo .= "Izbrani izdelki: $izdelki\n\n";

    // Dodamo komentarje
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'komentar_') === 0 && !empty($value)) {
            $sporocilo .= ucfirst(str_replace("_", " ", $key)) . ": " . $value . "\n";
        }
    }

    // Nastavi prejemnika
    $to = "tvojmail@example.com"; // <-- tukaj vpiši svoj email
    $subject = "Novo povpraševanje za 3D izdelke";

    // Za pošiljanje z datotekami
    $boundary = md5(time());
    $headers = "From: $email\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

    $body = "--$boundary\r\n";
    $body .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";
    $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $body .= $sporocilo . "\r\n";

    // Dodamo datoteke
    foreach ($_FILES as $file) {
        if ($file['error'] == 0) {
            $file_content = chunk_split(base64_encode(file_get_contents($file['tmp_name'])));
            $body .= "--$boundary\r\n";
            $body .= "Content-Type: application/octet-stream; name=\"" . $file['name'] . "\"\r\n";
            $body .= "Content-Disposition: attachment; filename=\"" . $file['name'] . "\"\r\n";
            $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
            $body .= $file_content . "\r\n";
        }
    }

    $body .= "--$boundary--";

    if (mail($to, $subject, $body, $headers)) {
        echo "✅ Povpraševanje uspešno poslano.";
    } else {
        echo "❌ Prišlo je do napake pri pošiljanju.";
    }
}
?>
