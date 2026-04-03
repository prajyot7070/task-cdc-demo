<?php
namespace App;

class Task
{
    public string $title;
    public string $status;

    public function __construct(array $data)
    {
        $this->title = $data['title'] ?? '';
        $this->status = $data['status'] ?? 'open';
    }

    public static function validate(string $title): bool
    {
        return preg_match("/^[\w\s\-]{3,255}$/", $title) === 1;
    }
}
