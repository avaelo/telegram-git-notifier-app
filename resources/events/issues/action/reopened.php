<?php
/**
 * @var $payload mixed
 */

$message = "⚠️⚠️ <b>Issue has been reopened</b> to <a href=\"{$payload->issue->html_url}\">{$payload->repository->full_name}#{$payload->issue->number}</a> by <a href=\"{$payload->issue->user->html_url}\">@{$payload->issue->user->login}</a>\n\n";

$message .= "📢 <b>{$payload->issue->title}</b>\n";

if (isset($payload->issue->assignee)) {
    $message .= "🙋 Assignee: <a href=\"{$payload->issue->assignee->html_url}\">@{$payload->issue->assignee->login}</a>\n\n";
}

$message .= require __DIR__ . '/../partials/_body.php';

echo $message;
