# Agenciafmd – Filament Postal

Pacote de formulários e envios de e-mail (Postal) para o painel administrativo (Admix), baseado em Filament v4 e Laravel 12. Ele fornece o CRUD completo de modelos de e-mail (destinatários, assunto, cópias), incluindo auditoria, filtros, envio de teste direto da tabela, e infraestrutura de notificação com canal de evento para integrações.

## Requisitos

- PHP ^8.4
- Laravel ^12.0
- Filament ^4.0
- agenciafmd/filament-admix v1.x-dev | dev-master

## Instalação

1. Instale o pacote via Composer:

```bash
composer require agenciafmd/filament-postal
```

2. Execute as migrações:

```bash
php artisan migrate
```

3. (Opcional) Popule o banco:

```bash
php artisan db:seed --class=Agenciafmd\\Postal\\Database\\Seeders\\PostalSeeder
```

## Ativando no painel Filament

Este pacote inclui um Plugin Filament que registra o `PostalResource` automaticamente. Adicione o plugin na config do admix `config/filament-admix.php`:

```php
use Agenciafmd\Postal\PostalPlugin;

return [
    'plugins' => [
        PostalPlugin::class,
    ],
];
```

Após isso, o menu "Formulários" aparecerá no painel, com as páginas de Listar, Criar e Editar.

## Recursos incluídos

- Model: `Agenciafmd\Postal\Models\Postal` (Soft Deletes, HasFactory, Notifiable, Auditing e limpeza programada via `prunable()`)
- Migração: cria a tabela `postal` com campos principais (`name`, `slug` único, `to`, `to_name`, `subject`, `cc` e `bcc` em array, flag `is_active`, timestamps e soft deletes)
- Factory e Seeder: `PostalFactory` e `PostalSeeder`
- Resource Filament: `PostalResource` com páginas `ListPostal`, `CreatePostal`, `EditPostal`
- Formulário: `PostalForm` com seções "General" e "Information"
- Tabela: `PostalTable` com colunas, filtros, ação de envio de teste e ordenação padrão
- Serviço: `PostalService` (sugestões de e-mails únicas para `cc`/`bcc`)
- Traduções pt_BR prontas
- Views Blade para e-mail com tema/layout próprios e ícones (publicáveis)

## Notificações, Canais e Eventos

O pacote provê um fluxo de notificação pronto para integrar com filas e eventos:

- `Agenciafmd\Postal\Notifications\SendNotification` (implements `ShouldQueue`):
  - Canais: `MailChannel` e `EventChannel` (custom do pacote)
  - Markdown do e-mail: `filament-postal::markdown.email`
  - Tema: `filament-postal::theme.tabler`
  - Respeita `cc`/`bcc` definidos no registro Postal e permite `replyTo` via `from` no construtor
  - Aceita `attach` (lista de paths de arquivos) – ver comentário no código para futura troca por `attachFromStorage`

- `Agenciafmd\Postal\Channels\EventChannel`:
  - Extrai pares `chave: valor` das linhas de `introLines` do conteúdo do e-mail
  - Normaliza chaves (`slug`) e emite um evento com os dados do formulário e `source` apontando para o `slug` do registro

- `Agenciafmd\Postal\Events\NotificationSent`:
  - Evento simples contendo o array de dados processado pelo canal

Exemplo simples de uso manual (fora da tabela), assumindo `$postal` é uma instância de `Postal`:

```php
use Agenciafmd\Postal\Models\Postal;
use Agenciafmd\Postal\Notifications\SendNotification;

$postal = Postal::query()
    ->where('slug', 'contato')
    ->first();

if (!$postal) {
    $this->dispatch(
        event: 'swal',
        level: 'error',
        message: 'Formulário de disparo não configurado.',
    );

    return;
}

$postal->notify(new SendNotification(data: [
    'greeting' => 'Contato',
    'introLines' => [
        "**Nome:** {$data['name']}",
        "**E-mail:** {$data['email']}",
        "**Telefone:** {$data['phone']}",
    ],
], from: [
    $data['email'] => $data['name']
]));
```

## Views e Assets publicáveis

As views de e-mail e os ícones utilizados no template podem ser publicados:

```bash
php artisan vendor:publish --tag=filament-postal:mail --no-interaction
php artisan vendor:publish --tag=filament-postal:images --no-interaction
```

- Views de e-mail: `resources/views/vendor/agenciafmd/filament-postal/mail` (inclui `layout`, `markdown/message`, `markdown/email`, componentes como `icon`, `header`, `footer`, etc.)
- Imagens: `public/vendor/agenciafmd/filament-postal/images/{color}/{icon}.png`

## Atualização

Para manter os assets atualizados, adicione o comando `@php artisan vendor:publish --tag=filament-postal:images --ansi --force` ao seu `post-update-cmd` no `composer.json` do seu projeto.

## Configuração

Arquivo: `config/filament-postal.php`

```php
return [
    'name' => 'Postal',
];
```

Atualmente, a configuração define apenas o nome exibido em traduções/labels. Ajustes adicionais podem ser introduzidos conforme evolução do pacote.

## Auditoria

O `PostalResource` inclui o relation manager `Tapp\FilamentAuditing\RelationManagers\AuditsRelationManager`, exibindo o histórico de auditorias quando o pacote `tapp/filament-auditing` for utilizado pelo projeto via `filament-admix`.

## Licença

Este pacote é software livre e está disponível nos termos da licença MIT.
