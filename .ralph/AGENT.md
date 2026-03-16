# Ralph Agent Configuration — AXIOM

## Stack
- PHP 8.1.10 (Laragon)
- CodeIgniter 3
- MySQL 8.0.30 (banco: db_axiom)
- Apache via Laragon
- Node.js v24 (Evolution API)

## Acessar o projeto
URL: http://localhost/axiom/
Admin: http://localhost/axiom/admin
Login padrão: admin / (conforme configurado no banco)

## Banco de dados
```bash
# Conectar ao MySQL do Laragon
mysql -u root db_axiom
```

## Logs
- PHP errors: C:/laragon/tmp/php_errors.log
- CodeIgniter: C:/laragon/www/axiom/application/logs/log-YYYY-MM-DD.php
- Apache: C:/laragon/logs/apache2/

## Regenerar assets frontend
```bash
cd C:/laragon/www/axiom
npm install
npx grunt build-assets
```

## Módulos com Composer próprio
Após mudanças nos módulos, rode em cada um se necessário:
```bash
cd modules/backup && composer install --ignore-platform-reqs
cd modules/contactcenter && composer install --ignore-platform-reqs
cd modules/surveys && composer install --ignore-platform-reqs
```

## Evolution API (WhatsApp)
```bash
# Iniciar
C:/laragon/www/evolution/start-evolution.bat
# Ou:
cd C:/laragon/www/evolution && "C:/Program Files/nodejs/node.exe" dist/main.js
```

## Notes
- Não use `npm run build` — use `npx grunt build-assets`
- Não há suite de testes automatizados — validar via browser
- Arquivos de config sensíveis: application/config/app-config.php
