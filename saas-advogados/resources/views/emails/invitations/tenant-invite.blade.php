<x-mail::message>
# Convite para o seu SaaS

Você foi convidado para se juntar ao nosso SaaS. Clique no botão abaixo para aceitar o convite e criar sua conta:

<x-mail::button :url="$url">
Aceitar Convite
</x-mail::button>

Se você não esperava este convite, pode ignorar este email.

Obrigado,
{{ config("app.name") }}
</x-mail::message>
