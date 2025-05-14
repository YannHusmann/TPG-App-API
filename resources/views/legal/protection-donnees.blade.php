<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>Protection des données</title>
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

  <h1>Protection des données</h1>
  <p>Nous collectons uniquement les informations nécessaires au fonctionnement de l'application (email, nom d'utilisateur, avatar, signalements).</p>
  <p>Ces données sont stockées de manière sécurisée et ne sont jamais partagées avec des tiers sans consentement explicite.</p>
  <p>Vous pouvez demander la suppression de votre compte et de vos données à tout moment en nous contactant via l'adresse email fournie dans les mentions légales.</p>

  <script>
    function sendBackMessage() {
      if (window.ReactNativeWebView && window.ReactNativeWebView.postMessage) {
        window.ReactNativeWebView.postMessage('goBack');
      }
    }
  </script>
</body>
</html>
