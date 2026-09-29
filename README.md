# Filament – Postal

[![Downloads](https://img.shields.io/packagist/dt/agenciafmd/filament-postal.svg?style=flat-square)](https://packagist.org/packages/agenciafmd/filament-postal)
[![Licença](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

Adiciona ao Admix o cadastro dos formulários do site (destinatário, assunto, cópias) e a notificação pronta para disparar os e-mails desses formulários, com envio de teste direto da listagem e evento para integrações (ex.: `agenciafmd/filament-leads`).

## Requisitos

- PHP ^8.4
- Laravel ^12.0 | ^13.0
- Filament ^5.0
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

3. Populando o banco com dados de testes

Adicione o seeder no `database/seeders/DatabaseSeeder.php`:

```php
use Agenciafmd\Postal\Database\Seeders\PostalSeeder;

$this->call([
    PostalSeeder::class,
]);
```

Ou rode o seeder manualmente:

```bash
php artisan db:seed --class="Agenciafmd\Postal\Database\Seeders\PostalSeeder"
```

## Ativando no painel

Adicione o plugin na config do admix `config/filament-admix.php`:

```php
use Agenciafmd\Postal\PostalPlugin;

return [
    'plugins' => [
        PostalPlugin::class,
    ],
];
```

Após isso, o menu **Formulários** aparecerá no painel, com as páginas de Listar, Criar e Editar. Cada registro da listagem tem a ação **Enviar**, que dispara um e-mail de teste para o destinatário e as cópias configuradas.

## Configuração

Arquivo: `config/filament-postal.php`

```php
return [
    'name' => 'Postal',
    'navigation_group' => null,
    'navigation_sort' => 3,
];
```

| Chave              | Padrão   | Descrição                                                     |
|--------------------|----------|---------------------------------------------------------------|
| `name`             | `Postal` | Nome do pacote.                                               |
| `navigation_group` | `null`   | Grupo do menu em que o Resource aparece (`null` = sem grupo). |
| `navigation_sort`  | `3`      | Posição do item no menu.                                      |

Formulários na lixeira há mais de 30 dias são removidos pelo `model:prune`, agendado diariamente às 03h (minuto definido em `filament-admix.schedule.minutes`).

## Uso

### Enviando um formulário

Cada registro do Postal (`Agenciafmd\Postal\Models\Postal`) é notificável: o e-mail vai para `to`/`to_name`, com as cópias de `cc` e `bcc`. Busque o registro pelo `slug` (campo "Identificador") e notifique com `Agenciafmd\Postal\Notifications\SendNotification`. Exemplo em um componente Livewire, com os dados do formulário em `$data`:

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

Parâmetros do `SendNotification`:

| Parâmetro | Descrição                                                                                                                                             |
|-----------|-------------------------------------------------------------------------------------------------------------------------------------------------------|
| `data`    | Conteúdo do e-mail: `greeting`, `introLines`, `actionText`, `actionUrl` e `outroLines`. Sem `greeting`/`introLines`, usa a saudação e o texto padrão. |
| `from`    | `['e-mail' => 'nome']` usado como `replyTo`.                                                                                                          |
| `attach`  | Lista de caminhos de arquivos anexados ao e-mail.                                                                                                     |
| `subject` | Assunto do e-mail. Quando omitido, usa `{APP_NAME} \| {assunto do registro}`.                                                                          |

A notificação implementa `ShouldQueue` (4 tentativas, backoff de 10, 30 e 60 segundos), usa o markdown `filament-postal::markdown.email` com o tema `filament-postal::theme.tabler` e adiciona o header `X-Mailgun-Tag` com o `APP_NAME`.

### Evento `NotificationSent`

Além do e-mail, a notificação passa pelo canal `Agenciafmd\Postal\Channels\EventChannel`, que:

- lê as linhas de `introLines` no formato `chave: valor` (removendo os `*` do markdown);
- normaliza as chaves com `slug` e descarta valores vazios;
- adiciona `source` com o `slug` do registro do Postal;
- dispara `Agenciafmd\Postal\Events\NotificationSent`, com esses dados na propriedade `$data`.

No exemplo acima, o evento recebe `['nome' => ..., 'e-mail' => ..., 'telefone' => ..., 'source' => 'contato']`. O `agenciafmd/filament-leads` escuta esse evento para criar leads; para outras integrações, registre um listener:

```php
use Agenciafmd\Postal\Events\NotificationSent;
use Illuminate\Support\Facades\Event;

Event::listen(NotificationSent::class, function (NotificationSent $event): void {
    // $event->data
});
```

## Permissões

O `PostalResource` entra automaticamente no controle de permissões por Grupos do Admix. Usuários sem grupo são administradores e têm acesso total.

Permissões extras (`getExtraPermissions()`):

| Permissão | Descrição                                          |
|-----------|----------------------------------------------------|
| `send`    | Libera a ação **Enviar** (e-mail de teste) na listagem. |

## Auditoria

O `PostalResource` inclui o relation manager `Tapp\FilamentAuditing\RelationManagers\AuditsRelationManager`, exibindo o histórico de auditorias do registro.

## Publicação de assets

As views de e-mail e os ícones usados no template podem ser publicados:

```bash
php artisan vendor:publish --tag=filament-postal:mail --no-interaction
php artisan vendor:publish --tag=filament-postal:images --no-interaction
```

- `filament-postal:mail`: views de e-mail em `resources/views/vendor/agenciafmd/filament-postal/mail` (`layout`, `markdown/message`, `markdown/email`, `theme/tabler.css` e componentes como `icon`, `header`, `footer`, `button`, `greeting` e `subcopy`).
- `filament-postal:images`: ícones em `public/vendor/agenciafmd/filament-postal/images/icons/{cor}/{icone}.png` (cores `blue`, `gray`, `green`, `red` e `yellow`).

## Atualização

Para manter os ícones atualizados, adicione o comando abaixo ao `post-update-cmd` do `composer.json` do projeto:

```json
{
    "scripts": {
        "post-update-cmd": [
            "@php artisan vendor:publish --tag=filament-postal:images --ansi --force"
        ]
    }
}
```

## Licença

Este pacote é software livre e está disponível nos termos da licença MIT.
