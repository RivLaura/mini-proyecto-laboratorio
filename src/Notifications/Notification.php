<?php

interface Notification
{
    public function send(string $message): void;
}