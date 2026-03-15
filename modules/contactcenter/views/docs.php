<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Documentation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/4.18.2/swagger-ui.css" />
</head>

<body>
    <div id="swagger-ui"></div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/4.18.2/swagger-ui-bundle.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/4.18.2/swagger-ui-standalone-preset.js"></script>
    <script>
        // Obter dinamicamente o caminho base para o JSON
        const jsonUrl = "<?= site_url('contactcenter/swagger/json'); ?>";
        const apiBasePath = "<?= site_url('contactcenter'); ?>";

        const ui = SwaggerUIBundle({
            url: jsonUrl,
            dom_id: '#swagger-ui',
            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIStandalonePreset
            ],
            layout: "BaseLayout",
            deepLinking: true,
            validatorUrl: null,
            docExpansion: 'none',
            defaultModelRendering: 'schema',
            requestInterceptor: (req) => {
                // Ajusta todas as requisições para começar com apiBasePath
                req.url = apiBasePath + req.url.replace(/.*\/contactcenter/, '');

                // Adiciona o token de autenticação, se presente
                const token = localStorage.getItem("token");
                if (token) {
                    req.headers['AXIOM-Authorization'] = `${token}`;
                }
                return req;
            }
        });

        document.addEventListener("DOMContentLoaded", () => {
            document.querySelector("#swagger-ui .authorize").addEventListener("click", () => {
                const token = prompt("Insira o Token:");
                if (token) {
                    localStorage.setItem("token", token); // Armazena o token no localStorage
                    alert("Token salvo! Recarregue a página para aplicar.");
                }
            });
        });
    </script>
    <style>
    .url {
        display: none;
    }
</style>
</body>

</html>