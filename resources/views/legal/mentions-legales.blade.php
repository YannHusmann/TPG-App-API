<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>Mentions Légales</title>
  <style>
    html, body {
      margin: 0;
      padding: 0;
      background-color: #fff;
    }
    body {
      font-family: sans-serif;
      padding: calc(env(safe-area-inset-top, 20px) + 20px) 20px 20px 20px;
      line-height: 1.6;
      color: #333;
    }
    h1 {
      color: #fd5312;
      margin-top: 0;
    }
    .back-button {
      color: #fd5312;
      font-weight: bold;
      text-decoration: none;
      display: inline-block;
      margin-bottom: 20px;
      font-size: 16px;
    }
  </style>
</head>
<body>
  <a class="back-button" href="#" onclick="sendBackMessage()">← Retour</a>

  <h1>Mentions légales</h1>
  <p>Cette application est développée dans le cadre d’un projet universitaire à la HEG de Genève.</p>
  <p>Toutes les informations présentes sont fournies à titre informatif et n'ont aucune valeur contractuelle.</p>
  <p><strong>Cette application n’est pas affiliée, soutenue ou validée par les Transports publics genevois (TPG).</strong> Elle n’est en aucun cas destinée à un usage commercial ou à concurrencer l’application officielle des TPG.</p>
  <p>Responsable de la publication : <strong>Yann Husmann</strong></p>
  <p>Email de contact : <strong>MAIL</strong></p>
  <p>Hébergement : HEBERGEUR</p>
  <p>Le contenu de cette application ne peut être reproduit sans autorisation préalable.</p>

  <script>
    function sendBackMessage() {
      if (window.ReactNativeWebView && window.ReactNativeWebView.postMessage) {
        window.ReactNativeWebView.postMessage('goBack');
      }
    }
  </script>
</body>
</html>
